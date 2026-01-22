<?php
/**
 * SKY WhatsApp API - Advanced Chat UI
 */

require_once __DIR__ . '/SkyWhatsAppAPI.php';

// ============================================
// CONFIGURATION
// ============================================
$config = [
    'api_url'     => 'https://orange.ericksky.online/api/v1',
    'api_key'     => 'sk_ozv68TatDd5NLDpr18vZJATsfh6Rl6uYAC5ouAv7FNmjlq14',
    'instance_id' => 13,
];

$logFile = __DIR__ . '/webhook_messages.json';

// Handle Form Submission (Sending Message)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $msgBody = $_POST['message'] ?? '';

    if ($phone && $msgBody) {
        try {
            $api = new SkyWhatsAppAPI(
                $config['api_url'],
                $config['api_key'],
                $config['instance_id']
            );
            $api->setTimeout(30);
            $result = $api->sendMessage($phone, $msgBody);

            if ($result['success']) {
                // Log sent message locally for UI
                $currentMessages = [];
                if (file_exists($logFile)) {
                    $currentMessages = json_decode(file_get_contents($logFile), true) ?? [];
                }
                array_unshift($currentMessages, [
                    'id' => $result['data']['message']['id'] ?? uniqid(),
                    'from' => 'Me',
                    'to' => $phone,
                    'body' => $msgBody,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'type' => 'outbound'
                ]);
                file_put_contents($logFile, json_encode(array_slice($currentMessages, 0, 50), JSON_PRETTY_PRINT));
                
                $status = 'success';
            } else {
                $status = 'error';
                $errorMsg = $result['error']['message'] ?? 'Unknown error';
            }
        } catch (Exception $e) {
            $status = 'error';
            $errorMsg = $e->getMessage();
        }
    }
}

// Read Messages
$messages = [];
if (file_exists($logFile)) {
    $messages = json_decode(file_get_contents($logFile), true) ?? [];
}

