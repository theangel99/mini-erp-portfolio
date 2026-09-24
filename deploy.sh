#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Enable maintenance mode
php artisan down || true

# Pull latest changes from git
echo "📥 Pulling latest changes..."
git pull origin main

# Install/update composer dependencies
echo "📦 Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate --force

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "⚡ Optimizing..."
php artisan optimize
php artisan filament:optimize

# Disable maintenance mode
echo "✅ Bringing application back online..."
php artisan up

echo "🎉 Deployment completed successfully!"
