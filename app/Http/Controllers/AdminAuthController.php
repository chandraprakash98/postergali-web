<?php

namespace App\Http\Controllers;

use App\Models\AdSubcategory;
use App\Models\BatchRunLog;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Referral;
use App\Models\User;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Notification;
use App\Services\PosterExpiryNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Password is required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if (!$user->is_admin) {
            return back()->withErrors(['email' => 'Unauthorized access'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
    }

    public function storeNotification(Request $request)
    {
        $data = $request->validate([
            'device_id' => 'nullable|string|max:255',
            'fcm_tocken' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
        ]);

        if (empty($data['device_id']) && empty($data['fcm_tocken']) && empty($data['mobile'])) {
            return response()->json([
                'message' => 'Please provide at least one of device_id, fcm_tocken, or mobile.',
            ], 422);
        }

        $notification = Notification::create($data);

        return response()->json([
            'message' => 'Notification record stored successfully.',
            'notification' => $notification,
        ], 201);
    }

    private function renderDashboard(string $active, array $data = [])
    {
        return view('admin.dashboard', array_merge([
            'active'      => $active,
            'stats'       => $this->getStats(),
            'batchStatus' => $this->getBatchStatus(),
        ], $data));
    }

    public function dashboard()
    {
        return $this->renderDashboard('all', ['allAds' => $this->getAllAds()]);
    }

    public function allAds()
    {
        return $this->renderDashboard('all', ['allAds' => $this->getAllAds()]);
    }

    public function showAd(string $type, int $id)
    {
        if (!in_array($type, ['job', 'offer'])) {
            abort(404);
        }

        $model = $type === 'job' ? Job::findOrFail($id) : Offer::findOrFail($id);
        $plan = Plan::find($model->plan_id) ?: $model->plan;

        return view('admin.ad-details', [
            'type'               => $type,
            'ad'                 => $model,
            'plan'               => $plan,
            'jobSubcategories'   => AdSubcategory::namesForType('job'),
            'offerSubcategories' => AdSubcategory::namesForType('offer'),
        ]);
    }

    public function updateAdStatus(Request $request, string $type, int $id)
    {
        if (!in_array($type, ['job', 'offer'])) {
            abort(404);
        }

        $action = $request->input('action', 'status');

        if ($action === 'status') {
            $data = $request->validate([
                'status' => 'required|in:approved,rejected',
                'comment' => 'nullable|string|max:255',
                'subcategory' => 'nullable|string|max:100',
            ]);

            $model = $type === 'job' ? Job::findOrFail($id) : Offer::findOrFail($id);

            $model->status = $data['status'];
            if (isset($data['subcategory'])) {
                $model->subcategory = $data['subcategory'];
            }
            if ($type === 'job') {
                $model->status_comment = $data['comment'];
            } else {
                $model->status_note = $data['comment'];
            }
            $model->reviewed_by = Auth::user()->name;

            $planDuration = Plan::find($model->plan_id)?->duration ?? '1 day';
            if ($data['status'] === 'approved') {
                $model->approved_at = $model->approved_at ?: now();
                $model->expires_at = $this->calculateExpiryFromDuration($model->approved_at, $planDuration);
            } else {
                $model->approved_at = null;
                $model->expires_at = null;
            }

            $model->save();

            if ($data['status'] === 'approved') {
                $this->handleReferralCredit($model);
            }

            // Send FCM Notification using device_id as FCM token
            try {
                $firebaseCredentials = env('FIREBASE_CREDENTIALS', 'storage/app/firebase/firebase-service-account.json');
                $serviceAccountPath = base_path($firebaseCredentials);
                $factory = (new Factory())->withServiceAccount($serviceAccountPath);
                $messaging = $factory->createMessaging();

                // Use device_id directly as FCM token
                $fcmToken = $model->device_id;

                if (empty($fcmToken)) {
                    \Log::warning('Device ID not found for model ID: ' . $model->id);
                    return;
                }

                $title = $data['status'] === 'approved' ? 'Ad Approved ✓' : 'Ad Rejected ✕';
                $body = $data['status'] === 'approved' 
                    ? 'Your ad has been approved and is now live!' 
                    : ('Your ad was rejected. ' . ($data['comment'] ?? 'Contact support for details.'));

                $message = CloudMessage::withTarget('token', $fcmToken)
                    ->withNotification(FcmNotification::create($title, $body))
                    ->withData([
                        'ad_id' => (string) $model->id,
                        'type' => $type,
                        'status' => $data['status'],
                        'timestamp' => now()->toIso8601String(),
                    ]);

                $messaging->send($message);
                
                \Log::info('FCM notification sent successfully for device_id: ' . $fcmToken);
            } catch (\Throwable $e) {
                \Log::error('FCM send failed for device_id ' . ($model->device_id ?? 'unknown') . ': ' . $e->getMessage());
            }
        } else {
            // subcategory-only update (no notifications)
            $data = $request->validate([
                'subcategory' => 'nullable|string|max:100',
            ]);

            $model = $type === 'job' ? Job::findOrFail($id) : Offer::findOrFail($id);
            if (isset($data['subcategory'])) {
                $model->subcategory = $data['subcategory'];
                $model->save();
            }
        }

        return redirect()->route('admin.ad.show', ['type' => $type, 'id' => $id])->with('success', 'Ad updated successfully.');
    }

    public function pendingAds()
    {
        $jobs = Job::where('status', 'pending')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Pending Verification',
            'date' => $job->created_at ? $job->created_at->format('M d, Y') : 'N/A',
            'created_at' => $job->created_at ?? now(),
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'pending')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Pending Verification',
            'date' => $offer->created_at ? $offer->created_at->format('M d, Y') : 'N/A',
            'created_at' => $offer->created_at ?? now(),
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return $this->renderDashboard('pending', ['allAds' => $allAds]);
    }

    public function liveAds()
    {
        $jobs = Job::where('status', 'approved')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Live',
            'date' => $job->created_at ? $job->created_at->format('M d, Y') : 'N/A',
            'created_at' => $job->created_at ?? now(),
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'approved')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Live',
            'date' => $offer->created_at ? $offer->created_at->format('M d, Y') : 'N/A',
            'created_at' => $offer->created_at ?? now(),
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return $this->renderDashboard('live', ['allAds' => $allAds]);
    }

    public function expiredAds()
    {
        $jobs = Job::where('status', 'rejected')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Rejected',
            'date' => $job->created_at ? $job->created_at->format('M d, Y') : 'N/A',
            'created_at' => $job->created_at ?? now(),
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'rejected')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Rejected',
            'date' => $offer->created_at ? $offer->created_at->format('M d, Y') : 'N/A',
            'created_at' => $offer->created_at ?? now(),
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return $this->renderDashboard('expired', ['allAds' => $allAds]);
    }

    public function pricingInfo()
    {
        $plans = Plan::orderBy('price')->get();
        return $this->renderDashboard('pricing', ['plans' => $plans]);
    }

    public function createPlan()
    {
        return view('admin.plans.form', ['plan' => null]);
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'plan_title' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        Plan::create($data);

        return redirect()->route('admin.pricingInfo')->with('success', 'Plan created successfully.');
    }

    public function editPlan(Plan $plan)
    {
        return view('admin.plans.form', ['plan' => $plan]);
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'plan_title' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $plan->update($data);

        return redirect()->route('admin.pricingInfo')->with('success', 'Plan updated successfully.');
    }

    public function referrals()
    {
        $referrals = Referral::orderBy('created_at', 'desc')->get();
        return $this->renderDashboard('referrals', ['referrals' => $referrals]);
    }

    private function getAllAds()
    {
        $jobs = Job::all()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => ucfirst($job->status),
            'date' => $job->created_at ? $job->created_at->format('M d, Y') : 'N/A',
            'created_at' => $job->created_at ?? now(),
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::all()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => ucfirst($offer->status),
            'date' => $offer->created_at ? $offer->created_at->format('M d, Y') : 'N/A',
            'created_at' => $offer->created_at ?? now(),
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        return $jobs->concat($offers)->sortByDesc('created_at')->values();
    }

    private function getStats()
    {
        return [
            'pending' => Job::where('status', 'pending')->count() + Offer::where('status', 'pending')->count(),
            'live' => Job::where('status', 'approved')->count() + Offer::where('status', 'approved')->count(),
            'expired' => Job::where('status', 'rejected')->count() + Offer::where('status', 'rejected')->count(),
        ];
    }

    private function handleReferralCredit($model): void
    {
        $phoneNumber = null;

        if ($model instanceof Job) {
            $phoneNumber = $model->phone_number;
        } elseif ($model instanceof Offer) {
            $phoneNumber = $model->mobile_number;
        }

        if (empty($phoneNumber)) {
            return;
        }

        $normalizedPhone = preg_replace('/[^0-9]/', '', (string) $phoneNumber);
        if ($normalizedPhone === '') {
            return;
        }

        $referral = Referral::where('referral_mobile', $normalizedPhone)->first();
        if (!$referral) {
            return;
        }

        $referrerCustomer = Customer::where('mobile', preg_replace('/[^0-9]/', '', (string) $referral->referrer_mobile))->first();
        if (!$referrerCustomer) {
            return;
        }

        $credit = CustomerCredit::where('customer_id', $referrerCustomer->customer_id)->first();
        if (!$credit) {
            $credit = CustomerCredit::create([
                'customer_id' => $referrerCustomer->customer_id,
                'balance' => 0,
            ]);
        }

        $credit->balance = (float) $credit->balance + 100;
        $credit->save();

        $referral->status = 'Success';
        $referral->save();
    }

    private function calculateExpiryFromDuration($approvedAt, string $duration)
    {
        $approvedAt = $approvedAt instanceof \Carbon\Carbon ? $approvedAt : Carbon::parse($approvedAt);
        $value = trim(strtolower($duration));

        if (preg_match('/^(\d+)\s*(day|days|d)$/', $value, $matches)) {
            return $approvedAt->copy()->addDays((int) $matches[1]);
        }
        if (preg_match('/^(\d+)\s*(week|weeks|w)$/', $value, $matches)) {
            return $approvedAt->copy()->addWeeks((int) $matches[1]);
        }
        if (preg_match('/^(\d+)\s*(month|months|m)$/', $value, $matches)) {
            return $approvedAt->copy()->addMonths((int) $matches[1]);
        }
        if (preg_match('/^(\d+)\s*(year|years|y)$/', $value, $matches)) {
            return $approvedAt->copy()->addYears((int) $matches[1]);
        }
        if (is_numeric($value)) {
            return $approvedAt->copy()->addDays((int) $value);
        }

        $numericValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        if ($numericValue > 0) {
            return $approvedAt->copy()->addDays($numericValue);
        }

        return $approvedAt->copy()->addDay();
    }

    private function getBatchStatus(): array
    {
        $enabled       = (bool) config('posters.expiry_notification.enabled', true);
        $schedule      = PosterExpiryNotificationService::getSchedule();
        $timezone      = PosterExpiryNotificationService::getTimezone();
        $scheduleHuman = PosterExpiryNotificationService::getScheduleHuman();
        $windowHours   = (int) config('posters.expiry_notification.window_hours', 24);

        $totalRuns          = BatchRunLog::count();
        $lastRun            = BatchRunLog::latest('ran_at')->first();
        $totalNotifications = (int) BatchRunLog::sum('notifications_sent');
        $totalSkipped       = (int) BatchRunLog::sum('skipped_no_token');

        // Dynamic step minutes for periodic intervals if applicable
        $stepMinutes = 2;
        if (preg_match('/^\*\/(\d+)/', trim($schedule), $matches)) {
            $stepMinutes = max(1, (int) $matches[1]);
        } elseif (str_starts_with(trim($schedule), '* * * * *')) {
            $stepMinutes = 1;
        }

        $nowIst     = Carbon::now($timezone);
        $nextRunIst = PosterExpiryNotificationService::getNextRunIst();

        // Convert lastRun ran_at to India Timezone (Asia/Kolkata)
        $lastRunIst = $lastRun && $lastRun->ran_at
            ? $lastRun->ran_at->copy()->timezone($timezone)
            : null;

        return [
            'enabled'              => $enabled,
            'schedule'             => $schedule,
            'scheduleHuman'        => $scheduleHuman,
            'timezone'             => $timezone,
            'windowHours'          => $windowHours,
            'stepMinutes'          => $stepMinutes,
            'totalRuns'            => $totalRuns,
            'totalNotifications'   => $totalNotifications,
            'totalSkipped'         => $totalSkipped,
            'lastRun'              => $lastRun,
            'lastRunIst'           => $lastRunIst,
            'lastRunFormatted'     => $lastRunIst ? $lastRunIst->format('d M Y, h:i:s A') . ' IST' : 'No runs yet',
            'lastRunDate'          => $lastRunIst ? $lastRunIst->format('d M Y') : '—',
            'lastRunTimeOnly'      => $lastRunIst ? $lastRunIst->format('h:i:s A') . ' IST' : 'Never',
            'lastRunHuman'         => $lastRun && $lastRun->ran_at ? $lastRun->ran_at->diffForHumans() : 'Never',
            'lastRunSent'          => $lastRun ? (int) $lastRun->notifications_sent : 0,
            'lastRunSkipped'       => $lastRun ? (int) $lastRun->skipped_no_token : 0,
            'lastRunDuration'      => $lastRun ? (int) $lastRun->duration_ms : 0,
            'lastRunStatus'        => $lastRun ? ($lastRun->dry_run ? 'Dry Run' : ucfirst($lastRun->status)) : 'None',
            'nextRun'              => $nextRunIst,
            'nextRunIst'           => $nextRunIst,
            'nextRunFormatted'     => $nextRunIst->format('h:i:s A') . ' IST',
            'nextRunFullFormatted' => $nextRunIst->format('d M Y, h:i:s A') . ' IST',
            'nextRunTimestampMs'   => $nextRunIst->getTimestamp() * 1000,
            'serverNowTimestampMs' => $nowIst->getTimestamp() * 1000,
            'currentIstTime'       => $nowIst->format('h:i:s A') . ' IST',
        ];
    }

    public function batchStatus()
    {
        return response()->json($this->getBatchStatus());
    }

    public function batchMonitor()
    {
        $batchStatus = $this->getBatchStatus();
        $recentRuns  = BatchRunLog::latest('ran_at')->limit(50)->get();

        return view('admin.batch-monitor', array_merge($batchStatus, [
            'active'      => 'batch',
            'batchStatus' => $batchStatus,
            'recentRuns'  => $recentRuns,
            'stats'       => $this->getStats(),
        ]));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
