<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show the email verification code entry form.
     */
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify');
    }

    /**
     * Verify the submitted code.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $verificationCode = EmailVerificationCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->first();

        if (!$verificationCode) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        if ($verificationCode->isExpired()) {
            $verificationCode->delete();
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        $verificationCode->delete();

        return redirect()->route('dashboard')->with('verified', true);
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
