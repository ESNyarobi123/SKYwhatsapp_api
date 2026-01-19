@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Revenue Management</h1>
            <p class="text-white/70">Track payments and revenue</p>
        </div>
        <a href="{{ route('admin.revenue.export', request()->all()) }}">
            <x-button variant="secondary" size="md">Export CSV</x-button>
        </a>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-metric-card :value="number_format($stats['total_revenue'], 2)" label="Total Revenue" />
        <x-metric-card :value="$stats['total_payments']" label="Completed Payments" />
        <x-metric-card :value="$stats['pending_payments']" label="Pending Payments" />
        <x-metric-card :value="$stats['failed_payments']" label="Failed Payments" />
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" action="{{ route('admin.revenue.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-white/70 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="provider" class="block text-sm font-medium text-white/70 mb-2">Provider</label>
                <select id="provider" name="provider" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <option value="">All</option>
                    <option value="mpesa" {{ request('provider') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                    <option value="tigopesa" {{ request('provider') === 'tigopesa' ? 'selected' : '' }}>TigoPesa</option>
                    <option value="airtelmoney" {{ request('provider') === 'airtelmoney' ? 'selected' : '' }}>AirtelMoney</option>
                    <option value="stripe" {{ request('provider') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-white/70 mb-2">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
            </div>
            <div class="flex items-end">
                <x-button type="submit" variant="primary" size="md" class="w-full">Filter</x-button>
            </div>
        </form>
    </x-card>

    @if($payments->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Subscription</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Provider</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-4 text-white/70 text-sm">#{{ $payment->id }}</td>
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="text-white">{{ $payment->user->name ?? 'N/A' }}</p>
                                        <p class="text-white/60 text-xs">{{ $payment->user->email ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ $payment->subscription->plan_name ?? 'N/A' }}</td>
                                <td class="px-4 py-4">
                                    <p class="text-white font-medium">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ ucfirst($payment->provider) }}</td>
                                <td class="px-4 py-4">
                                    <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'error' : ($payment->status === 'pending' ? 'warning' : 'default')) }}">
                                        {{ ucfirst($payment->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('admin.revenue.show', $payment) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                        @if($payment->status === 'pending' && in_array($payment->payment_method, ['paypal', 'trc20']))
                                            <form method="POST" action="{{ route('admin.revenue.verify', $payment) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-[#00D9A5] hover:text-[#00C494] text-sm transition-colors" onclick="return confirm('Are you sure you want to verify this payment? This will activate the subscription.')">Verify</button>
                                            </form>
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
        </x-card>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No payments found</p>
        </x-card>
    @endif
</div>
@endsection
