<?php
/**
 * SKY WhatsApp API - Simple Chat UI
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
        .msg-in { background-color: #1e293b; border-radius: 0 12px 12px 12px; }
        .msg-out { background-color: #2563eb; color: white; border-radius: 12px 0 12px 12px; margin-left: auto; }
    </style>
</head>
<body class="h-screen flex flex-col md:flex-row overflow-hidden">

    <!-- Sidebar / Send Form -->
    <div class="w-full md:w-1/3 bg-[#1e293b] border-r border-gray-700 p-6 flex flex-col h-full overflow-y-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white mb-1">🚀 SKY Chat</h1>
            <p class="text-gray-400 text-sm">Instance: <?php echo $config['instance_id']; ?></p>
            <p class="text-xs text-gray-500 mt-2">Webhook URL: <br><code class="bg-black/30 p-1 rounded select-all">https://orange.ericksky.online/integration/webhook.php</code></p>
        </div>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Phone Number</label>
                <input type="text" name="phone" placeholder="2557..." required value="<?php echo $_POST['phone'] ?? ''; ?>"
                    class="w-full bg-[#0f172a] border border-gray-700 rounded-lg py-2 px-3 text-white focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Message</label>
                <textarea name="message" rows="3" placeholder="Type message..." required
                    class="w-full bg-[#0f172a] border border-gray-700 rounded-lg py-2 px-3 text-white focus:border-blue-500 focus:outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-lg transition">
                Send Message ➤
            </button>
        </form>

        <?php if (isset($status)): ?>
            <div class="mt-4 p-3 rounded-lg text-sm <?php echo $status === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'; ?>">
                <?php echo $status === 'success' ? 'Message Sent! ✅' : 'Error: ' . htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 bg-[#0f172a] flex flex-col h-full">
        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="font-semibold text-white">Live Messages</h2>
            <span class="text-xs text-green-400 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Live
            </span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <?php if (empty($messages)): ?>
                <div class="text-center text-gray-500 mt-20">
                    <p class="text-4xl mb-2">📭</p>
                    <p>No messages yet.</p>
                    <p class="text-sm mt-2">Set your webhook URL to start receiving messages.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="flex <?php echo ($msg['type'] === 'outbound') ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-[80%] p-3 <?php echo ($msg['type'] === 'outbound') ? 'msg-out' : 'msg-in'; ?>">
                            <div class="flex justify-between items-baseline gap-4 mb-1">
                                <span class="text-xs font-bold opacity-70">
                                    <?php echo ($msg['type'] === 'outbound') ? 'To: ' . $msg['to'] : 'From: ' . $msg['from']; ?>
                                </span>
                                <span class="text-[10px] opacity-50"><?php echo date('H:i', strtotime($msg['timestamp'])); ?></span>
                            </div>
                            <p class="text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($msg['body'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
