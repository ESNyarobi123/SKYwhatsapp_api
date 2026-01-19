# WhatsApp Service - Sending Incoming Messages to Laravel

Guide hii inaelezea jinsi ya kutuma incoming messages kutoka WhatsApp Service (Node.js/Baileys) kwenye Laravel backend.

## Overview

Wakati message inapokea kutoka WhatsApp, whatsapp-service inafaa:
1. Extract message data (text, media, sender, etc.)
2. Send message data kwenye Laravel API endpoint
3. Handle errors gracefully

## Laravel API Endpoint

**URL:** `POST /api/internal/messages`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer YOUR_INTERNAL_API_KEY
```

**OR**

```
Content-Type: application/json
X-API-Key: YOUR_INTERNAL_API_KEY
```

**Authentication:** 
- Endpoint inatumia `AuthenticateInternalApi` middleware
- Key inafaa kuwa sawa na `WHATSAPP_SERVICE_API_KEY` kwenye Laravel `.env` au `config/services.php`

## Request Body

```json
{
  "instance_id": 1,
  "from": "255712345678@s.whatsapp.net",
  "to": "255798765432@s.whatsapp.net",
  "message": "Hello! This is the message text",
  "message_id": "3EB0123456789ABCDEF",
  "timestamp": "2024-01-20T10:30:00Z",
  "metadata": {
    "hasMedia": false,
    "mediaType": null,
    "mediaUrl": null
  }
}
```

### Required Fields

- `instance_id` (integer, required): ID ya instance kwenye Laravel database
- `from` (string, required): JID ya sender (e.g., `255712345678@s.whatsapp.net`)
- `to` (string, required): JID ya receiver (e.g., `255798765432@s.whatsapp.net`)
- `message` (string, required): Message body. Kwa media messages, tumia placeholder kama `[Image]`, `[Video]`, `[Audio]`, etc.
- `message_id` (string, nullable): WhatsApp message ID kutoka Baileys
- `timestamp` (string, nullable, ISO 8601): Timestamp ya message. Ikiwa null, Laravel itatumia current time.
- `metadata` (object, nullable): Additional data kwa message (media info, etc.)

### Metadata Object (Optional)

```json
{
  "hasMedia": true,
  "mediaType": "image/jpeg",
  "mediaUrl": "https://example.com/media/image.jpg",
  "mediaPath": "messages/image_123.jpg",
  "mediaMimetype": "image/jpeg",
  "mediaSize": 12345,
  "caption": "Optional caption for media"
}
```

## Implementation Example

### 1. Setup: Add Helper Function

Katika `src/services/laravelApi.js` (au file yako ya Laravel API helper):

```javascript
const axios = require('axios');

const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8000';
const INTERNAL_API_KEY = process.env.INTERNAL_API_KEY || process.env.WHATSAPP_SERVICE_API_KEY;

/**
 * Send incoming message to Laravel
 */
