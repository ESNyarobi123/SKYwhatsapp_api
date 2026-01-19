@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $ticket->subject }}</h1>
                <p class="text-white/70">Ticket #{{ $ticket->ticket_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(!$ticket->isClosed())
                    <form method="POST" action="{{ route('dashboard.support.close', $ticket) }}" class="inline" onsubmit="return confirm('Are you sure you want to close this ticket?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                            Close Ticket
                        </button>
                    </form>
                @endif
                <a href="{{ route('dashboard.support.index') }}" class="px-4 py-2 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                    Back to Tickets
                </a>
            </div>
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

    {{-- Ticket Info --}}
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-white/50 text-sm mb-1">Status</p>
                <x-badge variant="{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'error' : 'info')) }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </x-badge>
            </div>
            <div>
                <p class="text-white/50 text-sm mb-1">Priority</p>
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
                <p class="text-white/50 text-sm mb-1">Category</p>
                <p class="text-white">{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</p>
            </div>
        </div>
        @if($ticket->assignedAdmin)
            <div class="mt-4 pt-4 border-t border-white/5">
                <p class="text-white/50 text-sm mb-1">Assigned To</p>
                <p class="text-white">{{ $ticket->assignedAdmin->name }}</p>
            </div>
        @endif
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
            <form method="POST" action="{{ route('dashboard.support.message', $ticket) }}" class="border-t border-white/5 pt-4">
                @csrf
                <div class="space-y-3">
                    <textarea 
                        name="message" 
                        required 
                        maxlength="5000"
                        rows="4"
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] resize-none"
                        placeholder="Type your message here..."
                    ></textarea>
                    @error('message')
                        <p class="text-xs text-[#EA3943]">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center justify-between">
                        <p class="text-white/50 text-xs">Maximum 5000 characters</p>
                        <button type="submit" class="px-6 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                            Send Message
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

    {{-- WhatsApp Link --}}
    <x-card>
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-[#25D366]/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-white font-semibold mb-1">Need Immediate Help?</h3>
                <p class="text-white/70 text-sm mb-2">Contact us directly on WhatsApp for faster support</p>
                <a href="https://wa.me/255123456789" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-[#25D366] text-white font-semibold rounded-lg hover:bg-[#20BA5A] transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
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
