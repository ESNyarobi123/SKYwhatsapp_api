# Mwongozo: WhatsApp Service - Ku-send Outbound Messages na Ku-update Status

Hii ni mwongozo wa jinsi ya ku-fix outbound messages zi-fike na ku-update status correctly.

## Matatizo yaliyopo

1. **Outbound messages hazifiki**: Messages zina-save kwenye database lakini hazitumwa kwa WhatsApp
2. **Status haija-update**: Status ina-baki "sent" badala ya "delivered"
3. **Phone numbers hazionekani**: Namba zinaonyeshwa kama JID badala ya real numbers

## Solutions

### 1. Process Pending Outbound Messages

WhatsApp service ina-hitaji ku-poll kwa pending outbound messages na ku-send:

```javascript
const axios = require('axios');

async function processPendingMessages(sock, instanceId) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY;
        
        // Get pending messages (status = 'sent', direction = 'outbound')
        const response = await axios.get(
            `${laravelUrl}/api/internal/messages/pending`,
            {
                headers: {
                    'Authorization': `Bearer ${internalApiKey}`,
                    'X-API-Key': internalApiKey
                }
            }
        );
        
        const messages = response.data.data.messages || [];
        
        // Filter messages for this instance
        const instanceMessages = messages.filter(msg => msg.instance_id === instanceId);
        
        for (const message of instanceMessages) {
            try {
                // Format phone number (remove + and add @s.whatsapp.net)
                const toJID = formatPhoneToJID(message.to);
                
                // Send message
                if (message.metadata?.hasMedia && message.metadata?.mediaUrl) {
                    // Send image
                    await sendImageMessage(sock, toJID, message.metadata.mediaUrl, message.body);
                } else {
                    // Send text
                    await sock.sendMessage(toJID, {
                        text: message.body
                    });
                }
                
                // Update message status to delivered
                await updateMessageStatus(message.id, 'delivered');
                
                console.log(`[${instanceId}] Message ${message.id} sent successfully`);
                
            } catch (error) {
                console.error(`[${instanceId}] Error sending message ${message.id}:`, error);
                await updateMessageStatus(message.id, 'failed');
            }
        }
        
    } catch (error) {
        console.error(`[${instanceId}] Error processing pending messages:`, error);
    }
}

function formatPhoneToJID(phone) {
    // Remove any non-digit characters except +
    let digits = phone.replace(/[^\d+]/g, '');
    
    // Remove leading +
    if (digits.startsWith('+')) {
        digits = digits.substring(1);
    }
    
    // Add @s.whatsapp.net
    return `${digits}@s.whatsapp.net`;
}

async function sendImageMessage(sock, toJID, imageUrl, caption = '') {
    try {
        // Download image
        const response = await axios.get(imageUrl, {
            responseType: 'arraybuffer'
        });
        
        const imageBuffer = Buffer.from(response.data);
        
        await sock.sendMessage(toJID, {
            image: imageBuffer,
            caption: caption
        });
        
        return { success: true };
    } catch (error) {
        console.error('Error sending image:', error);
        throw error;
    }
}

async function updateMessageStatus(messageId, status) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY;
        
        await axios.post(
            `${laravelUrl}/api/internal/messages/${messageId}/status`,
            { status },
            {
                headers: {
                    'Authorization': `Bearer ${internalApiKey}`,
                    'X-API-Key': internalApiKey
                }
            }
        );
    } catch (error) {
        console.error('Error updating message status:', error);
    }
}
```

### 2. Poll for Pending Messages Regularly

Add polling kwa pending messages:

```javascript
// Poll every 5 seconds
setInterval(() => {
    if (sock && instanceId) {
        processPendingMessages(sock, instanceId);
    }
}, 5000);
```

### 3. Filter Group Messages (Only Private)

When receiving messages, filter out groups:

```javascript
sock.ev.on('messages.upsert', async (m) => {
    const messages = m.messages || [];
    
    for (const msg of messages) {
        // Skip if it's a group message
        if (msg.key.remoteJid?.includes('@g.us') || msg.key.remoteJid?.includes('@lid')) {
            continue; // Skip group messages
        }
        
        // Only process private messages
        const from = msg.key.remoteJid;
        const messageText = msg.message?.conversation || 
                           msg.message?.extendedTextMessage?.text || 
                           '[Media or unsupported message type]';
        
        // Store message in Laravel
        await storeMessage(instanceId, from, messageText, msg);
    }
});
```

### 4. Extract Real Phone Numbers

When storing messages, extract real phone numbers:

```javascript
function extractPhoneNumber(jid) {
    if (!jid) return null;
    
    // Skip groups
    if (jid.includes('@g.us') || jid.includes('@lid')) {
        return null;
    }
    
    // Remove @s.whatsapp.net
    let phone = jid.replace(/@.*$/, '');
    
    // Remove non-digits except +
    phone = phone.replace(/[^\d+]/g, '');
    
    return phone || null;
}

// When storing message
const phoneNumber = extractPhoneNumber(from);
if (phoneNumber) {
    // Store with real phone number
    await storeMessage(instanceId, phoneNumber, messageText, msg);
}
```