async function sendIncomingMessageToLaravel(instanceId, messageData) {
    try {
        const response = await axios.post(
            `${LARAVEL_API_URL}/api/internal/messages`,
            {
                instance_id: instanceId,
                from: messageData.from,
                to: messageData.to,
                message: messageData.body || '[Media]',
                message_id: messageData.messageId,
                timestamp: messageData.timestamp ? new Date(messageData.timestamp * 1000).toISOString() : null,
                metadata: messageData.metadata || {}
            },
            {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${INTERNAL_API_KEY}`,
                    // OR use X-API-Key header
                    // 'X-API-Key': INTERNAL_API_KEY,
                },
                timeout: 10000 // 10 seconds
            }
        );

        return response.data;
    } catch (error) {
        console.error(`[${instanceId}] Error sending incoming message to Laravel:`, error.response?.data || error.message);
        throw error;
    }
}

module.exports = { sendIncomingMessageToLaravel };
```

### 2. Handle Message Events

Katika file yako ya ku-handle messages (e.g., `src/handlers/messageHandler.js`):

```javascript
const { sendIncomingMessageToLaravel } = require('../services/laravelApi');

/**
 * Handle incoming messages
 */
async function handleIncomingMessage(sock, instanceId, messageData) {
    try {
        // Extract message info
        const message = messageData.message;
        const from = messageData.key.remoteJid; // e.g., "255712345678@s.whatsapp.net"
        
        // Skip own messages
        if (messageData.key.fromMe) {
            return;
        }
        
        // Skip group messages (optional - Laravel will filter, but good to skip here)
        if (from.includes('@g.us') || from.includes('@lid')) {
            console.log(`[${instanceId}] Skipping group message from ${from}`);
            return;
        }
        
        // Get instance phone number (to field)
        // You need to get this from your instance data
        const instancePhoneNumber = await getInstancePhoneNumber(instanceId);
        const to = `${instancePhoneNumber}@s.whatsapp.net`;

        // Extract message body and metadata
        let body = '';
        let metadata = {
            hasMedia: false,
            mediaType: null,
            mediaUrl: null
        };

        // Handle text messages
        if (message.conversation) {
            body = message.conversation;
        } 
        // Handle extended text messages (with links, mentions, etc.)
        else if (message.extendedTextMessage) {
            body = message.extendedTextMessage.text || '';
        }
        // Handle image messages
        else if (message.imageMessage) {
            body = message.imageMessage.caption || '[Image]';
            metadata = {
                hasMedia: true,
                mediaType: message.imageMessage.mimetype || 'image/jpeg',
                mediaUrl: null, // Download and upload to your storage first
                mediaPath: null,
                mediaMimetype: message.imageMessage.mimetype,
                mediaSize: message.imageMessage.fileLength,
                caption: message.imageMessage.caption || null
            };
            
            // TODO: Download image and upload to storage, then set mediaUrl
            // metadata.mediaUrl = await downloadAndUploadMedia(message.imageMessage, instanceId);
        }
        // Handle video messages
        else if (message.videoMessage) {
            body = message.videoMessage.caption || '[Video]';
            metadata = {
                hasMedia: true,
                mediaType: message.videoMessage.mimetype || 'video/mp4',
                mediaUrl: null,
                mediaPath: null,
                mediaMimetype: message.videoMessage.mimetype,
                mediaSize: message.videoMessage.fileLength,
                caption: message.videoMessage.caption || null
            };
        }
        // Handle audio messages
        else if (message.audioMessage) {
            body = '[Audio]';
            metadata = {
                hasMedia: true,
                mediaType: message.audioMessage.mimetype || 'audio/ogg; codecs=opus',
                mediaUrl: null,
                mediaPath: null,
                mediaMimetype: message.audioMessage.mimetype,
                mediaSize: message.audioMessage.fileLength
            };
        }
        // Handle document messages
        else if (message.documentMessage) {
            body = message.documentMessage.fileName || '[Document]';
            metadata = {
                hasMedia: true,
                mediaType: message.documentMessage.mimetype || 'application/octet-stream',
                mediaUrl: null,
                mediaPath: null,
                mediaMimetype: message.documentMessage.mimetype,
                mediaSize: message.documentMessage.fileLength,
                fileName: message.documentMessage.fileName
            };
        }
        // Handle location messages
        else if (message.locationMessage) {
            body = '[Location]';
            metadata = {
                hasMedia: false,
                latitude: message.locationMessage.degreesLatitude,
                longitude: message.locationMessage.degreesLongitude,
                name: message.locationMessage.name || null,
                address: message.locationMessage.address || null
            };
        }
        // Handle contact messages
        else if (message.contactMessage) {
            body = '[Contact]';
            metadata = {
                hasMedia: false,
                contactName: message.contactMessage.displayName || null,
                contactNumber: message.contactMessage.vcard || null
            };
        }
        // Handle sticker messages
        else if (message.stickerMessage) {
            body = '[Sticker]';
            metadata = {
                hasMedia: true,
                mediaType: 'image/webp',
                mediaUrl: null,
                mediaPath: null,
                mediaMimetype: message.stickerMessage.mimetype || 'image/webp',
                mediaSize: message.stickerMessage.fileLength
            };
        }
        // Unknown message type
        else {
            body = '[Media or unsupported message type]';
            console.warn(`[${instanceId}] Unknown message type:`, Object.keys(message));
        }

        // Get message ID
        const messageId = messageData.key.id;

        // Get timestamp (from message data or current time)
        const timestamp = messageData.messageTimestamp 
            ? new Date(messageData.messageTimestamp * 1000).toISOString() 
            : null;

        // Send to Laravel
        await sendIncomingMessageToLaravel(instanceId, {
            from,
            to,
            body,
            messageId,
            timestamp,
            metadata
        });

        console.log(`[${instanceId}] ✓ Incoming message sent to Laravel: ${from} -> ${body.substring(0, 50)}`);

    } catch (error) {
        console.error(`[${instanceId}] ✗ Error handling incoming message:`, error.message);
        // Don't throw - we don't want to crash on message handling errors
    }
}

module.exports = { handleIncomingMessage };
```

### 3. Register Message Handler

Katika file yako ya socket connection (e.g., `src/instanceManager.js`):

```javascript
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const { handleIncomingMessage } = require('./handlers/messageHandler');

async function createSocketForInstance(instanceId, authState) {
    const sock = makeWASocket({
        auth: authState.state,
        printQRInTerminal: false, // We send QR to Laravel instead
        logger: pino({ level: 'silent' }), // Or your logger
    });

    // Handle incoming messages
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        if (type === 'notify') {
            for (const messageData of messages) {
                // Only process new messages (not old ones from history)
                if (messageData.key.fromMe) {
                    continue; // Skip own messages
                }

                await handleIncomingMessage(sock, instanceId, messageData);
            }
        }
    });

    // Handle connection events
    sock.ev.on('connection.update', (update) => {
        // ... your connection handling code
    });

    return sock;
}
```

## Complete Example with Baileys

Hapa ni complete example ya jinsi ya ku-handle incoming messages:

```javascript
const { default: makeWASocket } = require('@whiskeysockets/baileys');
const axios = require('axios');

const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8000';
const INTERNAL_API_KEY = process.env.INTERNAL_API_KEY;

// Create socket for instance
const sock = makeWASocket({ /* your config */ });

// Listen for messages
sock.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type === 'notify') {
        for (const messageData of messages) {
            // Skip own messages
            if (messageData.key.fromMe) {
                continue;
            }
            
            const from = messageData.key.remoteJid;
            const instanceId = getInstanceIdFromSocket(sock); // Your function to get instance ID
            
            // Skip groups
            if (from.includes('@g.us') || from.includes('@lid')) {
                continue;
            }
            
            const message = messageData.message;
            let body = '';
            
            // Extract message body
            if (message.conversation) {
                body = message.conversation;
            } else if (message.extendedTextMessage) {
                body = message.extendedTextMessage.text || '';
            } else if (message.imageMessage) {
                body = message.imageMessage.caption || '[Image]';
            } else if (message.videoMessage) {
                body = message.videoMessage.caption || '[Video]';
            } else {
                body = '[Media or unsupported message type]';
            }
            
            // Get instance phone number
            const instancePhoneNumber = getInstancePhoneNumber(instanceId);
            const to = `${instancePhoneNumber}@s.whatsapp.net`;
            
            // Send to Laravel
            try {
                await axios.post(
                    `${LARAVEL_API_URL}/api/internal/messages`,
                    {
                        instance_id: instanceId,
                        from: from,
                        to: to,
                        message: body,
                        message_id: messageData.key.id,
                        timestamp: messageData.messageTimestamp 
                            ? new Date(messageData.messageTimestamp * 1000).toISOString() 
                            : null,
                        metadata: {}
                    },
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${INTERNAL_API_KEY}`,
                        }
                    }
                );
                
                console.log(`[${instanceId}] ✓ Message sent to Laravel: ${from}`);
            } catch (error) {
                console.error(`[${instanceId}] ✗ Error:`, error.response?.data || error.message);
            }
        }
    }
});
```

## Response Format

**Success (201 Created):**
```json
{
  "success": true,
  "data": {
    "message": {
      "id": 123,
      "user_id": 1,
      "instance_id": 1,
      "message_id": "3EB0123456789ABCDEF",
      "direction": "inbound",
      "status": "delivered",
      "to": "255798765432@s.whatsapp.net",
      "from": "255712345678@s.whatsapp.net",
      "body": "Hello! This is the message text",
      "metadata": {},
      "created_at": "2024-01-20T10:30:00.000000Z"
    }
  },
  "message": "Message stored successfully."
}
```

**Error (422 Validation Error):**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed.",
    "errors": {
      "instance_id": ["The instance id field is required."],
      "from": ["The from field is required."]
    }
  }
}
```

