#!/bin/bash

# Realtor SaaS Initialization Script
# Run this script after cloning the repository for the first time

set -e

APP_DIR=$(dirname "$(dirname "$(readlink -f "$0")")")
cd "$APP_DIR"

echo "🏠 Initializing Realtor SaaS..."

# Check requirements
echo "🔍 Checking requirements..."

if ! command -v php &> /dev/null; then
    echo "❌ PHP is required but not installed."
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer is required but not installed."
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ NPM is required but not installed."
    exit 1
fi

echo "✅ All requirements met!"

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install

echo "📦 Installing NPM dependencies..."
npm install

# Environment setup
if [ ! -f .env ]; then
    echo "⚙️ Creating .env file..."
    cp .env.example .env

    echo "🔑 Generating application key..."
    php artisan key:generate
else
    echo "✅ .env file already exists"
fi

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || true

# Create directories
echo "📁 Creating required directories..."
mkdir -p storage/app/public/{logos,headshots,heroes,properties}
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,views}

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Build assets
echo "🔨 Building assets..."
npm run build

echo ""
echo "══════════════════════════════════════════════════════════════"
echo "✅ Initialization complete!"
echo ""
echo "Next steps:"
echo "1. Configure your .env file with database and Stripe credentials"
echo "2. Run: php artisan migrate --seed"
echo "3. Start the development server: php artisan serve"
echo "4. Or use Docker: docker-compose up -d"
echo ""
echo "Default login credentials (after seeding):"
echo "  Admin: admin@example.com / password"
echo "  Demo:  demo@example.com / password"
echo "══════════════════════════════════════════════════════════════"
