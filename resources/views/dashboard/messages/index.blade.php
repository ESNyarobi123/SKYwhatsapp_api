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
        <!-- Left Pane: Messages List -->
        <div class="w-1/3 min-w-[350px] max-w-[450px] border-r border-white/10 bg-[#111B21] flex flex-col" style="background-color: #111B21 !important;">
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
                        id="messageSearch" 
                        placeholder="Search messages..." 
                        class="w-full px-4 py-2 pl-10 bg-[#2A3942] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    >
                    <svg class="w-5 h-5 text-white/40 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Messages List -->
            <div id="messagesList" class="flex-1 overflow-y-auto bg-[#0B141A]" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                <!-- Loading state will be shown here initially -->
            </div>
        </div>

        <!-- Right Pane: Chat Area -->
        <div class="flex-1 flex flex-col bg-[#0B141A]" style="background-color: #0B141A !important; background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.02\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            <!-- Chat Header (shown when contact selected) -->
            <div id="chatHeader" class="hidden bg-[#202C33] px-4 py-3 flex items-center justify-between border-b border-white/10" style="background-color: #202C33 !important;">
                <div class="flex items-center space-x-3">
                    <!-- Back Button (Mobile/Tablet) -->
                    <button onclick="goBackToMessages()" class="p-2 text-white/70 hover:text-white transition-colors lg:hidden" title="Back">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    
                    <div class="w-10 h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-[#FCD535] font-semibold text-sm" id="activeContactInitial">?</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-white font-semibold truncate" id="activeContactName">Select a contact</h3>
                        <p class="text-white/60 text-xs truncate" id="activeContactPhone"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="refreshChat()" class="p-2 text-white/70 hover:text-white transition-colors" title="Refresh">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
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
                                ├ù
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
let allMessages = [];
let filteredMessages = [];
let selectedMessage = null;
let selectedContact = null;

// Debug: Log initial state
console.log('Messages page initialized. Variables:', {
    allMessages: allMessages.length,
    filteredMessages: filteredMessages.length
});

