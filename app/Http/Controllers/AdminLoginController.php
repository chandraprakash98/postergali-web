<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle login logic
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if admin user exists with these credentials
        $adminUser = AdminUser::where('email', $credentials['email'])->first();

        if ($adminUser && Hash::check($credentials['password'], $adminUser->password)) {
            // Log the admin user in
            Auth::guard('admin')->login($adminUser, $request->remember ?? false);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
