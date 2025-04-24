<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
    return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([ 'email' => 'required|email|exists:users,email',
        ]);
            $status = Password::sendResetLink($request->only('email'));
            return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your email.')
            : back()->withErrors(['email' => 'Failed to send reset link.']);
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6|confirmed',
        'token' => 'required'
    ]);

        $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation','token'),
        function (User $user, string $password) {$user->forceFill([ 'password' => Hash::make($password)

        ])->save();
    }

    );
        return $status === Password::PASSWORD_RESET
        ? redirect('/login')->with('success', 'Password has been reset successfully.')
        : back()->withErrors(['email' => 'Invalid or expired token.']);
    }

}
