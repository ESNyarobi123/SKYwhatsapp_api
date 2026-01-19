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
                Admin Dashboard
            </h1>
            <p class="text-white/90 text-lg">Complete platform overview & analytics</p>
        </div>
    </div>

    <!-- Pending Actions Alert -->
    @if($expiredSubscriptions > 0 || $pendingPayments > 0)
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#F59E0B] via-[#EF4444] to-[#F59E0B] p-6 mb-8 shadow-lg border border-[#F59E0B]/50 animate-pulse">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Action Required
                </h2>
                <div class="flex flex-wrap gap-4">
                    @if($expiredSubscriptions > 0)
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}" class="flex items-center space-x-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg transition-all text-white font-medium">
                            <span class="bg-white/30 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">{{ $expiredSubscriptions }}</span>
                            <span>Expired Subscriptions</span>
                        </a>
                    @endif
                    @if($pendingPayments > 0)
                        <a href="{{ route('admin.revenue.index', ['status' => 'pending']) }}" class="flex items-center space-x-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg transition-all text-white font-medium">
                            <span class="bg-white/30 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">{{ $pendingPayments }}</span>
                            <span>Pending Payments</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Main Metrics Grid with Colorful Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#3B82F6] to-[#1E40AF] p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="text-white/70 text-sm">Users</span>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ number_format($overview['users']['total']) }}</h3>
                <p class="text-white/80 text-sm">Total registered</p>
            </div>
        </div>

        <!-- Active Subscriptions Card -->
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#10B981] to-[#059669] p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="text-white/70 text-sm">Active</span>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ number_format($overview['subscriptions']['active']) }}</h3>
                <p class="text-white/80 text-sm">Subscriptions</p>
            </div>
        </div>

        <!-- Connected Instances Card -->
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <span class="text-white/70 text-sm">Instances</span>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ number_format($overview['instances']['connected']) }}</h3>
                <p class="text-white/80 text-sm">Connected</p>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#F59E0B] via-[#EF4444] to-[#EC4899] p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-white/70 text-sm">Revenue</span>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">TZS {{ number_format($overview['revenue']['total'], 0) }}</h3>
                <p class="text-white/80 text-sm">All time</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions with Modern Design -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.users.create') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1A1A1A] to-[#252525] p-6 border border-[#3B82F6]/30 hover:border-[#3B82F6] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-[#3B82F6]/20">
            <div class="absolute inset-0 bg-gradient-to-br from-[#3B82F6]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="bg-gradient-to-br from-[#3B82F6] to-[#1E40AF] rounded-lg p-3 mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <span class="text-white font-medium">Create User</span>
            </div>
        </a>

        <a href="{{ route('admin.packages.create') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1A1A1A] to-[#252525] p-6 border border-[#10B981]/30 hover:border-[#10B981] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-[#10B981]/20">
            <div class="absolute inset-0 bg-gradient-to-br from-[#10B981]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="bg-gradient-to-br from-[#10B981] to-[#059669] rounded-lg p-3 mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <span class="text-white font-medium">Create Package</span>
            </div>
        </a>

        <a href="{{ route('admin.subscriptions.create') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1A1A1A] to-[#252525] p-6 border border-[#8B5CF6]/30 hover:border-[#8B5CF6] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-[#8B5CF6]/20">
            <div class="absolute inset-0 bg-gradient-to-br from-[#8B5CF6]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] rounded-lg p-3 mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-white font-medium">New Subscription</span>
            </div>
        </a>

        <a href="{{ route('admin.analytics') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1A1A1A] to-[#252525] p-6 border border-[#F59E0B]/30 hover:border-[#F59E0B] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-[#F59E0B]/20">
            <div class="absolute inset-0 bg-gradient-to-br from-[#F59E0B]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="bg-gradient-to-br from-[#F59E0B] to-[#D97706] rounded-lg p-3 mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-white font-medium">Analytics</span>
            </div>
        </a>
    </div>

    <!-- Detailed Stats Grid with Colors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Users Overview -->
        <x-card class="relative overflow-hidden border-l-4 border-[#3B82F6]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#3B82F6]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-[#3B82F6] rounded-full"></div>
                    Users Overview
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#3B82F6]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Total Users</span>
                        <span class="text-2xl font-bold text-[#3B82F6]">{{ $overview['users']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#10B981]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Active Users</span>
                        <span class="text-2xl font-bold text-[#10B981]">{{ $overview['users']['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#EF4444]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Suspended</span>
                        <span class="text-2xl font-bold text-[#EF4444]">{{ $overview['users']['suspended'] }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Subscriptions Overview -->
        <x-card class="relative overflow-hidden border-l-4 border-[#10B981]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#10B981]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-[#10B981] rounded-full"></div>
                    Subscriptions Overview
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#3B82F6]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Total</span>
                        <span class="text-2xl font-bold text-[#3B82F6]">{{ $overview['subscriptions']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#10B981]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Active</span>
                        <span class="text-2xl font-bold text-[#10B981]">{{ $overview['subscriptions']['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#EF4444]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Expired</span>
                        <span class="text-2xl font-bold text-[#EF4444]">{{ $overview['subscriptions']['expired'] }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- API Keys Overview -->
        <x-card class="relative overflow-hidden border-l-4 border-[#8B5CF6]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#8B5CF6]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-[#8B5CF6] rounded-full"></div>
                    API Keys Overview
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#8B5CF6]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Total Keys</span>
                        <span class="text-2xl font-bold text-[#8B5CF6]">{{ $overview['api_keys']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#10B981]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Active</span>
                        <span class="text-2xl font-bold text-[#10B981]">{{ $overview['api_keys']['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#EF4444]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Revoked</span>
                        <span class="text-2xl font-bold text-[#EF4444]">{{ $overview['api_keys']['revoked'] }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Instances Overview -->
        <x-card class="relative overflow-hidden border-l-4 border-[#F59E0B]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#F59E0B]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-[#F59E0B] rounded-full"></div>
                    Instances Overview
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#3B82F6]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Total Instances</span>
                        <span class="text-2xl font-bold text-[#3B82F6]">{{ $overview['instances']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#10B981]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Connected</span>
                        <span class="text-2xl font-bold text-[#10B981]">{{ $overview['instances']['connected'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-[#EF4444]/10 to-transparent rounded-lg">
                        <span class="text-white/80">Disconnected</span>
                        <span class="text-2xl font-bold text-[#EF4444]">{{ $overview['instances']['disconnected'] }}</span>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Usage & Revenue Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Usage Statistics -->
        <x-card class="relative overflow-hidden bg-gradient-to-br from-[#1A1A1A] via-[#252525] to-[#1A1A1A] border border-[#8B5CF6]/30">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#8B5CF6]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Usage Statistics
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-[#3B82F6]/20 to-transparent rounded-lg border border-[#3B82F6]/30">
                        <p class="text-white/60 text-xs mb-2">Total</p>
                        <p class="text-3xl font-bold text-[#3B82F6]">{{ number_format($overview['usage']['total_requests']) }}</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-[#10B981]/20 to-transparent rounded-lg border border-[#10B981]/30">
                        <p class="text-white/60 text-xs mb-2">Success</p>
                        <p class="text-3xl font-bold text-[#10B981]">{{ number_format($overview['usage']['successful']) }}</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-[#EF4444]/20 to-transparent rounded-lg border border-[#EF4444]/30">
                        <p class="text-white/60 text-xs mb-2">Failed</p>
                        <p class="text-3xl font-bold text-[#EF4444]">{{ number_format($overview['usage']['failed']) }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Revenue Statistics -->
        <x-card class="relative overflow-hidden bg-gradient-to-br from-[#1A1A1A] via-[#252525] to-[#1A1A1A] border border-[#F59E0B]/30">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-[#F59E0B]/20 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Revenue Statistics
                </h2>
                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-r from-[#F59E0B]/20 to-transparent rounded-lg border-l-4 border-[#F59E0B]">
                        <p class="text-white/60 text-sm mb-1">Total Revenue</p>
                        <p class="text-4xl font-bold text-[#F59E0B]">TZS {{ number_format($overview['revenue']['total'], 0) }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-[#10B981]/20 to-transparent rounded-lg border-l-4 border-[#10B981]">
                        <p class="text-white/60 text-sm mb-1">Today's Revenue</p>
                        <p class="text-3xl font-bold text-[#10B981]">TZS {{ number_format($overview['revenue']['today'], 0) }}</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <x-card class="relative overflow-hidden border-l-4 border-[#10B981]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#10B981]/10 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Recent Payments
                    </h2>
                    <a href="{{ route('admin.revenue.index') }}" class="text-[#10B981] hover:text-[#059669] text-sm font-medium transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                @if($recentPayments->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentPayments as $payment)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#1A1A1A] to-[#252525] rounded-lg border border-white/5 hover:border-[#10B981]/50 transition-all group">
                                <div class="flex-1">
                                    <p class="text-white font-medium group-hover:text-[#10B981] transition-colors">{{ $payment->user->name ?? 'N/A' }}</p>
                                    <p class="text-white/60 text-sm mt-1">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                                </div>
                                <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'error') }}">
                                    {{ ucfirst($payment->status) }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-white/60 text-center py-8">No recent payments</p>
                @endif
            </div>
        </x-card>

        <!-- Recent Activity -->
        <x-card class="relative overflow-hidden border-l-4 border-[#8B5CF6]">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#8B5CF6]/10 to-transparent rounded-bl-full"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Recent Activity
                    </h2>
                    <a href="{{ route('admin.activity.index') }}" class="text-[#8B5CF6] hover:text-[#6D28D9] text-sm font-medium transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                @if($recentActivity->count() > 0)
                    <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-center space-x-3 p-4 bg-gradient-to-r from-[#1A1A1A] to-[#252525] rounded-lg border border-white/5 hover:border-[#8B5CF6]/50 transition-all group">
                                <div class="flex-shrink-0">
                                    <x-badge variant="{{ $activity->status_code >= 200 && $activity->status_code < 300 ? 'success' : 'error' }}" size="sm">
                                        {{ $activity->status_code }}
                                    </x-badge>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white text-sm truncate group-hover:text-[#8B5CF6] transition-colors">
                                        <span class="text-white/70">{{ $activity->method }}</span>
                                        <code class="text-xs text-white/60 ml-2">{{ $activity->endpoint }}</code>
                                    </p>
                                    <p class="text-white/60 text-xs mt-1">{{ $activity->user->name ?? 'N/A' }} • {{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-white/60 text-center py-8">No recent activity</p>
                @endif
            </div>
        </x-card>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #8B5CF6, #6D28D9);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #6D28D9, #4C1D95);
    }
</style>
@endsection
