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
    'api_url'     => 'https://orange.ericksky.online/api/v1',
    'api_key'     => 'sk_ozv68TatDd5NLDpr18vZJATsfh6Rl6uYAC5ouAv7FNmjlq14', // API Key yako
    'instance_id' => 13,                                         // Instance ID yako
];

// ============================================
// INITIALIZE API CLIENT
// ============================================

$api = new SkyWhatsAppAPI(
    $config['api_url'],
    $config['api_key'],
    $config['instance_id']
);

// ============================================
// EXAMPLE 1: Send a Message
// ============================================

echo "📤 Sending Message...\n";
echo str_repeat('-', 50) . "\n";

$result = $api->sendMessage(
    '255712345678',           // Phone number (with country code)
    'Hello! Hii ni test message kutoka SKY WhatsApp API.'
);

if ($result['success']) {
    echo "✅ Message sent successfully!\n";
    echo "Message ID: " . $result['data']['message']['id'] . "\n";
    echo "Status: " . $result['data']['message']['status'] . "\n";
} else {
    echo "❌ Failed to send message!\n";
    echo "Error: " . ($result['error']['message'] ?? 'Unknown error') . "\n";
    echo "Code: " . ($result['error']['code'] ?? 'N/A') . "\n";
    
    // Debug info
    if ($api->getLastError()) {
        echo "Debug: " . $api->getLastError() . "\n";
    }
}

echo "\n";

// ============================================
// EXAMPLE 2: Get Instances
// ============================================

echo "📱 Getting Instances...\n";
echo str_repeat('-', 50) . "\n";

$instances = $api->getInstances();

if ($instances['success']) {
    echo "✅ Instances retrieved!\n";
    foreach ($instances['data']['instances'] ?? $instances['data'] as $instance) {
        if (is_array($instance)) {
            echo "  - ID: {$instance['id']}, Name: {$instance['name']}, Status: {$instance['status']}\n";
        }
    }
} else {
    echo "❌ Failed to get instances: " . ($instances['error']['message'] ?? 'Unknown') . "\n";
}

echo "\n";

// ============================================
// EXAMPLE 3: Get Messages
// ============================================

echo "📨 Getting Message History...\n";
echo str_repeat('-', 50) . "\n";

$messages = $api->getMessages(10);  // Get last 10 messages

if ($messages['success']) {
    echo "✅ Messages retrieved!\n";
    $messageList = $messages['data']['messages'] ?? [];
    echo "Total: " . count($messageList) . " messages\n";
} else {
    echo "❌ Failed to get messages: " . ($messages['error']['message'] ?? 'Unknown') . "\n";
}

echo "\n";

// ============================================
// EXAMPLE 4: Get Usage Stats
// ============================================

echo "📊 Getting Usage Statistics...\n";
echo str_repeat('-', 50) . "\n";

$usage = $api->getUsage();

if ($usage['success']) {
    echo "✅ Usage stats retrieved!\n";
    print_r($usage['data'] ?? $usage);
} else {
    echo "❌ Failed to get usage: " . ($usage['error']['message'] ?? 'Unknown') . "\n";
}

echo "\n";

// ============================================
// EXAMPLE 5: Manage Webhooks
// ============================================

echo "🔗 Managing Webhooks...\n";
echo str_repeat('-', 50) . "\n";

// 1. Create Webhook
$webhookUrl = 'https://webhook.site/YOUR-UNIQUE-ID'; // Badilisha hii na URL yako halisi
echo "Creating webhook for URL: $webhookUrl\n";

$webhook = $api->createWebhook($webhookUrl, ['message.inbound', 'message.status']);

if ($webhook['success']) {
    echo "✅ Webhook created! ID: " . $webhook['data']['webhook']['id'] . "\n";
    echo "Secret: " . $webhook['data']['webhook']['secret'] . " (Save this!)\n";
} else {
    echo "❌ Failed to create webhook: " . ($webhook['error']['message'] ?? 'Unknown') . "\n";
}

// 2. List Webhooks
echo "\nListing webhooks...\n";
$webhooks = $api->getWebhooks();

if ($webhooks['success']) {
    foreach ($webhooks['data']['webhooks'] ?? [] as $wh) {
        echo "  - ID: {$wh['id']}, URL: {$wh['url']}, Events: " . implode(', ', $wh['events']) . "\n";
    }
}

echo "\n";
echo "✨ Done!\n";
