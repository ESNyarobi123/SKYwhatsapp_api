<?php
/**
 * Debug API Routes Script
 * Upload this to your production server to check routes
 * DELETE THIS FILE AFTER DEBUGGING!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if running from CLI
$isCli = php_sapi_name() === 'cli';
$nl = $isCli ? "\n" : "<br>";

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<style>
        body { font-family: 'Consolas', monospace; padding: 20px; background: #1a1a2e; color: #eee; }
        .success { color: #00ff88; }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        h1 { color: #fff; }
        pre { background: #0d0d1a; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .section { margin: 20px 0; padding: 15px; background: #252542; border-radius: 8px; }
    </style>";
}

echo $isCli ? "" : "<h1>🔍 API Routes Debug Script</h1>";

// Section 1: Laravel App Check
echo $isCli ? "\n=== 1. LARAVEL APP CHECK ===$nl" : "<div class='section'><h2>1. Laravel App Check</h2>";

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo $isCli ? "[OK] Laravel app loaded$nl" : "<p class='success'>✅ Laravel app loaded successfully</p>";
} catch (Exception $e) {
    echo $isCli ? "[ERROR] " . $e->getMessage() . $nl : "<p class='error'>❌ " . $e->getMessage() . "</p>";
    exit;
}
echo $isCli ? "" : "</div>";

// Section 2: Route List
echo $isCli ? "\n=== 2. REGISTERED API ROUTES ===$nl" : "<div class='section'><h2>2. Registered API Routes (v1)</h2>";

$router = $app->make('router');
$routes = $router->getRoutes();

$apiRoutes = [];
foreach ($routes as $route) {
    $uri = $route->uri();
    if (str_starts_with($uri, 'api/v1')) {
        $apiRoutes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $uri,
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
}

echo $isCli ? "Found " . count($apiRoutes) . " API v1 routes$nl" : "<p class='info'>Found " . count($apiRoutes) . " API v1 routes</p>";
echo $isCli ? "" : "<pre>";

foreach ($apiRoutes as $r) {
    $line = sprintf("%-10s %-40s %-35s", $r['method'], $r['uri'], $r['name'] ?? '-');
    echo $line . $nl;
}
echo $isCli ? "" : "</pre></div>";

// Section 3: Check messages/send route specifically
echo $isCli ? "\n=== 3. MESSAGES/SEND ROUTE CHECK ===$nl" : "<div class='section'><h2>3. Messages/Send Route Check</h2>";

$messagesSendRoute = null;
foreach ($routes as $route) {
    if ($route->uri() === 'api/v1/messages/send') {
        $messagesSendRoute = $route;
        break;
    }
}

if ($messagesSendRoute) {
    echo $isCli ? "[OK] Route found!$nl" : "<p class='success'>✅ Route 'api/v1/messages/send' is registered</p>";
    echo $isCli ? "Methods: " . implode(', ', $messagesSendRoute->methods()) . $nl : "<p>Methods: " . implode(', ', $messagesSendRoute->methods()) . "</p>";
    echo $isCli ? "Action: " . $messagesSendRoute->getActionName() . $nl : "<p>Action: " . $messagesSendRoute->getActionName() . "</p>";
    echo $isCli ? "Middleware: " . implode(', ', $messagesSendRoute->middleware()) . $nl : "<p>Middleware: " . implode(', ', $messagesSendRoute->middleware()) . "</p>";
} else {
    echo $isCli ? "[ERROR] Route NOT FOUND!$nl" : "<p class='error'>❌ Route 'api/v1/messages/send' is NOT registered!</p>";
}
echo $isCli ? "" : "</div>";

// Section 4: .htaccess check
echo $isCli ? "\n=== 4. .HTACCESS CHECK ===$nl" : "<div class='section'><h2>4. .htaccess Check</h2>";

$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    $htaccess = file_get_contents($htaccessPath);
    echo $isCli ? "[OK] .htaccess exists$nl" : "<p class='success'>✅ .htaccess file exists</p>";
    
    // Check for RewriteEngine
    if (strpos($htaccess, 'RewriteEngine On') !== false) {
        echo $isCli ? "[OK] RewriteEngine On$nl" : "<p class='success'>✅ RewriteEngine is ON</p>";
    } else {
        echo $isCli ? "[ERROR] RewriteEngine not found!$nl" : "<p class='error'>❌ RewriteEngine is OFF or missing</p>";
    }
    
    // Check for Authorization header
    if (strpos($htaccess, 'HTTP_AUTHORIZATION') !== false) {
        echo $isCli ? "[OK] Authorization header handling$nl" : "<p class='success'>✅ Authorization header handling present</p>";
    } else {
        echo $isCli ? "[WARNING] Authorization header handling might be missing$nl" : "<p class='warning'>⚠️ Authorization header handling might be missing</p>";
    }
} else {
    echo $isCli ? "[ERROR] .htaccess NOT FOUND!$nl" : "<p class='error'>❌ .htaccess NOT FOUND!</p>";
}
echo $isCli ? "" : "</div>";

// Section 5: Apache mod_rewrite check
echo $isCli ? "\n=== 5. SERVER MODULES ===$nl" : "<div class='section'><h2>5. Server Modules</h2>";

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo $isCli ? "[OK] mod_rewrite is enabled$nl" : "<p class='success'>✅ mod_rewrite is enabled</p>";
    } else {
        echo $isCli ? "[ERROR] mod_rewrite NOT enabled!$nl" : "<p class='error'>❌ mod_rewrite is NOT enabled!</p>";
    }
} else {
    echo $isCli ? "[INFO] Cannot check mod_rewrite (not running under Apache handler)$nl" : "<p class='warning'>⚠️ Cannot check mod_rewrite (PHP not running as Apache module)</p>";
}
echo $isCli ? "" : "</div>";

// Section 6: Test direct route resolution
echo $isCli ? "\n=== 6. ROUTE RESOLUTION TEST ===$nl" : "<div class='section'><h2>6. Route Resolution Test</h2>";

try {
    $testRequest = \Illuminate\Http\Request::create('/api/v1/messages/send', 'POST');
    $testRequest->headers->set('Content-Type', 'application/json');
    $testRequest->headers->set('Accept', 'application/json');
    
    $route = $routes->match($testRequest);
    echo $isCli ? "[OK] Route matched: " . $route->uri() . $nl : "<p class='success'>✅ Route can be matched: " . $route->uri() . "</p>";
} catch (Exception $e) {
    echo $isCli ? "[ERROR] " . $e->getMessage() . $nl : "<p class='error'>❌ " . $e->getMessage() . "</p>";
}
echo $isCli ? "" : "</div>";

// Section 7: Check MessageController
echo $isCli ? "\n=== 7. CONTROLLER CHECK ===$nl" : "<div class='section'><h2>7. MessageController Check</h2>";

$controllerClass = \App\Http\Controllers\Api\MessageController::class;
if (class_exists($controllerClass)) {
    echo $isCli ? "[OK] MessageController exists$nl" : "<p class='success'>✅ MessageController exists</p>";
    
    if (method_exists($controllerClass, 'send')) {
        echo $isCli ? "[OK] send() method exists$nl" : "<p class='success'>✅ send() method exists</p>";
    } else {
        echo $isCli ? "[ERROR] send() method NOT FOUND!$nl" : "<p class='error'>❌ send() method NOT FOUND!</p>";
    }
} else {
    echo $isCli ? "[ERROR] MessageController NOT FOUND!$nl" : "<p class='error'>❌ MessageController NOT FOUND!</p>";
}
echo $isCli ? "" : "</div>";

// Section 8: Cache status
echo $isCli ? "\n=== 8. CACHE STATUS ===$nl" : "<div class='section'><h2>8. Cache Status</h2>";

$routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
$configCached = file_exists(base_path('bootstrap/cache/config.php'));

echo $isCli ? "Routes cached: " . ($routesCached ? 'Yes' : 'No') . $nl : "<p>Routes cached: " . ($routesCached ? '<span class="warning">Yes</span>' : '<span class="success">No</span>') . "</p>";
echo $isCli ? "Config cached: " . ($configCached ? 'Yes' : 'No') . $nl : "<p>Config cached: " . ($configCached ? '<span class="warning">Yes</span>' : '<span class="success">No</span>') . "</p>";

if ($routesCached) {
    echo $isCli ? "[TIP] Try: php artisan route:clear$nl" : "<p class='warning'>💡 Routes are cached. Try running: <code>php artisan route:clear</code></p>";
}
echo $isCli ? "" : "</div>";

// Section 9: Quick API Test
echo $isCli ? "\n=== 9. QUICK API SIMULATION ===$nl" : "<div class='section'><h2>9. Quick API Simulation</h2>";
echo $isCli ? "Testing what happens with a POST to /api/v1/messages/send...$nl" : "<p class='info'>Testing what happens with a POST to /api/v1/messages/send...</p>";

try {
    $request = \Illuminate\Http\Request::create(
        '/api/v1/messages/send',
        'POST',
        ['instance_id' => 11, 'to' => '255712345678', 'body' => 'Test'],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json']
    );
    
    $route = $routes->match($request);
    $middlewares = $route->middleware();
    
    echo $isCli ? "[OK] Route matched$nl" : "<p class='success'>✅ Route matched</p>";
    echo $isCli ? "Middlewares: " . implode(', ', $middlewares) . $nl : "<p>Middlewares that will run: " . implode(', ', $middlewares) . "</p>";
    
    // Check if middleware exists
    foreach ($middlewares as $middleware) {
        if (str_starts_with($middleware, 'App\\')) {
            if (class_exists($middleware)) {
                echo $isCli ? "[OK] Middleware exists: $middleware$nl" : "<p class='success'>✅ $middleware</p>";
            } else {
                echo $isCli ? "[ERROR] Middleware not found: $middleware$nl" : "<p class='error'>❌ Middleware not found: $middleware</p>";
            }
        }
    }
} catch (Exception $e) {
    echo $isCli ? "[ERROR] " . $e->getMessage() . $nl : "<p class='error'>❌ " . $e->getMessage() . "</p>";
}
echo $isCli ? "" : "</div>";

// Section 10: Recommendations
echo $isCli ? "\n=== 10. RECOMMENDATIONS ===$nl" : "<div class='section'><h2>10. Recommendations</h2><pre>";
echo "1. Clear all caches:{$nl}";
echo "   php artisan route:clear{$nl}";
echo "   php artisan config:clear{$nl}";
echo "   php artisan cache:clear{$nl}";
echo "   php artisan optimize:clear{$nl}{$nl}";
echo "2. Check .htaccess has RewriteEngine On{$nl}{$nl}";
echo "3. Verify mod_rewrite is enabled{$nl}";
echo "   a2enmod rewrite (Apache){$nl}{$nl}";
echo "4. Check Apache config allows .htaccess:{$nl}";
echo "   AllowOverride All{$nl}{$nl}";
echo "5. Test with curl:{$nl}";
echo '   curl -X POST https://orange.ericksky.online/api/v1/messages/send \\' . $nl;
echo '        -H "Accept: application/json" \\' . $nl;
echo '        -H "Content-Type: application/json" \\' . $nl;
echo '        -H "Authorization: Bearer YOUR_API_KEY"' . $nl;
echo $isCli ? "" : "</pre></div>";

echo $isCli ? "\n=== DONE ===$nl" : "<p><strong>⚠️ DELETE THIS FILE AFTER DEBUGGING!</strong></p>";
