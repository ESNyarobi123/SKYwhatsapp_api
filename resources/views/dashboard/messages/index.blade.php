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
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Direction</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Message</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Time</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($messages as $message)
                        <tr class="hover:bg-white/5 transition-colors cursor-pointer" onclick="openChat('{{ $message->contact_phone_number }}', {{ $message->instance_id }})">
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-10 h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center">
                                        <span class="text-[#FCD535] font-semibold text-sm">{{ substr($message->contact_phone_number, -1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $message->contact_phone_number }}</div>
                                        <div class="text-white/60 text-xs">{{ $message->instance->name ?? 'Unknown' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $message->direction === 'inbound' ? 'success' : 'gold' }}">
                                    {{ ucfirst($message->direction) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm max-w-xs">
                                @if($message->hasMedia())
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $message->getMediaType() ?? 'Media' }}</span>
                                    </div>
                                @else
                                    {{ Str::limit($message->body, 50) }}
                                @endif
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
                                <button onclick="event.stopPropagation(); openChat('{{ $message->contact_phone_number }}', {{ $message->instance_id }})" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">
                                    Chat
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

<!-- WhatsApp-like Chat Modal -->
<div id="chatModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-[#0B141A] border border-white/10 rounded-lg w-full max-w-4xl h-[85vh] mx-4 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-[#202C33] px-6 py-4 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center space-x-3">
                <button onclick="closeChatModal()" class="text-white/70 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="w-10 h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center">
                    <span class="text-[#FCD535] font-semibold text-sm" id="chatContactInitial">?</span>
                </div>
                <div>
                    <h3 class="text-white font-semibold" id="chatContactName">Loading...</h3>
                    <p class="text-white/60 text-xs" id="chatContactPhone">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Messages Area (WhatsApp-like) -->
        <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-2 bg-[#0B141A]">
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div>
                <p class="ml-3 text-white/70">Loading messages...</p>
            </div>
        </div>

        <!-- Message Input Area -->
        <div class="bg-[#202C33] px-4 py-3 border-t border-white/10">
            <form id="chatReplyForm" class="flex items-end space-x-2">
                <input type="hidden" id="chatInstanceId" name="instance_id">
                <input type="hidden" id="chatToPhone" name="to">
                
                <!-- Image Upload Button -->
                <label for="chatImageInput" class="cursor-pointer p-2 text-white/70 hover:text-[#FCD535] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <input type="file" id="chatImageInput" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                </label>
                
                <!-- Message Input -->
                <div class="flex-1 relative">
                    <textarea 
                        id="chatMessageInput" 
                        name="body" 
                        rows="1"
                        placeholder="Type a message..."
                        class="w-full px-4 py-3 bg-[#2A3942] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] resize-none"
                        onkeydown="handleChatKeyDown(event)"
                    ></textarea>
                    <div id="chatImagePreview" class="hidden mt-2 relative">
                        <img id="chatImagePreviewImg" src="" alt="Preview" class="max-w-xs rounded-lg">
                        <button type="button" onclick="removeImagePreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                            ×
                        </button>
                    </div>
                </div>
                
                <!-- Send Button -->
                <button 
                    type="submit" 
                    class="p-3 bg-[#FCD535] hover:bg-[#F0C420] rounded-full transition-colors"
                    id="chatSendButton"
                >
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
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
let currentChatPhone = null;
let currentChatInstanceId = null;
let chatImageFile = null;
let chatPollInterval = null;

// Extract phone number from JID
function extractPhoneNumber(jid) {
    if (!jid) return 'Unknown';
    // Remove @lid, @g.us, @s.whatsapp.net, etc.
    let phone = jid.replace(/@.*$/, '');
    // Remove any non-digit characters except +
    phone = phone.replace(/[^\d+]/g, '');
    return phone || jid;
}

// Format phone number for display
function formatPhoneNumber(phone) {
    if (!phone) return 'Unknown';
    // If it's a group ID (long number), return as is
    if (phone.length > 15) return phone;
    // Format: +255 712 345 678
    if (phone.startsWith('+')) {
        return phone.replace(/(\+?\d{3})(\d{3})(\d{3})(\d+)/, '$1 $2 $3 $4');
    }
    return phone;
}

function openChat(phoneNumber, instanceId) {
    currentChatPhone = phoneNumber;
    currentChatInstanceId = instanceId;
    
    // Update chat header
    const formattedPhone = formatPhoneNumber(phoneNumber);
    document.getElementById('chatContactName').textContent = formattedPhone;
    document.getElementById('chatContactPhone').textContent = formattedPhone;
    document.getElementById('chatContactInitial').textContent = phoneNumber.slice(-1) || '?';
    
    // Set form values
    document.getElementById('chatInstanceId').value = instanceId;
    document.getElementById('chatToPhone').value = phoneNumber;
    
    // Show modal
    document.getElementById('chatModal').classList.remove('hidden');
    
    // Load messages
    loadChatMessages(phoneNumber, instanceId);
    
    // Start polling for new messages
    startChatPolling(phoneNumber, instanceId);
}

function closeChatModal() {
    document.getElementById('chatModal').classList.add('hidden');
    stopChatPolling();
    currentChatPhone = null;
    currentChatInstanceId = null;
    chatImageFile = null;
    removeImagePreview();
}

function loadChatMessages(phoneNumber, instanceId) {
    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div><p class="ml-3 text-white/70">Loading messages...</p></div>';
    
    // Fetch messages for this contact
    fetch(`/api/messages?instance_id=${instanceId}&per_page=50`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messages = data.data.messages || [];
            
            // Filter messages for this contact
            const contactMessages = messages.filter(msg => {
                const msgPhone = msg.direction === 'inbound' 
                    ? extractPhoneNumber(msg.from)
                    : extractPhoneNumber(msg.to);
                return msgPhone === phoneNumber;
            });
            
            // Sort by created_at (oldest first)
            contactMessages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            
            // Render messages
            renderChatMessages(contactMessages);
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        messagesContainer.innerHTML = '<p class="text-red-500 text-center py-8">Error loading messages</p>';
    });
}

function renderChatMessages(messages) {
    const messagesContainer = document.getElementById('chatMessages');
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<p class="text-white/60 text-center py-8">No messages yet. Start the conversation!</p>';
        return;
    }
    
    messagesContainer.innerHTML = messages.map(msg => {
        const isInbound = msg.direction === 'inbound';
        const hasMedia = msg.metadata?.hasMedia || msg.metadata?.has_media;
        const mediaUrl = msg.metadata?.mediaUrl || msg.metadata?.media_url;
        const mediaType = msg.metadata?.mediaType || msg.metadata?.media_type || msg.metadata?.mimetype;
        
        const messageClass = isInbound 
            ? 'bg-[#202C33] text-white' 
            : 'bg-[#005C4B] text-white ml-auto';
        
        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        let messageContent = '';
        
        if (hasMedia && mediaUrl) {
            if (mediaType?.startsWith('image/')) {
                messageContent = `<img src="${mediaUrl}" alt="Media" class="max-w-xs rounded-lg mb-2" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' viewBox=\\'0 0 24 24\\'%3E%3Cpath fill=\\'%23fff\\' d=\\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z\\'/%3E%3C/svg%3E'">`;
            } else {
                messageContent = `<div class="flex items-center space-x-2 p-2 bg-white/10 rounded"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg><span>${mediaType || 'Media'}</span></div>`;
            }
        }
        
        if (msg.body && msg.body !== '[Media or unsupported message type]') {
            messageContent += `<p class="whitespace-pre-wrap break-words">${escapeHtml(msg.body)}</p>`;
        }
        
        return `
            <div class="flex ${isInbound ? 'justify-start' : 'justify-end'} mb-2">
                <div class="max-w-[70%] ${messageClass} rounded-lg px-3 py-2 shadow-md">
                    ${messageContent}
                    <div class="text-xs text-white/70 mt-1 flex items-center justify-end space-x-1">
                        <span>${time}</span>
                        ${!isInbound ? `<svg class="w-3 h-3 ${msg.status === 'read' ? 'text-blue-400' : msg.status === 'delivered' ? 'text-white/70' : ''}" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }
    
    chatImageFile = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('chatImagePreviewImg').src = e.target.result;
        document.getElementById('chatImagePreview').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeImagePreview() {
    document.getElementById('chatImagePreview').classList.add('hidden');
    document.getElementById('chatImageInput').value = '';
    chatImageFile = null;
}

function handleChatKeyDown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        document.getElementById('chatReplyForm').dispatchEvent(new Event('submit'));
    }
}

