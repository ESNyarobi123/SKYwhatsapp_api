@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $activeSubscription = $user->activeSubscription;
    $currentPackage = $activeSubscription?->package;
    $hasActiveSubscription = $activeSubscription && $activeSubscription->isActive();
    $currentFeatures = $currentPackage?->features ?? [];
    $daysRemaining = $hasActiveSubscription ? now()->diffInDays($activeSubscription->expires_at, false) : 0;
@endphp

<div class="space-y-8">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#8B5CF6] via-[#7C3AED] to-[#6D28D9] p-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Subscription Plans
                </h1>
                <p class="text-white/80 text-lg">Choose the perfect plan for your business needs</p>
            </div>
            
            @if($hasActiveSubscription)
                <!-- Current Plan Badge -->
                <div class="bg-white/15 backdrop-blur-sm border border-white/30 rounded-2xl p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80 text-sm font-medium">Current Plan</span>
                    </div>
                    <p class="text-white text-2xl font-bold mb-1">{{ $currentPackage->name }}</p>
                    @if($daysRemaining > 0)
                        <p class="text-white/70 text-sm">{{ $daysRemaining }} days remaining</p>
                    @else
                        <p class="text-red-300 text-sm font-medium">Expires soon!</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($hasActiveSubscription && $currentPackage)
        <!-- Current Plan Features -->
        <div class="bg-gradient-to-br from-[#252525] to-[#1A1A1A] border border-[#8B5CF6]/30 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-[#8B5CF6]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">Your Current Plan Features</h3>
                    <p class="text-white/60 text-sm">Everything included in {{ $currentPackage->name }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $instanceLimit = $currentPackage->getFeatureLimit('instances');
                    $messageLimit = $currentPackage->getFeatureLimit('messages');
                    $apiKeyLimit = $currentPackage->getFeatureLimit('api_keys');
                    $botRuleLimit = $currentPackage->getFeatureLimit('bot_rules');
                @endphp
                
                <div class="bg-[#1A1A1A] rounded-xl p-4 border border-white/5">
                    <p class="text-white/50 text-xs uppercase tracking-wide mb-1">Instances</p>
                    <p class="text-white font-bold text-2xl">{{ $instanceLimit === null ? '∞' : $instanceLimit }}</p>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-4 border border-white/5">
                    <p class="text-white/50 text-xs uppercase tracking-wide mb-1">Messages/Day</p>
                    <p class="text-white font-bold text-2xl">{{ $messageLimit === null ? '∞' : number_format($messageLimit) }}</p>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-4 border border-white/5">
                    <p class="text-white/50 text-xs uppercase tracking-wide mb-1">API Keys</p>
                    <p class="text-white font-bold text-2xl">{{ $apiKeyLimit === null ? '∞' : $apiKeyLimit }}</p>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-4 border border-white/5">
                    <p class="text-white/50 text-xs uppercase tracking-wide mb-1">Bot Rules</p>
                    <p class="text-white font-bold text-2xl">{{ $botRuleLimit === null ? '∞' : $botRuleLimit }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- All Plans -->
    <div>
        <h2 class="text-2xl font-bold text-white mb-6">{{ $hasActiveSubscription ? 'Upgrade Your Plan' : 'Choose Your Plan' }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($packages as $package)
                @php
                    $features = $package->features ?? [];
                    $isCurrentPlan = $currentPackage && $currentPackage->id === $package->id;
                    $botType = $features['bot_type'] ?? 'simple';
                    $isAdvancedBot = $botType === 'advanced';
                    $instanceLimit = $package->getFeatureLimit('instances');
                    $messageLimit = $package->getFeatureLimit('messages');
                    $apiKeyLimit = $package->getFeatureLimit('api_keys');
                    $botRuleLimit = $package->getFeatureLimit('bot_rules');
                    $canUpgrade = $hasActiveSubscription && !$isCurrentPlan && ($package->price_usd > ($currentPackage->price_usd ?? 0));
                @endphp
                
                <div class="relative group {{ $isCurrentPlan ? 'ring-2 ring-[#8B5CF6]' : '' }}">
                    @if($isCurrentPlan)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10">
                            <span class="px-4 py-1 bg-[#8B5CF6] text-white text-xs font-bold rounded-full whitespace-nowrap">✓ Current Plan</span>
                        </div>
                    @endif
                    
                    <div class="h-full bg-[#252525] border {{ $isCurrentPlan ? 'border-[#8B5CF6]/50' : 'border-white/10' }} rounded-2xl p-6 hover:border-[#FCD535]/30 transition-all">
                        <!-- Package Header -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-xl font-bold text-white">{{ $package->name }}</h3>
                                @if($package->isFree())
                                    <span class="px-2 py-0.5 bg-[#00D9A5]/20 text-[#00D9A5] text-xs font-semibold rounded-full">FREE</span>
                                @endif
                            </div>
                            
                            <!-- Price -->
                            <div class="flex items-baseline gap-1 mb-2">
                                <span class="text-4xl font-extrabold text-white">
                                    {{ $package->isFree() ? 'Free' : '$' . number_format($package->price_usd, 0) }}
                                </span>
                                @if(!$package->isFree())
                                    <span class="text-white/50 text-sm">/ {{ $package->duration_days }}d</span>
                                @endif
                            </div>
                            
                            <!-- Bot Type Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium {{ $isAdvancedBot ? 'bg-[#8B5CF6]/20 text-[#A78BFA]' : 'bg-white/10 text-white/70' }}">
                                @if($isAdvancedBot)
                                    🤖 Advanced Bot
                                @else
                                    💬 Simple Bot
                                @endif
                            </span>
                        </div>
                        
                        <!-- Features List -->
                        <ul class="space-y-3 mb-6 text-sm">
                            <li class="flex items-center gap-2 text-white/80">
                                <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                <span class="font-medium text-white">{{ $instanceLimit === null ? '∞' : $instanceLimit }}</span> Instances
                            </li>
                            <li class="flex items-center gap-2 text-white/80">
                                <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                <span class="font-medium text-white">{{ $messageLimit === null ? '∞' : number_format($messageLimit) }}</span> Msg/Day
                            </li>
                            <li class="flex items-center gap-2 text-white/80">
                                <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                <span class="font-medium text-white">{{ $apiKeyLimit === null ? '∞' : $apiKeyLimit }}</span> API Keys
                            </li>
                            <li class="flex items-center gap-2 text-white/80">
                                <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                <span class="font-medium text-white">{{ $botRuleLimit === null ? '∞' : $botRuleLimit }}</span> Bot Rules
                            </li>
                            
                            @if($features['bot_menus'] ?? false)
                                <li class="flex items-center gap-2 text-white/80">
                                    <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    </svg>
                                    Menu Bots
                                </li>
                            @endif
                            
                            @if($features['bot_analytics'] ?? false)
                                <li class="flex items-center gap-2 text-white/80">
                                    <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    </svg>
                                    Bot Analytics
                                </li>
                            @endif
                            
                            @if($features['priority_support'] ?? false)
                                <li class="flex items-center gap-2 text-white/80">
                                    <svg class="w-4 h-4 text-[#FCD535] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Priority Support
                                </li>
                            @endif
                        </ul>
                        
                        <!-- Action Button -->
                        @if($isCurrentPlan)
                            <button disabled class="w-full py-3 bg-white/10 text-white/50 font-semibold rounded-xl cursor-not-allowed">
                                Current Plan
                            </button>
                        @elseif($hasActiveSubscription && !$canUpgrade)
                            <button disabled class="w-full py-3 bg-white/5 text-white/30 font-semibold rounded-xl cursor-not-allowed text-sm">
                                Lower Tier
                            </button>
                        @else
                            <form method="POST" action="{{ route('dashboard.packages.subscribe', $package) }}">
                                @csrf
                                <button type="submit" class="w-full py-3 {{ $package->isFree() ? 'bg-[#00D9A5] hover:bg-[#00C896]' : 'bg-[#FCD535] hover:bg-[#F0C420]' }} text-[#1A1A1A] font-bold rounded-xl transition-all transform hover:scale-[1.02]">
                                    @if($package->isFree())
                                        Start Free Trial
                                    @elseif($hasActiveSubscription)
                                        Upgrade Now
                                    @else
                                        Subscribe
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Money Back Guarantee -->
    <div class="text-center py-6">
        <div class="inline-flex items-center gap-2 px-6 py-3 bg-[#252525] border border-white/10 rounded-full">
            <svg class="w-5 h-5 text-[#00D9A5]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-white/70 text-sm">30-Day Money Back Guarantee</span>
            <span class="text-white/30 mx-2">•</span>
            <span class="text-white/70 text-sm">Cancel Anytime</span>
        </div>
    </div>
</div>
@endsection
