#!/bin/sh

set -e

cd /var/www/html

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

echo "Discovering Laravel packages..."
php artisan package:discover --ansi

echo "Running database migrations..."
php artisan migrate --force

if [ "${SEED_DATABASE:-false}" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

echo "Optimizing Laravel..."
php artisan optimize

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
exec nginx -g "daemon off;"