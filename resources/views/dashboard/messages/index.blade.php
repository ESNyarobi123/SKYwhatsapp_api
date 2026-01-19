@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Messages</h1>
        <p class="text-white/70">View your message history</p>
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" action="{{ route('dashboard.messages') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="instance_id" class="block text-sm font-medium text-white/90 mb-2">Instance</label>
                <select id="instance_id" name="instance_id" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <option value="">All Instances</option>
                    @foreach($instances as $instance)
                        <option value="{{ $instance->id }}" {{ request('instance_id') == $instance->id ? 'selected' : '' }}>
                            {{ $instance->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="direction" class="block text-sm font-medium text-white/90 mb-2">Direction</label>
                <select id="direction" name="direction" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <option value="">All</option>
                    <option value="inbound" {{ request('direction') == 'inbound' ? 'selected' : '' }}>Inbound</option>
                    <option value="outbound" {{ request('direction') == 'outbound' ? 'selected' : '' }}>Outbound</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-button type="submit" variant="primary" size="md" class="w-full">Filter</x-button>
            </div>
        </form>
    </x-card>

    @if($messages->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">From/To</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Direction</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Body</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Time</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($messages as $message)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $message->direction === 'inbound' ? $message->from : $message->to }}</td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $message->direction === 'inbound' ? 'success' : 'gold' }}">
                                    {{ ucfirst($message->direction) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm max-w-xs truncate">
                                {{ Str::limit($message->body, 50) }}
                            </td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $message->status === 'delivered' ? 'success' : ($message->status === 'failed' ? 'error' : 'warning') }}">
                                    {{ ucfirst($message->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $message->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="showMessageDetails({{ $message->id }})" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">
                                    View
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <p class="text-white/70">No messages yet</p>
            </div>
        </x-card>
    @endif
</div>

<!-- Message Details Modal -->
<div id="messageDetailsModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#252525] border border-white/5 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <h2 class="text-xl font-semibold text-white mb-4">Message Details</h2>
        <div id="messageDetailsContent" class="text-white/70">
            <p>Loading...</p>
        </div>
        <div class="mt-6 text-center">
            <x-button type="button" variant="outline" size="md" onclick="closeMessageDetailsModal()">Close</x-button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showMessageDetails(messageId) {
    document.getElementById('messageDetailsModal').classList.remove('hidden');
    fetch(`/api/messages/${messageId}`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const msg = data.data.message;
            document.getElementById('messageDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div><strong class="text-white">From:</strong> ${msg.from || 'N/A'}</div>
                    <div><strong class="text-white">To:</strong> ${msg.to || 'N/A'}</div>
                    <div><strong class="text-white">Direction:</strong> ${msg.direction}</div>
                    <div><strong class="text-white">Status:</strong> ${msg.status}</div>
                    <div><strong class="text-white">Body:</strong><br><div class="mt-2 bg-[#1A1A1A] p-4 rounded-lg">${msg.body || 'N/A'}</div></div>
                    <div><strong class="text-white">Created:</strong> ${new Date(msg.created_at).toLocaleString()}</div>
                </div>
            `;
        }
    });
}

function closeMessageDetailsModal() {
    document.getElementById('messageDetailsModal').classList.add('hidden');
}
</script>
@endpush
@endsection
