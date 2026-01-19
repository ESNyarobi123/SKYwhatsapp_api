@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Webhooks</h1>
            <p class="text-white/70">Manage your webhook endpoints</p>
        </div>
        <x-button variant="primary" size="md" onclick="openCreateWebhookModal()">
            Add Webhook
        </x-button>
    </div>

    @if($webhooks->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach($webhooks as $webhook)
                <x-card hover>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-white mb-2">{{ $webhook->url }}</h3>
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($webhook->events as $event)
                                    <x-badge variant="gold">{{ $event }}</x-badge>
                                @endforeach
                            </div>
                            @if($webhook->instance)
                                <p class="text-white/60 text-sm">Instance: {{ $webhook->instance->name }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3">
                            <x-badge variant="{{ $webhook->is_active ? 'success' : 'error' }}">
                                {{ $webhook->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                        <form method="POST" action="{{ route('api.webhooks.update', $webhook) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="{{ $webhook->is_active ? 0 : 1 }}">
                            <button type="submit" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium transition-colors">
                                {{ $webhook->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('api.webhooks.destroy', $webhook) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this webhook?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm font-medium transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <p class="text-white/70 mb-4">No webhooks yet</p>
                <x-button variant="primary" size="md" onclick="openCreateWebhookModal()">
                    Add Your First Webhook
                </x-button>
            </div>
        </x-card>
    @endif
</div>

<!-- Create Webhook Modal -->
<div id="createWebhookModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#252525] border border-white/5 rounded-lg p-6 max-w-md w-full mx-4 max-h-[80vh] overflow-y-auto">
        <h2 class="text-xl font-semibold text-white mb-4">Add Webhook</h2>
        <form method="POST" action="{{ route('api.webhooks.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="url" class="block text-sm font-medium text-white/90 mb-2">Webhook URL</label>
                    <input type="url" id="url" name="url" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
                <div>
                    <label for="instance_id" class="block text-sm font-medium text-white/90 mb-2">Instance (Optional)</label>
                    <select id="instance_id" name="instance_id" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">All Instances</option>
                        @foreach($instances as $instance)
                            <option value="{{ $instance->id }}">{{ $instance->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-2">Events</label>
                    <div class="space-y-2 bg-[#1A1A1A] p-4 rounded-lg">
                        @foreach(['message.inbound', 'message.status', 'instance.connected', 'instance.disconnected', 'billing.expiring'] as $event)
                            <label class="flex items-center">
                                <input type="checkbox" name="events[]" value="{{ $event }}" class="text-[#FCD535] focus:ring-[#FCD535]">
                                <span class="ml-2 text-white/90 text-sm">{{ $event }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <x-button type="submit" variant="primary" size="md" class="flex-1">Create</x-button>
                <x-button type="button" variant="outline" size="md" onclick="closeCreateWebhookModal()">Cancel</x-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCreateWebhookModal() {
    document.getElementById('createWebhookModal').classList.remove('hidden');
}

function closeCreateWebhookModal() {
    document.getElementById('createWebhookModal').classList.add('hidden');
}
</script>
@endpush
@endsection
