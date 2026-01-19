@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Usage</h1>
        <p class="text-white/70">Track your API usage and statistics</p>
    </div>

    <!-- Usage Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-metric-card 
            :value="number_format($stats['total_requests'] ?? 0)" 
            label="Total Requests"
        />
        <x-metric-card 
            :value="number_format($stats['successful_requests'] ?? 0)" 
            label="Successful Requests"
        />
        <x-metric-card 
            :value="number_format($stats['failed_requests'] ?? 0)" 
            label="Failed Requests"
        />
    </div>

    <!-- Feature Usage Limits -->
    @if(isset($featureUsageStats) && count($featureUsageStats) > 0)
        <x-card class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-6">Feature Limits & Usage</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($featureUsageStats as $featureName => $featureStat)
                    @php
                        $featureNames = [
                            'instances' => 'Instances',
                            'messages' => 'Messages',
                            'api_calls' => 'API Calls',
                            'api_keys' => 'API Keys',
                        ];
                        $featureNameLabel = $featureNames[$featureName] ?? ucfirst($featureName);
                        $periodLabels = [
                            'day' => 'per day',
                            'month' => 'per month',
                            'year' => 'per year',
                            'lifetime' => 'total',
                        ];
                        $periodLabel = $periodLabels[$featureStat['period']] ?? '';
                        $usage = $featureStat['usage'] ?? 0;
                        $limit = $featureStat['limit'];
                        $isUnlimited = $featureStat['is_unlimited'] ?? false;
                        
                        if ($isUnlimited || $limit === null) {
                            $percentage = 0;
                            $progressColor = '#10B981';
                        } else {
                            $percentage = min(100, ($usage / $limit) * 100);
                            $progressColor = $percentage >= 90 ? '#EF4444' : ($percentage >= 70 ? '#F59E0B' : '#10B981');
                        }
                    @endphp
                    <div class="p-4 bg-gradient-to-br from-[#1A1A1A] to-[#252525] rounded-lg border border-white/10">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-white font-semibold">{{ $featureNameLabel }}</h3>
                            @if($isUnlimited || $limit === null)
                                <span class="text-xs text-[#10B981] font-medium">Unlimited</span>
                            @else
                                <span class="text-xs text-white/60">{{ $usage }} / {{ $limit }} {{ $periodLabel }}</span>
                            @endif
                        </div>
                        @if(!$isUnlimited && $limit !== null)
                            <div class="w-full bg-[#1A1A1A] rounded-full h-3 mb-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300" style="width: {{ $percentage }}%; background: linear-gradient(to right, {{ $progressColor }}, {{ $progressColor }}CC);"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-white/50">Used: {{ number_format($usage) }}</span>
                                <span class="text-white/50">Remaining: {{ number_format($featureStat['remaining'] ?? 0) }}</span>
                            </div>
                            @if($featureStat['period'] !== 'lifetime')
                                <p class="text-white/40 text-xs mt-2">
                                    Resets: 
                                    @if($featureStat['period'] === 'day')
                                        {{ now()->endOfDay()->diffForHumans() }}
                                    @elseif($featureStat['period'] === 'month')
                                        {{ now()->endOfMonth()->diffForHumans() }}
                                    @endif
                                </p>
                            @endif
                        @else
                            <p class="text-white/50 text-sm mt-2">No limit restrictions</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Rate Limits -->
    @if(isset($stats['rate_limit']))
        <x-card class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">Rate Limits</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-white/70">Remaining Requests</span>
                    <span class="text-[#FCD535] font-semibold">{{ $stats['rate_limit']['remaining'] ?? 0 }} / {{ $stats['rate_limit']['limit'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-[#1A1A1A] rounded-full h-2">
                    <div class="bg-[#FCD535] h-2 rounded-full" style="width: {{ ($stats['rate_limit']['remaining'] ?? 0) / ($stats['rate_limit']['limit'] ?? 1) * 100 }}%"></div>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Usage Logs -->
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Recent Usage Logs</h2>
        @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Endpoint</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Method</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Response Time</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($logs as $log)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-4 text-white text-sm">{{ $log->endpoint }}</td>
                                <td class="px-4 py-4">
                                    <x-badge variant="gold">{{ $log->method }}</x-badge>
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge variant="{{ $log->status_code >= 200 && $log->status_code < 300 ? 'success' : ($log->status_code >= 400 ? 'error' : 'warning') }}">
                                        {{ $log->status_code }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">
                                    {{ $log->response_time ? number_format($log->response_time, 2) . ' ms' : 'N/A' }}
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @else
            <p class="text-white/70 text-center py-8">No usage logs yet</p>
        @endif
    </x-card>
</div>
@endsection
