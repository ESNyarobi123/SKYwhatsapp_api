@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <p class="text-[#EA3943] text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-[#FCD535]/20 border border-[#FCD535]/50 rounded-lg p-4 mb-6">
            <p class="text-[#FCD535] text-sm">{{ session('info') }}</p>
        </div>
    @endif

    @php
        $user = auth()->user();
        $pendingPayment = $user->payments()
            ->where('status', 'pending')
            ->whereHas('subscription', function($query) {
                $query->where('status', 'pending');
            })
            ->latest()
            ->first();
    @endphp

    @if($pendingPayment)
        <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-white font-medium mb-1">Payment Pending</p>
                    <p class="text-white/70 text-sm mb-2">You have a pending payment for subscription: <strong>{{ $pendingPayment->subscription->plan_name ?? 'N/A' }}</strong></p>
                    <a href="{{ route('dashboard.payment.zenopay.mobile.show', ['payment' => $pendingPayment->id]) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium underline">
                        View Payment Status →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Dashboard</h1>
        <p class="text-white/70">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $user = auth()->user();
            $instancesCount = $user->instances()->count();
            $messagesCount = $user->messages()->count();
            $apiKeysCount = $user->apiKeys()->where('is_active', true)->count();
            $activeSubscription = $user->activeSubscription;
            $hasActiveSubscription = $activeSubscription && $activeSubscription->isActive();
            
            $instancesIcon = '<svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>';
            $messagesIcon = '<svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>';
            $apiKeysIcon = '<svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>';
            $subscriptionIcon = '<svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>';
        @endphp

        <x-metric-card 
            :value="$instancesCount" 
            label="Total Instances"
            :icon="$instancesIcon"
        />

        <x-metric-card 
            :value="$messagesCount" 
            label="Messages Sent"
            :icon="$messagesIcon"
        />

        <x-metric-card 
            :value="$apiKeysCount" 
            label="Active API Keys"
            :icon="$apiKeysIcon"
        />

        <x-metric-card 
            :value="$hasActiveSubscription ? 'Active' : 'None'" 
            label="Subscription Status"
            :icon="$subscriptionIcon"
        />
    </div>

    <!-- Subscription Plans Section -->
    @if(isset($packages) && $packages && count($packages) > 0)
        @if(!$hasActiveSubscription)
            <x-card class="mb-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-2">Choose Your Plan</h2>
                    <p class="text-white/70">Select a subscription plan or start with a free trial to get access to instances and API keys.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($packages as $package)
                        <div class="relative p-6 bg-[#252525] border border-white/10 rounded-lg {{ $package->isFree() ? 'border-[#FCD535]/50 ring-2 ring-[#FCD535]/20' : '' }}">
                            @if($package->isFree())
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 bg-[#FCD535]/10 text-[#FCD535] text-xs font-semibold rounded-full border border-[#FCD535]/30">FREE TRIAL</span>
                                </div>
                            @endif
                            
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-white mb-2">{{ $package->name }}</h3>
                                @if($package->description)
                                    <p class="text-white/70 text-sm mb-4">{{ $package->description }}</p>
                                @endif
                                <div class="mb-4">
                                    <span class="text-3xl font-bold text-[#FCD535]">
                                        {{ $package->isFree() ? 'Free' : number_format($package->getPriceForCurrency('USD'), 2) }}
                                    </span>
                                    @if(!$package->isFree())
                                        <span class="text-white/60 text-lg ml-2">USD</span>
                                    @endif
                                    <p class="text-white/60 text-xs mt-1">Per {{ $package->duration_days }} {{ $package->duration_days == 1 ? 'day' : 'days' }}</p>
                                    @if(!$package->isFree())
                                        <p class="text-white/40 text-xs mt-1">💡 Price converts to TZS/USDT based on payment method</p>
                                    @endif
                                </div>
                            </div>

                            @if($package->features && count($package->features) > 0)
                                <ul class="space-y-2 mb-6">
                                    @php
                                        $features = $package->features ?? [];
                                        $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                                    @endphp
                                    
                                    @if($isOldFormat)
                                        @foreach($features as $feature)
                                            <li class="text-white/70 text-sm flex items-start">
                                                <svg class="w-5 h-5 text-[#FCD535] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    @else
                                        @if(isset($features['instances']))
                                            <li class="text-white/70 text-sm flex items-start">
                                                <svg class="w-5 h-5 text-[#FCD535] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>
                                                    @if($features['instances']['limit'] == -1)
                                                        Unlimited Instances
                                                    @else
                                                        {{ $features['instances']['limit'] }} Instance{{ $features['instances']['limit'] != 1 ? 's' : '' }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endif
                                        @if(isset($features['messages']))
                                            <li class="text-white/70 text-sm flex items-start">
                                                <svg class="w-5 h-5 text-[#FCD535] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>
                                                    @if($features['messages']['limit'] == -1)
                                                        Unlimited Messages
                                                    @else
                                                        {{ number_format($features['messages']['limit']) }} Messages
                                                    @endif
                                                    @if($features['messages']['period'] != 'lifetime')
                                                        / {{ ucfirst($features['messages']['period']) }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endif
                                        @if(isset($features['api_calls']))
                                            <li class="text-white/70 text-sm flex items-start">
                                                <svg class="w-5 h-5 text-[#FCD535] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>
                                                    @if($features['api_calls']['limit'] == -1)
                                                        Unlimited API Calls
                                                    @else
                                                        {{ number_format($features['api_calls']['limit']) }} API Calls
                                                    @endif
                                                    @if($features['api_calls']['period'] != 'lifetime')
                                                        / {{ ucfirst($features['api_calls']['period']) }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endif
                                        @if(isset($features['api_keys']))
                                            <li class="text-white/70 text-sm flex items-start">
                                                <svg class="w-5 h-5 text-[#FCD535] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>
                                                    @if($features['api_keys']['limit'] == -1)
                                                        Unlimited API Keys
                                                    @else
                                                        {{ $features['api_keys']['limit'] }} API Key{{ $features['api_keys']['limit'] != 1 ? 's' : '' }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            @endif

                            <form method="POST" action="{{ route('dashboard.packages.subscribe', $package) }}" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r {{ $package->isFree() ? 'from-[#FCD535] to-[#F0C420] text-[#1A1A1A]' : 'from-[#252525] to-[#1A1A1A] text-white border border-white/10 hover:border-[#FCD535]/50' }} font-semibold rounded-lg transition-all duration-200">
                                    {{ $package->isFree() ? 'Start Free Trial' : 'Select Plan' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @else
            @php
                $activeSubscription = $user->activeSubscription;
                $currentPackage = $activeSubscription && $activeSubscription->package ? $activeSubscription->package : null;
            @endphp
            <x-card class="mb-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-2">Current Subscription</h2>
                    <p class="text-white/70">Your active plan details and all available features.</p>
                </div>
                
                {{-- Enhanced Current Plan Card --}}
                <div class="relative bg-gradient-to-br from-[#252525] via-[#1A1A1A] to-[#252525] border-2 border-[#FCD535]/50 rounded-2xl p-8 mb-8 shadow-xl shadow-[#FCD535]/10">
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(252,213,53,0.5) 1px, transparent 0); background-size: 40px 40px;"></div>
                    </div>
                    
                    <div class="relative z-10">
                        {{-- Header Section --}}
                        <div class="flex items-start justify-between mb-6 pb-6 border-b border-white/10">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-3xl font-bold text-white">{{ $activeSubscription->plan_name }}</h3>
                                    <span class="px-3 py-1 bg-[#00D9A5]/10 text-[#00D9A5] text-sm font-semibold rounded-full border border-[#00D9A5]/30">Active</span>
                                    <span class="px-3 py-1 bg-[#FCD535]/10 text-[#FCD535] text-xs font-semibold rounded-full border border-[#FCD535]/30">Current Plan</span>
                                </div>
                                @if($currentPackage && $currentPackage->description)
                                    <p class="text-white/70 text-base mb-4">{{ $currentPackage->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {{-- Left Column: Pricing & Dates --}}
                            <div class="space-y-6">
                                {{-- Pricing Section --}}
                                @if($currentPackage)
                                    <div class="bg-[#1A1A1A]/50 rounded-xl p-6 border border-white/5">
                                        <p class="text-white/70 text-sm mb-3">Pricing</p>
                                        <div class="mb-4">
                                            <span class="text-5xl font-bold text-[#FCD535]">
                                                {{ $currentPackage->isFree() ? 'Free' : number_format($currentPackage->getPriceForCurrency('USD'), 2) }}
                                            </span>
                                            @if(!$currentPackage->isFree())
                                                <span class="text-white/60 text-xl ml-2">USD</span>
                                            @endif
                                            <p class="text-white/60 text-sm mt-2">Per {{ $currentPackage->duration_days }} {{ $currentPackage->duration_days == 1 ? 'day' : 'days' }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Date Information Section --}}
                                <div class="bg-[#1A1A1A]/50 rounded-xl p-6 border border-white/5">
                                    <p class="text-white/70 text-sm mb-4">Subscription Details</p>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-white/70 text-sm">Started:</span>
                                            <span class="text-white font-medium">{{ $activeSubscription->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-white/70 text-sm">Expires:</span>
                                            <span class="text-white font-medium">{{ $activeSubscription->expires_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        @php
                                            $daysRemaining = now()->diffInDays($activeSubscription->expires_at, false);
                                        @endphp
                                        @if($daysRemaining > 0)
                                            <div class="flex justify-between items-center pt-3 border-t border-white/5">
                                                <span class="text-white/70 text-sm">Time Remaining:</span>
                                                <span class="text-[#FCD535] font-semibold">{{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Features --}}
                            @if($currentPackage && $currentPackage->features && count($currentPackage->features) > 0)
                                <div>
                                    <p class="text-white font-semibold text-lg mb-4">All Features Included</p>
                                    <div class="bg-[#1A1A1A]/50 rounded-xl p-6 border border-white/5 max-h-[500px] overflow-y-auto">
                                        <ul class="space-y-3">
                                            @php
                                                $features = $currentPackage->features ?? [];
                                                $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                                            @endphp
                                            
                                            @if($isOldFormat)
                                                @foreach($features as $feature)
                                                    <li class="text-white/80 text-sm flex items-start group">
                                                        <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="flex-1">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            @else
                                                @if(isset($features['instances']))
                                                    <li class="text-white/80 text-sm flex items-start group">
                                                        <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="flex-1">
                                                            @if($features['instances']['limit'] == -1)
                                                                Unlimited Instances
                                                            @else
                                                                {{ $features['instances']['limit'] }} Instance{{ $features['instances']['limit'] != 1 ? 's' : '' }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endif
                                                @if(isset($features['messages']))
                                                    <li class="text-white/80 text-sm flex items-start group">
                                                        <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="flex-1">
                                                            @if($features['messages']['limit'] == -1)
                                                                Unlimited Messages
                                                            @else
                                                                {{ number_format($features['messages']['limit']) }} Messages
                                                            @endif
                                                            @if($features['messages']['period'] != 'lifetime')
                                                                / {{ ucfirst($features['messages']['period']) }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endif
                                                @if(isset($features['api_calls']))
                                                    <li class="text-white/80 text-sm flex items-start group">
                                                        <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="flex-1">
                                                            @if($features['api_calls']['limit'] == -1)
                                                                Unlimited API Calls
                                                            @else
                                                                {{ number_format($features['api_calls']['limit']) }} API Calls
                                                            @endif
                                                            @if($features['api_calls']['period'] != 'lifetime')
                                                                / {{ ucfirst($features['api_calls']['period']) }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endif
                                                @if(isset($features['api_keys']))
                                                    <li class="text-white/80 text-sm flex items-start group">
                                                        <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span class="flex-1">
                                                            @if($features['api_keys']['limit'] == -1)
                                                                Unlimited API Keys
                                                            @else
                                                                {{ $features['api_keys']['limit'] }} API Key{{ $features['api_keys']['limit'] != 1 ? 's' : '' }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-white mb-4">Available Plans</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($packages as $package)
                            @if(!$currentPackage || $currentPackage->id !== $package->id)
                                <div class="p-6 bg-[#252525] border border-white/10 rounded-lg">
                                    <h4 class="text-lg font-bold text-white mb-2">{{ $package->name }}</h4>
                                    <div class="mb-4">
                                        <span class="text-2xl font-bold text-[#FCD535]">
                                            {{ $package->isFree() ? 'Free' : number_format($package->getPriceForCurrency('USD'), 2) }}
                                        </span>
                                        @if(!$package->isFree())
                                            <span class="text-white/60 ml-2">USD</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('dashboard.packages.subscribe', $package) }}">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 text-white font-medium rounded-lg hover:border-[#FCD535]/50 transition-colors">
                                            Change Plan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </x-card>
        @endif
    @endif

    <!-- Quick Actions -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('dashboard.instances') }}" class="p-4 bg-[#1A1A1A] rounded-lg border border-white/5 hover:border-[#FCD535]/30 transition-colors">
                <h3 class="text-white font-medium mb-2">Create Instance</h3>
                <p class="text-white/60 text-sm">Set up a new WhatsApp instance</p>
            </a>
            <a href="{{ route('dashboard.api-keys') }}" class="p-4 bg-[#1A1A1A] rounded-lg border border-white/5 hover:border-[#FCD535]/30 transition-colors">
                <h3 class="text-white font-medium mb-2">Generate API Key</h3>
                <p class="text-white/60 text-sm">Create a new API key for authentication</p>
            </a>
            <a href="#" class="p-4 bg-[#1A1A1A] rounded-lg border border-white/5 hover:border-[#FCD535]/30 transition-colors">
                <h3 class="text-white font-medium mb-2">View Documentation</h3>
                <p class="text-white/60 text-sm">API documentation and guides</p>
            </a>
        </div>
    </x-card>
</div>
@endsection
