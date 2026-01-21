<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        $packageId = request()->get('package_id');
        $package = null;

        if ($packageId) {
            $package = Package::where('id', $packageId)->where('is_active', true)->first();
        }

        return response()
            ->view('auth.register', compact('package'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function register(RegisterRequest $request)
    {
        // ... (keep existing user creation)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // ... (keep existing package logic)
        // Handle package selection if provided
        $packageId = $request->input('package_id');
        if ($packageId) {
            $package = Package::where('id', $packageId)->where('is_active', true)->first();

            if ($package && $package->isFree()) {
                // Auto-create subscription for free packages
                $expiresAt = now()->addDays($package->duration_days);

                Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'plan_name' => $package->name,
                    'status' => 'active',
                    'expires_at' => $expiresAt,
                    'amount' => 0.00,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                ],
                'message' => 'Registration successful.',
            ], 201);
        }

        // If package was selected but not free, redirect to pricing/payment
        if ($packageId && $package && ! $package->isFree()) {
            return redirect()->route('dashboard')->with('info', 'Please complete payment to activate your selected plan.');
        }

        return redirect()->route('dashboard');
    }
}
