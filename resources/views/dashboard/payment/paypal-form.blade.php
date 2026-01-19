@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">PayPal Payment</h1>
        <p class="text-white/70">Pay securely with PayPal</p>
    </div>

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <ul class="text-sm text-[#EA3943] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6 text-[#EA3943]">
            {{ session('error') }}
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
                    <span class="text-white font-semibold">{{ number_format($subscription->amount, 2) }} {{ $subscription->package->currency ?? 'USD' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Duration:</span>
                    <span class="text-white">{{ $subscription->package->duration_days ?? 30 }} days</span>
                </div>
            </div>
        </div>

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

        <div class="border-t border-white/5 pt-6">
            <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-white font-semibold mb-2">Payment Instructions</h4>
                        <ol class="text-white/70 text-sm space-y-2 list-decimal list-inside">
                            <li>Click "Initiate Payment" to generate your payment link</li>
                            <li>You will be redirected to a page with payment instructions</li>
                            <li>Send the payment amount to the PayPal email shown</li>
                            <li>Our admin will verify your payment and activate your subscription</li>
                            <li>You will be notified once your subscription is activated</li>
                        </ol>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.payment.paypal') }}">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                
                <div class="flex items-center space-x-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-[#0070ba] hover:bg-[#005ea6] text-white font-semibold rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.533zm14.146-14.42a13.249 13.249 0 00-.076-.437c-.29-1.868-.002-3.137 1.01-4.287C22.26 1.544 24.27 1 26.838 1h7.46c.525 0 .973.382 1.055.9l3.107 19.696a.641.641 0 01-.633.74h-4.605a.64.64 0 01-.632-.74l1.123-7.528a1.044 1.044 0 00-1.05-.9h-2.19c-4.298 0-7.664-1.747-8.647-6.797z"/>
                        </svg>
                        Initiate Payment
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
