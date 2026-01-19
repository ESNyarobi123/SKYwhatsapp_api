@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">TRC20 Crypto Payment</h1>
        <p class="text-white/70">Pay with USDT (TRC20 network)</p>
    </div>

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

        <div class="border-t border-white/5 pt-6">
            <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-white font-semibold mb-2">Payment Information</h4>
                        <ul class="text-white/70 text-sm space-y-1 list-disc list-inside">
                            <li>Payment will be in USDT (TRC20 network)</li>
                            <li>You will receive wallet address and payment instructions</li>
                            <li>After sending payment, admin will verify and activate your subscription</li>
                            <li>Verification usually takes 24-48 hours</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.payment.trc20') }}">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                
                <div class="flex items-center space-x-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Get Payment Instructions
                    </button>
                    <a href="{{ route('dashboard.payment.select', ['subscription' => $subscription->id]) }}" class="px-6 py-3 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
