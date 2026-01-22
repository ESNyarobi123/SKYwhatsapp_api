<?php
/**
 * API Connection Debug Script
 * Inakagua connection kati ya:
 * 1. Laravel API endpoints
 * 2. Node.js WhatsApp Service
 * 3. Database instances
 */

// Simple bootstrap for Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 API Debug Tool</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #0f172a; color: #e2e8f0; }
        h1 { color: #38bdf8; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
        h2 { color: #94a3b8; margin-top: 30px; }
        .box { background: #1e293b; border-radius: 10px; padding: 20px; margin: 15px 0; border-left: 4px solid #3b82f6; }
        .success { border-left-color: #22c55e; background: #052e16; }
        .error { border-left-color: #ef4444; background: #450a0a; }
        .warning { border-left-color: #eab308; background: #3d2608; }
        .info { border-left-color: #06b6d4; background: #083344; }
        code { background: #0f172a; color: #f472b6; padding: 2px 6px; border-radius: 4px; font-size: 14px; }
        pre { background: #0f172a; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; color: #a5f3fc; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-ok { background: #22c55e; color: #fff; }
        .badge-fail { background: #ef4444; color: #fff; }
        .badge-pending { background: #eab308; color: #000; }
        ul { line-height: 2; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #1e293b; color: #94a3b8; }
        tr:hover { background: #1e293b; }
    </style>
</head>
<body>
    <h1>🔧 SKY WhatsApp API - Debug Tool</h1>
    
    <h2>📋 1. Configuration Check</h2>
    <?php
    $whatsappUrl = config('services.whatsapp_service.url', 'NOT SET');
    $whatsappKey = config('services.whatsapp_service.api_key') ? '✓ SET' : '✗ NOT SET';
    $appUrl = config('app.url');
    ?>
    <div class="box info">
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><code>WHATSAPP_SERVICE_URL</code></td>
                <td><code><?= htmlspecialchars($whatsappUrl) ?></code></td>
                <td><span class="badge <?= $whatsappUrl !== 'NOT SET' ? 'badge-ok' : 'badge-fail' ?>"><?= $whatsappUrl !== 'NOT SET' ? 'OK' : 'MISSING' ?></span></td>
            </tr>
            <tr>
                <td><code>WHATSAPP_SERVICE_API_KEY</code></td>
                <td><?= $whatsappKey ?></td>
                <td><span class="badge <?= $whatsappKey === '✓ SET' ? 'badge-ok' : 'badge-fail' ?>"><?= $whatsappKey === '✓ SET' ? 'OK' : 'MISSING' ?></span></td>
            </tr>
            <tr>
                <td><code>APP_URL</code></td>
                <td><code><?= htmlspecialchars($appUrl) ?></code></td>
                <td><span class="badge badge-ok">OK</span></td>
            </tr>
        </table>
    </div>

    <h2>🔌 2. Node.js Service Connection</h2>
    <?php
    $nodeUrl = $whatsappUrl;
    $nodeStatus = null;
    $nodeError = null;
    
    // Test health endpoint
    $healthEndpoints = ['/api/health', '/health', '/api/v1/health', '/'];
    $workingEndpoint = null;
    
    foreach ($healthEndpoints as $endpoint) {
        $ch = curl_init($nodeUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 400) {
            $workingEndpoint = $endpoint;
            $nodeStatus = $httpCode;
            break;
        }
        $nodeError = $error ?: "HTTP $httpCode";
    }
    ?>
    
    <div class="box <?= $workingEndpoint ? 'success' : 'error' ?>">
        <strong>Node.js Service URL:</strong> <code><?= htmlspecialchars($nodeUrl) ?></code><br><br>
        <?php if ($workingEndpoint): ?>
            <span class="badge badge-ok">✓ CONNECTED</span> via <code><?= $workingEndpoint ?></code> (HTTP <?= $nodeStatus ?>)
        <?php else: ?>
            <span class="badge badge-fail">✗ NOT REACHABLE</span><br><br>
            <strong>Error:</strong> <?= htmlspecialchars($nodeError) ?><br><br>
            <strong>Possible Causes:</strong>
            <ul>
                <li>Node.js service haija-start - run <code>npm start</code> or <code>node index.js</code></li>
                <li>Port 3000 imefungwa au inatumika na app nyingine</li>
                <li>Firewall inazuia connection</li>
                <li>URL si sahihi kwenye <code>.env</code></li>
            </ul>
        <?php endif; ?>
    </div>

    <h2>📊 3. Database - Instances</h2>
    <?php
    $instances = \App\Models\Instance::with('user')->get();
    ?>
    <div class="box">
        <strong>Total Instances:</strong> <?= $instances->count() ?>
        
        <?php if ($instances->count() > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>User</th>
                <th>Status</th>
                <th>Phone</th>
                <th>Created</th>
            </tr>
            <?php foreach ($instances as $instance): ?>
            <tr>
                <td><code><?= $instance->id ?></code></td>
                <td><?= htmlspecialchars($instance->name) ?></td>
                <td><?= htmlspecialchars($instance->user->name ?? 'N/A') ?></td>
                <td>
                    <span class="badge <?= $instance->status === 'connected' ? 'badge-ok' : ($instance->status === 'pending' ? 'badge-pending' : 'badge-fail') ?>">
                        <?= strtoupper($instance->status) ?>
                    </span>
                </td>
                <td><code><?= htmlspecialchars($instance->phone_number ?? 'N/A') ?></code></td>
                <td><?= $instance->created_at->diffForHumans() ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p style="color: #fbbf24;">⚠️ Hakuna instances. Unda instance mpya kwanza!</p>
        <?php endif; ?>
    </div>

    <h2>📨 4. Pending Messages</h2>
    <?php
    $pendingMessages = \App\Models\Message::where('status', 'pending')
        ->where('direction', 'outbound')
        ->with(['instance', 'user'])
        ->latest()
        ->take(10)
        ->get();
    ?>
    <div class="box <?= $pendingMessages->count() > 0 ? 'warning' : 'success' ?>">
        <strong>Pending Messages:</strong> <?= $pendingMessages->count() ?>
        
        <?php if ($pendingMessages->count() > 0): ?>
        <p style="color: #fbbf24;">⚠️ Messages ziko pending - Node.js service haizichukui!</p>
        <table>
            <tr>
                <th>ID</th>
                <th>To</th>
                <th>Instance</th>
                <th>Body</th>
                <th>Created</th>
            </tr>
            <?php foreach ($pendingMessages as $msg): ?>
            <tr>
                <td><code><?= $msg->id ?></code></td>
                <td><code><?= htmlspecialchars($msg->to) ?></code></td>
                <td><?= htmlspecialchars($msg->instance->name ?? 'N/A') ?> (ID: <?= $msg->instance_id ?>)</td>
                <td><?= htmlspecialchars(substr($msg->body, 0, 50)) ?>...</td>
                <td><?= $msg->created_at->diffForHumans() ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>✓ Hakuna pending messages</p>
        <?php endif; ?>
    </div>

    <h2>🔗 5. API Endpoints Test</h2>
    <?php
    $apiBase = rtrim($appUrl, '/') . '/api';
    $endpoints = [
        ['GET', '/v1/instances', 'List Instances', true],
        ['GET', '/internal/instances', 'Internal - Get Instances', false],
        ['GET', '/internal/messages/pending', 'Internal - Pending Messages', false],
    ];
    ?>
    <div class="box info">
        <strong>API Base:</strong> <code><?= htmlspecialchars($apiBase) ?></code>
        <p>Laravel API endpoints (zinahitaji authentication):</p>
        <table>
            <tr>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Description</th>
                <th>Auth</th>
            </tr>
            <tr>
                <td><span class="badge" style="background:#22c55e">POST</span></td>
                <td><code>/api/v1/messages/send</code></td>
                <td>Send WhatsApp Message</td>
                <td>API Key</td>
            </tr>
            <tr>
                <td><span class="badge" style="background:#3b82f6">GET</span></td>
                <td><code>/api/v1/messages</code></td>
                <td>List Messages</td>
                <td>API Key</td>
            </tr>
            <tr>
                <td><span class="badge" style="background:#3b82f6">GET</span></td>
                <td><code>/api/v1/instances</code></td>
                <td>List Instances</td>
                <td>API Key</td>
            </tr>
            <tr>
                <td><span class="badge" style="background:#a855f7">GET</span></td>
                <td><code>/api/internal/messages/pending</code></td>
                <td>Node.js - Get Pending</td>
                <td>Internal Key</td>
            </tr>
        </table>
    </div>

    <h2>📝 6. How to Fix HTTP 404</h2>
    <div class="box info">
        <strong>Common causes za HTTP 404:</strong>
        <ol>
            <li><strong>Instance ID si sahihi:</strong> Check instance_id unayo itumia ipo kwenye database na ni yako</li>
            <li><strong>API Key si sahihi:</strong> Check API key yako iko valid
                <pre>Authorization: Bearer YOUR_API_KEY</pre>
            </li>
            <li><strong>Wrong URL:</strong> Check URL ni sahihi:
                <pre><?= htmlspecialchars($appUrl) ?>/api/v1/messages/send</pre>
            </li>
            <li><strong>Instance haiko connected:</strong> Check instance status ni "connected"</li>
        </ol>
    </div>

    <h2>🧪 7. Test API Call (cURL Example)</h2>
    <div class="box">
        <pre>curl -X POST "<?= htmlspecialchars($appUrl) ?>/api/v1/messages/send" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "instance_id": YOUR_INSTANCE_ID,
    "to": "255712345678",
    "body": "Test message from API"
  }'</pre>
    </div>

    <p style="text-align:center; color:#64748b; margin-top:30px;">
        Generated at <?= now()->format('Y-m-d H:i:s') ?> | SKY WhatsApp API Debug Tool
    </p>
</body>
</html>
