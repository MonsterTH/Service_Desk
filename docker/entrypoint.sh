#!/bin/bash

echo "🔧 A preparar Laravel..."

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -d "vendor" ]; then
    echo "📦 Installing dependencies..."
    composer install --no-interaction --prefer-dist
fi

php artisan key:generate --force
php artisan optimize:clear

php artisan migrate --force
php artisan db:seed --force

echo "🚀 Laravel pronto!"

apache2-foreground
