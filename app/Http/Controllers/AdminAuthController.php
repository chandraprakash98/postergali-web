<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function dashboard()
    {
        $stats = $this->getStats();
        $allAds = $this->getAllAds();

        return view('admin.dashboard', ['active' => 'all', 'allAds' => $allAds, 'stats' => $stats]);
    }

    public function allAds()
    {
        $allAds = $this->getAllAds();
        return view('admin.dashboard', ['active' => 'all', 'allAds' => $allAds, 'stats' => $this->getStats()]);
    }

    public function showAd(string $type, int $id)
    {
        if (!in_array($type, ['job', 'offer'])) {
            abort(404);
        }

        $model = $type === 'job' ? Job::findOrFail($id) : Offer::findOrFail($id);
        $plan = Plan::find($model->plan_id) ?: $model->plan;

        return view('admin.ad-details', [
            'type' => $type,
            'ad' => $model,
            'plan' => $plan,
        ]);
    }

    public function updateAdStatus(Request $request, string $type, int $id)
    {
        if (!in_array($type, ['job', 'offer'])) {
            abort(404);
        }

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

        return redirect()->route('admin.ad.show', ['type' => $type, 'id' => $id])
            ->with('success', 'Ad status updated successfully.');
    }

    public function pendingAds()
    {
        $jobs = Job::where('status', 'pending')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Pending Verification',
            'date' => $job->created_at->format('M d, Y'),
            'created_at' => $job->created_at,
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'pending')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Pending Verification',
            'date' => $offer->created_at->format('M d, Y'),
            'created_at' => $offer->created_at,
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return view('admin.dashboard', ['active' => 'pending', 'allAds' => $allAds, 'stats' => $this->getStats()]);
    }

    public function liveAds()
    {
        $jobs = Job::where('status', 'approved')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Live',
            'date' => $job->created_at->format('M d, Y'),
            'created_at' => $job->created_at,
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'approved')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Live',
            'date' => $offer->created_at->format('M d, Y'),
            'created_at' => $offer->created_at,
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return view('admin.dashboard', ['active' => 'live', 'allAds' => $allAds, 'stats' => $this->getStats()]);
    }

    public function expiredAds()
    {
        $jobs = Job::where('status', 'rejected')->get()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => 'Rejected',
            'date' => $job->created_at->format('M d, Y'),
            'created_at' => $job->created_at,
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::where('status', 'rejected')->get()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => 'Rejected',
            'date' => $offer->created_at->format('M d, Y'),
            'created_at' => $offer->created_at,
            'type' => 'Offer',
            'model_id' => $offer->id,
        ]);

        $allAds = $jobs->concat($offers)->sortByDesc('created_at')->values();

        return view('admin.dashboard', ['active' => 'expired', 'allAds' => $allAds, 'stats' => $this->getStats()]);
    }

    public function pricingInfo()
    {
        $plans = Plan::all();
        return view('admin.dashboard', ['active' => 'pricing', 'plans' => $plans, 'stats' => $this->getStats()]);
    }

    private function getAllAds()
    {
        $jobs = Job::all()->map(fn($job) => [
            'id' => 'JOB-' . str_pad($job->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $job->business_name,
            'phone' => $job->phone_number,
            'city' => $job->city,
            'status' => ucfirst($job->status),
            'date' => $job->created_at->format('M d, Y'),
            'created_at' => $job->created_at,
            'type' => 'Job',
            'model_id' => $job->id,
        ]);

        $offers = Offer::all()->map(fn($offer) => [
            'id' => 'OFF-' . str_pad($offer->id, 3, '0', STR_PAD_LEFT),
            'business_name' => $offer->business_name,
            'phone' => $offer->mobile_number,
            'city' => $offer->city,
            'status' => ucfirst($offer->status),
            'date' => $offer->created_at->format('M d, Y'),
            'created_at' => $offer->created_at,
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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
