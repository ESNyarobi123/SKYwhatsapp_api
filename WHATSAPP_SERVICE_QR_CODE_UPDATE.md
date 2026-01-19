# Mwongozo wa Ku-update WhatsApp Service kwa QR Code

Hii ni mwongozo wa jinsi ya ku-update Node.js WhatsApp service ili itumie QR code kama image (base64) badala ya ASCII art, na kutuma kwenye Laravel web interface.

## Hitaji

1. **Install QR Code Library**: Tumia `qrcode` npm package ku-generate QR code kama image
2. **Convert QR Code kuwa Base64**: Badilisha QR code kuwa base64 string
3. **Tuma kwenye Laravel API**: Tumia internal API endpoint ku-save QR code

## Hatua za 1: Install Dependencies

```bash
cd whatsapp-service
npm install qrcode
```

## Hatua za 2: Update Code yako

### 2.1 Import QR Code Library

Katika file yako ya ku-handle WhatsApp connection (kwa mfano `src/index.js` au file yako ya connection):

```javascript
const QRCode = require('qrcode');
```

### 2.2 Badilisha QR Code Generation

Badala ya ku-print QR code kama ASCII art, generate kama image na convert kuwa base64:

**KABLA (ASCII Art):**
```javascript
// Hii ni mfano wa code yako ya zamani
const qr = generateQRCode(qrString); // Ina-return ASCII art
console.log(qr); // Prints ASCII art
```

**BAADA (Base64 Image):**
```javascript
// Generate QR code kama base64 image
async function generateQRCodeBase64(qrString) {
    try {
        // Generate QR code kama base64 string (PNG format)
        const qrCodeBase64 = await QRCode.toDataURL(qrString, {
            errorCorrectionLevel: 'M',
            type: 'image/png',
            quality: 0.92,
            margin: 1,
            color: {
                dark: '#000000',  // QR code color
                light: '#FFFFFF'  // Background color
            },
            width: 300  // Size ya QR code image
        });
        
        return qrCodeBase64; // Returns: "data:image/png;base64,iVBORw0KGgo..."
    } catch (error) {
        console.error('Error generating QR code:', error);
        throw error;
    }
}
```

### 2.3 Tuma QR Code kwenye Laravel API

Wakati QR code ina-generate, tuma kwenye Laravel kupitia internal API:

```javascript
const axios = require('axios');

async function sendQRCodeToLaravel(instanceId, qrCodeBase64) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY; // Set hii kwenye .env
        
        // Extract base64 string peke yake (remove "data:image/png;base64," prefix)
        const base64String = qrCodeBase64.split(',')[1] || qrCodeBase64;
        
        const response = await axios.post(
            `${laravelUrl}/api/internal/instances/${instanceId}/qr`,
            {
                qr_code: base64String, // Tuma base64 string peke yake
                expires_at: new Date(Date.now() + 5 * 60 * 1000).toISOString() // 5 minutes from now
            },
            {
                headers: {
                    'Authorization': `Bearer ${internalApiKey}`,
                    'X-API-Key': internalApiKey, // Alternative header
                    'Content-Type': 'application/json'
                }
            }
        );
        
        if (response.data.success) {
            console.log(`QR code sent to Laravel for instance ${instanceId}`);
        } else {
            console.error('Failed to send QR code:', response.data);
        }
        
        return response.data;
    } catch (error) {
        console.error('Error sending QR code to Laravel:', error.response?.data || error.message);
        throw error;
    }
}
```

### 2.4 Integrate katika Connection Flow

Mfano kamili wa jinsi ya ku-integrate:

```javascript
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whapi/baileys');
const QRCode = require('qrcode');
const axios = require('axios');

async function initializeConnection(instanceId) {
    const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${instanceId}`);
    
    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: false, // Usi-print ASCII art
        logger: pino({ level: 'silent' }), // Optional: silence logs
    });
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            // Generate QR code kama base64 image
            try {
                const qrCodeBase64 = await QRCode.toDataURL(qr, {
                    errorCorrectionLevel: 'M',
                    type: 'image/png',
                    width: 300,
                    margin: 1
                });
                
                console.log(`QR code generated for instance ${instanceId}`);
                
                // Tuma kwenye Laravel
                await sendQRCodeToLaravel(instanceId, qrCodeBase64);
                
                // Optional: Print ASCII art kwa terminal (kwa debugging)
                // const qrTerminal = await QRCode.toString(qr, { type: 'terminal' });
                // console.log(qrTerminal);
                
            } catch (error) {
                console.error('Error processing QR code:', error);
            }
        }
        
        if (connection === 'close') {
            const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            
            if (shouldReconnect) {
                console.log(`Reconnecting instance ${instanceId}...`);
                initializeConnection(instanceId);
            }
        } else if (connection === 'open') {
            console.log(`Instance ${instanceId} connected successfully`);
            
            // Update status kwenye Laravel
            await updateInstanceStatus(instanceId, 'connected');
        }
    });
    
    sock.ev.on('creds.update', saveCreds);
    
    return sock;
}

