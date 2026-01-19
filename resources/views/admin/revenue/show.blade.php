@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Payment Details</h1>
            <p class="text-white/70">Reference: {{ $payment->reference }}</p>
        </div>
        <a href="{{ route('admin.revenue.index') }}">
            <x-button variant="outline" size="md">Back</x-button>
        </a>
    </div>

    <x-card>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">Payment ID</p>
                    <p class="text-white font-medium text-lg">#{{ $payment->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Reference</p>
                    <p class="text-white font-medium text-lg">{{ $payment->reference }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">User</p>
                    <p class="text-white font-medium text-lg">{{ $payment->user->name ?? 'N/A' }}</p>
                    <p class="text-white/60 text-sm">{{ $payment->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Subscription</p>
                    <p class="text-white font-medium">{{ $payment->subscription->plan_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Amount</p>
                    <p class="text-2xl font-bold text-[#FCD535]">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Provider</p>
                    <p class="text-white font-medium text-lg">{{ ucfirst($payment->provider) }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Status</p>
                    <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'error' : ($payment->status === 'pending' ? 'warning' : 'default')) }}">
                        {{ ucfirst($payment->status) }}
                    </x-badge>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Date</p>
                    <p class="text-white font-medium">{{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>

            @if($payment->metadata)
                <div class="pt-4 border-t border-white/5">
                    <p class="text-sm text-white/70 mb-2">Metadata</p>
                    <pre class="bg-[#1A1A1A] p-4 rounded-lg text-xs text-white/70 overflow-x-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </x-card>
</div>
@endsection
