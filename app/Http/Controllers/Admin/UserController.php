<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users with filters.
     */
    public function index(Request $request)
    {
        $query = User::with(['activeSubscription', 'subscriptions']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('subscription_status')) {
            if ($request->subscription_status === 'active') {
                $query->whereHas('activeSubscription');
            } elseif ($request->subscription_status === 'expired') {
                $query->whereDoesntHave('activeSubscription');
            }
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                    ],
                ],
            ]);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user.
     */
    public function store(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['user' => $user],
                'message' => 'User created successfully.',
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Get user details.
     */
    public function show(Request $request, User $user)
    {
        $user->load([
            'subscriptions',
            'apiKeys',
            'instances',
            'messages' => fn ($query) => $query->latest()->limit(10),
            'usageLogs' => fn ($query) => $query->latest()->limit(10),
        ]);

        $stats = [
            'subscriptions_count' => $user->subscriptions()->count(),
            'api_keys_count' => $user->apiKeys()->count(),
            'instances_count' => $user->instances()->count(),
            'messages_count' => $user->messages()->count(),
            'usage_logs_count' => $user->usageLogs()->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'stats' => $stats,
                ],
            ]);
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show edit user form.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $updateData = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['user' => $user->fresh()],
                'message' => 'User updated successfully.',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->isAdmin() && $user->id === Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'CANNOT_DELETE_SELF',
                        'message' => 'You cannot delete your own account.',
                    ],
                ], 400);
            }

            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Suspend a user.
     */
    public function suspend(Request $request, User $user)
    {
        if ($user->isAdmin() && $user->id === Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'CANNOT_SUSPEND_SELF',
                        'message' => 'You cannot suspend your own account.',
                    ],
                ], 400);
            }

            return back()->withErrors(['user' => 'You cannot suspend your own account.']);
        }

        // For now, use role to mark as suspended (can be enhanced with suspended_at field)
        $user->update(['role' => 'suspended']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully.',
            ]);
        }

        return back()->with('success', 'User suspended successfully.');
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(Request $request, User $user)
    {
        $user->update(['role' => 'user']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User unsuspended successfully.',
            ]);
        }

        return back()->with('success', 'User unsuspended successfully.');
    }

    /**
     * Impersonate a user (login as user).
     */
    public function impersonate(Request $request, User $user)
    {
        // Store original admin user ID in session
        session(['impersonating' => Auth::id()]);
        
        // Log in as the target user
        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Impersonating user successfully.',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Logged in as ' . $user->name);
    }

    /**
     * Stop impersonating and return to admin account.
     */
    public function logoutImpersonate(Request $request)
    {
        $adminId = session('impersonating');

        if (!$adminId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'NOT_IMPERSONATING',
                        'message' => 'Not currently impersonating a user.',
                    ],
                ], 400);
            }

            return redirect()->route('admin.dashboard');
        }

        // Log out current user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        // Log back in as admin
        $admin = User::find($adminId);
        if ($admin) {
            Auth::login($admin);
            $request->session()->forget('impersonating');
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Returned to admin account.',
                ]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Returned to admin account.');
        }

        return redirect()->route('login');
    }
}