// Check if JID is a group
// @g.us is always a group
// @lid can be private messages (WhatsApp service already filters groups before sending to Laravel)
function isGroupJID(jid) {
    if (!jid) return false;
    
    // @g.us is always a group
    if (jid.includes('@g.us')) {
        return true;
    }
    
    // @lid can be private messages, but if ID is very long (>15 digits), it's likely a group
    // WhatsApp service already filters groups, so we trust @lid messages from backend
    // Only filter @lid if it's clearly a group (very long ID)
    if (jid.includes('@lid')) {
        const idPart = jid.split('@')[0];
        // If ID is longer than 15 digits, it's likely a group
        const isLongId = idPart && idPart.length > 15;
        return isLongId;
    }
    
    return false;
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

// Group messages by contact (phone number)
function groupMessagesByContact(messages) {
    const contactsMap = new Map();
    
    messages.forEach(msg => {
        const contactJID = msg.direction === 'inbound' ? msg.from : msg.to;
        
        // Skip groups
        if (isGroupJID(contactJID)) {
            return;
        }
        
        // Extract phone number
        const phoneNumber = extractPhoneNumber(contactJID);
        if (!phoneNumber || phoneNumber.length > 15) {
            return;
        }
        
        // Normalize for grouping
        const normalizedPhone = phoneNumber.replace(/^\+/, '');
        const contactKey = `${normalizedPhone}_${msg.instance_id}`;
        
        if (!contactsMap.has(contactKey)) {
            contactsMap.set(contactKey, {
                phoneNumber: phoneNumber,
                jid: contactJID,
                instanceId: msg.instance_id,
                instanceName: msg.instance?.name || 'Unknown',
                messages: [msg],
                lastMessage: msg,
                lastMessageTime: msg.created_at,
                unreadCount: 0
            });
        } else {
            const contact = contactsMap.get(contactKey);
            contact.messages.push(msg);
            
            // Update last message if this is newer
            if (new Date(msg.created_at) > new Date(contact.lastMessageTime)) {
                contact.lastMessage = msg;
                contact.lastMessageTime = msg.created_at;
            }
        }
    });
    
    return Array.from(contactsMap.values());
}

// Auto-refresh interval for new messages
let messagesRefreshInterval = null;
const MESSAGES_REFRESH_INTERVAL_MS = 3000; // Check every 3 seconds for new messages

// Start auto-refresh for messages
function startMessagesAutoRefresh() {
    // Clear existing interval if any
    if (messagesRefreshInterval) {
        clearInterval(messagesRefreshInterval);
    }
    
    // Start polling for new messages
    messagesRefreshInterval = setInterval(() => {
        // Only refresh if page is visible (not hidden in background tab)
        if (!document.hidden) {
            console.log('≡ƒöä Auto-refreshing messages...');
            loadMessages(true); // silent = true (don't show loading spinner)
        }
    }, MESSAGES_REFRESH_INTERVAL_MS);
    
    console.log('Γ£à Auto-refresh started. Checking for new messages every', MESSAGES_REFRESH_INTERVAL_MS, 'ms');
}

// Stop auto-refresh for messages
function stopMessagesAutoRefresh() {
    if (messagesRefreshInterval) {
        clearInterval(messagesRefreshInterval);
        messagesRefreshInterval = null;
    }
}

// Load messages list (left pane) - individual messages (numbers can repeat)
function loadMessages(silent = false) {
    const messagesList = document.getElementById('messagesList');
    
    // Only show loading state if not silent
    if (!silent) {
        messagesList.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#FCD535] mx-auto mb-3"></div>
                    <p class="text-white/70 text-sm">Loading conversations...</p>
                </div>
            </div>
        `;
    }
    
    const instanceId = document.getElementById('filterInstance')?.value || '';
    const searchTerm = document.getElementById('messageSearch')?.value.toLowerCase() || '';
    
    fetch(`/api/messages?per_page=200${instanceId ? '&instance_id=' + instanceId : ''}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    })
    .then(response => {
        console.log('Messages API response status:', response.status);
        if (!response.ok) {
            return response.json().then(err => {
                console.error('Messages API error:', err);
                throw new Error(err.message || 'Failed to load messages');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Messages API response data:', data);
        console.log('API Response full:', JSON.stringify(data, null, 2));
        if (data.success) {
            const messages = data.data?.messages || [];
            console.log('Total messages received from API:', messages.length);
            console.log('Pagination info:', data.data?.pagination);
            
            // Filter out group messages (backend already filters, but double-check for safety)
            const newAllMessages = messages.filter(msg => {
                // Check both from and to fields to ensure it's not a group
                const fromIsGroup = isGroupJID(msg.from);
                const toIsGroup = isGroupJID(msg.to);
                
                if (fromIsGroup || toIsGroup) {
                    console.log('Filtered out group message:', {
                        id: msg.id,
                        from: msg.from,
                        to: msg.to,
                        fromIsGroup,
                        toIsGroup
                    });
                    return false;
                }
                
                // Accept the message if it's not a group
                // Messages can have phone numbers with or without @s.whatsapp.net suffix
                // Examples: "255741712734" or "255741712734@s.whatsapp.net" are both valid
                return true;
            });
            
            console.log('Messages after filtering groups:', newAllMessages.length);
            console.log('Sample messages:', newAllMessages.slice(0, 3).map(m => ({
                id: m.id,
                from: m.from,
                to: m.to,
                body: m.body?.substring(0, 30),
                direction: m.direction
            })));
            
            // Check if we have new messages (compare IDs)
            const oldMessageIds = new Set(allMessages.map(m => m.id));
            const newMessages = newAllMessages.filter(msg => !oldMessageIds.has(msg.id));
            const hasNewMessages = newMessages.length > 0;
            
            if (hasNewMessages) {
                console.log(`Γ£¿ Found ${newMessages.length} new message(s)!`, newMessages.map(m => ({
                    id: m.id,
                    from: m.from,
                    to: m.to,
                    body: m.body?.substring(0, 30),
                    direction: m.direction,
                    created_at: m.created_at
                })));
            }
            
            // Update allMessages
            allMessages = newAllMessages;
            
            // Filter by search term (only if search term exists, otherwise show all)
            if (searchTerm && searchTerm.trim() !== '') {
                filteredMessages = allMessages.filter(msg => {
                    const contactJID = msg.direction === 'inbound' ? msg.from : msg.to;
                    const phoneNumber = extractPhoneNumber(contactJID);
                    const formattedPhone = phoneNumber ? formatPhoneNumber(phoneNumber) : (contactJID ? contactJID.replace(/@.*$/, '') : '');
                    const messageBody = msg.body || '';
                    const instanceName = msg.instance?.name || '';
                    
                    return formattedPhone.toLowerCase().includes(searchTerm) || 
                           messageBody.toLowerCase().includes(searchTerm) ||
                           instanceName.toLowerCase().includes(searchTerm);
                });
            } else {
                // No search term - show all messages
                filteredMessages = allMessages;
            }
            
            console.log('Filtered messages after search:', filteredMessages.length);
            
            // If no messages after all filtering, log details
            if (filteredMessages.length === 0 && messages.length > 0) {
                console.warn('All messages filtered out!');
                console.log('Sample messages from API:', messages.slice(0, 3));
                messages.slice(0, 3).forEach((msg, idx) => {
                    console.log(`Message ${idx + 1}:`, {
                        id: msg.id,
                        direction: msg.direction,
                        from: msg.from,
                        to: msg.to,
                        body: msg.body?.substring(0, 50),
                        isGroupFrom: isGroupJID(msg.from),
                        isGroupTo: isGroupJID(msg.to)
                    });
                });
            }
            
            // Sort by created_at (newest first)
            filteredMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            console.log('About to render messages. filteredMessages count:', filteredMessages.length);
            console.log('First 3 filtered messages:', filteredMessages.slice(0, 3));
            
            // Safety check: If filteredMessages is empty but we have messages, use allMessages
            if (filteredMessages.length === 0 && allMessages.length > 0) {
                console.warn('filteredMessages is empty but allMessages has items. Using allMessages as fallback.');
                filteredMessages = allMessages;
            }
            
            // Re-render messages list
            renderMessagesList();
            
            // If we have new messages and chat is open, refresh chat too
            if (hasNewMessages && currentChatPhone && currentChatInstanceId) {
                loadChatMessages(currentChatPhone, currentChatInstanceId, true); // silent = true
            }
        } else {
            if (!silent) {
                messagesList.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-16 px-4">
                        <div class="w-20 h-20 rounded-full bg-yellow-500/10 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-yellow-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">No conversations found</h3>
                        <p class="text-white/60 text-sm text-center mb-4">${data.error?.message || 'Start chatting by sending or receiving a message'}</p>
                        <button onclick="loadMessages()" class="px-4 py-2 bg-[#FCD535] hover:bg-[#F0C420] text-black rounded-lg text-sm font-medium transition-colors">
                            Retry
                        </button>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        if (!silent) {
            messagesList.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 px-4">
                    <div class="w-20 h-20 rounded-full bg-red-500/10 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-red-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-2">Error loading messages</h3>
                    <p class="text-white/60 text-sm text-center mb-4">${error.message || 'Failed to load messages. Please check your connection and try again.'}</p>
                    <button onclick="loadMessages()" class="px-4 py-2 bg-[#FCD535] hover:bg-[#F0C420] text-black rounded-lg text-sm font-medium transition-colors">
                        Retry
                    </button>
                </div>
            `;
        }
    });
}

// Render messages list (individual messages - numbers can repeat)
function renderMessagesList() {
    const messagesList = document.getElementById('messagesList');
    
    console.log('renderMessagesList called');
    console.log('filteredMessages length:', filteredMessages?.length || 0);
    console.log('filteredMessages:', filteredMessages);
    console.log('allMessages length:', allMessages?.length || 0);
    
    if (!filteredMessages || filteredMessages.length === 0) {
        console.warn('No filtered messages to display');
        console.log('allMessages:', allMessages);
        console.log('filteredMessages:', filteredMessages);
        messagesList.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-20 h-20 rounded-full bg-[#FCD535]/10 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">No messages found</h3>
                <p class="text-white/60 text-sm text-center max-w-xs">
                    Start chatting by sending or receiving a message
                </p>
            </div>
        `;
        return;
    }
    
    console.log('Rendering', filteredMessages.length, 'messages');
    
    const htmlContent = filteredMessages.map((msg, index) => {
        try {
            // Extract contact info
            const contactJID = msg.direction === 'inbound' ? msg.from : msg.to;
            const phoneNumber = extractPhoneNumber(contactJID);
            
            // Declare variables outside if/else block
            let formattedPhone;
            let phoneLastDigit;
            
            // Log if phone number extraction fails
            if (!phoneNumber || phoneNumber.length > 15) {
                console.warn(`Message ${index + 1}: Failed to extract phone number:`, {
                    id: msg.id,
                    jid: contactJID,
                    direction: msg.direction,
                    from: msg.from,
                    to: msg.to,
                    extractedPhone: phoneNumber
                });
                // Still render the message, but use JID or a fallback
                const fallbackPhone = contactJID ? contactJID.replace(/@.*$/, '').substring(0, 15) : 'Unknown';
                formattedPhone = fallbackPhone || 'Unknown';
                phoneLastDigit = fallbackPhone ? fallbackPhone.slice(-1) : '?';
            } else {
                formattedPhone = formatPhoneNumber(phoneNumber);
                phoneLastDigit = phoneNumber.slice(-1) || '?';
            }
            
            // Continue with rendering
            const messageTime = new Date(msg.created_at);
            const timeStr = messageTime.toLocaleDateString() === new Date().toLocaleDateString()
                ? messageTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
                : messageTime.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            
            const isSelected = selectedMessage && selectedMessage.id === msg.id;
            const isInbound = msg.direction === 'inbound';
            const messageBody = msg.body || '[Media]';
            const safeMessageBody = escapeHtml(messageBody.substring(0, 50));
            const safeInstanceName = escapeHtml(msg.instance?.name || 'Unknown');
            
            // Direction indicator
            const directionIcon = isInbound 
                ? '<svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                : '<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
            
            return `
                <div 
                    class="flex items-start space-x-3 p-3 hover:bg-white/5 cursor-pointer transition-all duration-150 border-b border-white/5 ${isSelected ? 'bg-[#202C33] border-l-4 border-l-[#FCD535]' : 'hover:border-l-4 hover:border-l-white/10'}"
                    onclick="selectMessage(${msg.id}, '${contactJID.replace(/'/g, "\\'")}', ${msg.instance_id}, '${safeInstanceName.replace(/'/g, "\\'")}')"
                >
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#FCD535]/30 to-[#FCD535]/10 flex items-center justify-center flex-shrink-0 ring-2 ring-[#FCD535]/20">
                        <span class="text-[#FCD535] font-bold text-sm">${phoneLastDigit}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <h4 class="text-white font-semibold text-[15px] truncate">${formattedPhone}</h4>
                                ${directionIcon}
                            </div>
                            <span class="text-white/50 text-xs flex-shrink-0 ml-2 font-medium">${timeStr}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-white/60 text-sm truncate flex-1 leading-relaxed">${safeMessageBody}${messageBody.length > 50 ? '...' : ''}</p>
                            ${msg.instance?.name ? `<span class="text-[#FCD535]/70 text-[10px] px-2 py-0.5 rounded-full bg-[#FCD535]/10 flex-shrink-0 whitespace-nowrap font-medium">${safeInstanceName}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-white/40 text-[10px] px-2 py-0.5 rounded ${isInbound ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400'}">${isInbound ? 'Incoming' : 'Outgoing'}</span>
                            <span class="text-white/40 text-[10px]">${msg.status || 'sent'}</span>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error(`Error rendering message ${index + 1}:`, error, msg);
            return '';
        }
    }).filter(html => html !== '').join('');
    
    console.log('Generated HTML length:', htmlContent.length);
    console.log('First 500 chars of HTML:', htmlContent.substring(0, 500));
    
    messagesList.innerHTML = htmlContent;
    
    if (htmlContent.length === 0) {
        console.error('No HTML generated from messages!');
    }
}

// Back to messages list
function goBackToMessages() {
    // Hide chat area, show empty state
    document.getElementById('emptyChatState').classList.remove('hidden');
    document.getElementById('chatHeader').classList.add('hidden');
    document.getElementById('chatMessagesArea').classList.add('hidden');
    document.getElementById('chatInputArea').classList.add('hidden');
    
    // Clear selection
    selectedContact = null;
    selectedMessage = null;
    currentChatPhone = null;
    currentChatInstanceId = null;
    
    // Re-render messages list to remove selection
    renderMessagesList();
}

// Select message and show chat
function selectMessage(messageId, jid, instanceId, instanceName) {
    // Find the selected message
    const message = allMessages.find(m => m.id === messageId);
    if (!message) return;
    
    selectedMessage = message;
    
    // Extract contact info from message
    // Use the original JID from the message (preserves @lid or @s.whatsapp.net suffix)
    let contactJID = message.direction === 'inbound' ? message.from : message.to;
    
    // Check if metadata has original JID (from_jid or to_jid)
    if (message.metadata) {
        if (message.direction === 'inbound' && message.metadata.from_jid) {
            contactJID = message.metadata.from_jid;
        } else if (message.direction === 'outbound' && message.metadata.to_jid) {
            contactJID = message.metadata.to_jid;
        }
    }
    
    // If contactJID still doesn't have a suffix (@lid, @s.whatsapp.net, etc.), use the passed jid parameter
    // This ensures we use the original JID format for replies
    if (contactJID && !contactJID.includes('@')) {
        contactJID = jid || contactJID; // Use the jid parameter if contactJID has no suffix
    }
    
    const phoneNumber = extractPhoneNumber(contactJID);
    
    if (!phoneNumber && !contactJID) return;
    
    currentChatPhone = contactJID;
    currentChatInstanceId = instanceId;
    
    const formattedPhone = phoneNumber ? formatPhoneNumber(phoneNumber) : (contactJID ? contactJID.replace(/@.*$/, '') : 'Unknown');
    
    // Update chat header
    document.getElementById('activeContactName').textContent = formattedPhone;
    document.getElementById('activeContactPhone').textContent = formattedPhone;
    document.getElementById('activeContactInitial').textContent = phoneNumber ? (phoneNumber.slice(-1) || '?') : (contactJID ? contactJID.slice(-1) : '?');
    
    // Set form values - use original JID to preserve @lid or @s.whatsapp.net format
    document.getElementById('chatInstanceId').value = instanceId;
    document.getElementById('chatToPhone').value = contactJID; // This preserves @lid or @s.whatsapp.net
    
    // Show chat area, hide empty state
    document.getElementById('emptyChatState').classList.add('hidden');
    document.getElementById('chatHeader').classList.remove('hidden');
    document.getElementById('chatMessagesArea').classList.remove('hidden');
    document.getElementById('chatInputArea').classList.remove('hidden');
    
    // Load messages for this contact
    loadChatMessages(contactJID, instanceId);
    
    // Re-render messages list to show selection
    renderMessagesList();
}

// Refresh chat manually
function refreshChat() {
    if (currentChatPhone && currentChatInstanceId) {
        loadChatMessages(currentChatPhone, currentChatInstanceId);
    }
}

function loadChatMessages(phoneNumber, instanceId, silent = false) {
    const messagesContainer = document.getElementById('chatMessagesArea');
    
    // Only show loading if not silent update
    if (!silent) {
        messagesContainer.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#FCD535]"></div><p class="ml-3 text-white/70">Loading messages...</p></div>';
    }
    
    // Store current scroll position if silent
    const scrollPosition = silent ? messagesContainer.scrollTop : null;
    const wasAtBottom = silent ? (messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight < 50) : false;
    
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

function renderChatMessages(messages, silent = false, wasAtBottom = false, oldScrollHeight = null) {
    const messagesContainer = document.getElementById('chatMessagesArea');
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<p class="text-white/60 text-center py-8">No messages yet. Start the conversation!</p>';
        return;
    }
    
    // Preserve scroll position for silent updates
    const currentScrollTop = silent ? messagesContainer.scrollTop : null;
    
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
    
    // Handle scroll position
    if (silent && oldScrollHeight) {
        // Maintain scroll position for silent updates
        const newScrollHeight = messagesContainer.scrollHeight;
        const scrollDiff = newScrollHeight - oldScrollHeight;
        
        if (wasAtBottom) {
            // Was at bottom, stay at bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else if (scrollDiff > 0) {
            // New messages added above, adjust scroll
            messagesContainer.scrollTop = currentScrollTop + scrollDiff;
        }
    } else {
        // Normal scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
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
    const originalButtonContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-black"></div>';
    
    // Optimistic update - show message immediately
    const messagesContainer = document.getElementById('chatMessagesArea');
    const tempId = 'temp_' + Date.now();
    const optimisticMessage = {
        id: tempId,
        direction: 'outbound',
        body: messageBody || '[Image]',
        created_at: new Date().toISOString(),
        status: 'sending',
        from: 'You',
        to: toPhone,
        metadata: chatImageFile ? { hasMedia: true, mediaType: chatImageFile.type } : {}
    };
    
    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
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
            // Clear input immediately
            messageInput.value = '';
            removeImagePreview();
            
            // Reload chat messages in background (silent update)
            setTimeout(() => {
                loadChatMessages(currentChatPhone, currentChatInstanceId, true); // silent = true
            }, 300);
            
            // Reload contacts list in background (silent update)
            setTimeout(() => {
                loadMessages(); // This will update the list silently
            }, 500);
        } else {
            // Remove optimistic message on error
            const tempMsg = document.getElementById(`msg-${tempId}`);
            if (tempMsg) {
                tempMsg.remove();
            }
            alert(data.error?.message || 'Failed to send message');
        }
    } catch (error) {
        // Remove optimistic message on error
        const tempMsg = document.getElementById(`msg-${tempId}`);
        if (tempMsg) {
            tempMsg.remove();
        }
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        sendButton.disabled = false;
        sendButton.innerHTML = originalButtonContent;
    }
});

// Search messages
document.getElementById('messageSearch')?.addEventListener('input', (e) => {
    loadMessages();
});

// Filter by instance
document.getElementById('filterInstance')?.addEventListener('change', () => {
    loadMessages();
});

// Handle page visibility change (pause polling when page is hidden)
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopMessagesAutoRefresh();
    } else {
        startMessagesAutoRefresh();
        loadMessages(true); // Silent refresh when page becomes visible
    }
});

// Load messages on page load and start auto-refresh
document.addEventListener('DOMContentLoaded', () => {
    loadMessages();
    startMessagesAutoRefresh();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopMessagesAutoRefresh();
});

</script>
@endpush
@endsection