**Error (401 Unauthorized):**
```json
{
  "success": false,
  "error": {
    "code": "INVALID_INTERNAL_API_KEY",
    "message": "Invalid internal API key."
  }
}
```

## Important Notes

1. **Skip Group Messages**: Filter out group messages (`@g.us` or `@lid`) kabla ya kutuma kwenye Laravel.

2. **Skip Own Messages**: Don't send messages sent by the bot itself (`fromMe === true`).

3. **Error Handling**: Don't crash kama kutuma message ina-fail. Log error na continue processing.

4. **Message ID**: Use WhatsApp message ID kutoka Baileys kama unique identifier.

5. **Timestamp**: Convert Baileys timestamp (seconds) to ISO 8601 format kwa Laravel.

6. **Testing**: Test na real messages kutoka different contacts na different message types (text, image, video, etc.).

7. **Instance Phone Number**: You need to get the instance phone number kwa `to` field. This can be stored in your instance data or retrieved from Laravel.

## Environment Variables

Add kwenye whatsapp-service `.env`:

```env
LARAVEL_API_URL=http://localhost:8000
INTERNAL_API_KEY=your_internal_api_key_here
# OR
WHATSAPP_SERVICE_API_KEY=your_internal_api_key_here
```

Key hii inafaa kuwa sawa na key kwenye Laravel `.env`:

