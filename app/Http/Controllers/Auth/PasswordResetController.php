<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form (enter email).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send the reset code to the user's email.
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        $resetCode = PasswordResetCode::generateFor($request->email);
        $user->notify(new PasswordResetCodeNotification($resetCode->code));

        return redirect()->route('password.code.form', ['email' => $request->email])
            ->with('status', 'A reset code has been sent to your email.');
    }

    /**
     * Show the code verification form.
     */
    public function showCodeForm(Request $request)
    {
        return view('auth.verify-reset-code', ['email' => $request->email]);
    }

    /**
     * Verify the submitted code.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $resetCode = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Invalid reset code.'])->withInput();
        }

        if ($resetCode->isExpired()) {
            $resetCode->delete();
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.'])->withInput();
        }

        // Store email in session for the reset form
        session(['password_reset_email' => $request->email]);
        session(['password_reset_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm()
    {
        if (!session('password_reset_verified')) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password', ['email' => session('password_reset_email')]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        if (!session('password_reset_verified')) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('password_reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clean up
        PasswordResetCode::where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. You can now log in.');
    }
}
