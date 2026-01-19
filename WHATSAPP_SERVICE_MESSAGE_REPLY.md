# Mwongozo: WhatsApp Service - Ku-send Messages na Images

Hii ni mwongozo wa jinsi ya ku-update Node.js WhatsApp service ili iweze ku-send messages na images kwa kujibu messages kutoka Laravel.

## Uwezo wa WhatsApp Service

**Ndiyo, WhatsApp service inaweza ku-send messages na images!** 

Baileys library (ambayo hutumika kwa WhatsApp Web API) ina-support:
- ✅ Text messages
- ✅ Images (JPEG, PNG, GIF, WebP)
- ✅ Videos
- ✅ Documents
- ✅ Audio files
- ✅ Location sharing
- ✅ Contacts

## Implementation

### 1. Send Text Message

```javascript
const { default: makeWASocket } = require('@whapi/baileys');

async function sendTextMessage(sock, to, message) {
    try {
        // Format phone number (remove + and add @s.whatsapp.net)
        const jid = `${to.replace(/[^\d]/g, '')}@s.whatsapp.net`;
        
        await sock.sendMessage(jid, {
            text: message
        });
        
        console.log(`Message sent to ${to}`);
        return { success: true };
    } catch (error) {
        console.error('Error sending message:', error);
        throw error;
    }
}
```

### 2. Send Image Message

```javascript
const fs = require('fs');
const { default: makeWASocket } = require('@whapi/baileys');

async function sendImageMessage(sock, to, imagePath, caption = '') {
    try {
        const jid = `${to.replace(/[^\d]/g, '')}@s.whatsapp.net`;
        
        // Read image file
        const imageBuffer = fs.readFileSync(imagePath);
        
        await sock.sendMessage(jid, {
            image: imageBuffer,
            caption: caption
        });
        
        console.log(`Image sent to ${to}`);
        return { success: true };
    } catch (error) {
        console.error('Error sending image:', error);
        throw error;
    }
}
```

### 3. Send Image from Base64 or URL

```javascript
const axios = require('axios');
const { default: makeWASocket } = require('@whapi/baileys');

async function sendImageFromUrl(sock, to, imageUrl, caption = '') {
    try {
        const jid = `${to.replace(/[^\d]/g, '')}@s.whatsapp.net`;
        
        // Download image
        const response = await axios.get(imageUrl, {
            responseType: 'arraybuffer'
        });
        
        const imageBuffer = Buffer.from(response.data);
        
        await sock.sendMessage(jid, {
            image: imageBuffer,
            caption: caption
        });
        
        console.log(`Image sent to ${to}`);
        return { success: true };
    } catch (error) {
        console.error('Error sending image:', error);
        throw error;
    }
}

// For base64
async function sendImageFromBase64(sock, to, base64String, caption = '') {
    try {
        const jid = `${to.replace(/[^\d]/g, '')}@s.whatsapp.net`;
        
        // Remove data URL prefix if present
        const base64Data = base64String.includes(',') 
            ? base64String.split(',')[1] 
            : base64String;
        
        const imageBuffer = Buffer.from(base64Data, 'base64');
        
        await sock.sendMessage(jid, {
            image: imageBuffer,
            caption: caption
        });
        
        console.log(`Image sent to ${to}`);
        return { success: true };
    } catch (error) {
        console.error('Error sending image:', error);
        throw error;
    }
}
```

### 4. Process Pending Messages from Laravel

```javascript
const axios = require('axios');

async function processPendingMessages(sock, instanceId) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY;
        
        // Get pending messages
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
        
        for (const message of messages) {
            try {
                const to = message.to;
                const body = message.body;
                const metadata = message.metadata || {};
                
                // Check if message has image
                if (metadata.hasMedia && metadata.mediaUrl) {
                    // Send image
                    await sendImageFromUrl(sock, to, metadata.mediaUrl, body);
                } else {
                    // Send text
                    await sendTextMessage(sock, to, body);
                }
                
                // Update message status to delivered
                await updateMessageStatus(message.id, 'delivered');
                
            } catch (error) {
                console.error(`Error processing message ${message.id}:`, error);
                await updateMessageStatus(message.id, 'failed');
            }
        }
        
    } catch (error) {
        console.error('Error fetching pending messages:', error);
    }
}

async function updateMessageStatus(messageId, status) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY;
        
        // Note: You may need to add this endpoint to Laravel
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

### 5. Complete Example: Message Handler

```javascript
const { default: makeWASocket, useMultiFileAuthState } = require('@whapi/baileys');
const axios = require('axios');

class WhatsAppService {
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
        
        // Poll for pending messages every 5 seconds
        setInterval(() => {
            this.processPendingMessages();
        }, 5000);
    }
    
    async sendMessage(to, body, metadata = {}) {
        if (!this.sock) {
            throw new Error('Socket not initialized');
        }
        
        const jid = `${to.replace(/[^\d]/g, '')}@s.whatsapp.net`;
        
        try {
            if (metadata.hasMedia && metadata.mediaUrl) {
                // Download and send image
                const response = await axios.get(metadata.mediaUrl, {
                    responseType: 'arraybuffer'
                });
                
                const imageBuffer = Buffer.from(response.data);
                
                await this.sock.sendMessage(jid, {
                    image: imageBuffer,
                    caption: body || ''
                });
            } else {
                // Send text message
                await this.sock.sendMessage(jid, {
                    text: body
                });
            }
            
            return { success: true };
        } catch (error) {
            console.error('Error sending message:', error);
            throw error;
        }
    }
    
    async processPendingMessages() {
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
            
            for (const message of messages) {
                try {
                    await this.sendMessage(
                        message.to,
                        message.body,
                        message.metadata || {}
                    );
                    
                    // Mark as delivered (you'll need to implement this endpoint)
                    console.log(`Message ${message.id} sent successfully`);
                } catch (error) {
                    console.error(`Error sending message ${message.id}:`, error);
                }
            }
        } catch (error) {
            console.error('Error processing pending messages:', error);
        }
    }
}

module.exports = WhatsAppService;
```

## Notes Muhimu

1. **Phone Number Format**: WhatsApp ina-expect phone numbers zisizo na + na zina-append `@s.whatsapp.net`
   - Example: `255712345678@s.whatsapp.net`

2. **Image Formats**: Supported formats:
   - JPEG/JPG
   - PNG
   - GIF
   - WebP

3. **Image Size Limits**: 
   - Recommended: < 5MB
   - Maximum: ~16MB (depends on WhatsApp limits)

4. **Error Handling**: Always handle errors kwa uangalifu, especially kwa network requests

5. **Rate Limiting**: WhatsApp ina-rate limits, so don't send too many messages at once

## Testing

1. Test text messages kwanza
2. Test images with small files
3. Test error handling (invalid phone numbers, network errors)
4. Test with different image formats

## Integration na Laravel

Laravel ina-save messages na metadata (including image URLs) kwenye database. WhatsApp service ina-poll kwa pending messages na ku-send kwa WhatsApp.

**Flow:**
1. User sends message through Laravel web interface
2. Laravel saves message kwenye database na status = 'sent'
3. WhatsApp service polls for pending messages
4. WhatsApp service sends message through WhatsApp
5. WhatsApp service updates message status to 'delivered'
