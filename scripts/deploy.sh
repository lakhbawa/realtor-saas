#!/bin/bash

# Realtor SaaS Deployment Script
# Usage: ./scripts/deploy.sh [environment]

set -e

ENVIRONMENT=${1:-production}
APP_DIR=$(dirname "$(dirname "$(readlink -f "$0")")")

echo "🚀 Deploying Realtor SaaS to ${ENVIRONMENT}..."

cd "$APP_DIR"

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Installing NPM dependencies..."
npm ci --production

# Build assets
echo "🔨 Building assets..."
npm run build

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Cache configuration for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Clear opcache if using PHP-FPM
if command -v cachetool &> /dev/null; then
    echo "🔄 Clearing OPcache..."
    cachetool opcache:reset --fcgi=/var/run/php-fpm.sock
fi

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment complete!"
