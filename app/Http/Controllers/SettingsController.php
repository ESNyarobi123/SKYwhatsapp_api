<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show settings page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $activeSubscription = $user->activeSubscription;
        $payments = $user->payments()->latest()->limit(10)->get();

        return view('dashboard.settings', compact('user', 'activeSubscription', 'payments'));
    }

    /**
     * Update user settings.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required_with:password'],
            'password' => ['sometimes', 'required', Password::defaults(), 'confirmed'],
        ]);

        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        if ($request->has('email')) {
            $user->email = $validated['email'];
        }

        if ($request->has('password')) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully.',
            ]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
