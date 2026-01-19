@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Select Payment Method</h1>
        <p class="text-white/70">Choose your preferred payment method for subscription: {{ $subscription->plan_name }}</p>
    </div>

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <p class="text-[#EA3943] text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <x-card>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-2">Subscription Details</h3>
            <div class="text-white/70 text-sm space-y-1">
                <p><strong>Plan:</strong> {{ $subscription->plan_name }}</p>
                <p><strong>Duration:</strong> {{ $subscription->package->duration_days ?? 30 }} days</p>
                <p class="text-white/50 text-xs mt-2">💡 Price varies by payment method (shown below)</p>
            </div>
        </div>

        <div class="border-t border-white/5 pt-6">
            <h3 class="text-lg font-semibold text-white mb-4">Available Payment Methods</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($enabledMethods as $method)
                    @php
                        $package = $subscription->package;
                        $methodNames = [
                            'zenopay_card' => 'ZenoPay Card',
                            'zenopay_mobile' => 'ZenoPay Mobile Money',
                            'paypal' => 'PayPal',
                            'trc20' => 'TRC20 Crypto',
                        ];
                        
                        $methodDescriptions = [
                            'zenopay_card' => 'Visa and MasterCard accepted',
                            'zenopay_mobile' => 'Mobile money (Tanzania)',
                            'paypal' => 'PayPal payments (manual verification)',
                            'trc20' => 'Cryptocurrency payments (manual verification)',
                        ];
                        
                        $methodIcons = [
                            'zenopay_card' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
                            'zenopay_mobile' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>',
                            'paypal' => '<svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.533zm14.146-14.42a13.249 13.249 0 00-.076-.437c-.29-1.868-.002-3.137 1.01-4.287C22.26 1.544 24.27 1 26.838 1h7.46c.525 0 .973.382 1.055.9l3.107 19.696a.641.641 0 01-.633.74h-4.605a.64.64 0 01-.632-.74l1.123-7.528a1.044 1.044 0 00-1.05-.9h-2.19c-4.298 0-7.664-1.747-8.647-6.797z"/></svg>',
                            'trc20' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                        ];
                        
                        // Map payment method names to form route names
                        $formRouteMap = [
                            'zenopay_card' => 'dashboard.payment.zenopay.card.form',
                            'zenopay_mobile' => 'dashboard.payment.zenopay.mobile.form',
                            'paypal' => 'dashboard.payment.paypal.form',
                            'trc20' => 'dashboard.payment.trc20.form',
                        ];
                        
                        // Get currency and price for this payment method
                        $methodCurrency = $package ? $package->getCurrencyForPaymentMethod($method->method) : 'USD';
                        $methodPrice = $package ? $package->getPriceForCurrency($methodCurrency) : $subscription->amount;
                        
                        $formRouteName = $formRouteMap[$method->method] ?? 'dashboard.payment.select';
                    @endphp
                    
                    <a href="{{ route($formRouteName, ['subscription' => $subscription->id]) }}" class="block w-full p-6 bg-[#252525] border border-white/10 rounded-lg hover:bg-[#FCD535]/10 hover:border-[#FCD535]/30 transition-all text-left group">
                        <div class="flex items-start space-x-4">
                            <div class="text-[#FCD535] group-hover:scale-110 transition-transform">
                                {!! $methodIcons[$method->method] ?? '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' !!}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-semibold mb-1">{{ $methodNames[$method->method] ?? $method->name }}</h4>
                                <p class="text-white/50 text-sm mb-2">{{ $methodDescriptions[$method->method] ?? 'Payment method' }}</p>
                                <p class="text-[#FCD535] font-semibold text-lg">
                                    {{ number_format($methodPrice, 2) }} {{ $methodCurrency }}
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-white/30 group-hover:text-[#FCD535] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-white/5">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-white/70 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-card>
</div>
@endsection