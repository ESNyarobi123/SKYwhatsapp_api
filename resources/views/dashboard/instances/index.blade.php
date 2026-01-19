@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Instances</h1>
            <p class="text-white/70">Manage your WhatsApp instances</p>
        </div>
        <x-button variant="primary" size="md" onclick="openCreateInstanceModal()">
            Create Instance
        </x-button>
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

    @if($instances->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($instances as $instance)
                <x-card hover>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">{{ $instance->name }}</h3>
                            @if($instance->phone_number)
                                <p class="text-white/60 text-sm">{{ $instance->phone_number }}</p>
                            @endif
                        </div>
                        <x-badge variant="{{ $instance->status === 'connected' ? 'success' : ($instance->status === 'disconnected' ? 'error' : 'warning') }}">
                            {{ ucfirst($instance->status) }}
                        </x-badge>
                    </div>

                    <div class="space-y-3 mb-4">
                        @if($instance->last_connected_at)
                            <div class="flex items-center text-sm text-white/70">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Last connected: {{ $instance->last_connected_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                        @if($instance->status !== 'connected')
                            <button onclick="connectInstance({{ $instance->id }})" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium transition-colors">
                                Connect
                            </button>
                            @if($instance->qr_code)
                                <button onclick="showQrCode({{ $instance->id }})" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium transition-colors">
                                    Show QR
                                </button>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('api.instances.destroy', $instance) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this instance?')">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="text-white/70 mb-4">No instances yet</p>
                <x-button variant="primary" size="md" onclick="openCreateInstanceModal()">
                    Create Your First Instance
                </x-button>
            </div>
        </x-card>
    @endif
</div>

<!-- Create Instance Modal -->
<div id="createInstanceModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#252525] border border-white/5 rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-semibold text-white mb-4">Create Instance</h2>
        <form method="POST" action="{{ route('api.instances.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Instance Name</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-white/90 mb-2">Phone Number (Optional)</label>
                    <input type="text" id="phone_number" name="phone_number" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <x-button type="submit" variant="primary" size="md" class="flex-1">Create</x-button>
                <x-button type="button" variant="outline" size="md" onclick="closeCreateInstanceModal()">Cancel</x-button>
            </div>
        </form>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#252525] border border-white/5 rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-semibold text-white mb-4">QR Code</h2>
        <div id="qrCodeContent" class="text-center">
            <p class="text-white/70">Loading QR code...</p>
        </div>
        <div class="mt-6 text-center">
            <x-button type="button" variant="outline" size="md" onclick="closeQrModal()">Close</x-button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCreateInstanceModal() {
    document.getElementById('createInstanceModal').classList.remove('hidden');
}

function closeCreateInstanceModal() {
    document.getElementById('createInstanceModal').classList.add('hidden');
}

function connectInstance(instanceId) {
    fetch(`/api/instances/${instanceId}/connect`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error?.message || 'Failed to connect instance');
        }
    });
}

function showQrCode(instanceId) {
    document.getElementById('qrModal').classList.remove('hidden');
    fetch(`/api/instances/${instanceId}/qr`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qrCodeContent').innerHTML = `<img src="${data.data.qr_code}" alt="QR Code" class="mx-auto max-w-full">`;
        } else {
            document.getElementById('qrCodeContent').innerHTML = `<p class="text-[#EA3943]">${data.error?.message || 'QR code not available'}</p>`;
        }
    });
}

function closeQrModal() {
    document.getElementById('qrModal').classList.add('hidden');
}
</script>
@endpush
@endsection
