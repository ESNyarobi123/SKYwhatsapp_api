@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Subscriptions Management</h1>
            <p class="text-white/70">View and manage all subscriptions</p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}">
            <x-button variant="primary" size="md">Create Subscription</x-button>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($subscriptions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Plan</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Expires</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($subscriptions as $subscription)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $subscription->user->name }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $subscription->plan_name }}</td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'error' : 'warning') }}">
                                    {{ ucfirst($subscription->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ number_format($subscription->amount, 2) }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $subscription->expires_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-white/70 hover:text-white text-sm transition-colors">Edit</a>
                                    @if($subscription->isExpired() || $subscription->status === 'expired')
                                        <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[#00D9A5] hover:text-[#00C595] text-sm transition-colors">Renew</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this subscription?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $subscriptions->links() }}
        </div>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No subscriptions found</p>
        </x-card>
    @endif
</div>
@endsection
