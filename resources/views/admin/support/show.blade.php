@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $ticket->subject }}</h1>
                <p class="text-white/70">Ticket #{{ $ticket->ticket_number }} • Created by {{ $ticket->user->name }}</p>
            </div>
            <a href="{{ route('admin.support.index') }}" class="px-4 py-2 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                Back to Tickets
            </a>
        </div>
    </div>

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

    {{-- Ticket Actions --}}
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-white/70 mb-2">Status</label>
                <form method="POST" action="{{ route('admin.support.status', $ticket) }}" class="flex items-center space-x-2">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="flex-1 px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>
            </div>
            <div>
                <label class="block text-sm font-medium text-white/70 mb-2">Assign To</label>
                <form method="POST" action="{{ route('admin.support.assign', $ticket) }}" class="flex items-center space-x-2">
                    @csrf
                    <select name="admin_id" class="flex-1 px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white">
                        <option value="">Unassigned</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                        Assign
                    </button>
                </form>
            </div>
            <div class="flex items-end">
                @if(!$ticket->isClosed())
                    <form method="POST" action="{{ route('admin.support.close', $ticket) }}" onsubmit="return confirm('Are you sure you want to close this ticket?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 border border-[#EA3943]/30 text-[#EA3943] rounded-lg hover:bg-[#EA3943]/10 transition-colors">
                            Close Ticket
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-white/5 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-white/50 mb-1">Priority</p>
                <p class="text-white">
                    @if($ticket->priority === 'urgent')
                        <span class="text-[#EA3943] font-semibold">Urgent</span>
                    @elseif($ticket->priority === 'high')
                        <span class="text-[#FCD535] font-semibold">High</span>
                    @else
                        {{ ucfirst($ticket->priority) }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-white/50 mb-1">Category</p>
                <p class="text-white">{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</p>
            </div>
            <div>
                <p class="text-white/50 mb-1">Created</p>
                <p class="text-white">{{ $ticket->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </x-card>

    {{-- Messages --}}
    <x-card>
        <h2 class="text-xl font-semibold text-white mb-4">Conversation</h2>
        
        <div class="space-y-4 max-h-96 overflow-y-auto mb-6" id="messages-container">
            @foreach($messages as $message)
                <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-2xl {{ $message->is_admin ? 'bg-[#FCD535]/10 border border-[#FCD535]/20' : 'bg-white/5 border border-white/10' }} rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="text-white font-semibold text-sm">
                                {{ $message->is_admin ? 'Admin' : $message->user->name }}
                            </span>
                            <span class="text-white/50 text-xs">{{ $message->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="text-white/90 whitespace-pre-wrap">{{ $message->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if(!$ticket->isClosed())
            <form method="POST" action="{{ route('admin.support.message', $ticket) }}" class="border-t border-white/5 pt-4">
                @csrf
                <div class="space-y-3">
                    <textarea 
                        name="message" 
                        required 
                        maxlength="5000"
                        rows="4"
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] resize-none"
                        placeholder="Type your response here..."
                    ></textarea>
                    @error('message')
                        <p class="text-xs text-[#EA3943]">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center justify-between">
                        <p class="text-white/50 text-xs">Maximum 5000 characters</p>
                        <button type="submit" class="px-6 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                            Send Response
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="border-t border-white/5 pt-4">
                <p class="text-white/50 text-sm text-center">This ticket is closed. You cannot send more messages.</p>
            </div>
        @endif
    </x-card>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endpush
@endsection