// Filter only incoming messages for the "Incoming Log" section
$incomingMessages = array_filter($messages, fn($m) => isset($m['type']) && $m['type'] === 'inbound');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKY WhatsApp Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta http-equiv="refresh" content="10"> <!-- Auto refresh every 10s -->
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* Chat Bubbles */
        .msg-in { background-color: #1e293b; border-radius: 0 12px 12px 12px; position: relative; }
        .msg-in::before { content: ''; position: absolute; top: 0; left: -8px; width: 0; height: 0; border-top: 10px solid #1e293b; border-left: 10px solid transparent; }
        
        .msg-out { background-color: #2563eb; color: white; border-radius: 12px 0 12px 12px; margin-left: auto; position: relative; }
        .msg-out::before { content: ''; position: absolute; top: 0; right: -8px; width: 0; height: 0; border-top: 10px solid #2563eb; border-right: 10px solid transparent; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-screen flex flex-col lg:flex-row overflow-hidden">

    <!-- LEFT: Send Form -->
    <div class="w-full lg:w-1/4 bg-[#1e293b] border-r border-gray-700 p-6 flex flex-col h-auto lg:h-full overflow-y-auto z-10 shadow-xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white mb-1 flex items-center gap-2">
                🚀 SKY Chat
            </h1>
            <p class="text-gray-400 text-xs">Instance ID: <span class="text-blue-400 font-mono"><?php echo $config['instance_id']; ?></span></p>
        </div>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Phone Number</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 text-xs">📞</span>
                    <input type="text" name="phone" placeholder="2557..." required value="<?php echo $_POST['phone'] ?? ''; ?>"
                        class="w-full bg-[#0f172a] border border-gray-700 rounded-lg py-2 pl-8 pr-3 text-white text-sm focus:border-blue-500 focus:outline-none transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Message</label>
                <textarea name="message" rows="4" placeholder="Type message..." required
                    class="w-full bg-[#0f172a] border border-gray-700 rounded-lg py-2 px-3 text-white text-sm focus:border-blue-500 focus:outline-none transition"></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-2.5 rounded-lg shadow-lg transition transform active:scale-95 text-sm">
                Send Message ➤
            </button>
        </form>

        <?php if (isset($status)): ?>
            <div class="mt-4 p-3 rounded-lg text-xs border <?php echo $status === 'success' ? 'bg-green-500/10 border-green-500/30 text-green-400' : 'bg-red-500/10 border-red-500/30 text-red-400'; ?>">
                <?php echo $status === 'success' ? '✅ Message Sent Successfully!' : '❌ Error: ' . htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-auto pt-6 text-[10px] text-gray-600">
            <p>Webhook URL:</p>
            <code class="block bg-black/20 p-1 rounded mt-1 break-all select-all">https://orange.ericksky.online/integration/webhook.php</code>
        </div>
    </div>

    <!-- CENTER: Chat Conversation -->
    <div class="flex-1 bg-[#0f172a] flex flex-col h-full relative">
        <!-- Header -->
        <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-[#1e293b]/50 backdrop-blur">
            <h2 class="font-semibold text-white flex items-center gap-2">
                💬 Live Conversation
            </h2>
            <span class="text-xs text-green-400 flex items-center gap-1 bg-green-500/10 px-2 py-1 rounded-full border border-green-500/20">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Online
            </span>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6 bg-[url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png')] bg-fixed bg-opacity-5">
            <?php if (empty($messages)): ?>
                <div class="flex flex-col items-center justify-center h-full text-gray-500 opacity-50">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p>No messages yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="flex w-full <?php echo ($msg['type'] === 'outbound') ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-[75%] p-3 shadow-md <?php echo ($msg['type'] === 'outbound') ? 'msg-out' : 'msg-in'; ?>">
                            <div class="flex justify-between items-baseline gap-4 mb-1 border-b border-white/10 pb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider opacity-70">
                                    <?php echo ($msg['type'] === 'outbound') ? '👤 Me → ' . $msg['to'] : '📩 ' . $msg['from']; ?>
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($msg['body']); ?></p>
                            <div class="text-[9px] text-right mt-1 opacity-50">
                                <?php echo date('H:i', strtotime($msg['timestamp'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- RIGHT: Incoming Log (Dedicated Section) -->
    <div class="w-full lg:w-1/4 bg-[#111827] border-l border-gray-700 flex flex-col h-auto lg:h-full overflow-hidden">
        <div class="p-4 border-b border-gray-700 bg-[#1f2937]">
            <h3 class="font-bold text-white text-sm flex items-center gap-2">
                📥 Incoming Log
                <span class="bg-blue-600 text-white text-[10px] px-1.5 py-0.5 rounded-full"><?php echo count($incomingMessages); ?></span>
            </h3>
            <p class="text-[10px] text-gray-400">Received messages only</p>
        </div>
        
        <div class="flex-1 overflow-y-auto p-0">
            <?php if (empty($incomingMessages)): ?>
                <div class="p-8 text-center text-gray-600 text-xs">
                    No incoming messages yet.
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-800">
                    <?php foreach ($incomingMessages as $msg): ?>
                        <div class="p-3 hover:bg-gray-800/50 transition border-l-2 border-transparent hover:border-blue-500">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-blue-400 font-mono text-xs font-bold"><?php echo $msg['from']; ?></span>
                                <span class="text-[9px] text-gray-500"><?php echo date('M d, H:i', strtotime($msg['timestamp'])); ?></span>
                            </div>
                            <p class="text-gray-300 text-xs line-clamp-2"><?php echo htmlspecialchars($msg['body']); ?></p>
                            <div class="mt-2 flex items-center gap-1">
                                <span class="text-[9px] bg-gray-700 text-gray-300 px-1 rounded">ID: <?php echo substr($msg['id'], 0, 8); ?>...</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
