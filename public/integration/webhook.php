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

// Log file path
$logFile = __DIR__ . '/webhook_messages.json';

if ($data) {
    // Soma messages zilizopo
    $currentMessages = [];
    if (file_exists($logFile)) {
        $currentMessages = json_decode(file_get_contents($logFile), true) ?? [];
    }
    
    // Ongeza message mpya (kama ni inbound message)
    if (isset($data['event']) && $data['event'] === 'message.inbound') {
        $newMessage = [
            'id' => $data['data']['id'] ?? uniqid(),
            'from' => $data['data']['from'] ?? 'Unknown',
            'body' => $data['data']['body'] ?? '',
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
