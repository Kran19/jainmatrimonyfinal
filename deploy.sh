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
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# 3. Create native Linux symlink for storage (bypasses PHP disable_functions)
echo "🔗 Creating storage symlink via native Linux commands..."
if [ -d "public" ]; then
    rm -rf public/storage
    ln -sfn "$PWD/storage/app/public" "$PWD/public/storage"
    echo "   ✅ Symlink connected: public/storage -> storage/app/public"
fi

# 4. Run Laravel database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 5. Clear application caches safely
echo "🧹 Clearing application caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 6. Set file & directory permissions for shared hosting
echo "🔒 Setting storage & cache directory permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "=================================================="
echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
echo "=================================================="
