@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#374151] via-[#4B5563] to-[#6B7280] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10">
            <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Account Settings
            </h1>
            <p class="text-white/90 text-lg">Manage your profile, security, and subscription</p>
        </div>
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
