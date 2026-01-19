@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Support Tickets</h1>
                <p class="text-white/70">View and manage your support tickets</p>
            </div>
            <a href="{{ route('dashboard.support.create') }}" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                Create New Ticket
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($tickets->count() > 0)
        <div class="space-y-3">
            @foreach($tickets as $ticket)
                <x-card class="hover:bg-white/5 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <a href="{{ route('dashboard.support.show', $ticket) }}" class="text-white font-semibold hover:text-[#FCD535] transition-colors">
                                    {{ $ticket->subject }}
                                </a>
                                <span class="text-white/50 text-sm font-mono">{{ $ticket->ticket_number }}</span>
                            </div>
                            <p class="text-white/70 text-sm mb-3 line-clamp-2">{{ Str::limit($ticket->description, 150) }}</p>
                            <div class="flex items-center space-x-4 text-xs text-white/50">
                                <span>Category: <span class="text-white/70">{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</span></span>
                                <span>Priority: 
                                    <span class="text-white/70">
                                        @if($ticket->priority === 'urgent')
                                            <span class="text-[#EA3943]">Urgent</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="text-[#FCD535]">High</span>
                                        @else
                                            {{ ucfirst($ticket->priority) }}
                                        @endif
                                    </span>
                                </span>
                                <span>Created: {{ $ticket->created_at->diffForHumans() }}</span>
                                @if($ticket->last_replied_at)
                                    <span>Last reply: {{ $ticket->last_replied_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex flex-col items-end space-y-2">
                            <x-badge variant="{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'error' : 'info')) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </x-badge>
                            <a href="{{ route('dashboard.support.show', $ticket) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium">
                                View →
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎫</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Support Tickets</h3>
                <p class="text-white/70 mb-4">You haven't created any support tickets yet.</p>
                <a href="{{ route('dashboard.support.create') }}" class="inline-block px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                    Create Your First Ticket
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
