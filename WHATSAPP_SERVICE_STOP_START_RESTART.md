# Mwongozo: WhatsApp Service - Stop, Start, na Restart Instances

Hii ni mwongozo wa jinsi ya ku-handle stop, start, na restart za instances kwenye WhatsApp service.

## Muhimu: Messages Hazipotezi! ✅

**Messages zote zina-baki kwenye database** - hakuna kitu kinapotea. Hii ni muhimu sana kwa user experience.

## Status Flow

```
disconnected → connecting → connected
     ↑              ↓
     └──────────────┘
    (stop/restart)
```

## 1. Stop Instance

**Laravel Action:**
- Status ina-badilika kuwa `disconnected`
- QR code ina-clear (set kuwa `null`)
- **Session data ina-baki** (hii ni muhimu!)
- Messages zote zina-baki kwenye database

**WhatsApp Service Action:**
```javascript
async function stopInstance(instanceId) {
    try {
        // Get socket for this instance
        const sock = instanceSockets.get(instanceId);
        
        if (sock) {
            // End connection gracefully
            await sock.end();
            
            // Remove from active connections
            instanceSockets.delete(instanceId);
            
            console.log(`Instance ${instanceId} stopped successfully`);
        }
    } catch (error) {
        console.error(`Error stopping instance ${instanceId}:`, error);
    }
}
```

## 2. Start Instance

**Laravel Action:**
- Status ina-badilika kuwa `connecting`
- Ikiwa instance ina `session_data`:
  - Try ku-reconnect na existing session
  - No QR code needed
- Ikiwa instance haina `session_data`:
  - Generate new QR code
  - User anahitaji ku-scan tena

**WhatsApp Service Action:**

```javascript
async function startInstance(instanceId, instanceData) {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${instanceId}`);
        
        // Check if we have existing session
        const hasSession = instanceData.session_data !== null;
        
        if (hasSession) {
            // Try to reconnect with existing session
            console.log(`Reconnecting instance ${instanceId} with existing session...`);
            
            const sock = makeWASocket({
                auth: state,
                printQRInTerminal: false,
            });
            
            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect } = update;
                
                if (connection === 'open') {
                    console.log(`Instance ${instanceId} reconnected successfully`);
                    
                    // Update status to connected
                    await updateInstanceStatus(instanceId, 'connected');
                    
                    // Store socket
                    instanceSockets.set(instanceId, sock);
                } else if (connection === 'close') {
                    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    
                    if (!shouldReconnect) {
                        // Session expired, need new QR
                        console.log(`Instance ${instanceId} session expired, generating new QR...`);
                        await clearSessionData(instanceId);
                        await generateNewQR(instanceId);
                    }
                }
            });
            
            sock.ev.on('creds.update', saveCreds);
            
        } else {
            // No session, generate new QR code
            console.log(`Starting instance ${instanceId} without session, generating QR...`);
            await generateNewQR(instanceId);
        }
        
    } catch (error) {
        console.error(`Error starting instance ${instanceId}:`, error);
        await updateInstanceStatus(instanceId, 'error');
    }
}

