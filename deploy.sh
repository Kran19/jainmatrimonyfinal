#!/usr/bin/env bash

# Exit immediately if a command fails
set -e

echo "=================================================="
echo "🚀 Starting Deployment for Digambar Jain Parichay"
echo "=================================================="

# 1. Pull latest code from git repository
echo "📥 Pulling latest updates from Git..."
git pull origin main

# 2. Ensure storage directories exist
echo "📁 Ensuring storage directories exist..."
mkdir -p storage/app/public/uploads
mkdir -p storage/app/private/imports
mkdir -p storage/app/private/uploads
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# 3. Securely move sensitive public/imports to storage/app/private/imports
if [ -d "public/imports" ]; then
    echo "🔒 Securing public/imports to private storage..."
    cp -rn public/imports/* storage/app/private/imports/ 2>/dev/null || true
    rm -rf public/imports
    echo "   ✅ Moved public/imports -> storage/app/private/imports"
fi

# 4. Create native Linux symlink for storage (bypasses PHP disable_functions)
echo "🔗 Creating storage symlink via native Linux commands..."
if [ -d "public" ]; then
    rm -rf public/storage
    ln -sfn "$PWD/storage/app/public" "$PWD/public/storage"
    echo "   ✅ Symlink connected: public/storage -> storage/app/public"
fi

# 5. Run Laravel database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 6. Clear application caches safely
echo "🧹 Clearing application caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 7. Set file & directory permissions for shared hosting
echo "🔒 Setting storage & cache directory permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "=================================================="
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "=================================================="
