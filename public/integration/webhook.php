<?php
/**
 * SKY WhatsApp API - Simple Webhook Handler
 * 
 * Hii script inapokea data kutoka kwa API (Webhook) na kuzisave
 * kwenye file ili ziweze kuonekana kwenye UI.
 */

// Ruhusu requests zote (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Pokea data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log file paths
$logFile = __DIR__ . '/webhook_messages.json';
$debugFile = __DIR__ . '/webhook_debug.txt';

// 1. LOG RAW DATA (Kwa ajili ya debugging)
file_put_contents($debugFile, date('[Y-m-d H:i:s] ') . $input . "\n\n", FILE_APPEND);

if ($data) {
    // Soma messages zilizopo
    $currentMessages = [];
    if (file_exists($logFile)) {
        $currentMessages = json_decode(file_get_contents($logFile), true) ?? [];
    }
    
    // 2. CHECK EVENT TYPE
    // Kubali 'message.inbound' au kama kuna data ya message moja kwa moja
    $isMessage = false;
    $msgData = [];

    if (isset($data['event']) && $data['event'] === 'message.inbound') {
        $isMessage = true;
        $msgData = $data['data'];
    } elseif (isset($data['type']) && $data['type'] === 'message') {
        // Fallback structure
        $isMessage = true;
        $msgData = $data;
    }

    if ($isMessage) {
        $newMessage = [
            'id' => $msgData['id'] ?? uniqid(),
            'from' => $msgData['from'] ?? 'Unknown',
            'to' => $msgData['to'] ?? 'Me',
            'body' => $msgData['body'] ?? $msgData['message'] ?? '(No Content)',
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'inbound'
        ];
        
        // Weka mwanzoni (LIFO)
        array_unshift($currentMessages, $newMessage);
        
        // Tunza messages 50 za mwisho tu
        $currentMessages = array_slice($currentMessages, 0, 50);
        
        // Save
        file_put_contents($logFile, json_encode($currentMessages, JSON_PRETTY_PRINT));
    }
    
    // Jibu API kwamba tumepokea
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
