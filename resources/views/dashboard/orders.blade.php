@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Order History</h1>
        <p class="text-white/70">View all your payment orders and transactions</p>
    </div>

    @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Order ID</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Plan</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Payment Method</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white font-mono text-sm">#{{ $payment->id }}</td>
                            <td class="px-4 py-4 text-white">
                                {{ $payment->subscription->plan_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-white font-semibold">
                                {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                @php
                                    $methodLabels = [
                                        'zenopay_mobile' => 'ZenoPay Mobile',
                                        'zenopay_card' => 'ZenoPay Card',
                                        'paypal' => 'PayPal',
                                        'trc20' => 'TRC20 (USDT)',
                                        'mpesa' => 'M-Pesa',
                                    ];
                                    $methodLabel = $methodLabels[$payment->payment_method ?? $payment->provider] ?? ucfirst($payment->provider ?? 'N/A');
                                @endphp
                                {{ $methodLabel }}
                            </td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'error') }}">
                                    {{ ucfirst($payment->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $payment->created_at->format('Y-m-d H:i') }}
                                <span class="text-white/50 text-xs block mt-1">{{ $payment->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($payment->payment_method === 'zenopay_mobile' || ($payment->provider === 'mpesa' && $payment->payment_method === null))
                                        <a href="{{ route('dashboard.payment.zenopay.mobile.show', $payment) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    @elseif($payment->payment_method === 'paypal')
                                        <a href="{{ route('dashboard.payment.paypal.show', $payment) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    @elseif($payment->payment_method === 'trc20')
                                        <a href="{{ route('dashboard.payment.trc20.show', $payment) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-white/70 mb-4">No orders yet</p>
                <p class="text-white/50 text-sm mb-6">Your payment history will appear here once you make a payment.</p>
                <a href="{{ route('dashboard') }}">
                    <x-button variant="primary" size="md">Browse Packages</x-button>
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
