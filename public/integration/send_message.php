<?php
/**
 * SKY WhatsApp API - Simple Sender UI
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

$message = '';
$status = '';
$responseDetails = null;

// Handle Form Submission
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
            
            // Add IPv4 resolve option
            $api->setTimeout(30);

            $result = $api->sendMessage($phone, $msgBody);

            if ($result['success']) {
                $status = 'success';
                $message = 'Message Sent Successfully! ✅';
                $responseDetails = $result['data'];
            } else {
                $status = 'error';
                $message = 'Failed to Send Message ❌';
                $responseDetails = $result['error'];
            }

        } catch (Exception $e) {
            $status = 'error';
            $message = 'System Error: ' . $e->getMessage();
        }
    } else {
        $status = 'error';
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKY WhatsApp Sender</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">🚀 Send Message</h1>
            <p class="text-gray-400">SKY WhatsApp API Integration</p>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-2xl p-8 shadow-2xl">
            
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-xl <?php echo $status === 'success' ? 'bg-green-500/20 border border-green-500/50 text-green-400' : 'bg-red-500/20 border border-red-500/50 text-red-400'; ?>">
                    <div class="flex items-center gap-3">
                        <span class="text-xl"><?php echo $status === 'success' ? '✅' : '❌'; ?></span>
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($message); ?></p>
                            <?php if ($responseDetails): ?>
                                <div class="mt-2 text-xs opacity-80 font-mono bg-black/20 p-2 rounded">
                                    <?php 
                                    if ($status === 'success') {
                                        echo "ID: " . ($responseDetails['message']['id'] ?? 'N/A') . "<br>";
                                        echo "Status: " . ($responseDetails['message']['status'] ?? 'N/A');
                                    } else {
                                        echo "Code: " . ($responseDetails['code'] ?? 'N/A') . "<br>";
                                        echo "Error: " . ($responseDetails['message'] ?? 'Unknown');
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <!-- Phone Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-gray-500">📞</span>
                        <input type="text" name="phone" placeholder="255712345678" required
                            class="w-full bg-[#0f172a] border border-gray-700 rounded-xl py-3 pl-12 pr-4 text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Include country code (e.g., 255...)</p>
                </div>

                <!-- Message Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Message</label>
                    <textarea name="message" rows="4" placeholder="Type your message here..." required
                        class="w-full bg-[#0f172a] border border-gray-700 rounded-xl p-4 text-white placeholder-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg transform transition hover:-translate-y-0.5 active:translate-y-0">
                    Send Message 🚀
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>Instance ID: <span class="text-gray-300 font-mono"><?php echo $config['instance_id']; ?></span></p>
            <p class="mt-2 text-xs">Powered by SKY WhatsApp API</p>
        </div>
    </div>

</body>
</html>
