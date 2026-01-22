# SKY WhatsApp API - PHP Integration

## 📋 Quick Start

### 1. Copy Files
Copy folder `integration/` kwenye project yako.

### 2. Include Class
```php
<?php
require_once 'path/to/SkyWhatsAppAPI.php';
```

### 3. Initialize
```php
$api = new SkyWhatsAppAPI(
    'https://orange.ericksky.online/api/v1',  // API URL
    'sk_your_api_key_here',                    // API Key
    11                                          // Instance ID
);
```

### 4. Send Message
```php
$result = $api->sendMessage('255712345678', 'Hello World!');

if ($result['success']) {
    echo "Message sent! ID: " . $result['data']['message']['id'];
} else {
    echo "Error: " . $result['error']['message'];
}
```

---

## 🔑 Siri ya Ku-avoid 404 Error

Headers hizi ni **MUHIMU**:

```php
$headers = [
    'Authorization: Bearer YOUR_API_KEY',  // ✅ REQUIRED
    'Content-Type: application/json',       // ✅ REQUIRED
    'Accept: application/json',             // ✅ MUHIMU SANA!
];
```

**Bila `Accept: application/json`**, server inaweza kurudisha HTML badala ya JSON = 404!

---

## 📚 Available Methods

| Method | Description |
|--------|-------------|
| `sendMessage($to, $body)` | Send WhatsApp message |
| `getMessages($perPage)` | Get message history |
| `getMessage($id)` | Get single message |
| `getInstances()` | List all instances |
| `getInstance($id)` | Get single instance |
| `getUsage()` | Get API usage stats |
| `getLastError()` | Get last error message |

---

## ⚠️ Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| 401 | Invalid API Key | Check API key is correct |
| 403 | No permission | Check subscription/limits |
| 404 | Wrong URL or missing headers | Add `Accept: application/json` |
| 400 | Invalid data | Check instance_id and phone format |

---

## 🧪 Test Your Setup

Run example script:
```bash
php integration/example.php
```
