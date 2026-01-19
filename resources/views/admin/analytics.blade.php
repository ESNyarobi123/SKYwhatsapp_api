@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#6366F1] via-[#8B5CF6] to-[#EC4899] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10">
            <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Platform Analytics
            </h1>
            <p class="text-white/90 text-lg">Deep insights & performance metrics</p>
        </div>
    </div>

    <!-- Period Filter -->
    <x-card class="mb-8">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-white">Time Period</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.analytics', ['days' => 7]) }}" class="px-4 py-2 rounded-lg {{ $days == 7 ? 'bg-gradient-to-r from-[#6366F1] to-[#8B5CF6] text-white' : 'bg-[#1A1A1A] text-white/70 hover:bg-[#252525]' }} transition-all">
                    7 Days
                </a>
                <a href="{{ route('admin.analytics', ['days' => 30]) }}" class="px-4 py-2 rounded-lg {{ $days == 30 ? 'bg-gradient-to-r from-[#6366F1] to-[#8B5CF6] text-white' : 'bg-[#1A1A1A] text-white/70 hover:bg-[#252525]' }} transition-all">
                    30 Days
                </a>
                <a href="{{ route('admin.analytics', ['days' => 90]) }}" class="px-4 py-2 rounded-lg {{ $days == 90 ? 'bg-gradient-to-r from-[#6366F1] to-[#8B5CF6] text-white' : 'bg-[#1A1A1A] text-white/70 hover:bg-[#252525]' }} transition-all">
                    90 Days
                </a>
            </div>
        </div>
    </x-card>

    <!-- Revenue by Provider with Visual Chart -->
    @if($analytics['revenue']['by_provider']->count() > 0)
        <x-card class="relative overflow-hidden border-l-4 border-[#F59E0B]">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#F59E0B]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Revenue by Payment Provider
                </h2>
                <div class="space-y-4">
                    @php
                        $maxRevenue = $analytics['revenue']['by_provider']->max('total');
                        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'];
                    @endphp
                    @foreach($analytics['revenue']['by_provider'] as $index => $provider)
                        @php
                            $percentage = $maxRevenue > 0 ? ($provider->total / $maxRevenue) * 100 : 0;
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-medium capitalize">{{ $provider->provider }}</span>
                                <span class="text-[#FCD535] font-bold">TZS {{ number_format($provider->total, 2) }}</span>
                            </div>
                            <div class="w-full bg-[#1A1A1A] rounded-full h-6 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 flex items-center justify-end pr-3" style="width: {{ $percentage }}%; min-width: 8%; background: linear-gradient(to right, {{ $color }}, {{ $color }}B3);">
                                    <span class="text-white text-xs font-semibold">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Instances by Status -->
        @if($analytics['instances']['by_status']->count() > 0)
            <x-card class="relative overflow-hidden border-l-4 border-[#8B5CF6]">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#8B5CF6]/20 to-transparent rounded-bl-full"></div>
                <div class="relative z-10">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Instances Status
                    </h2>
                    <div class="space-y-3">
                        @foreach($analytics['instances']['by_status'] as $status)
                            <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#8B5CF6]/10 to-transparent rounded-lg border border-[#8B5CF6]/20">
                                <span class="text-white/80 capitalize">{{ $status->status }}</span>
                                <span class="text-2xl font-bold text-[#8B5CF6]">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
        @endif

        <!-- Subscriptions by Status -->
        @if($analytics['subscriptions']['by_status']->count() > 0)
            <x-card class="relative overflow-hidden border-l-4 border-[#10B981]">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#10B981]/20 to-transparent rounded-bl-full"></div>
                <div class="relative z-10">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Subscriptions Status
                    </h2>
                    <div class="space-y-3">
                        @foreach($analytics['subscriptions']['by_status'] as $status)
                            <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#10B981]/10 to-transparent rounded-lg border border-[#10B981]/20">
                                <span class="text-white/80 capitalize">{{ $status->status }}</span>
                                <span class="text-2xl font-bold text-[#10B981]">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
        @endif

        <!-- Performance Metrics -->
        @if(isset($analytics['usage']['average_response_time_ms']))
            <x-card class="relative overflow-hidden border-l-4 border-[#3B82F6]">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#3B82F6]/20 to-transparent rounded-bl-full"></div>
                <div class="relative z-10">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Performance
                    </h2>
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-r from-[#3B82F6]/10 to-transparent rounded-lg border border-[#3B82F6]/20">
                            <p class="text-white/70 text-sm mb-2">Avg Response Time</p>
                            <p class="text-4xl font-bold text-[#3B82F6]">{{ number_format($analytics['usage']['average_response_time_ms'], 2) }}</p>
                            <p class="text-white/60 text-xs mt-1">milliseconds</p>
                        </div>
                    </div>
                </div>
            </x-card>
        @endif
    </div>

    <!-- User Growth Chart (Simple Bar Chart) -->
    @if($analytics['users']['growth']->count() > 0)
        <x-card class="relative overflow-hidden border-l-4 border-[#EC4899]">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#EC4899]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#EC4899]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    User Growth Trend
                </h2>
                <div class="flex items-end justify-between gap-2 h-64">
                    @php
                        $maxUsers = max(1, $analytics['users']['growth']->max('count'));
                    @endphp
                    @foreach($analytics['users']['growth']->slice(-14) as $growth)
                        @php
                            $height = ($growth->count / $maxUsers) * 100;
                            $colorIndex = rand(0, 5);
                            $colors = ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#3B82F6'];
                            $color = $colors[$colorIndex];
                        @endphp
                        <div class="flex-1 flex flex-col items-center group cursor-pointer">
                            <div class="w-full rounded-t-lg transition-all duration-300 group-hover:opacity-80" style="height: {{ max($height, 5) }}%; background: linear-gradient(to top, {{ $color }}, {{ $color }}B3);">
                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-[#1A1A1A] text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    {{ $growth->count }} users
                                </div>
                            </div>
                            <span class="text-white/40 text-xs mt-2 transform -rotate-45 origin-bottom-left">{{ \Carbon\Carbon::parse($growth->date)->format('M d') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif

    <!-- Revenue Period Chart -->
    @if($analytics['revenue']['by_period']->count() > 0)
        <x-card class="relative overflow-hidden border-l-4 border-[#10B981]">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#10B981]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Revenue Trend (Last {{ $days }} Days)
                </h2>
                <div class="flex items-end justify-between gap-2 h-64">
                    @php
                        $maxRevenue = max(1, $analytics['revenue']['by_period']->max('total'));
                    @endphp
                    @foreach($analytics['revenue']['by_period']->slice(-14) as $revenue)
                        @php
                            $height = ($revenue->total / $maxRevenue) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center group cursor-pointer">
                            <div class="w-full bg-gradient-to-t from-[#10B981] to-[#059669] rounded-t-lg transition-all duration-300 group-hover:opacity-80" style="height: {{ max($height, 5) }}%">
                                <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-[#1A1A1A] text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    TZS {{ number_format($revenue->total, 0) }}
                                </div>
                            </div>
                            <span class="text-white/40 text-xs mt-2 transform -rotate-45 origin-bottom-left">{{ \Carbon\Carbon::parse($revenue->date)->format('M d') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif
</div>
@endsection
