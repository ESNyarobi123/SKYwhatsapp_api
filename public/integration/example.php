<?php
/**
 * SKY WhatsApp API - Usage Example
 * 
 * Hii script inaonyesha jinsi ya kutumia SkyWhatsAppAPI class
 * ku-send messages kupitia API.
 */

require_once __DIR__ . '/SkyWhatsAppAPI.php';

// ============================================
// CONFIGURATION - Badilisha hizi values!
// ============================================

$config = [
    'api_url'     => 'https://127.0.0.1/api/v1',                 // Use HTTPS Localhost (Fix for Webuzo/Apache)
    'api_key'     => 'sk_ozv68TatDd5NLDpr18vZJATsfh6Rl6uYAC5ouAv7FNmjlq14', // API Key yako
    'instance_id' => 13,                                         // Instance ID yako
];

// Check if running in browser
$isBrowser = !defined('STDIN');

if ($isBrowser) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SKY WhatsApp API Integration Test</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { background-color: #0f172a; color: #e2e8f0; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
            .card { background-color: #1e293b; border: 1px solid #334155; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem; }
            .success { color: #4ade80; }
            .error { color: #f87171; }
            .info { color: #60a5fa; }
            .warning { color: #fbbf24; }
            pre { background-color: #0f172a; padding: 1rem; border-radius: 0.375rem; overflow-x: auto; margin-top: 0.5rem; border: 1px solid #334155; }
        </style>
    </head>
    <body class="p-8 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-white border-b border-gray-700 pb-4">🚀 SKY WhatsApp API Integration Test</h1>';
}

function logOutput($title, $content, $type = 'info') {
    global $isBrowser;
    if ($isBrowser) {
        $colorClass = match($type) {
            'success' => 'success',
            'error' => 'error',
            'warning' => 'warning',
            default => 'info'
        };
        echo "<div class='card'>
                <h2 class='text-xl font-semibold mb-2 {$colorClass}'>{$title}</h2>
                <div class='text-sm opacity-90'>";
        if (is_array($content) || is_object($content)) {
            echo "<pre>" . htmlspecialchars(print_r($content, true)) . "</pre>";
        } else {
            echo nl2br(htmlspecialchars($content));
        }
        echo "</div></div>";
    } else {
        echo "\n" . strtoupper($title) . "\n";
        echo str_repeat('-', 50) . "\n";
        if (is_array($content) || is_object($content)) {
            print_r($content);
        } else {
            echo $content . "\n";
        }
        echo "\n";
    }
}

// ============================================
// INITIALIZE API CLIENT
// ============================================

try {
    $api = new SkyWhatsAppAPI(
        $config['api_url'],
        $config['api_key'],
        $config['instance_id']
    );
    
    // FORCE HOST HEADER (Fix for 404 Loopback Error)
    $api->addHeader('Host', 'orange.ericksky.online');
    
    logOutput("API Client Initialized", "URL: {$config['api_url']}\nInstance ID: {$config['instance_id']}\nHost Header: orange.ericksky.online", 'success');
} catch (Exception $e) {
    logOutput("Initialization Error", $e->getMessage(), 'error');
    exit;
}

// ============================================
// EXAMPLE 1: Send a Message
// ============================================

$result = $api->sendMessage(
    '255712345678',           // Phone number (with country code)
    'Hello! Hii ni test message kutoka SKY WhatsApp API.'
);

if ($result['success']) {
    logOutput("📤 Send Message Result", [
        'Status' => 'Success ✅',
        'Message ID' => $result['data']['message']['id'],
        'Status' => $result['data']['message']['status']
    ], 'success');
} else {
    logOutput("📤 Send Message Failed", [
        'Error' => $result['error']['message'] ?? 'Unknown error',
        'Code' => $result['error']['code'] ?? 'N/A',
        'Debug' => $api->getLastError(),
        'Raw Response' => htmlspecialchars(substr($result['raw_response'] ?? '', 0, 500)) // Show first 500 chars
    ], 'error');
}

// ============================================
// EXAMPLE 2: Get Instances
// ============================================

$instances = $api->getInstances();

if ($instances['success']) {
    $list = [];
    foreach ($instances['data']['instances'] ?? $instances['data'] as $instance) {
        if (is_array($instance)) {
            $list[] = "ID: {$instance['id']} | Name: {$instance['name']} | Status: {$instance['status']}";
        }
    }
    logOutput("📱 Instances List", implode("\n", $list), 'success');
} else {
    logOutput("📱 Get Instances Failed", $instances['error']['message'] ?? 'Unknown', 'error');
}

// ============================================
// EXAMPLE 3: Get Messages
// ============================================

$messages = $api->getMessages(5);  // Get last 5 messages

if ($messages['success']) {
    $messageList = $messages['data']['messages'] ?? [];
    logOutput("📨 Recent Messages", "Retrieved " . count($messageList) . " messages successfully.", 'success');
} else {
    logOutput("📨 Get Messages Failed", $messages['error']['message'] ?? 'Unknown', 'error');
}

// ============================================
// EXAMPLE 4: Get Usage Stats
// ============================================

$usage = $api->getUsage();

if ($usage['success']) {
    logOutput("📊 Usage Statistics", $usage['data'] ?? $usage, 'success');
} else {
    logOutput("📊 Get Usage Failed", $usage['error']['message'] ?? 'Unknown', 'error');
}

// ============================================
// EXAMPLE 5: Manage Webhooks
// ============================================

// 1. Create Webhook (Commented out to avoid creating duplicates on every run)
/*
$webhookUrl = 'https://webhook.site/YOUR-UNIQUE-ID'; 
$webhook = $api->createWebhook($webhookUrl, ['message.inbound', 'message.status']);

if ($webhook['success']) {
    logOutput("🔗 Webhook Created", [
        'ID' => $webhook['data']['webhook']['id'],
        'Secret' => $webhook['data']['webhook']['secret']
    ], 'success');
} else {
    logOutput("🔗 Create Webhook Failed", $webhook['error']['message'] ?? 'Unknown', 'error');
}
*/

// 2. List Webhooks
$webhooks = $api->getWebhooks();

if ($webhooks['success']) {
    $whList = [];
    foreach ($webhooks['data']['webhooks'] ?? [] as $wh) {
        $whList[] = "ID: {$wh['id']} | URL: {$wh['url']} | Events: " . implode(', ', $wh['events']);
    }
    logOutput("🔗 Active Webhooks", empty($whList) ? "No webhooks found." : implode("\n", $whList), 'success');
} else {
    logOutput("🔗 List Webhooks Failed", $webhooks['error']['message'] ?? 'Unknown', 'error');
}

if ($isBrowser) {
    echo '<div class="mt-8 text-center text-gray-500 text-sm">
            <p>End of Test Execution</p>
          </div>
    </body>
    </html>';
} else {
    echo "\n✨ Done!\n";
}
