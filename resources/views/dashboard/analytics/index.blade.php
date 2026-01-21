@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                Analytics Dashboard
            </h1>
            <p class="text-white/60 mt-1">Track your messaging performance and insights</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Period Selector -->
            <div class="flex bg-[#252525] border border-white/10 rounded-xl p-1">
                @foreach(['7' => '7 Days', '30' => '30 Days', '90' => '90 Days'] as $value => $label)
                    <a href="{{ route('dashboard.analytics', ['period' => $value]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period == $value ? 'bg-[#8B5CF6] text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            
            <!-- Export Button -->
            <a href="{{ route('dashboard.analytics.export', ['period' => $period]) }}" 
               class="px-4 py-2 bg-[#252525] border border-white/10 rounded-xl text-white/70 hover:text-white hover:border-[#FCD535]/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Messages -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#3B82F6]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                @if($stats['growth'] != 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-lg {{ $stats['growth'] > 0 ? 'bg-[#10B981]/20 text-[#10B981]' : 'bg-[#EF4444]/20 text-[#EF4444]' }}">
                        {{ $stats['growth'] > 0 ? '+' : '' }}{{ $stats['growth'] }}%
                    </span>
                @endif
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['total_messages']) }}</p>
            <p class="text-white/50 text-sm">Total Messages</p>
        </div>

        <!-- Sent -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#10B981]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['outbound']) }}</p>
            <p class="text-white/50 text-sm">Messages Sent</p>
        </div>

        <!-- Received -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#8B5CF6]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['inbound']) }}</p>
            <p class="text-white/50 text-sm">Messages Received</p>
        </div>

        <!-- Success Rate -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#FCD535]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $successRates['success_rate'] }}%</p>
            <p class="text-white/50 text-sm">Success Rate</p>
        </div>

        <!-- Avg Per Day -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#06B6D4]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#06B6D4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['avg_per_day'] }}</p>
            <p class="text-white/50 text-sm">Avg. Per Day</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Messages Per Day Chart -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Messages Over Time</h3>
            <div class="h-[300px]">
                <canvas id="messagesChart"></canvas>
            </div>
        </div>

        <!-- Peak Hours Chart -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Peak Hours</h3>
            <div class="h-[300px]">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Message Status Breakdown -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Delivery Status</h3>
            <div class="h-[200px] flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#10B981]"></div>
                    <span class="text-white/70 text-sm">Delivered: {{ number_format($successRates['delivered']) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#3B82F6]"></div>
                    <span class="text-white/70 text-sm">Sent: {{ number_format($successRates['sent']) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#F59E0B]"></div>
                    <span class="text-white/70 text-sm">Pending: {{ number_format($successRates['pending']) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[#EF4444]"></div>
                    <span class="text-white/70 text-sm">Failed: {{ number_format($successRates['failed']) }}</span>
                </div>
            </div>
        </div>

        <!-- Message Types -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Message Types</h3>
            <div class="space-y-3">
                @php
                    $typeIcons = [
                        'text' => ['icon' => '💬', 'color' => '#3B82F6'],
                        'image' => ['icon' => '🖼️', 'color' => '#10B981'],
                        'video' => ['icon' => '🎬', 'color' => '#8B5CF6'],
                        'audio' => ['icon' => '🎵', 'color' => '#F59E0B'],
                        'document' => ['icon' => '📄', 'color' => '#06B6D4'],
                        'other' => ['icon' => '📦', 'color' => '#6B7280'],
                    ];
                    $totalTypes = array_sum($messageTypes);
                @endphp
                @foreach($messageTypes as $type => $count)
                    @if($count > 0)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-white/70 text-sm flex items-center gap-2">
                                    <span>{{ $typeIcons[$type]['icon'] }}</span>
                                    {{ ucfirst($type) }}
                                </span>
                                <span class="text-white font-medium">{{ number_format($count) }}</span>
                            </div>
                            <div class="h-2 bg-[#1A1A1A] rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $totalTypes > 0 ? ($count / $totalTypes * 100) : 0 }}%; background: {{ $typeIcons[$type]['color'] }}"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Top Instances -->
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Top Instances</h3>
            @if(count($topInstances) > 0)
                <div class="space-y-3">
                    @foreach($topInstances as $index => $instance)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold {{ $index === 0 ? 'bg-[#FCD535]/20 text-[#FCD535]' : 'bg-white/10 text-white/60' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium truncate">{{ $instance['name'] }}</p>
                                <p class="text-white/50 text-xs">{{ $instance['phone'] }}</p>
                            </div>
                            <span class="text-white font-bold">{{ number_format($instance['count']) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-white/50">No data yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js defaults for dark theme
    Chart.defaults.color = 'rgba(255, 255, 255, 0.6)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

    // Messages Over Time Chart
    const messagesData = @json($messagesPerDay);
    new Chart(document.getElementById('messagesChart'), {
        type: 'line',
        data: messagesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });

    // Peak Hours Chart
    const peakData = @json($peakHours);
    new Chart(document.getElementById('peakHoursChart'), {
        type: 'bar',
        data: {
            labels: peakData.labels,
            datasets: [{
                label: 'Messages',
                data: peakData.data,
                backgroundColor: peakData.data.map((val, idx) => 
                    peakData.peak_hours.includes(idx) ? '#FCD535' : 'rgba(139, 92, 246, 0.6)'
                ),
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });

    // Status Doughnut Chart
    const statusData = @json($successRates);
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Delivered', 'Sent', 'Pending', 'Failed'],
            datasets: [{
                data: [statusData.delivered, statusData.sent, statusData.pending, statusData.failed],
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            cutout: '70%',
        },
    });
});
</script>
@endpush
@endsection
