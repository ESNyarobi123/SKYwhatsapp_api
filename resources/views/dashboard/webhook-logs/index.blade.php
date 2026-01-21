@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#06B6D4] to-[#0891B2] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                Webhook Logs
            </h1>
            <p class="text-white/60 mt-1">View webhook delivery history and debug issues</p>
        </div>
        
        <form action="{{ route('dashboard.webhook-logs.clear') }}" method="POST" onsubmit="return confirm('Clear logs older than 30 days?')">
            @csrf
            <button type="submit" class="px-4 py-2 bg-[#252525] border border-white/10 rounded-xl text-white/70 hover:text-white hover:border-[#EF4444]/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Clear Old Logs
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#3B82F6]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                    <p class="text-white/50 text-sm">Total</p>
                </div>
            </div>
        </div>
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#10B981]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['success']) }}</p>
                    <p class="text-white/50 text-sm">Success</p>
                </div>
            </div>
        </div>
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#EF4444]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['failed']) }}</p>
                    <p class="text-white/50 text-sm">Failed</p>
                </div>
            </div>
        </div>
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#F59E0B]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['pending']) }}</p>
                    <p class="text-white/50 text-sm">Pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
        <!-- Status Filter -->
        <div class="flex bg-[#252525] border border-white/10 rounded-xl p-1">
            @foreach(['all' => 'All', 'success' => 'Success', 'failed' => 'Failed', 'pending' => 'Pending'] as $key => $label)
                <a href="{{ route('dashboard.webhook-logs.index', array_merge(request()->except('status'), $key === 'all' ? [] : ['status' => $key])) }}" 
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ ($status ?? null) === $key || (!$status && $key === 'all') ? 'bg-[#06B6D4] text-white' : 'text-white/60 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        
        <!-- Webhook Filter -->
        @if($webhooks->count() > 0)
            <select onchange="filterByWebhook(this.value)" class="px-4 py-2 bg-[#252525] border border-white/10 rounded-xl text-white/70 focus:border-[#06B6D4] outline-none">
                <option value="">All Webhooks</option>
                @foreach($webhooks as $webhook)
                    <option value="{{ $webhook->id }}" {{ $webhookId == $webhook->id ? 'selected' : '' }}>
                        {{ Str::limit($webhook->url, 40) }}
                    </option>
                @endforeach
            </select>
        @endif
        
        <!-- Event Type Filter -->
        @if(count($eventTypes) > 0)
            <select onchange="filterByEvent(this.value)" class="px-4 py-2 bg-[#252525] border border-white/10 rounded-xl text-white/70 focus:border-[#06B6D4] outline-none">
                <option value="">All Events</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event }}" {{ $eventType === $event ? 'selected' : '' }}>{{ $event }}</option>
                @endforeach
            </select>
        @endif
    </div>

    <!-- Logs List -->
    @if($logs->count() > 0)
        <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Webhook</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Response Time</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-white/50 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-[#8B5CF6]/20 text-[#8B5CF6] text-xs rounded-lg font-mono">
                                    {{ $log->event_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white/70 text-sm truncate max-w-[200px]" title="{{ $log->webhook?->url }}">
                                    {{ $log->webhook?->url ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'success' => ['bg' => 'bg-[#10B981]/20', 'text' => 'text-[#10B981]', 'icon' => '✓'],
                                        'failed' => ['bg' => 'bg-[#EF4444]/20', 'text' => 'text-[#EF4444]', 'icon' => '✗'],
                                        'pending' => ['bg' => 'bg-[#F59E0B]/20', 'text' => 'text-[#F59E0B]', 'icon' => '⏳'],
                                    ];
                                    $config = $statusConfig[$log->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                    {{ $config['icon'] }} {{ ucfirst($log->status) }}
                                    @if($log->status_code)
                                        ({{ $log->status_code }})
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->response_time_ms)
                                    <span class="text-white/70 text-sm">{{ $log->response_time_ms }}ms</span>
                                @else
                                    <span class="text-white/30 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-white/70 text-sm">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="viewDetails({{ $log->id }})" class="p-2 text-white/50 hover:text-[#06B6D4] transition-colors" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    @if($log->status === 'failed')
                                        <form action="{{ route('dashboard.webhook-logs.retry', $log) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-white/50 hover:text-[#10B981] transition-colors" title="Retry">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-[#06B6D4]/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#06B6D4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">No Webhook Logs</h3>
            <p class="text-white/60">Webhook deliveries will appear here</p>
        </div>
    @endif
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Webhook Log Details</h3>
            <button onclick="closeDetailsModal()" class="p-2 text-white/50 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="detailsContent" class="p-6 overflow-y-auto max-h-[60vh]">
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin w-8 h-8 border-2 border-[#06B6D4] border-t-transparent rounded-full"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const detailsModal = document.getElementById('detailsModal');

function viewDetails(logId) {
    detailsModal.classList.remove('hidden');
    detailsModal.classList.add('flex');
    
    fetch('/dashboard/webhook-logs/' + logId + '/details')
        .then(r => r.json())
        .then(data => {
            const log = data.log;
            document.getElementById('detailsContent').innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-white/50 text-xs mb-1">Event Type</p>
                            <p class="text-white font-mono text-sm">${log.event_type}</p>
                        </div>
                        <div>
                            <p class="text-white/50 text-xs mb-1">Status</p>
                            <p class="text-white font-medium">${log.status} ${log.status_code ? '(' + log.status_code + ')' : ''}</p>
                        </div>
                        <div>
                            <p class="text-white/50 text-xs mb-1">Response Time</p>
                            <p class="text-white">${log.response_time_ms ? log.response_time_ms + 'ms' : 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-white/50 text-xs mb-1">Created</p>
                            <p class="text-white">${log.created_at}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-white/50 text-xs mb-1">Webhook URL</p>
                        <p class="text-white text-sm font-mono break-all">${log.webhook_url || 'N/A'}</p>
                    </div>
                    
                    ${log.error_message ? `
                        <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 rounded-xl p-3">
                            <p class="text-[#EF4444] text-sm">${log.error_message}</p>
                        </div>
                    ` : ''}
                    
                    <div>
                        <p class="text-white/50 text-xs mb-2">Payload</p>
                        <pre class="bg-[#1A1A1A] rounded-xl p-4 text-xs text-white/80 overflow-x-auto">${JSON.stringify(log.payload, null, 2)}</pre>
                    </div>
                    
                    ${log.response ? `
                        <div>
                            <p class="text-white/50 text-xs mb-2">Response</p>
                            <pre class="bg-[#1A1A1A] rounded-xl p-4 text-xs text-white/80 overflow-x-auto">${JSON.stringify(log.response, null, 2)}</pre>
                        </div>
                    ` : ''}
                </div>
            `;
        });
}

function closeDetailsModal() {
    detailsModal.classList.add('hidden');
    detailsModal.classList.remove('flex');
}

function filterByWebhook(webhookId) {
    const url = new URL(window.location);
    if (webhookId) {
        url.searchParams.set('webhook_id', webhookId);
    } else {
        url.searchParams.delete('webhook_id');
    }
    window.location = url;
}

function filterByEvent(eventType) {
    const url = new URL(window.location);
    if (eventType) {
        url.searchParams.set('event_type', eventType);
    } else {
        url.searchParams.delete('event_type');
    }
    window.location = url;
}

detailsModal.addEventListener('click', function(e) {
    if (e.target === detailsModal) closeDetailsModal();
});
</script>
@endpush
@endsection