async function updateInstanceStatus(instanceId, status, phoneNumber = null) {
    try {
        const laravelUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        const internalApiKey = process.env.INTERNAL_API_KEY;
        
        await axios.post(
            `${laravelUrl}/api/internal/instances/${instanceId}/status`,
            {
                status: status,
                phone_number: phoneNumber
            },
            {
                headers: {
                    'Authorization': `Bearer ${internalApiKey}`,
                    'X-API-Key': internalApiKey,
                    'Content-Type': 'application/json'
                }
            }
        );
    } catch (error) {
        console.error('Error updating instance status:', error.response?.data || error.message);
    }
}
```

## Hatua za 3: Environment Variables

Ongeza hizi kwenye `.env` file yako ya whatsapp-service:

```env
LARAVEL_URL=http://localhost:8000
INTERNAL_API_KEY=your-internal-api-key-here
```

**Muhimu**: `INTERNAL_API_KEY` lazima i-match na `WHATSAPP_SERVICE_API_KEY` kwenye Laravel `.env` file.

Kwenye Laravel `.env`:
```env
WHATSAPP_SERVICE_API_KEY=your-internal-api-key-here
```

## Hatua za 4: Testing

1. **Start Laravel server**: `php artisan serve`
2. **Start WhatsApp service**: `npm start`
3. **Create instance** kwenye web interface
4. **Click "Connect"** - QR code inaonyesha kiotomatiki kwenye modal
5. **Scan QR code** na WhatsApp yako
6. **Verify** kuwa connection ina-succeed

## Mfano wa Complete Code Structure

```
whatsapp-service/
├── src/
│   ├── index.js          # Main entry point
│   ├── connection.js     # Connection handler
│   └── api.js            # Laravel API client
├── .env                  # Environment variables
└── package.json          # Dependencies
```

**src/api.js** (Mfano):
```javascript
const axios = require('axios');

class LaravelAPI {
    constructor() {
        this.baseUrl = process.env.LARAVEL_URL || 'http://localhost:8000';
        this.apiKey = process.env.INTERNAL_API_KEY;
    }
    
    getHeaders() {
        return {
            'Authorization': `Bearer ${this.apiKey}`,
            'X-API-Key': this.apiKey,
            'Content-Type': 'application/json'
        };
    }
    
    async storeQRCode(instanceId, qrCodeBase64, expiresInMinutes = 5) {
        const base64String = qrCodeBase64.split(',')[1] || qrCodeBase64;
        const expiresAt = new Date(Date.now() + expiresInMinutes * 60 * 1000).toISOString();
        
        const response = await axios.post(
            `${this.baseUrl}/api/internal/instances/${instanceId}/qr`,
            {
                qr_code: base64String,
                expires_at: expiresAt
            },
            { headers: this.getHeaders() }
        );
        
        return response.data;
    }
    
    async updateInstanceStatus(instanceId, status, phoneNumber = null) {
        const payload = { status };
        if (phoneNumber) payload.phone_number = phoneNumber;
        
        const response = await axios.post(
            `${this.baseUrl}/api/internal/instances/${instanceId}/status`,
            payload,
            { headers: this.getHeaders() }
        );
        
        return response.data;
    }
}

module.exports = new LaravelAPI();
```

**src/connection.js** (Mfano):
```javascript
const { default: makeWASocket, useMultiFileAuthState } = require('@whapi/baileys');
const QRCode = require('qrcode');
const api = require('./api');

async function createConnection(instanceId) {
    const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${instanceId}`);
    
    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
    });
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            try {
                // Generate QR code kama base64
                const qrCodeBase64 = await QRCode.toDataURL(qr, {
                    errorCorrectionLevel: 'M',
                    type: 'image/png',
                    width: 300,
                    margin: 1
                });
                
                console.log(`[${instanceId}] QR code generated`);
                
                // Tuma kwenye Laravel
                await api.storeQRCode(instanceId, qrCodeBase64);
                
            } catch (error) {
                console.error(`[${instanceId}] QR code error:`, error);
            }
        }
        
        if (connection === 'open') {
            console.log(`[${instanceId}] Connected`);
            await api.updateInstanceStatus(instanceId, 'connected');
        }
    });
    
    sock.ev.on('creds.update', saveCreds);
    
    return sock;
}

module.exports = { createConnection };
```

## Notes Muhimu

1. **QR Code Format**: Laravel ina-expect base64 string peke yake (bila "data:image/png;base64," prefix), lakini web interface ina-handle both formats.

2. **QR Code Expiration**: QR codes zina-expire baada ya dakika 5. Ikiwa user haja-scan, QR code mpya ita-generate automatically.

3. **Error Handling**: Always handle errors kwa uangalifu. Ikiwa kutuma QR code kwenye Laravel ina-fail, bado print kwenye terminal kwa debugging.

4. **Security**: Hakikisha `INTERNAL_API_KEY` ni secret na haija-commit kwenye git.

5. **Testing**: Test kwa makini kabla ya ku-deploy production.

## Troubleshooting

**Problem**: QR code haionekani kwenye web
- **Solution**: Angalia kuwa `INTERNAL_API_KEY` ina-match kwenye both Laravel na Node.js `.env` files

**Problem**: QR code inaonekana kama ASCII art
- **Solution**: Hakikisha unatumia `QRCode.toDataURL()` na si `QRCode.toString()`

**Problem**: API call ina-fail
- **Solution**: Angalia network connectivity, Laravel URL, na API key

**Problem**: QR code haisacan
- **Solution**: Hakikisha unatumia base64 string peke yake (bila data URL prefix) au data URL kamili
