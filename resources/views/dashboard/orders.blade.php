@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $totalOrders = $payments->total() ?? $payments->count();
    $completedOrders = $user->payments()->where('status', 'completed')->count();
    $pendingOrders = $user->payments()->where('status', 'pending')->count();
    $totalSpent = $user->payments()->where('status', 'completed')->sum('amount');
@endphp

<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#F59E0B] via-[#D97706] to-[#B45309] p-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Order History
                </h1>
                <p class="text-white/80 text-lg">Track all your payment transactions and invoices</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Stats Cards -->
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/20">
                    <p class="text-white/70 text-xs uppercase tracking-wide mb-1">Total Orders</p>
                    <p class="text-white font-bold text-2xl">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/20">
                    <p class="text-green-200 text-xs uppercase tracking-wide mb-1">✓ Completed</p>
                    <p class="text-white font-bold text-2xl">{{ $completedOrders }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/20">
                    <p class="text-yellow-200 text-xs uppercase tracking-wide mb-1">⏳ Pending</p>
                    <p class="text-white font-bold text-2xl">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/20">
                    <p class="text-white/70 text-xs uppercase tracking-wide mb-1">Total Spent</p>
                    <p class="text-white font-bold text-2xl">${{ number_format($totalSpent, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($payments->count() > 0)
        <!-- Orders Table Card -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#1A1A1A]/50 border-b border-white/10">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-5">
                                    <span class="text-[#FCD535] font-mono font-semibold text-sm bg-[#FCD535]/10 px-3 py-1.5 rounded-lg">#{{ $payment->id }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#8B5CF6] to-[#6366F1] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <span class="text-white font-medium">{{ $payment->subscription->plan_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-white font-bold text-lg">{{ number_format($payment->amount, 2) }}</span>
                                    <span class="text-white/50 text-sm ml-1">{{ $payment->currency }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    @php
                                        $methodLabels = [
                                            'zenopay_mobile' => ['label' => 'ZenoPay Mobile', 'icon' => '📱', 'color' => 'from-green-500 to-emerald-600'],
                                            'zenopay_card' => ['label' => 'ZenoPay Card', 'icon' => '💳', 'color' => 'from-blue-500 to-indigo-600'],
                                            'paypal' => ['label' => 'PayPal', 'icon' => '🅿️', 'color' => 'from-blue-400 to-blue-600'],
                                            'trc20' => ['label' => 'TRC20 (USDT)', 'icon' => '₮', 'color' => 'from-teal-500 to-cyan-600'],
                                            'mpesa' => ['label' => 'M-Pesa', 'icon' => '💵', 'color' => 'from-green-600 to-lime-600'],
                                        ];
                                        $method = $payment->payment_method ?? $payment->provider;
                                        $methodInfo = $methodLabels[$method] ?? ['label' => ucfirst($method ?? 'N/A'), 'icon' => '💰', 'color' => 'from-gray-500 to-gray-600'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r {{ $methodInfo['color'] }} text-white text-sm font-medium">
                                        <span>{{ $methodInfo['icon'] }}</span>
                                        {{ $methodInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    @php
                                        $statusStyles = [
                                            'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30 animate-pulse',
                                            'failed' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        ];
                                        $statusIcons = [
                                            'completed' => '✓',
                                            'pending' => '⏳',
                                            'failed' => '✗',
                                        ];
                                        $style = $statusStyles[$payment->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                        $icon = $statusIcons[$payment->status] ?? '?';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold border {{ $style }}">
                                        {{ $icon }} {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-white text-sm">{{ $payment->created_at->format('M d, Y') }}</div>
                                    <div class="text-white/40 text-xs mt-1">{{ $payment->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($payment->payment_method === 'zenopay_mobile' || ($payment->provider === 'mpesa' && $payment->payment_method === null))
                                        <a href="{{ route('dashboard.payment.zenopay.mobile.show', $payment) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#FCD535]/10 text-[#FCD535] hover:bg-[#FCD535]/20 rounded-lg text-sm font-medium transition-all group-hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    @elseif($payment->payment_method === 'paypal')
                                        <a href="{{ route('dashboard.payment.paypal.show', $payment) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 rounded-lg text-sm font-medium transition-all group-hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    @elseif($payment->payment_method === 'trc20')
                                        <a href="{{ route('dashboard.payment.trc20.show', $payment) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500/10 text-teal-400 hover:bg-teal-500/20 rounded-lg text-sm font-medium transition-all group-hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    @else
                                        <span class="text-white/30 text-sm">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-6 py-4 bg-[#1A1A1A]/30 border-t border-white/10">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-[#252525] border border-white/10 border-dashed rounded-2xl">
            <div class="text-center py-16 px-4">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-[#F59E0B]/20 to-[#D97706]/20 flex items-center justify-center">
                    <svg class="w-12 h-12 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-white text-2xl font-bold mb-3">No Orders Yet</h3>
                <p class="text-white/60 max-w-md mx-auto mb-8">Your payment history will appear here once you subscribe to a plan. Start your journey today!</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#FCD535] to-[#F59E0B] text-[#1A1A1A] font-bold rounded-xl hover:opacity-90 transition-all transform hover:scale-105 shadow-lg shadow-[#FCD535]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Browse Packages
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
