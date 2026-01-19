@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">API Keys</h1>
            <p class="text-white/70">Manage your API keys for authentication</p>
        </div>
        <x-button variant="primary" size="md" onclick="openCreateApiKeyModal()">
            Generate API Key
        </x-button>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
            @if(session('api_key_created'))
                <div class="mt-4 p-4 bg-[#1A1A1A] border border-white/10 rounded-lg">
                    <p class="text-white/70 text-xs mb-2 font-medium">Your API Key (save this now - it won't be shown again):</p>
                    <div class="flex items-center space-x-2">
                        <code class="text-sm text-white bg-[#252525] px-3 py-2 rounded flex-1 break-all">{{ session('api_key_created.key') }}</code>
                        <button onclick="copyToClipboard('{{ session('api_key_created.key') }}')" class="px-3 py-2 bg-[#FCD535] text-[#1A1A1A] rounded hover:bg-[#F0C420] transition-colors font-medium text-sm">
                            Copy
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <p class="text-[#EA3943] text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if($apiKeys->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Key Preview</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Last Used</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Expires</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($apiKeys as $apiKey)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $apiKey->name }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    <code class="text-sm text-white/70 bg-[#1A1A1A] px-2 py-1 rounded">{{ $apiKey->key_preview }}</code>
                                    <button onclick="copyToClipboard('{{ $apiKey->key_preview }}')" class="text-[#FCD535] hover:text-[#F0C420] transition-colors" title="Copy">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $apiKey->is_active ? 'success' : 'error' }}">
                                    {{ $apiKey->is_active ? 'Active' : 'Revoked' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : 'Never' }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($apiKey->is_active)
                                        <form method="POST" action="{{ route('api.api-keys.destroy', $apiKey) }}" class="inline" onsubmit="return confirm('Are you sure you want to revoke this API key?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm transition-colors">Revoke</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <p class="text-white/70 mb-4">No API keys yet</p>
                <x-button variant="primary" size="md" onclick="openCreateApiKeyModal()">
                    Generate Your First API Key
                </x-button>
            </div>
        </x-card>
    @endif
</div>

<!-- Create API Key Modal -->
<div id="createApiKeyModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#252525] border border-white/5 rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-semibold text-white mb-4">Generate API Key</h2>
        <form method="POST" action="{{ route('api.api-keys.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Key Name</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-white/90 mb-2">Expires At (Optional)</label>
                    <input type="date" id="expires_at" name="expires_at" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <x-button type="submit" variant="primary" size="md" class="flex-1">Generate</x-button>
                <x-button type="button" variant="outline" size="md" onclick="closeCreateApiKeyModal()">Cancel</x-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCreateApiKeyModal() {
    document.getElementById('createApiKeyModal').classList.remove('hidden');
}

function closeCreateApiKeyModal() {
    document.getElementById('createApiKeyModal').classList.add('hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification (simple alert for now)
        alert('Copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard');
    });
}
</script>
@endpush
@endsection
