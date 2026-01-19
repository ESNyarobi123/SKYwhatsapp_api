@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">PayPal Payment</h1>
        <p class="text-white/70">Complete your PayPal payment to activate your subscription</p>
    </div>

    <x-card>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Payment Details</h3>
            <div class="space-y-3 text-white/70">
                <div class="flex justify-between">
                    <span>Payment ID:</span>
                    <span class="text-white">#{{ $payment->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount:</span>
                    <span class="text-white font-semibold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Status:</span>
                    <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'error') }}">
                        {{ ucfirst($payment->status) }}
                    </x-badge>
                </div>
                <div class="flex justify-between">
                    <span>Subscription:</span>
                    <span class="text-white">{{ $payment->subscription->plan_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        @if($payment->status === 'pending')
            <div class="border-t border-white/5 pt-6">
                @if($paypalEmail)
                    <div class="mb-6 bg-[#1A1A1A] border border-white/10 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-[#0070ba]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.533zm14.146-14.42a13.249 13.249 0 00-.076-.437c-.29-1.868-.002-3.137 1.01-4.287C22.26 1.544 24.27 1 26.838 1h7.46c.525 0 .973.382 1.055.9l3.107 19.696a.641.641 0 01-.633.74h-4.605a.64.64 0 01-.632-.74l1.123-7.528a1.044 1.044 0 00-1.05-.9h-2.19c-4.298 0-7.664-1.747-8.647-6.797z"/>
                            </svg>
                            <div>
                                <p class="text-white/70 text-sm">Send payment to:</p>
                                <p class="text-white font-semibold">{{ $paypalEmail }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($paymentLink)
                    <div class="mb-6">
                        <h4 class="text-white font-semibold mb-3">Payment Instructions</h4>
                        <div class="bg-[#1A1A1A] border border-white/10 rounded-lg p-4 mb-4">
                            <ol class="list-decimal list-inside space-y-2 text-white/70 text-sm">
                                <li>Send the payment amount ({{ number_format($payment->amount, 2) }} {{ $payment->currency }}) to the PayPal email shown above</li>
                                <li>Click the PayPal payment link below to open PayPal in a new tab</li>
                                <li>After sending payment, our admin will verify and activate your subscription</li>
                                <li>You will be notified once your subscription is activated</li>
                            </ol>
                        </div>
                        
                        <a href="{{ $paymentLink }}" target="_blank" class="inline-flex items-center justify-center w-full px-6 py-3 bg-[#0070ba] hover:bg-[#005ea6] text-white font-semibold rounded-lg transition-colors mb-4">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.533zm14.146-14.42a13.249 13.249 0 00-.076-.437c-.29-1.868-.002-3.137 1.01-4.287C22.26 1.544 24.27 1 26.838 1h7.46c.525 0 .973.382 1.055.9l3.107 19.696a.641.641 0 01-.633.74h-4.605a.64.64 0 01-.632-.74l1.123-7.528a1.044 1.044 0 00-1.05-.9h-2.19c-4.298 0-7.664-1.747-8.647-6.797z"/>
                            </svg>
                            Pay with PayPal
                        </a>
                    </div>
                @endif

                <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-white font-semibold mb-2">Manual Verification Required</h4>
                            <p class="text-white/70 text-sm">This payment requires manual verification by our admin team. Your subscription will be activated once payment is verified.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @elseif($payment->status === 'completed')
            <div class="border-t border-white/5 pt-6">
                <div class="bg-[#00D9A5]/10 border border-[#00D9A5]/30 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-[#00D9A5] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="text-white font-semibold mb-2">Payment Verified!</h4>
                    <p class="text-white/70 text-sm mb-4">Your subscription has been activated successfully.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </x-card>
</div>
@endsection
