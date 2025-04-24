<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EmailVerificationController;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_verified' => 0,
            'verification_token' => Str::random(64),
        ]);

        Mail::to($user->email)->send(new VerifyEmail($user));

        return redirect('/login')->with('message', 'Please check your email to verify your account before logging in.');
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid verification token.');
        }

        $user->is_verified = 1;
        $user->verification_token = null;
        $user->save();

        return redirect('/login')->with('success', 'Email verified successfully. You can now login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Auth::logout(); // Logout immediately for 2FA
            
            if (!$user->is_verified) {
                return back()->with('error', 'Please verify your email before logging in.');
            }
            // Generate and send 2FA code
            $user->two_factor_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            Mail::to($user->email)->send(new TwoFactorCodeMail($user));
            
            // Store user ID in session before redirecting
            $request->session()->put('2fa_user_id', $user->id);
            
            return redirect()->route('2fa.verify.form')->with('message', 'Please check your email for the 2FA code.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('2fa_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'No active login attempt found.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            return redirect()->route('login')->with('error', 'No 2FA code found. Please try logging in again.');
        }

        if (now()->gt($user->two_factor_expires_at)) {
            return redirect()->route('login')->with('error', '2FA code has expired. Please try logging in again.');
        }

        if ($user->two_factor_code !== $request->two_factor_code) {
            return back()->with('error', 'Invalid 2FA code.');
        }

        // Clear 2FA code and expiry
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->last_login = now();
        $user->save();

        Auth::login($user);
        $request->session()->forget('2fa_user_id');

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function show2FAForm()
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        return view('auth.2fa-verify');
    }
}
