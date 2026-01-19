@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-white mb-2">Messages</h1>
        <p class="text-white/70">Chat with your contacts</p>
    </div>

    <!-- WhatsApp-like Two-Pane Layout -->
    <div class="flex-1 flex bg-[#0B141A] rounded-lg border border-white/10 overflow-hidden min-h-0" style="background-color: #0B141A !important;">
        <!-- Left Pane: Contacts List -->
        <div class="w-1/3 min-w-[300px] max-w-[400px] border-r border-white/10 bg-[#111B21] flex flex-col" style="background-color: #111B21 !important;">
            <!-- Search and Filter Header -->
            <div class="p-4 border-b border-white/10 bg-[#202C33]" style="background-color: #202C33 !important;">
                <div class="flex items-center space-x-2 mb-3">
                    <select id="filterInstance" class="flex-1 px-3 py-2 bg-[#2A3942] border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">All Instances</option>
                        @foreach($instances as $instance)
                            <option value="{{ $instance->id }}">{{ $instance->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative">
                    <input 
                        type="text" 
                        id="contactSearch" 
                        placeholder="Search or start new chat" 
                        class="w-full px-4 py-2 pl-10 bg-[#2A3942] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    >
                    <svg class="w-5 h-5 text-white/40 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Contacts List -->
            <div id="contactsList" class="flex-1 overflow-y-auto bg-[#0B141A]" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                <!-- Loading state will be shown here initially -->
            </div>
        </div>

        <!-- Right Pane: Chat Area -->
        <div class="flex-1 flex flex-col bg-[#0B141A]" style="background-color: #0B141A !important; background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.02\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            <!-- Chat Header (shown when contact selected) -->
            <div id="chatHeader" class="hidden bg-[#202C33] px-6 py-4 flex items-center justify-between border-b border-white/10" style="background-color: #202C33 !important;">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center">
                        <span class="text-[#FCD535] font-semibold text-sm" id="activeContactInitial">?</span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold" id="activeContactName">Select a contact</h3>
                        <p class="text-white/60 text-xs" id="activeContactPhone"></p>
                    </div>
                </div>
                <button onclick="refreshChat()" class="p-2 text-white/70 hover:text-white transition-colors" title="Refresh">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>

            <!-- Empty State (shown when no contact selected) -->
            <div id="emptyChatState" class="flex-1 flex items-center justify-center">
                <div class="text-center max-w-md mx-auto px-4">
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-[#FCD535]/10 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold mb-2">No conversation selected</h3>
                    <p class="text-white/60 text-sm mb-4">Select a contact from the list on the left to start chatting</p>
                    <p class="text-white/40 text-xs">Your messages will appear here once you select a conversation</p>
                </div>
            </div>

            <!-- Messages Area (shown when contact selected) -->
            <div id="chatMessagesArea" class="hidden flex-1 overflow-y-auto p-4 space-y-2" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Message Input Area (shown when contact selected) -->
            <div id="chatInputArea" class="hidden bg-[#202C33] px-4 py-3 border-t border-white/10" style="background-color: #202C33 !important;">
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
</div>



@push('scripts')
<script>
let currentChatPhone = null;
let currentChatInstanceId = null;
let chatImageFile = null;
let allContacts = [];
let filteredContacts = [];
let selectedContact = null;

// Check if JID is a group
function isGroupJID(jid) {
    return jid && (jid.includes('@g.us') || jid.includes('@lid'));
}

// Extract phone number from JID (only for private chats)
function extractPhoneNumber(jid) {
    if (!jid) return null;
    
    // Skip groups
    if (isGroupJID(jid)) {
        return null;
    }
    
    // Remove @s.whatsapp.net, @lid, etc.
    let phone = jid.replace(/@.*$/, '');
    
    // Remove any non-digit characters except +
    phone = phone.replace(/[^\d+]/g, '');
    
    // If phone is too long (group ID), return null
    if (phone && phone.length > 15) {
        return null;
    }
    
    // If phone starts with +, keep it
    if (phone.startsWith('+')) {
        return phone;
    }
    
    // Return cleaned phone number
    return phone || null;
}

// Format phone number for display
function formatPhoneNumber(phone) {
    if (!phone) return 'Unknown';
    
    // If phone is a long number (likely group ID or invalid), show shortened
    if (phone.length > 20) {
        return phone.substring(0, 15) + '...';
    }
    
    // Remove any non-digit characters except +
    let digits = phone.replace(/[^\d+]/g, '');
    
    // If it's too long after cleaning, it's likely invalid
    if (digits.length > 15) {
        // Try to extract last 9-12 digits as phone number
        const lastDigits = digits.slice(-12);
        if (lastDigits.length >= 9) {
            digits = lastDigits;
        } else {
            return phone.substring(0, 15) + '...';
        }
    }
    
    const hasPlus = digits.startsWith('+');
    const numOnly = hasPlus ? digits.substring(1) : digits;
    
    // Skip if still too long
    if (numOnly.length > 15) {
        return phone.substring(0, 15) + '...';
    }
    
    // Handle Tanzanian numbers (255...)
    if (numOnly.length >= 12 && numOnly.startsWith('255')) {
        // Format: +255 712 345 678
        const country = numOnly.substring(0, 3);
        const operator = numOnly.substring(3, 6);
        const rest = numOnly.substring(6);
        return (hasPlus ? '+' : '') + country + ' ' + operator + ' ' + rest.match(/.{1,3}/g)?.join(' ') || rest;
    }
    
    // Handle local Tanzanian numbers (07... or 06...)
    if (numOnly.length >= 9 && (numOnly.startsWith('07') || numOnly.startsWith('06'))) {
        // Format: 0712 345 678
        const operator = numOnly.substring(0, 3);
        const rest = numOnly.substring(3);
        return operator + ' ' + rest.match(/.{1,3}/g)?.join(' ') || rest;
    }
    
    // Handle other international numbers
    if (numOnly.length >= 10 && numOnly.length <= 15) {
        return (hasPlus ? '+' : '') + numOnly.match(/.{1,3}/g)?.join(' ') || numOnly;
    }
    
    // If all else fails, return cleaned version
    return (hasPlus ? '+' : '') + numOnly || phone.substring(0, 15);
}

// Load contacts list (left pane)
function loadContacts() {
    const contactsList = document.getElementById('contactsList');
    
    // Show loading state
    contactsList.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#FCD535] mx-auto mb-3"></div>
                <p class="text-white/70 text-sm">Loading contacts...</p>
            </div>
        </div>
    `;
    
    const instanceId = document.getElementById('filterInstance')?.value || '';
    const searchTerm = document.getElementById('contactSearch')?.value.toLowerCase() || '';
    
    fetch(`/api/messages?per_page=100${instanceId ? '&instance_id=' + instanceId : ''}`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load contacts');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const messages = data.data.messages || [];
            
            // Group messages by contact (only private, not groups)
            const contactsMap = new Map();
            
            messages.forEach(msg => {
                const contactJID = msg.direction === 'inbound' ? msg.from : msg.to;
                
                // Skip groups
                if (isGroupJID(contactJID)) {
                    return;
                }
                
                // Extract phone number from JID
                const phoneNumber = extractPhoneNumber(contactJID);
                
                // Skip if no valid phone number or if it's too long (likely group ID)
                if (!phoneNumber || phoneNumber.length > 15) {
                    return; // Skip this message
                }
                
                // Normalize for grouping (remove + for consistency)
                const normalizedPhone = phoneNumber.replace(/^\+/, '');
                const contactKey = `${normalizedPhone}_${msg.instance_id}`;
                
                if (!contactsMap.has(contactKey)) {
                    contactsMap.set(contactKey, {
                        phoneNumber: phoneNumber, // Store extracted phone number
                        normalizedPhone: normalizedPhone, // For comparison
                        jid: contactJID, // Store JID for sending messages
                        instanceId: msg.instance_id,
                        instanceName: msg.instance?.name || 'Unknown',
                        lastMessage: (msg.body && msg.body !== '[Media or unsupported message type]') ? msg.body : '[Media]',
                        lastMessageTime: msg.created_at,
                        unreadCount: 0,
                        lastMessageDirection: msg.direction
                    });
                } else {
                    const contact = contactsMap.get(contactKey);
                    if (new Date(msg.created_at) > new Date(contact.lastMessageTime)) {
                        contact.lastMessage = (msg.body && msg.body !== '[Media or unsupported message type]') ? msg.body : '[Media]';
                        contact.lastMessageTime = msg.created_at;
                        contact.lastMessageDirection = msg.direction;
                    }
                }
            });
            
            allContacts = Array.from(contactsMap.values());
            
            // Filter by search term
            filteredContacts = allContacts.filter(contact => {
                const phone = formatPhoneNumber(contact.phoneNumber);
                return phone.toLowerCase().includes(searchTerm) || 
                       contact.instanceName.toLowerCase().includes(searchTerm);
            });
            
            // Sort by last message time (newest first)
            filteredContacts.sort((a, b) => new Date(b.lastMessageTime) - new Date(a.lastMessageTime));
            
            renderContactsList();
        }
    })
    .catch(error => {
        console.error('Error loading contacts:', error);
        contactsList.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-20 h-20 rounded-full bg-red-500/10 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-red-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Error loading contacts</h3>
                <p class="text-white/60 text-sm text-center mb-4">Failed to load contacts. Please try again.</p>
                <button onclick="loadContacts()" class="px-4 py-2 bg-[#FCD535] hover:bg-[#F0C420] text-black rounded-lg text-sm font-medium transition-colors">
                    Retry
                </button>
            </div>
        `;
    });
}

// Render contacts list
function renderContactsList() {
    const contactsList = document.getElementById('contactsList');
    
    if (filteredContacts.length === 0) {
        contactsList.innerHTML = '<p class="text-white/60 text-center py-8">No contacts found</p>';
        return;
    }
    
    contactsList.innerHTML = filteredContacts.map(contact => {
        // Use stored phone number (already extracted)
        const phoneToDisplay = contact.phoneNumber || extractPhoneNumber(contact.jid);
        
        // Skip if still invalid
        if (!phoneToDisplay || phoneToDisplay.length > 15) {
            return ''; // Skip this contact
        }
        
        const formattedPhone = formatPhoneNumber(phoneToDisplay);
        const lastMessageTime = new Date(contact.lastMessageTime);
        const timeStr = lastMessageTime.toLocaleDateString() === new Date().toLocaleDateString()
            ? lastMessageTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
            : lastMessageTime.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        
        const isSelected = selectedContact && 
                          selectedContact.phoneNumber === contact.phoneNumber && 
                          selectedContact.instanceId === contact.instanceId;
        
        // Escape instance name and last message for safety
        const safeInstanceName = escapeHtml(contact.instanceName);
        const safeLastMessage = escapeHtml(contact.lastMessage.substring(0, 40));
        
        return `
            <div 
                class="flex items-center space-x-3 p-4 hover:bg-white/5 cursor-pointer transition-all duration-150 border-b border-white/5 ${isSelected ? 'bg-[#202C33] border-l-4 border-l-[#FCD535]' : 'hover:border-l-4 hover:border-l-white/10'}"
                onclick="selectContact('${contact.phoneNumber.replace(/'/g, "\\'")}', '${contact.jid.replace(/'/g, "\\'")}', ${contact.instanceId}, '${safeInstanceName.replace(/'/g, "\\'")}')"
            >
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#FCD535]/30 to-[#FCD535]/10 flex items-center justify-center flex-shrink-0 ring-2 ring-[#FCD535]/20">
                    <span class="text-[#FCD535] font-bold text-base">${phoneToDisplay ? phoneToDisplay.slice(-1) : '?'}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <h4 class="text-white font-semibold text-[15px] truncate">${formattedPhone}</h4>
                        <span class="text-white/50 text-xs flex-shrink-0 ml-2 font-medium">${timeStr}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-white/60 text-sm truncate flex-1 leading-relaxed">${safeLastMessage}${contact.lastMessage.length > 40 ? '...' : ''}</p>
                        ${contact.instanceName ? `<span class="text-[#FCD535]/70 text-[10px] px-2 py-0.5 rounded-full bg-[#FCD535]/10 flex-shrink-0 whitespace-nowrap font-medium">${safeInstanceName}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Select contact and show chat
function selectContact(phoneNumber, jid, instanceId, instanceName) {
    selectedContact = { phoneNumber, jid, instanceId, instanceName };
    
    currentChatPhone = jid || phoneNumber;
    currentChatInstanceId = instanceId;
    
    // Extract and format phone number
    let cleanPhone = phoneNumber;
    if (jid && jid.includes('@')) {
        cleanPhone = extractPhoneNumber(jid) || phoneNumber;
    }
    
    const formattedPhone = formatPhoneNumber(cleanPhone);
    
    // Update chat header
    document.getElementById('activeContactName').textContent = formattedPhone;
    document.getElementById('activeContactPhone').textContent = formattedPhone;
    document.getElementById('activeContactInitial').textContent = cleanPhone.slice(-1) || '?';
    
    // Set form values
    document.getElementById('chatInstanceId').value = instanceId;
    document.getElementById('chatToPhone').value = jid || phoneNumber;
    
    // Show chat area, hide empty state
    document.getElementById('emptyChatState').classList.add('hidden');
    document.getElementById('chatHeader').classList.remove('hidden');
    document.getElementById('chatMessagesArea').classList.remove('hidden');
    document.getElementById('chatInputArea').classList.remove('hidden');
    
    // Load messages
    loadChatMessages(jid || phoneNumber, instanceId);
    
    // Re-render contacts to show selection
    renderContactsList();
}

// Refresh chat manually
function refreshChat() {
    if (currentChatPhone && currentChatInstanceId) {
        loadChatMessages(currentChatPhone, currentChatInstanceId);
    }
}

function loadChatMessages(phoneNumber, instanceId) {
    const messagesContainer = document.getElementById('chatMessagesArea');
    messagesContainer.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div><p class="ml-3 text-white/70">Loading messages...</p></div>';
    
    // Fetch messages for this contact
    fetch(`/api/messages?instance_id=${instanceId}&per_page=100`, {
        headers: {
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messages = data.data.messages || [];
            
            // Normalize phone numbers for comparison
            const normalizePhone = (phone) => {
                if (!phone) return '';
                let cleaned = phone.replace(/@.*$/, '').replace(/[^\d+]/g, '');
                return cleaned.replace(/^\+/, '');
            };
            
            const normalizedCurrentPhone = normalizePhone(phoneNumber);
            
            // Filter messages for this contact (exclude groups)
            const contactMessages = messages.filter(msg => {
                // Skip groups
                if (isGroupJID(msg.from) || isGroupJID(msg.to)) {
                    return false;
                }
                
                const msgJID = msg.direction === 'inbound' ? msg.from : msg.to;
                const msgPhone = extractPhoneNumber(msgJID);
                
                if (!msgPhone) return false;
                
                const normalizedMsgPhone = normalizePhone(msgPhone);
                return normalizedMsgPhone === normalizedCurrentPhone || 
                       msgJID === phoneNumber;
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
    const messagesContainer = document.getElementById('chatMessagesArea');
    
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
            
            // Reload messages and contacts
            setTimeout(() => {
                loadChatMessages(currentChatPhone, currentChatInstanceId);
                loadContacts(); // Refresh contacts list
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

// Search contacts
document.getElementById('contactSearch')?.addEventListener('input', (e) => {
    loadContacts();
});

// Filter by instance
document.getElementById('filterInstance')?.addEventListener('change', () => {
    loadContacts();
});

// Load contacts on page load
document.addEventListener('DOMContentLoaded', () => {
    loadContacts();
});

</script>
@endpush
@endsection
