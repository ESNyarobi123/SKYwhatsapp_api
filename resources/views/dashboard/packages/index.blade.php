@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto">
        <h1 class="text-4xl font-bold text-white mb-4">Choose Your Plan</h1>
        <p class="text-white/60 text-lg">Scale your WhatsApp messaging with our flexible pricing plans</p>
    </div>

    <!-- Billing Toggle -->
    <div class="flex items-center justify-center gap-4">
        <span id="monthlyLabel" class="text-white font-medium">Monthly</span>
        <button id="billingToggle" onclick="toggleBilling()" class="relative w-14 h-7 bg-[#252525] border border-white/20 rounded-full transition-all">
            <div id="toggleDot" class="absolute top-1 left-1 w-5 h-5 bg-[#FCD535] rounded-full transition-all"></div>
        </button>
        <span id="annualLabel" class="text-white/50 font-medium">
            Annual
            <span class="ml-1 px-2 py-0.5 bg-[#10B981]/20 text-[#10B981] text-xs rounded-full">Save 20%</span>
        </span>
    </div>

    <!-- Current Plan Info -->
    @if(auth()->user()->hasActiveSubscription())
        @php
            $currentPlan = auth()->user()->activeSubscription;
            $currentPackage = $currentPlan->package;
            $expiryDate = $currentPlan->expires_at;
            $daysLeft = $expiryDate ? floor(now()->diffInDays($expiryDate, false)) : 0;
        @endphp
        <div class="bg-gradient-to-r from-[#FCD535]/10 to-[#F59E0B]/10 border border-[#FCD535]/30 rounded-2xl p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#FCD535]/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/60 text-sm">Current Plan</p>
                        <p class="text-white font-bold text-xl">{{ $currentPackage?->name ?? 'No Plan' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-white/60 text-sm">Renews on</p>
                        <p class="text-white font-medium">{{ $expiryDate?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="h-10 w-px bg-white/10"></div>
                    <div class="text-right">
                        <p class="text-white/60 text-sm">Days Left</p>
                        <p class="text-white font-medium {{ $daysLeft <= 7 ? 'text-[#F59E0B]' : '' }}">{{ max(0, $daysLeft) }} days</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($packages as $package)
            @php
                $isCurrentPlan = auth()->user()->activeSubscription?->package_id == $package->id;
                $isPopular = $package->is_popular ?? false;
                $features = $package->features ?? [];
            @endphp
            <div class="relative {{ $isPopular ? 'lg:-mt-4 lg:mb-4' : '' }}">
                @if($isPopular)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                        <span class="px-4 py-1.5 bg-gradient-to-r from-[#FCD535] to-[#F59E0B] text-[#1A1A1A] text-sm font-bold rounded-full shadow-lg">
                            ⭐ MOST POPULAR
                        </span>
                    </div>
                @endif
                
                <div class="bg-[#252525] border rounded-2xl p-6 h-full flex flex-col transition-all hover:scale-[1.02] {{ $isPopular ? 'border-[#FCD535] shadow-lg shadow-[#FCD535]/20' : ($isCurrentPlan ? 'border-[#10B981]' : 'border-white/10 hover:border-white/20') }}">
                    <!-- Plan Name & Price -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-xl font-bold text-white">{{ $package->name }}</h3>
                            @if($isCurrentPlan)
                                <span class="px-2 py-0.5 bg-[#10B981]/20 text-[#10B981] text-xs rounded-full">Current</span>
                            @endif
                        </div>
                        <p class="text-white/50 text-sm mb-4">{{ $package->description ?? 'Perfect for getting started' }}</p>
                        
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold text-white monthly-price">${{ number_format($package->price_usd, 2) }}</span>
                            <span class="text-4xl font-bold text-white annual-price hidden">${{ number_format(($package->price_usd_annual ?? $package->price_usd * 12 * 0.8) / 12, 2) }}</span>
                            <span class="text-white/50 mb-1">/month</span>
                        </div>
                        <p class="text-white/40 text-sm mt-1 annual-price hidden">
                            Billed annually (${{ number_format($package->price_usd_annual ?? $package->price_usd * 12 * 0.8, 2) }}/year)
                        </p>
                    </div>
                    
                    <!-- Features List -->
                    <div class="flex-1 space-y-3 mb-6">
                        @php
                            // Check if features is array of strings (old format) or structured format
                            $isStringArray = is_array($features) && isset($features[0]) && is_string($features[0]);
                        @endphp
                        
                        @if($isStringArray)
                            {{-- String array format: ['Feature 1', 'Feature 2', ...] --}}
                            @foreach($features as $feature)
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-[#10B981] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-white/70 text-sm">{{ $feature }}</span>
                                </div>
                            @endforeach
                        @else
                            {{-- Structured format: ['instances' => ['limit' => 1], ...] --}}
                            @php
                                $featureLabels = [
                                    'instances' => 'WhatsApp Instances',
                                    'messages_per_day' => 'Messages per Day',
                                    'messages' => 'Messages/month',
                                    'api_keys' => 'API Keys',
                                    'bot_rules' => 'Bot Rules',
                                    'team_members' => 'Team Members',
                                    'support' => 'Support',
                                    'webhooks' => 'Webhooks',
                                ];
                            @endphp
                            
                            @foreach($features as $name => $config)
                                @php
                                    if (is_array($config)) {
                                        $limit = $config['limit'] ?? 0;
                                        $label = $featureLabels[$name] ?? ucfirst(str_replace('_', ' ', $name));
                                        $displayValue = $limit === -1 ? 'Unlimited' : number_format((int)$limit);
                                        $featureText = $displayValue . ' ' . $label;
                                    } else {
                                        // Config is a string like "Up to 1,000 messages/month"
                                        $featureText = (string)$config;
                                    }
                                @endphp
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-[#10B981] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-white/70 text-sm">{{ $featureText }}</span>
                                </div>
                            @endforeach
                        @endif
                        
                        @if($package->sort_order >= 2)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#10B981] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white/70 text-sm">Priority Support</span>
                            </div>
                        @endif
                        
                        @if($package->sort_order >= 3)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#10B981] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white/70 text-sm">API Access</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#10B981] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white/70 text-sm">Advanced Analytics</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- CTA Button -->
                    @if($isCurrentPlan)
                        <button disabled class="w-full py-3 bg-[#10B981]/20 text-[#10B981] font-semibold rounded-xl cursor-default">
                            Current Plan
                        </button>
                    @elseif($package->isFree())
                        <form action="{{ route('dashboard.packages.subscribe', $package) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition-all">
                                Get Started Free
                            </button>
                        </form>
                    @else
                        <form action="{{ route('dashboard.packages.subscribe', $package) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 {{ $isPopular ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-white/10 text-white hover:bg-white/20' }} font-semibold rounded-xl transition-all">
                                {{ auth()->user()->hasActiveSubscription() ? 'Upgrade' : 'Get Started' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Feature Comparison Table -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-white text-center mb-8">Compare Plans</h2>
        
        <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="bg-[#1A1A1A]">
                        <th class="px-6 py-4 text-left text-white/70 font-medium">Feature</th>
                        @foreach($packages as $package)
                            <th class="px-6 py-4 text-center">
                                <span class="text-white font-bold">{{ $package->name }}</span>
                                @if($package->is_popular ?? false)
                                    <span class="ml-1 text-[#FCD535] text-xs">⭐</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @php
                        $comparisonFeatures = [
                            ['key' => 'instances', 'label' => 'WhatsApp Instances', 'tooltip' => 'Number of WhatsApp accounts you can connect'],
                            ['key' => 'messages_per_day', 'label' => 'Messages per Day', 'tooltip' => 'Daily message sending limit'],
                            ['key' => 'api_keys', 'label' => 'API Keys', 'tooltip' => 'Keys for programmatic access'],
                            ['key' => 'bot_rules', 'label' => 'Bot Auto-Replies', 'tooltip' => 'Automated response rules'],
                            ['key' => 'webhooks', 'label' => 'Webhooks', 'tooltip' => 'Real-time event notifications'],
                            ['key' => 'analytics', 'label' => 'Advanced Analytics', 'tooltip' => 'Detailed message statistics'],
                            ['key' => 'templates', 'label' => 'Message Templates', 'tooltip' => 'Reusable message templates'],
                            ['key' => 'scheduler', 'label' => 'Message Scheduler', 'tooltip' => 'Schedule messages for later'],
                            ['key' => 'team', 'label' => 'Team Members', 'tooltip' => 'Collaborate with team'],
                            ['key' => 'support', 'label' => 'Support Level', 'tooltip' => 'Customer support priority'],
                        ];
                    @endphp
                    
                    @foreach($comparisonFeatures as $feature)
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 text-white/70 flex items-center gap-2">
                                {{ $feature['label'] }}
                                <div class="relative group">
                                    <svg class="w-4 h-4 text-white/30 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-xs text-white/70 whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                        {{ $feature['tooltip'] }}
                                    </div>
                                </div>
                            </td>
                            @foreach($packages as $package)
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $limit = $package->getFeatureLimit($feature['key']);
                                        $sortOrder = $package->sort_order ?? 0;
                                    @endphp
                                    @if($feature['key'] === 'support')
                                        <span class="text-white/70">
                                            @if($sortOrder >= 3) Priority @elseif($sortOrder >= 2) Standard @else Email @endif
                                        </span>
                                    @elseif(in_array($feature['key'], ['analytics', 'templates', 'scheduler', 'team']))
                                        @if($sortOrder >= 2)
                                            <svg class="w-5 h-5 text-[#10B981] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-white/20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @endif
                                    @elseif($limit === null)
                                        <span class="text-white/30">—</span>
                                    @elseif($limit === -1)
                                        <span class="text-[#10B981] font-medium">Unlimited</span>
                                    @else
                                        <span class="text-white font-medium">{{ number_format($limit) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-12 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-white text-center mb-8">Frequently Asked Questions</h2>
        
        <div class="space-y-4">
            @php
                $faqs = [
                    ['q' => 'Can I change plans anytime?', 'a' => 'Yes! You can upgrade or downgrade your plan at any time. When upgrading, you\'ll be charged the prorated difference. When downgrading, the new rate applies at the next billing cycle.'],
                    ['q' => 'What payment methods do you accept?', 'a' => 'We accept major credit cards (Visa, Mastercard, Amex), PayPal, Mobile Money (M-Pesa, Tigo Pesa), and USDT (TRC-20).'],
                    ['q' => 'Is there a free trial?', 'a' => 'Yes! Our Free plan lets you test the platform with limited features. No credit card required to get started.'],
                    ['q' => 'What happens if I exceed my limits?', 'a' => 'You\'ll receive a notification when approaching your limits. Messages are queued, not lost. You can upgrade anytime to increase limits.'],
                ];
            @endphp
            
            @foreach($faqs as $faq)
                <details class="bg-[#252525] border border-white/10 rounded-xl group">
                    <summary class="px-6 py-4 cursor-pointer text-white font-medium flex items-center justify-between">
                        {{ $faq['q'] }}
                        <svg class="w-5 h-5 text-white/50 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="px-6 pb-4 text-white/60">
                        {{ $faq['a'] }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
let isAnnual = false;

function toggleBilling() {
    isAnnual = !isAnnual;
    
    const toggle = document.getElementById('toggleDot');
    const monthlyLabel = document.getElementById('monthlyLabel');
    const annualLabel = document.getElementById('annualLabel');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const annualPrices = document.querySelectorAll('.annual-price');
    
    if (isAnnual) {
        toggle.style.left = '1.75rem';
        monthlyLabel.classList.replace('text-white', 'text-white/50');
        annualLabel.classList.replace('text-white/50', 'text-white');
        monthlyPrices.forEach(el => el.classList.add('hidden'));
        annualPrices.forEach(el => el.classList.remove('hidden'));
    } else {
        toggle.style.left = '0.25rem';
        monthlyLabel.classList.replace('text-white/50', 'text-white');
        annualLabel.classList.replace('text-white', 'text-white/50');
        monthlyPrices.forEach(el => el.classList.remove('hidden'));
        annualPrices.forEach(el => el.classList.add('hidden'));
    }
}
</script>
@endpush
@endsection
