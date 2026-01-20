@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#10B981] via-[#059669] to-[#047857] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Subscriptions Management
                </h1>
                <p class="text-white/90 text-lg">View and manage all customer subscriptions</p>
            </div>
            <a href="{{ route('admin.subscriptions.create') }}" class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold rounded-xl transition-all hover:scale-105 border border-white/30">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Subscription
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#3B82F6]">{{ $subscriptions->total() }}</p>
            <p class="text-sm text-white/60">Total Subscriptions</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#10B981]">{{ $subscriptions->where('status', 'active')->count() }}</p>
            <p class="text-sm text-white/60">Active</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#FCD535]">{{ $subscriptions->where('status', 'pending')->count() }}</p>
            <p class="text-sm text-white/60">Pending</p>
        </div>
        <div class="bg-[#252525] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-[#EA3943]">{{ $subscriptions->where('status', 'expired')->count() }}</p>
            <p class="text-sm text-white/60">Expired</p>
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

    @if($subscriptions->count() > 0)
        <x-card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Plan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-white/90">Expires</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-white/90">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($subscriptions as $subscription)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($subscription->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium group-hover:text-[#FCD535] transition-colors">{{ $subscription->user->name }}</p>
                                            <p class="text-white/60 text-sm">{{ $subscription->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-white/10 rounded-lg text-white text-sm font-medium">
                                        {{ $subscription->plan_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge variant="{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'error' : 'warning') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-white font-medium">${{ number_format($subscription->amount, 2) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-white/90 text-sm">{{ $subscription->expires_at->format('M d, Y') }}</p>
                                        <p class="text-white/50 text-xs">{{ $subscription->expires_at->diffForHumans() }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="p-2 text-white/50 hover:text-[#FCD535] hover:bg-white/10 rounded-lg transition-all" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="p-2 text-white/50 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($subscription->isExpired() || $subscription->status === 'expired')
                                            <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="p-2 text-white/50 hover:text-[#00D9A5] hover:bg-[#00D9A5]/10 rounded-lg transition-all" title="Renew">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this subscription?')">
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
                {{ $subscriptions->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-[#10B981]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Subscriptions Found</h3>
                <p class="text-white/70 mb-6">Get started by creating your first subscription.</p>
                <a href="{{ route('admin.subscriptions.create') }}">
                    <x-button variant="primary" size="md">Create First Subscription</x-button>
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