```env
WHATSAPP_SERVICE_API_KEY=your_internal_api_key_here
```

Au kwenye `config/services.php`:

```php
'whatsapp_service' => [
    'api_key' => env('WHATSAPP_SERVICE_API_KEY'),
],
```

## Testing

1. **Check Logs**: Look kwenye whatsapp-service logs - inaonyesha "✓ Incoming message sent to Laravel"
2. **Check Laravel Logs**: Look kwenye Laravel logs kwa errors
3. **Check Database**: Verify messages zina-save kwenye `messages` table
4. **Check Web UI**: Messages zinaonekana kwenye messages page

## Troubleshooting

**Problem:** "Invalid internal API key" error
- **Solution:** Check `INTERNAL_API_KEY` au `WHATSAPP_SERVICE_API_KEY` ina-match kati ya whatsapp-service na Laravel `.env` files

**Problem:** "Instance not found" error
- **Solution:** Check `instance_id` ni sahihi na ipo kwenye database

**Problem:** Messages hazionekani kwenye web
- **Solution:** 
  - Check messages zina-save kwenye database (check `messages` table)
  - Check `instance_id` ni sahihi
  - Check `from` na `to` fields zina correct JID format
  - Reload page ya messages

**Problem:** Group messages zinaonyeshwa
- **Solution:** Ensure kuwa unafilter out group messages (`@g.us` or `@lid`) kabla ya kutuma kwenye Laravel

**Problem:** Own messages zina-send kwenye Laravel
- **Solution:** Check kama `messageData.key.fromMe` ni `false` kabla ya kutuma
