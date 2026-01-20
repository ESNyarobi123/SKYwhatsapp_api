@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#3B82F6] via-[#6366F1] to-[#8B5CF6] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users Management
                </h1>
                <p class="text-white/90 text-lg">Manage all platform users and their subscriptions</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold rounded-xl transition-all hover:scale-105 border border-white/30">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Create User
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#3B82F6]">{{ $users->total() }}</p>
            <p class="text-sm text-white/60">Total Users</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#10B981]">{{ $users->where('activeSubscription', '!=', null)->count() }}</p>
            <p class="text-sm text-white/60">With Subscription</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#FCD535]">{{ $users->where('role', 'admin')->count() }}</p>
            <p class="text-sm text-white/60">Admins</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#8B5CF6]">{{ $users->where('created_at', '>=', now()->subDays(7))->count() }}</p>
            <p class="text-sm text-white/60">New This Week</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-xl p-4 flex items-center space-x-3">
            <svg class="w-6 h-6 text-[#00D9A5] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[#00D9A5]">{{ session('success') }}</p>
        </div>
    @endif

    @if($users->count() > 0)
        <x-card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Subscription</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Joined</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white/90">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($users as $user)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#8B5CF6] flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium group-hover:text-[#FCD535] transition-colors">{{ $user->name }}</p>
                                            <p class="text-white/60 text-sm">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge variant="{{ $user->role === 'admin' ? 'gold' : 'default' }}">
                                        {{ ucfirst($user->role) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->activeSubscription)
                                        <div class="flex items-center space-x-2">
                                            <x-badge variant="success">Active</x-badge>
                                            <span class="text-white/50 text-xs">{{ $user->activeSubscription->plan_name ?? '' }}</span>
                                        </div>
                                    @else
                                        <x-badge variant="error">None</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-white/70 text-sm">{{ $user->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="p-2 text-white/50 hover:text-[#FCD535] hover:bg-white/10 rounded-lg transition-all" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-white/50 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-white/50 hover:text-[#00D9A5] hover:bg-[#00D9A5]/10 rounded-lg transition-all" title="Login as User">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-white/50 hover:text-[#EA3943] hover:bg-[#EA3943]/10 rounded-lg transition-all" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 border-t border-white/5 pt-6">
                {{ $users->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-[#3B82F6]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Users Found</h3>
                <p class="text-white/70 mb-6">Get started by creating your first user.</p>
                <a href="{{ route('admin.users.create') }}">
                    <x-button variant="primary" size="md">Create First User</x-button>
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
