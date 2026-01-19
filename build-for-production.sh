#!/bin/bash

# Script ya ku-build assets kwa production na ku-verify

echo "🚀 Building assets for production..."

# Install dependencies kama hazipo
if [ ! -d "node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    npm install
fi

# Build assets
echo "🔨 Building with Vite..."
npm run build

# Verify build folder
if [ -d "public/build" ] && [ -f "public/build/manifest.json" ]; then
    echo "✅ Build successful!"
    echo "📁 Build folder: public/build/"
    echo ""
    echo "Files created:"
    ls -lh public/build/
    echo ""
    echo "📋 Next steps:"
    echo "1. Upload 'public/build/' folder to cPanel (public_html/build/)"
    echo "2. Upload all other files to cPanel"
    echo "3. Configure .env file on cPanel"
    echo "4. Run: php artisan migrate --force"
    echo "5. Run: php artisan storage:link"
    echo "6. Run: php artisan config:cache"
else
    echo "❌ Build failed! Check errors above."
    exit 1
fi
