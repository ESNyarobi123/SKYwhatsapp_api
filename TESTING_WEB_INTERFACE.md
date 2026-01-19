# Mwongozo wa Kujaribu Web Interface na WhatsApp Service

## Hatua za Kujaribu Web Interface

### 1. Login kwenye Web

1. **Fungua browser** na nenda kwenye:
   ```
   https://food.hosting.hollyn.online
   ```

2. **Login** kwa account yako (au register kama huna account)

3. **Nenda kwenye Dashboard:**
   ```
   https://food.hosting.hollyn.online/dashboard
   ```

### 2. Hakikisha Una Active Subscription

Kabla ya kuunda instance, hakikisha una active subscription:

1. Nenda kwenye **Dashboard**
2. Chagua package na subscribe
3. Complete payment process

**Kumbuka:** Bila active subscription, huwezi kuunda instances.

### 3. Create Instance (Kuunda Instance)

1. **Nenda kwenye Instances page:**
   ```
   https://food.hosting.hollyn.online/dashboard/instances
   ```

2. **Click button "Create Instance"** (au "Create Your First Instance" kama huna instances)

3. **Jaza form:**
   - **Instance Name:** Weka jina (mfano: "My WhatsApp Bot")
   - **Phone Number (Optional):** Weka namba ya simu (optional)

4. **Click "Create"**

5. **Angalia:** Instance itaonekana kwenye list na status itakuwa "disconnected"

### 4. Connect Instance (Kuunganisha Instance)

1. **Kwenye instances list**, utaona instance uliyo-unda

2. **Click button "Connect"** kwenye instance yako

3. **Nini kinatokea:**
   - Instance status itabadilika kuwa "connecting"
   - WhatsApp Service (Node.js) ita-poll na ku-detect instance hii
   - WhatsApp Service ita-generate QR code
   - QR code ita-save kwenye database

4. **Angalia status:**
   - Baada ya sekunde 5-10, status inaweza kuwa "connecting" na QR code itakuwa available

### 5. View QR Code (Kuona QR Code)

1. **Baada ya ku-click "Connect"**, button "Show QR" itaonekana

2. **Click "Show QR"**

3. **QR Code itaonekana** kwenye modal

4. **Scan QR Code** kwa WhatsApp yako:
   - Fungua WhatsApp kwenye simu yako
   - Nenda kwenye **Settings** → **Linked Devices** → **Link a Device**
   - Scan QR code kutoka kwenye web

5. **Baada ya ku-scan:**
   - Instance status itabadilika kuwa "connected"
   - WhatsApp Service ita-update status automatically

### 6. Verify Integration (Kuthibitisha Integration)

#### A. Check WhatsApp Service Logs

Angalia terminal ya whatsapp-service, utaona:

```
[INFO]: Started polling for pending connections
[INFO]: Initializing connection for instance {id}
[INFO]: QR code generated for instance {id}
[INFO]: Instance {id} connected successfully
```

**Kama unaona errors**, hakikisha:
- API key ime-set correctly kwenye production server
- WhatsApp Service ina-connect kwenye correct Laravel API URL

#### B. Check Web Interface

1. **Refresh page** ya instances (`/dashboard/instances`)

2. **Angalia:**
   - Instance status inaweza kuwa "connected"
   - "Last connected" time inaonekana
   - Button "Connect" haipo tena (kwa sababu tayari connected)

#### C. Test API Endpoints

Unaweza test API endpoints moja kwa moja:

1. **Get Pending Instances (Internal API):**
   ```bash
   curl -X GET "https://food.hosting.hollyn.online/api/internal/instances/pending" \
     -H "Authorization: Bearer sk_3R2qNWn2KuXFXPNMeuAmj2nk3eybtONbY7VrJqbEujLgHYZd" \
     -H "X-API-Key: sk_3R2qNWn2KuXFXPNMeuAmj2nk3eybtONbY7VrJqbEujLgHYZd"
   ```

2. **Get Instance QR Code:**
   ```bash
   curl -X GET "https://food.hosting.hollyn.online/api/instances/{instance_id}/qr" \
     -H "Cookie: {your_session_cookie}"
   ```

### 7. Test Complete Flow

**Complete Testing Flow:**

1. ✅ **Create Instance** → Instance created successfully
2. ✅ **Click Connect** → Status changes to "connecting"
3. ✅ **WhatsApp Service detects** → Logs show "Initializing connection"
4. ✅ **QR Code generated** → "Show QR" button appears
5. ✅ **Scan QR Code** → WhatsApp connects
6. ✅ **Status updates** → Changes to "connected"
7. ✅ **Web shows connected** → Status badge shows "Connected"

### 8. Troubleshooting

#### Problem: Instance haiconnect

**Solutions:**
1. Hakikisha WhatsApp Service ina-run (`npm start`)
2. Check logs ya WhatsApp Service kwa errors
3. Verify API key ime-set correctly kwenye production
4. Check kama una active subscription

#### Problem: QR Code haionekani

**Solutions:**
1. Wait sekunde 5-10 baada ya ku-click "Connect"
2. Refresh page na u-click "Show QR" tena
3. Check WhatsApp Service logs kwa errors
4. Verify instance status ni "connecting"

#### Problem: Status haibadilike kuwa "connected"

**Solutions:**
1. Hakikisha ume-scan QR code correctly
2. Check WhatsApp Service logs
3. Verify WhatsApp Service ina-update status kwenye Laravel
4. Refresh web page

#### Problem: "SUBSCRIPTION_REQUIRED" error

**Solutions:**
1. Nenda kwenye Dashboard
2. Subscribe kwa package
3. Complete payment
4. Try kuunda instance tena

### 9. Expected Behavior

**Kama kila kitu kinafanya kazi:**

✅ Web interface ina-show instances correctly
✅ Unaweza create instances
✅ Unaweza connect instances
✅ QR codes zina-generate na kuonekana
✅ WhatsApp Service ina-detect pending connections
✅ Status updates automatically
✅ Integration kati ya Web → Laravel → WhatsApp Service inafanya kazi

### 10. API Functions Available kwenye Web

Web interface ina access kwa:

- ✅ **Create Instance** (`POST /api/instances`)
- ✅ **Connect Instance** (`POST /api/instances/{id}/connect`)
- ✅ **Get QR Code** (`GET /api/instances/{id}/qr`)
- ✅ **Delete Instance** (`DELETE /api/instances/{id}`)
- ✅ **List Instances** (`GET /dashboard/instances`)

**Internal API (kwa WhatsApp Service):**
- ✅ **Get Pending Connections** (`GET /api/internal/instances/pending`)
- ✅ **Get Instance for Connection** (`GET /api/internal/instances/{id}/connect`)
- ✅ **Store QR Code** (`POST /api/internal/instances/{id}/qr`)
- ✅ **Update Status** (`POST /api/internal/instances/{id}/status`)
- ✅ **Get Pending Messages** (`GET /api/internal/messages/pending`)

## Summary

Kama unaona:
- ✅ No errors kwenye WhatsApp Service logs
- ✅ Instances zinaonekana kwenye web
- ✅ Unaweza create na connect instances
- ✅ QR codes zinaonekana
- ✅ Status updates correctly

**Hii inamaanisha integration yote inafanya kazi kikamilifu!** 🎉
