<?php
/**
 * Test Webhook Connectivity
 * 
 * Hii script inajaribu kutuma data kwenda webhook.php
 * ili kuona kama server inaweza kujifikia yenyewe.
 */

$webhookUrl = 'https://orange.ericksky.online/integration/webhook.php'; // Public URL

// Data ya kutuma (Simulated WhatsApp Message)
$data = [
    'event' => 'message.inbound',
    'data' => [
        'id' => 'TEST_' . uniqid(),
        'from' => '255700000000',
        'to' => '255712345678',
        'body' => 'Hii ni TEST message (Public URL + IPv4).',
        'timestamp' => time()
    ]
];

echo "<h1>Webhook Connectivity Test (Public URL + IPv4)</h1>";
echo "<p>Target URL: <strong>$webhookUrl</strong></p>";

// Initialize cURL
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL for test
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // Force IPv4 (Solution that worked for API)

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h2>Results:</h2>";

if ($error) {
    echo "<p style='color: red;'>❌ cURL Error: $error</p>";
    echo "<p>Hii inamaanisha server haiwezi ku-connect kwenye URL hiyo.</p>";
} else {
    echo "<p>HTTP Status Code: <strong>$httpCode</strong></p>";
    echo "<p>Response: <pre>" . htmlspecialchars($response) . "</pre></p>";
    
    if ($httpCode == 200) {
        echo "<p style='color: green;'>✅ Success! Webhook imefikiwa.</p>";
        echo "<p>Sasa angalia kwenye <strong>chat.php</strong> kama hii message imetokea.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Webhook ilifikiwa lakini ilirudisha error.</p>";
    }
}
