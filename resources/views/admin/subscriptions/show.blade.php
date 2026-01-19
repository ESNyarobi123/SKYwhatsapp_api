@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Subscription Details</h1>
            <p class="text-white/70">{{ $subscription->plan_name }} - {{ $subscription->user->name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.subscriptions.edit', $subscription) }}">
                <x-button variant="primary" size="md">Edit</x-button>
            </a>
            @if($subscription->isExpired() || $subscription->status === 'expired')
                <form method="POST" action="{{ route('admin.subscriptions.renew', $subscription) }}" class="inline">
                    @csrf
                    <x-button type="submit" variant="secondary" size="md">Renew</x-button>
                </form>
            @endif
            <a href="{{ route('admin.subscriptions.index') }}">
                <x-button variant="outline" size="md">Back</x-button>
            </a>
        </div>
    </div>

    <x-card>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">User</p>
                    <p class="text-white font-medium text-lg">{{ $subscription->user->name }}</p>
                    <p class="text-white/60 text-sm">{{ $subscription->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Plan Name</p>
                    <p class="text-white font-medium text-lg">{{ $subscription->plan_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Status</p>
                    <x-badge variant="{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'error' : 'warning') }}">
                        {{ ucfirst($subscription->status) }}
                    </x-badge>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Amount</p>
                    <p class="text-white font-medium text-lg">{{ number_format($subscription->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Expires At</p>
                    <p class="text-white font-medium">{{ $subscription->expires_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Created</p>
                    <p class="text-white font-medium">{{ $subscription->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            @if($subscription->package)
                <div>
                    <p class="text-sm text-white/70 mb-2">Package</p>
                    <p class="text-white">{{ $subscription->package->name }} - {{ number_format($subscription->package->price, 2) }} {{ $subscription->package->currency }}</p>
                </div>
            @endif

            @if($subscription->payments->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3">Payments</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Amount</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Provider</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($subscription->payments as $payment)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-4 py-4 text-white">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                        <td class="px-4 py-4 text-white/70 text-sm">{{ ucfirst($payment->provider) }}</td>
                                        <td class="px-4 py-4">
                                            <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'error' : 'warning') }}">
                                                {{ ucfirst($payment->status) }}
                                            </x-badge>
                                        </td>
                                        <td class="px-4 py-4 text-white/70 text-sm">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </x-card>
</div>
@endsection
