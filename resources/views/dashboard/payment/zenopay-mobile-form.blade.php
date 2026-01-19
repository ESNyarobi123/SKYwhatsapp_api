@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">ZenoPay Mobile Money</h1>
        <p class="text-white/70">Pay using mobile money (Tanzania)</p>
    </div>

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <p class="text-[#EA3943] text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <x-card>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Subscription Details</h3>
            <div class="space-y-3 text-white/70">
                <div class="flex justify-between">
                    <span>Plan:</span>
                    <span class="text-white font-semibold">{{ $subscription->plan_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount:</span>
                    <span class="text-white font-semibold">{{ number_format($subscription->amount, 2) }} {{ $subscription->package->currency ?? 'TZS' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Duration:</span>
                    <span class="text-white">{{ $subscription->package->duration_days ?? 30 }} days</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.payment.zenopay.mobile') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

            <div class="border-t border-white/5 pt-6">
                <label for="phone_number" class="block text-sm font-medium text-white/90 mb-2">
                    Phone Number <span class="text-[#EA3943]">*</span>
                </label>
                <input 
                    type="text" 
                    id="phone_number" 
                    name="phone_number" 
                    value="{{ old('phone_number') }}" 
                    placeholder="0751234567" 
                    required 
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                >
                <p class="mt-1 text-xs text-white/50">Enter your mobile money phone number (Tanzania)</p>
                @error('phone_number')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-white font-semibold mb-2">How it works</h4>
                        <ul class="text-white/70 text-sm space-y-1 list-disc list-inside">
                            <li>Enter your mobile money phone number</li>
                            <li>You will receive a payment prompt on your phone</li>
                            <li>Complete the payment on your phone</li>
                            <li>Your subscription will be activated automatically</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Initiate Payment
                </button>
                <a href="{{ route('dashboard.payment.select', ['subscription' => $subscription->id]) }}" class="px-6 py-3 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
