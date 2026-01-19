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
        <h2 class="text-xl font-semibold text-white mb-2">Scan QR Code</h2>
        <p class="text-white/60 text-sm mb-4 text-center">Open WhatsApp on your phone → Settings → Linked Devices → Link a Device, then scan this QR code</p>
        <div id="qrCodeContent" class="text-center bg-white p-4 rounded-lg min-h-[300px] flex items-center justify-center">
            <div class="flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div>
                <p class="mt-3 text-gray-700">Waiting for QR code...</p>
            </div>
        </div>
        <div id="qrCodeStatus" class="mt-4 text-center text-sm text-white/60"></div>
        <div class="mt-6 text-center">
            <x-button type="button" variant="outline" size="md" onclick="closeQrModal()">Close</x-button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let qrPollInterval = null;
let currentInstanceId = null;

function openCreateInstanceModal() {
    document.getElementById('createInstanceModal').classList.remove('hidden');
}

function closeCreateInstanceModal() {
    document.getElementById('createInstanceModal').classList.add('hidden');
}

function connectInstance(instanceId) {
    // Show QR modal immediately
    openQrModal(instanceId);
    
    // Start connection process
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
            // Start polling for QR code
            startQrPolling(instanceId);
        } else {
            closeQrModal();
            alert(data.error?.message || 'Failed to connect instance');
        }
    })
    .catch(error => {
        closeQrModal();
        alert('Failed to connect instance. Please try again.');
        console.error('Connection error:', error);
    });
}

function openQrModal(instanceId) {
    currentInstanceId = instanceId;
    document.getElementById('qrModal').classList.remove('hidden');
    document.getElementById('qrCodeContent').innerHTML = `
        <div class="flex flex-col items-center justify-center py-8 min-h-[300px]">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div>
            <p class="mt-3 text-gray-700">Waiting for QR code...</p>
        </div>
    `;
    document.getElementById('qrCodeStatus').textContent = '';
}

function startQrPolling(instanceId) {
    // Clear any existing interval
    if (qrPollInterval) {
        clearInterval(qrPollInterval);
    }
    
    let pollCount = 0;
    const maxPolls = 60; // Poll for up to 2 minutes (60 * 2 seconds)
    
    // Poll immediately first time
    checkQrCode(instanceId);
    
    // Then poll every 2 seconds
    qrPollInterval = setInterval(() => {
        pollCount++;
        
        if (pollCount >= maxPolls) {
            stopQrPolling();
            document.getElementById('qrCodeContent').innerHTML = `
                <p class="text-[#EA3943] py-8">QR code generation timed out. Please try connecting again.</p>
            `;
            return;
        }
        
        checkQrCode(instanceId);
    }, 2000);
}

function checkQrCode(instanceId) {
    // First check instance status
    fetch(`/api/instances/${instanceId}`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.instance) {
            const instance = data.data.instance;
            
            // If connected, stop polling and close modal
            if (instance.status === 'connected') {
                stopQrPolling();
                document.getElementById('qrCodeContent').innerHTML = `
                    <div class="py-8">
                        <div class="text-[#00D9A5] text-lg font-semibold mb-2">✓ Connected Successfully!</div>
                        <p class="text-white/70 text-sm">Your WhatsApp instance is now linked.</p>
                    </div>
                `;
                setTimeout(() => {
                    closeQrModal();
                    window.location.reload();
                }, 2000);
                return;
            }
        }
    })
    .catch(error => {
        console.error('Status check error:', error);
    });
    
    // Then check for QR code
    fetch(`/api/instances/${instanceId}/qr`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.qr_code) {
            // Stop polling once we have QR code
            stopQrPolling();
            
            // Display QR code
            const qrCode = data.data.qr_code;
            let qrImageHtml = '';
            
            // Check if QR code is base64 or data URL
            if (qrCode.startsWith('data:image')) {
                // It's already a data URL
                qrImageHtml = `<img src="${qrCode}" alt="QR Code" class="mx-auto max-w-full h-auto" style="max-width: 300px; height: auto;">`;
            } else if (qrCode.startsWith('http://') || qrCode.startsWith('https://')) {
                // It's a URL
                qrImageHtml = `<img src="${qrCode}" alt="QR Code" class="mx-auto max-w-full h-auto" style="max-width: 300px; height: auto;">`;
            } else if (qrCode.includes('▄') || qrCode.includes('█')) {
                // It's ASCII art QR code - display as preformatted text with black background
                qrImageHtml = `<pre class="text-black text-[10px] font-mono bg-white p-4 rounded overflow-auto text-left" style="max-width: 300px; margin: 0 auto; line-height: 1.2;">${qrCode}</pre>`;
            } else {
                // Assume it's base64, convert to data URL
                qrImageHtml = `<img src="data:image/png;base64,${qrCode}" alt="QR Code" class="mx-auto max-w-full h-auto" style="max-width: 300px; height: auto;">`;
            }
            
            document.getElementById('qrCodeContent').innerHTML = qrImageHtml;
            
            // Show expiration info if available
            if (data.data.expires_at) {
                const expiresAt = new Date(data.data.expires_at);
                const minutesLeft = Math.ceil((expiresAt - new Date()) / 60000);
                if (minutesLeft > 0) {
                    document.getElementById('qrCodeStatus').textContent = `QR code expires in ${minutesLeft} minute${minutesLeft !== 1 ? 's' : ''}`;
                }
            }
            
            // Restart polling to check for connection status
            startStatusPolling(instanceId);
        } else if (data.error?.code === 'QR_NOT_AVAILABLE') {
            // QR not available yet, keep waiting
            document.getElementById('qrCodeContent').innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 min-h-[300px]">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div>
                    <p class="mt-3 text-gray-700">Generating QR code...</p>
                </div>
            `;
        } else {
            // Error getting QR code
            document.getElementById('qrCodeContent').innerHTML = `
                <p class="text-[#EA3943] py-8">${data.error?.message || 'Failed to load QR code'}</p>
            `;
        }
    })
    .catch(error => {
        console.error('QR check error:', error);
    });
}

function startStatusPolling(instanceId) {
    // Poll every 3 seconds to check connection status
    if (qrPollInterval) {
        clearInterval(qrPollInterval);
    }
    
    qrPollInterval = setInterval(() => {
        fetch(`/api/instances/${instanceId}`, {
            headers: {
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.instance) {
                const instance = data.data.instance;
                
                if (instance.status === 'connected') {
                    stopQrPolling();
                    document.getElementById('qrCodeContent').innerHTML = `
                        <div class="py-8">
                            <div class="text-[#00D9A5] text-lg font-semibold mb-2">✓ Connected Successfully!</div>
                            <p class="text-white/70 text-sm">Your WhatsApp instance is now linked.</p>
                        </div>
                    `;
                    document.getElementById('qrCodeStatus').textContent = '';
                    setTimeout(() => {
                        closeQrModal();
                        window.location.reload();
                    }, 2000);
                }
            }
        })
        .catch(error => {
            console.error('Status polling error:', error);
        });
    }, 3000);
}

function stopQrPolling() {
    if (qrPollInterval) {
        clearInterval(qrPollInterval);
        qrPollInterval = null;
    }
}

function showQrCode(instanceId) {
    openQrModal(instanceId);
    checkQrCode(instanceId);
}

function closeQrModal() {
    stopQrPolling();
    document.getElementById('qrModal').classList.add('hidden');
    currentInstanceId = null;
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    stopQrPolling();
});
</script>
@endpush
@endsection
