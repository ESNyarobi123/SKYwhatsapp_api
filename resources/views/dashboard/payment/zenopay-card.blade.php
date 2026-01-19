@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">ZenoPay Card Payment</h1>
        <p class="text-white/70">Pay securely with your Visa or MasterCard</p>
    </div>

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <p class="text-[#EA3943] text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <ul class="text-sm text-[#EA3943] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($isApiKeyConfigured) && !$isApiKeyConfigured)
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-[#EA3943] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-[#EA3943] font-semibold mb-1">ZenoPay API Key Not Configured</p>
                    <p class="text-[#EA3943] text-sm">ZenoPay API key is not configured. Please contact the administrator to set up ZenoPay payment integration.</p>
                </div>
            </div>
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

        <div class="border-t border-white/5 pt-6">
            <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-white font-semibold mb-2">How ZenoPay Card Payment Works</h4>
                        <ol class="text-white/70 text-sm space-y-2 list-decimal list-inside">
                            <li>Click "Proceed to Payment" button below</li>
                            <li>You will be redirected to ZenoPay's secure payment page</li>
                            <li>Enter your card details (Visa or MasterCard) on ZenoPay's page</li>
                            <li>Complete the payment on their secure platform</li>
                            <li>You will be redirected back here after payment</li>
                        </ol>
                        <p class="text-white/70 text-sm mt-3"><strong>Note:</strong> You don't enter card details on this page. Card details are entered on ZenoPay's secure checkout page.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.payment.zenopay.card') }}">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                
                <div class="flex items-center space-x-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors {{ (isset($isApiKeyConfigured) && !$isApiKeyConfigured) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ (isset($isApiKeyConfigured) && !$isApiKeyConfigured) ? 'disabled' : '' }}>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Proceed to Payment
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
