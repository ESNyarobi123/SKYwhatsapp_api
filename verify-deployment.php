<?php

/**
 * Script ya ku-verify kuwa kila kitu kiko tayari kwa deployment
 * Run: php verify-deployment.php
 */

echo "🔍 Verifying deployment readiness...\n\n";

$errors = [];
$warnings = [];

// 1. Check build folder
echo "1. Checking build folder...\n";
if (!is_dir('public/build')) {
    $errors[] = "❌ public/build/ folder haipo. Run: npm run build";
} else {
    echo "   ✅ public/build/ folder ipo\n";
    
    // Check manifest.json
    if (!file_exists('public/build/manifest.json')) {
        $errors[] = "❌ public/build/manifest.json haipo";
    } else {
        echo "   ✅ manifest.json ipo\n";
        
        // Check if manifest has content
        $manifest = json_decode(file_get_contents('public/build/manifest.json'), true);
        if (empty($manifest)) {
            $warnings[] = "⚠️  manifest.json ni tupu";
        } else {
            echo "   ✅ manifest.json ina content\n";
        }
    }
    
    // Check assets folder
    if (!is_dir('public/build/assets')) {
        $warnings[] = "⚠️  public/build/assets/ folder haipo";
    } else {
        $assetFiles = glob('public/build/assets/*');
        if (empty($assetFiles)) {
            $warnings[] = "⚠️  Hakuna asset files kwenye public/build/assets/";
        } else {
            echo "   ✅ Asset files zipo (" . count($assetFiles) . " files)\n";
        }
    }
}

// 2. Check .env file
echo "\n2. Checking .env file...\n";
if (!file_exists('.env')) {
    $errors[] = "❌ .env file haipo";
} else {
    echo "   ✅ .env file ipo\n";
    
    // Check important keys
    $envContent = file_get_contents('.env');
    $requiredKeys = ['APP_KEY', 'APP_URL', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
    foreach ($requiredKeys as $key) {
        if (strpos($envContent, $key . '=') === false) {
            $warnings[] = "⚠️  $key haipo kwenye .env";
        } else {
            echo "   ✅ $key ipo\n";
        }
    }
}

// 3. Check storage permissions
echo "\n3. Checking storage permissions...\n";
if (!is_dir('storage')) {
    $errors[] = "❌ storage/ folder haipo";
} else {
    echo "   ✅ storage/ folder ipo\n";
    
    $storageWritable = is_writable('storage');
    if (!$storageWritable) {
        $warnings[] = "⚠️  storage/ folder haijaandikwa (chmod 755)";
    } else {
        echo "   ✅ storage/ ina write permissions\n";
    }
}

// 4. Check bootstrap/cache
echo "\n4. Checking bootstrap/cache...\n";
if (!is_dir('bootstrap/cache')) {
    $errors[] = "❌ bootstrap/cache/ folder haipo";
} else {
    echo "   ✅ bootstrap/cache/ folder ipo\n";
    
    $cacheWritable = is_writable('bootstrap/cache');
    if (!$cacheWritable) {
        $warnings[] = "⚠️  bootstrap/cache/ haijaandikwa (chmod 755)";
    } else {
        echo "   ✅ bootstrap/cache/ ina write permissions\n";
    }
}

// 5. Check vendor folder
echo "\n5. Checking vendor folder...\n";
if (!is_dir('vendor')) {
    $warnings[] = "⚠️  vendor/ folder haipo. Run: composer install";
} else {
    echo "   ✅ vendor/ folder ipo\n";
}

// 6. Check public/.htaccess
echo "\n6. Checking .htaccess...\n";
if (!file_exists('public/.htaccess')) {
    $warnings[] = "⚠️  public/.htaccess haipo";
} else {
    echo "   ✅ public/.htaccess ipo\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors) && empty($warnings)) {
    echo "✅ Kila kitu kiko tayari kwa deployment!\n";
    exit(0);
}

if (!empty($errors)) {
    echo "\n❌ ERRORS (lazima ufix kabla ya ku-deploy):\n";
    foreach ($errors as $error) {
        echo "   $error\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  WARNINGS (recommended kufix):\n";
    foreach ($warnings as $warning) {
        echo "   $warning\n";
    }
}

echo "\n";

if (!empty($errors)) {
    echo "🚫 Deployment haitaki kufanya kazi kwa sababu ya errors hapo juu.\n";
    exit(1);
} else {
    echo "✅ Unaweza ku-deploy, lakini fix warnings kwa best results.\n";
    exit(0);
}
