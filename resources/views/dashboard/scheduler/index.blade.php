@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#F59E0B] to-[#D97706] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Message Scheduler
            </h1>
            <p class="text-white/60 mt-1">Schedule messages to be sent automatically</p>
        </div>
        
        <button onclick="openScheduleModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Schedule Message
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#F59E0B]/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $stats['pending'] }}</p>
                    <p class="text-white/50 text-sm">Pending</p>
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
                    <p class="text-2xl font-bold text-white">{{ $stats['sent'] }}</p>
                    <p class="text-white/50 text-sm">Sent</p>
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
                    <p class="text-2xl font-bold text-white">{{ $stats['failed'] }}</p>
                    <p class="text-white/50 text-sm">Failed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'sent' => 'Sent', 'failed' => 'Failed'] as $key => $label)
            <a href="{{ route('dashboard.scheduler.index', $key === 'all' ? [] : ['status' => $key]) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ ($status ?? null) === $key || (!$status && $key === 'all') ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-[#252525] text-white/70 hover:text-white border border-white/10' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Scheduled Messages List -->
    @if($messages->count() > 0)
        <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Recipient</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Instance</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Scheduled For</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white/50 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-white/50 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($messages as $message)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-white font-medium">{{ $message->recipient }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white/70 text-sm truncate max-w-[200px]">{{ $message->content }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-white/70 text-sm">{{ $message->instance?->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-white text-sm">{{ $message->scheduled_at->format('M d, Y') }}</p>
                                    <p class="text-white/50 text-xs">{{ $message->scheduled_at->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-[#F59E0B]/20 text-[#F59E0B]',
                                        'sent' => 'bg-[#10B981]/20 text-[#10B981]',
                                        'failed' => 'bg-[#EF4444]/20 text-[#EF4444]',
                                        'cancelled' => 'bg-white/10 text-white/50',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$message->status] ?? '' }}">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($message->status === 'pending')
                                        <form action="{{ route('dashboard.scheduler.cancel', $message) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-white/50 hover:text-[#F59E0B] transition-colors" title="Cancel">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @elseif($message->status === 'failed')
                                        <form action="{{ route('dashboard.scheduler.retry', $message) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-white/50 hover:text-[#10B981] transition-colors" title="Retry">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('dashboard.scheduler.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Delete this scheduled message?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-white/50 hover:text-[#EF4444] transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    @else
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-[#F59E0B]/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">No Scheduled Messages</h3>
            <p class="text-white/60 mb-4">Schedule your first message to send later</p>
            <button onclick="openScheduleModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                Schedule Message
            </button>
        </div>
    @endif
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-lg">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Schedule Message</h3>
        </div>
        
        <form action="{{ route('dashboard.scheduler.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Instance</label>
                <select name="instance_id" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none">
                    @foreach($instances as $instance)
                        <option value="{{ $instance->id }}">{{ $instance->name }} ({{ $instance->phone_number ?? 'Not connected' }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Recipient Phone Number</label>
                <input type="text" name="recipient" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none" placeholder="255712345678">
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Message</label>
                <textarea name="content" required rows="4" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none resize-none" placeholder="Your message here..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-2">Date</label>
                    <input type="date" name="scheduled_date" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none">
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-2">Time</label>
                    <input type="time" name="scheduled_time" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none">
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeScheduleModal()" class="flex-1 px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                    Schedule
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const modal = document.getElementById('scheduleModal');

function openScheduleModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeScheduleModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

modal.addEventListener('click', function(e) {
    if (e.target === modal) closeScheduleModal();
});
</script>
@endpush
@endsection
