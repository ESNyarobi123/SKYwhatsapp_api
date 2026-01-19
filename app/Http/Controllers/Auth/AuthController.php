<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'INVALID_CREDENTIALS',
                        'message' => 'Invalid email or password.',
                    ],
                ], 401);
            }

            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => Auth::user(),
                ],
                'message' => 'Login successful.',
            ]);
        }

        // Redirect admin users to admin dashboard
        if (Auth::user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        // Get the session instance first
        $session = request()->session();

        // Logout the user explicitly using web guard (removes user from session)
        Auth::guard('web')->logout();

        // Invalidate session (this clears all session data and regenerates the token)
        $session->invalidate();

        // Regenerate CSRF token for security
        $session->regenerateToken();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
        }

        // Redirect to login page with no-cache headers to ensure fresh page load
        // Use 303 See Other status to force GET request and prevent form resubmission
        return redirect()->route('login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Get authenticated user.
     */
    public function user(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => Auth::user(),
            ],
        ]);
    }
}
