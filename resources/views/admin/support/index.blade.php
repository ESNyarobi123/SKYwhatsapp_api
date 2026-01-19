@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Support Tickets</h1>
        <p class="text-white/70">Manage all support tickets</p>
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

    {{-- Filters --}}
    <x-card>
        <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-white/70 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white">
                    <option value="">All</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-white/70 mb-2">Priority</label>
                <select name="priority" id="priority" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white">
                    <option value="">All</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-white/70 mb-2">Assigned To</label>
                <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white">
                    <option value="">All</option>
                    <option value="unassigned" {{ request('unassigned') ? 'selected' : '' }}>Unassigned</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </x-card>

    @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Ticket #</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Subject</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Priority</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Assigned To</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Created</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white font-mono text-sm">{{ $ticket->ticket_number }}</td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="text-white hover:text-[#FCD535] transition-colors">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $ticket->user->name }}</td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'error' : 'info')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4">
                                @if($ticket->priority === 'urgent')
                                    <span class="text-[#EA3943] font-semibold">Urgent</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="text-[#FCD535] font-semibold">High</span>
                                @else
                                    <span class="text-white/70">{{ ucfirst($ticket->priority) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $ticket->assignedAdmin ? $ticket->assignedAdmin->name : 'Unassigned' }}
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $ticket->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎫</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Support Tickets</h3>
                <p class="text-white/70">No tickets match your filters.</p>
            </div>
        </x-card>
    @endif
</div>
@endsection
