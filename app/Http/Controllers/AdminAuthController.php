<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Plan;
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

    public function dashboard()
    {
        $stats = $this->getStats();
        $allAds = $this->getAllAds();

        return view('admin.dashboard', compact('stats', 'allAds'));
    }

    public function allAds()
    {
        $allAds = $this->getAllAds();
        return view('admin.dashboard', ['active' => 'all', 'allAds' => $allAds, 'stats' => $this->getStats()]);
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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
