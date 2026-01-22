@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    
    // Determine target user for data fetching (Team Owner or Self)
    $targetUser = $user;
    if ($user->current_team_id && $user->currentTeam) {
        $targetUser = $user->currentTeam->owner;
    }

    $instancesCount = $targetUser->instances()->count();
    $messagesCount = $targetUser->messages()->count();
    $apiKeysCount = $targetUser->apiKeys()->where('is_active', true)->count();
    
    // For subscription, we should probably check the team owner's subscription if in a team
    $activeSubscription = $targetUser->activeSubscription;
    $hasActiveSubscription = $activeSubscription && $activeSubscription->isActive();
    $currentPackage = $activeSubscription?->package;
    $features = $currentPackage?->features ?? [];
    
    // Get feature limits
    $featureLimitService = app(\App\Services\FeatureLimitService::class);
    $usageStats = $featureLimitService->getFeatureUsageStats($targetUser);
    
    // Pending payment (check for target user)
    $pendingPayment = $targetUser->payments()
        ->where('status', 'pending')
        ->whereHas('subscription', function($query) {
            $query->where('status', 'pending');
        })
        ->latest()
        ->first();
    
    // Bot features
    $botType = $features['bot_type'] ?? 'simple';
    $isAdvancedBot = $botType === 'advanced';
    $botRulesCount = $targetUser->botReplies()->count();
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-xl p-4 flex items-center space-x-3">
            <svg class="w-6 h-6 text-[#00D9A5] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[#00D9A5]">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-xl p-4 flex items-center space-x-3">
            <svg class="w-6 h-6 text-[#EA3943] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[#EA3943]">{{ session('error') }}</p>
        </div>
    @endif

    @if($pendingPayment)
        <div class="bg-gradient-to-r from-[#FCD535]/20 to-[#F59E0B]/20 border border-[#FCD535]/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#FCD535]/20 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Payment Pending</p>
                        <p class="text-white/70 text-sm">{{ $pendingPayment->subscription->plan_name ?? 'Subscription' }}</p>
                    </div>
                </div>
                <a href="{{ route('dashboard.payment.zenopay.mobile.show', ['payment' => $pendingPayment->id]) }}" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-all">
                    View Status →
                </a>
            </div>
        </div>
    @endif

    <!-- Modern Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#00D9A5] via-[#10B981] to-[#059669] p-8 shadow-2xl mb-8">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-30"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    @php
                        $hour = date('H');
                        $greeting = 'Good Morning';
                        if ($hour >= 12 && $hour < 17) {
                            $greeting = 'Good Afternoon';
                        } elseif ($hour >= 17) {
                            $greeting = 'Good Evening';
                        }
                    @endphp
                    <p class="text-white/80 text-sm uppercase tracking-wider mb-2">{{ $greeting }}</p>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">{{ $user->name }} 👋</h1>
                    <p class="text-white/90 text-lg max-w-lg">Your WhatsApp API control center. Manage instances, monitor messages, and grow your business.</p>
                </div>
                
                <div class="flex flex-col gap-4">
                    @if($hasActiveSubscription)
                        <div class="bg-white/15 backdrop-blur-sm border border-white/30 rounded-2xl p-5 min-w-[200px]">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                <span class="text-white/80 text-sm font-medium">Active Plan</span>
                            </div>
                            <p class="text-white text-2xl font-bold mb-1">{{ $currentPackage->name ?? 'Pro Plan' }}</p>
                            @php
                                $daysRemaining = floor(now()->diffInDays($activeSubscription->expires_at, false));
                            @endphp
                            @if($daysRemaining > 0)
                                <p class="text-white/70 text-sm">{{ $daysRemaining }} days remaining</p>
                            @else
                                <p class="text-[#FCD535] text-sm font-medium">Expires soon!</p>
                            @endif
                        </div>
                    @endif

                    <!-- Connection Health Widget -->
                    @php
                        $connectedInstances = $targetUser->instances()->where('status', 'connected')->count();
                        $totalInstances = $targetUser->instances()->count();
                        $healthColor = $connectedInstances > 0 ? '#10B981' : ($totalInstances > 0 ? '#EF4444' : '#6B7280');
                        $healthText = $connectedInstances > 0 ? 'System Healthy' : ($totalInstances > 0 ? 'Action Needed' : 'No Instances');
                    @endphp
                    <div class="bg-black/20 backdrop-blur-sm border border-white/10 rounded-2xl p-4 flex items-center gap-4 min-w-[200px]">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: {{ $healthColor }}20">
                            <svg class="w-6 h-6" style="color: {{ $healthColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white/60 text-xs uppercase tracking-wider">System Status</p>
                            <p class="text-white font-bold">{{ $healthText }}</p>
                            <p class="text-white/50 text-xs">{{ $connectedInstances }} / {{ $totalInstances }} Connected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Usage Tracking Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $trackingCards = [
                ['label' => 'Instances', 'key' => 'instances', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'color' => '#3B82F6', 'value' => $instancesCount],
                ['label' => 'Messages', 'key' => 'messages', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => '#10B981', 'value' => $messagesCount],
                ['label' => 'API Keys', 'key' => 'api_keys', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', 'color' => '#8B5CF6', 'value' => $apiKeysCount],
                ['label' => 'Bot Rules', 'key' => 'bot_rules', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => '#F59E0B', 'value' => $botRulesCount],
            ];
        @endphp
        
        @foreach($trackingCards as $card)
            @php
                $stat = $usageStats[$card['key']] ?? null;
                $usage = $card['value'];
                $limit = $stat['limit'] ?? null;
                $isUnlimited = ($stat['is_unlimited'] ?? false) || $limit === null || $limit === 0;
                $percentage = (!$isUnlimited && $limit > 0) ? min(100, ($usage / $limit) * 100) : 0;
            @endphp
            <div class="group relative overflow-hidden bg-[#252525] border border-white/10 rounded-2xl p-5 hover:border-{{ str_replace('#', '', $card['color']) }}/50 transition-all duration-300">
                <div class="absolute top-0 right-0 w-20 h-20 rounded-full blur-2xl opacity-20 group-hover:opacity-40 transition-opacity" style="background: {{ $card['color'] }}"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: {{ $card['color'] }}20">
                            <svg class="w-6 h-6" style="color: {{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                        @if(!$isUnlimited)
                            <span class="text-xs text-white/50 bg-white/5 px-2 py-1 rounded-lg">{{ $usage }}/{{ $limit }}</span>
                        @else
                            <span class="text-xs text-[#10B981] bg-[#10B981]/10 px-2 py-1 rounded-lg">Unlimited</span>
                        @endif
                    </div>
                    
                    <p class="text-3xl font-bold text-white mb-1">{{ number_format($usage) }}</p>
                    <p class="text-white/60 text-sm">{{ $card['label'] }}</p>
                    
                    @if(!$isUnlimited && $limit > 0)
                        <div class="mt-3 h-1.5 bg-[#1A1A1A] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background: {{ $card['color'] }}"></div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    </div>
    
    <!-- Recent Activity & Quick Actions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions (Takes up 2 columns) -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('dashboard.instances') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#3B82F6]/20 to-[#3B82F6]/5 border border-[#3B82F6]/30 rounded-2xl p-6 hover:border-[#3B82F6]/60 transition-all duration-300">
                <div class="absolute top-0 right-0 w-20 h-20 bg-[#3B82F6]/20 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-[#3B82F6]/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-1">Create Instance</h3>
                    <p class="text-white/60 text-sm">Launch a new WhatsApp connection</p>
                </div>
            </a>
            
            <a href="{{ route('dashboard.api-keys') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#8B5CF6]/20 to-[#8B5CF6]/5 border border-[#8B5CF6]/30 rounded-2xl p-6 hover:border-[#8B5CF6]/60 transition-all duration-300">
                <div class="absolute top-0 right-0 w-20 h-20 bg-[#8B5CF6]/20 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-[#8B5CF6]/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-1">Generate API Key</h3>
                    <p class="text-white/60 text-sm">Create secure authentication</p>
                </div>
            </a>
            
            <a href="{{ route('dashboard.bot.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#F59E0B]/20 to-[#F59E0B]/5 border border-[#F59E0B]/30 rounded-2xl p-6 hover:border-[#F59E0B]/60 transition-all duration-300">
                <div class="absolute top-0 right-0 w-20 h-20 bg-[#F59E0B]/20 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-[#F59E0B]/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-1">Bot Builder</h3>
                    <p class="text-white/60 text-sm">Create auto-reply rules</p>
                </div>
            </a>
            
            <a href="{{ route('dashboard.usage') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#06B6D4]/20 to-[#06B6D4]/5 border border-[#06B6D4]/30 rounded-2xl p-6 hover:border-[#06B6D4]/60 transition-all duration-300">
                <div class="absolute top-0 right-0 w-20 h-20 bg-[#06B6D4]/20 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-[#06B6D4]/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-[#06B6D4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-1">View Analytics</h3>
                    <p class="text-white/60 text-sm">Track usage & performance</p>
                </div>
            </a>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-white font-bold text-lg">Recent Activity</h3>
                <a href="{{ route('dashboard.messages') }}" class="text-sm text-[#00D9A5] hover:text-[#00D9A5]/80 transition-colors">View All</a>
            </div>
            
            @php
                $recentMessages = $targetUser->messages()->latest()->take(5)->get();
            @endphp
            
            @if($recentMessages->count() > 0)
                <div class="space-y-4">
                    @foreach($recentMessages as $msg)
                        <div class="flex items-start gap-3 group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 {{ $msg->direction === 'outbound' ? 'bg-[#3B82F6]/20 text-[#3B82F6]' : 'bg-[#10B981]/20 text-[#10B981]' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $msg->direction === 'outbound' ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm truncate">{{ $msg->to }}</p>
                                <p class="text-white/50 text-xs truncate">{{ Str::limit($msg->body ?? 'Media message', 30) }}</p>
                            </div>
                            <span class="text-white/40 text-xs whitespace-nowrap">{{ $msg->created_at->diffForHumans(null, true, true) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-white/50 text-sm">No recent activity</p>
                </div>
            @endif
        </div>
    </div>

    @if(!$hasActiveSubscription && isset($packages) && $packages && count($packages) > 0)
        <!-- Choose Plan Section -->
        <div class="bg-gradient-to-br from-[#252525] to-[#1A1A1A] border border-white/10 rounded-2xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-3">Choose Your Plan</h2>
                <p class="text-white/70 max-w-xl mx-auto">Unlock the full power of WhatsApp API with our flexible plans. Start free or scale up.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages->take(3) as $package)
                    @php
                        $pkgFeatures = $package->features ?? [];
                        $pkgBotType = $pkgFeatures['bot_type'] ?? 'simple';
                    @endphp
                    <div class="relative bg-[#1A1A1A] border {{ $package->isFree() ? 'border-[#FCD535]/50 ring-2 ring-[#FCD535]/20' : 'border-white/10' }} rounded-2xl p-6 hover:border-[#FCD535]/30 transition-all">
                        @if($package->isFree())
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="px-4 py-1 bg-[#FCD535] text-[#1A1A1A] text-xs font-bold rounded-full">FREE TRIAL</span>
                            </div>
                        @endif
                        
                        <div class="mb-6 pt-2">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $package->name }}</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-bold text-[#FCD535]">
                                    {{ $package->isFree() ? 'Free' : '$' . number_format($package->getPriceForCurrency('USD'), 0) }}
                                </span>
                                @if(!$package->isFree())
                                    <span class="text-white/50 text-sm">/ {{ $package->duration_days }}d</span>
                                @endif
                            </div>
                        </div>
                        
                        <ul class="space-y-3 mb-6">
                            @if(isset($pkgFeatures['instances']))
                                <li class="flex items-center gap-2 text-sm text-white/80">
                                    <svg class="w-4 h-4 text-[#00D9A5]" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    {{ $pkgFeatures['instances']['limit'] == -1 ? '∞' : $pkgFeatures['instances']['limit'] }} Instances
                                </li>
                            @endif
                            @if(isset($pkgFeatures['messages']))
                                <li class="flex items-center gap-2 text-sm text-white/80">
                                    <svg class="w-4 h-4 text-[#00D9A5]" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    {{ $pkgFeatures['messages']['limit'] == -1 ? '∞' : number_format($pkgFeatures['messages']['limit']) }} Messages
                                </li>
                            @endif
                            <li class="flex items-center gap-2 text-sm text-white/80">
                                <svg class="w-4 h-4 text-{{ $pkgBotType === 'advanced' ? '[#FCD535]' : '[#00D9A5]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                {{ $pkgBotType === 'advanced' ? '🤖 Advanced Bot' : '💬 Simple Bot' }}
                            </li>
                        </ul>
                        
                        <form method="POST" action="{{ route('dashboard.packages.subscribe', $package) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 {{ $package->isFree() ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-white/10 text-white border border-white/20' }} font-semibold rounded-xl hover:opacity-90 transition-all">
                                {{ $package->isFree() ? 'Start Free Trial' : 'Subscribe Now' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($hasActiveSubscription && $currentPackage)
        <!-- Current Plan Summary -->
        <div class="bg-gradient-to-br from-[#252525] to-[#1A1A1A] border border-white/10 rounded-2xl p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-white/60 text-sm uppercase tracking-wider mb-1">Your Current Plan</h3>
                    <div class="flex items-center gap-3">
                        <p class="text-2xl font-bold text-white">{{ $currentPackage->name }}</p>
                        <span class="px-3 py-1 bg-[#00D9A5]/20 text-[#00D9A5] text-xs font-semibold rounded-full">Active</span>
                        @if($isAdvancedBot)
                            <span class="px-3 py-1 bg-[#8B5CF6]/20 text-[#8B5CF6] text-xs font-semibold rounded-full">🤖 Advanced Bot</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard.settings') }}" class="px-4 py-2 bg-white/10 text-white border border-white/20 rounded-xl hover:bg-white/20 transition-all">
                        Manage Plan
                    </a>
                    <a href="{{ route('dashboard.usage') }}" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                        View Usage
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
