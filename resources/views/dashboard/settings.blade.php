@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Settings</h1>
        <p class="text-white/70">Manage your account settings</p>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Profile Settings -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Profile Settings</h2>
        <form method="POST" action="{{ route('dashboard.settings.update') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-white/90 mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                @error('name')
                    <p class="text-[#EA3943] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                @error('email')
                    <p class="text-[#EA3943] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-button type="submit" variant="primary" size="md">Update Profile</x-button>
            </div>
        </form>
    </x-card>

    <!-- Password Change -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Change Password</h2>
        <form method="POST" action="{{ route('dashboard.settings.update') }}" class="space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-sm font-medium text-white/90 mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                @error('current_password')
                    <p class="text-[#EA3943] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-white/90 mb-2">New Password</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                @error('password')
                    <p class="text-[#EA3943] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
            </div>

            <div>
                <x-button type="submit" variant="primary" size="md">Change Password</x-button>
            </div>
        </form>
    </x-card>

    <!-- Subscription Management -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Subscription</h2>
        @if($activeSubscription)
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-white font-medium">{{ $activeSubscription->plan_name }}</p>
                        <p class="text-white/70 text-sm">Expires: {{ $activeSubscription->expires_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <x-badge variant="success">Active</x-badge>
                </div>
                <div>
                    <form method="POST" action="{{ route('api.subscriptions.cancel', $activeSubscription) }}" onsubmit="return confirm('Are you sure you want to cancel this subscription?')">
                        @csrf
                        @method('POST')
                        <x-button type="submit" variant="danger" size="sm">Cancel Subscription</x-button>
                    </form>
                </div>
            </div>
        @else
            <p class="text-white/70 mb-4">No active subscription</p>
            <x-button variant="primary" size="md" href="/api/subscriptions">Subscribe Now</x-button>
        @endif
    </x-card>

    <!-- Payment History -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Payment History</h2>
        @if($payments->count() > 0)
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
                        @foreach($payments as $payment)
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
        @else
            <p class="text-white/70 text-center py-8">No payment history</p>
        @endif
    </x-card>
</div>
@endsection