document.getElementById('chatReplyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const messageInput = document.getElementById('chatMessageInput');
    const messageBody = messageInput.value.trim();
    const instanceId = document.getElementById('chatInstanceId').value;
    const toPhone = document.getElementById('chatToPhone').value;
    
    if (!messageBody && !chatImageFile) {
        return;
    }
    
    const sendButton = document.getElementById('chatSendButton');
    sendButton.disabled = true;
    sendButton.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-black"></div>';
    
    try {
        const formData = new FormData();
        formData.append('instance_id', instanceId);
        formData.append('to', toPhone);
        formData.append('body', messageBody || '');
        if (chatImageFile) {
            formData.append('image', chatImageFile);
        }
        
        const response = await fetch('/api/v1/messages/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageInput.value = '';
            removeImagePreview();
            
            // Reload messages
            setTimeout(() => {
                loadChatMessages(currentChatPhone, currentChatInstanceId);
            }, 500);
        } else {
            alert(data.error?.message || 'Failed to send message');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        sendButton.disabled = false;
        sendButton.innerHTML = '<svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
    }
});

function startChatPolling(phoneNumber, instanceId) {
    if (chatPollInterval) {
        clearInterval(chatPollInterval);
    }
    
    chatPollInterval = setInterval(() => {
        if (currentChatPhone === phoneNumber && currentChatInstanceId === instanceId) {
            loadChatMessages(phoneNumber, instanceId);
        }
    }, 5000); // Poll every 5 seconds
}

function stopChatPolling() {
    if (chatPollInterval) {
        clearInterval(chatPollInterval);
        chatPollInterval = null;
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    stopChatPolling();
});

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