## Complete Example

```javascript
const { default: makeWASocket, useMultiFileAuthState } = require('@whapi/baileys');
const axios = require('axios');

class WhatsAppInstance {
    constructor(instanceId) {
        this.instanceId = instanceId;
        this.sock = null;
    }
    
    async initialize() {
        const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${this.instanceId}`);
        
        this.sock = makeWASocket({
            auth: state,
            printQRInTerminal: false,
        });
        
        this.sock.ev.on('creds.update', saveCreds);
        
        // Handle incoming messages (only private)
        this.sock.ev.on('messages.upsert', async (m) => {
            await this.handleIncomingMessages(m);
        });
        
        // Poll for pending outbound messages
        setInterval(() => {
            this.processPendingMessages();
        }, 5000);
        
        // Process immediately on connect
        if (this.sock.user) {
            this.processPendingMessages();
        }
    }
    
    async handleIncomingMessages(m) {
        const messages = m.messages || [];
        
        for (const msg of messages) {
            const fromJID = msg.key.remoteJid;
            
            // Skip groups
            if (this.isGroupJID(fromJID)) {
                continue;
            }
            
            const phoneNumber = this.extractPhoneNumber(fromJID);
            if (!phoneNumber) continue;
            
            const messageText = msg.message?.conversation || 
                               msg.message?.extendedTextMessage?.text || 
                               '[Media or unsupported message type]';
            
            // Store in Laravel
            await this.storeInboundMessage(phoneNumber, fromJID, messageText, msg);
        }
    }
    
    isGroupJID(jid) {
        return jid && (jid.includes('@g.us') || jid.includes('@lid'));
    }
    
    extractPhoneNumber(jid) {
        if (!jid || this.isGroupJID(jid)) return null;
        let phone = jid.replace(/@.*$/, '').replace(/[^\d+]/g, '');
        return phone || null;
    }
    
    formatPhoneToJID(phone) {
        let digits = phone.replace(/[^\d+]/g, '');
        if (digits.startsWith('+')) {
            digits = digits.substring(1);
        }
        return `${digits}@s.whatsapp.net`;
    }
    
    async processPendingMessages() {
        if (!this.sock || !this.sock.user) return;
        
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            const response = await axios.get(
                `${laravelUrl}/api/internal/messages/pending`,
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
            
            const messages = response.data.data.messages || [];
            const instanceMessages = messages.filter(msg => msg.instance_id === this.instanceId);
            
            for (const message of instanceMessages) {
                try {
                    const toJID = this.formatPhoneToJID(message.to);
                    
                    if (message.metadata?.hasMedia && message.metadata?.mediaUrl) {
                        await this.sendImageMessage(toJID, message.metadata.mediaUrl, message.body);
                    } else {
                        await this.sock.sendMessage(toJID, {
                            text: message.body
                        });
                    }
                    
                    await this.updateMessageStatus(message.id, 'delivered');
                    
                } catch (error) {
                    console.error(`[${this.instanceId}] Error sending message ${message.id}:`, error);
                    await this.updateMessageStatus(message.id, 'failed');
                }
            }
        } catch (error) {
            console.error(`[${this.instanceId}] Error processing pending messages:`, error);
        }
    }
    
    async sendImageMessage(toJID, imageUrl, caption) {
        const response = await axios.get(imageUrl, {
            responseType: 'arraybuffer'
        });
        
        const imageBuffer = Buffer.from(response.data);
        
        await this.sock.sendMessage(toJID, {
            image: imageBuffer,
            caption: caption
        });
    }
    
    async updateMessageStatus(messageId, status) {
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            await axios.post(
                `${laravelUrl}/api/internal/messages/${messageId}/status`,
                { status },
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
        } catch (error) {
            console.error('Error updating message status:', error);
        }
    }
    
    async storeInboundMessage(phoneNumber, jid, messageText, msg) {
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            await axios.post(
                `${laravelUrl}/api/internal/messages`,
                {
                    instance_id: this.instanceId,
                    from: jid, // Store JID for reference
                    to: phoneNumber, // Store real phone number
                    message: messageText,
                    message_id: msg.key.id,
                    timestamp: new Date(msg.messageTimestamp * 1000).toISOString(),
                    metadata: {
                        // Add any additional metadata
                    }
                },
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
        } catch (error) {
            console.error('Error storing inbound message:', error);
        }
    }
}

module.exports = WhatsAppInstance;
```

## Key Points

1. **Filter Groups**: Always check `@g.us` na `@lid` - skip group messages
2. **Extract Phone Numbers**: Extract real phone numbers kutoka JID kwa display
3. **Format for Sending**: Convert phone numbers to JID format (`phone@s.whatsapp.net`) wakati wa ku-send
4. **Update Status**: Always update message status baada ya ku-send successfully
5. **Poll Regularly**: Poll kwa pending messages kila 5 seconds

## Testing

1. Send message kutoka web interface
2. Check kwa pending messages kwenye WhatsApp service logs
3. Verify message ina-send successfully
4. Verify status ina-update to "delivered"
5. Verify phone numbers zinaonyeshwa correctly