async function generateNewQR(instanceId) {
    const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${instanceId}`);
    
    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
    });
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            // Generate and send QR code to Laravel
            const qrCodeBase64 = await QRCode.toDataURL(qr, {
                type: 'image/png',
                width: 300
            });
            
            await sendQRCodeToLaravel(instanceId, qrCodeBase64);
        }
        
        if (connection === 'open') {
            console.log(`Instance ${instanceId} connected`);
            await updateInstanceStatus(instanceId, 'connected');
            instanceSockets.set(instanceId, sock);
        }
    });
    
    sock.ev.on('creds.update', saveCreds);
}
```

## 3. Restart Instance

**Laravel Action:**
1. Stop instance (status → `disconnected`, clear QR)
2. Start instance (status → `connecting`)
3. Ikiwa ina session, reconnect
4. Ikiwa haina session, generate QR

**WhatsApp Service Action:**

```javascript
async function restartInstance(instanceId) {
    try {
        // Stop first
        await stopInstance(instanceId);
        
        // Wait a bit for cleanup
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Get fresh instance data
        const instanceData = await getInstanceData(instanceId);
        
        // Start again
        await startInstance(instanceId, instanceData);
        
        console.log(`Instance ${instanceId} restarted successfully`);
    } catch (error) {
        console.error(`Error restarting instance ${instanceId}:`, error);
    }
}
```

## Complete Implementation Example

```javascript
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whapi/baileys');
const QRCode = require('qrcode');
const axios = require('axios');

class InstanceManager {
    constructor() {
        this.instanceSockets = new Map(); // instanceId -> socket
    }
    
    async stopInstance(instanceId) {
        const sock = this.instanceSockets.get(instanceId);
        
        if (sock) {
            try {
                await sock.end();
                this.instanceSockets.delete(instanceId);
                console.log(`[${instanceId}] Stopped`);
            } catch (error) {
                console.error(`[${instanceId}] Stop error:`, error);
            }
        }
    }
    
    async startInstance(instanceId, instanceData) {
        try {
            const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${instanceId}`);
            
            const hasSession = instanceData.session_data !== null;
            
            const sock = makeWASocket({
                auth: state,
                printQRInTerminal: false,
            });
            
            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;
                
                if (qr && !hasSession) {
                    // Generate QR code
                    const qrCodeBase64 = await QRCode.toDataURL(qr, {
                        type: 'image/png',
                        width: 300
                    });
                    
                    await this.sendQRCodeToLaravel(instanceId, qrCodeBase64);
                }
                
                if (connection === 'open') {
                    console.log(`[${instanceId}] Connected`);
                    await this.updateInstanceStatus(instanceId, 'connected');
                    this.instanceSockets.set(instanceId, sock);
                } else if (connection === 'close') {
                    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    
                    if (!shouldReconnect) {
                        // Session expired
                        await this.clearSessionData(instanceId);
                        await this.startInstance(instanceId, { session_data: null });
                    }
                }
            });
            
            sock.ev.on('creds.update', saveCreds);
            
        } catch (error) {
            console.error(`[${instanceId}] Start error:`, error);
            await this.updateInstanceStatus(instanceId, 'error');
        }
    }
    
    async restartInstance(instanceId) {
        await this.stopInstance(instanceId);
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const instanceData = await this.getInstanceData(instanceId);
        await this.startInstance(instanceId, instanceData);
    }
    
    async sendQRCodeToLaravel(instanceId, qrCodeBase64) {
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            const base64String = qrCodeBase64.split(',')[1] || qrCodeBase64;
            
            await axios.post(
                `${laravelUrl}/api/internal/instances/${instanceId}/qr`,
                {
                    qr_code: base64String,
                    expires_at: new Date(Date.now() + 5 * 60 * 1000).toISOString()
                },
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
        } catch (error) {
            console.error(`[${instanceId}] QR send error:`, error);
        }
    }
    
    async updateInstanceStatus(instanceId, status, phoneNumber = null) {
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            await axios.post(
                `${laravelUrl}/api/internal/instances/${instanceId}/status`,
                {
                    status,
                    phone_number: phoneNumber
                },
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
        } catch (error) {
            console.error(`[${instanceId}] Status update error:`, error);
        }
    }
    
    async getInstanceData(instanceId) {
        try {
            const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
            const internalApiKey = process.env.INTERNAL_API_KEY;
            
            const response = await axios.get(
                `${laravelUrl}/api/internal/instances/${instanceId}/connect`,
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
            
            return response.data.data.instance;
        } catch (error) {
            console.error(`[${instanceId}] Get instance error:`, error);
            return null;
        }
    }
    
    async clearSessionData(instanceId) {
        // Clear session files
        const fs = require('fs');
        const path = require('path');
        
        const authDir = path.join(__dirname, `auth_info_${instanceId}`);
        if (fs.existsSync(authDir)) {
            fs.rmSync(authDir, { recursive: true, force: true });
        }
    }
}

// Polling for instance status changes
async function pollInstanceStatusChanges() {
    const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
    const internalApiKey = process.env.INTERNAL_API_KEY;
    
    setInterval(async () => {
        try {
            // Get all instances
            const response = await axios.get(
                `${laravelUrl}/api/internal/instances/pending`,
                {
                    headers: {
                        'Authorization': `Bearer ${internalApiKey}`,
                        'X-API-Key': internalApiKey
                    }
                }
            );
            
            const instances = response.data.data.instances || [];
            
            for (const instance of instances) {
                const instanceId = instance.id;
                const status = instance.status;
                const currentSocket = instanceManager.instanceSockets.get(instanceId);
                
                // Handle status changes
                if (status === 'connecting' && !currentSocket) {
                    // Start instance
                    await instanceManager.startInstance(instanceId, instance);
                } else if (status === 'disconnected' && currentSocket) {
                    // Stop instance
                    await instanceManager.stopInstance(instanceId);
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 5000); // Poll every 5 seconds
}

const instanceManager = new InstanceManager();

// Start polling
pollInstanceStatusChanges();
```

## Key Points

1. **Messages Hazipotezi**: Messages zote zina-baki kwenye database, hata baada ya stop/restart

2. **Session Data**: 
   - Ikiwa instance ina session_data, inaweza ku-reconnect automatically
   - Ikiwa haina session_data, ina-generate QR code mpya

3. **QR Code**:
   - Ina-clear wakati wa stop
   - Ina-generate tena wakati wa start (ikiwa haina session)
   - Ina-generate tena wakati wa restart (ikiwa haina session)

4. **Status Management**:
   - `disconnected` → Instance ime-stop
   - `connecting` → Instance ina-try ku-connect
   - `connected` → Instance ime-connect successfully

5. **Polling**: WhatsApp service ina-poll Laravel kwa status changes kila 5 seconds

## Testing

1. **Test Stop**:
   - Stop connected instance
   - Verify status changes to `disconnected`
   - Verify messages zina-baki

2. **Test Start (with session)**:
   - Start stopped instance (with session_data)
   - Verify reconnects automatically
   - No QR code needed

3. **Test Start (without session)**:
   - Start stopped instance (without session_data)
   - Verify QR code ina-generate
   - Scan QR code
   - Verify connects

4. **Test Restart**:
   - Restart connected instance
   - Verify stops then starts
   - Verify messages zina-baki
