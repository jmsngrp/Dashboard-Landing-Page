#!/bin/sh
set -e

echo "==> Caching config..."
php artisan config:cache

echo "==> Caching routes..."
php artisan route:cache

echo "==> Caching views..."
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding admin users..."
php artisan db:seed --class=AdminUserSeeder --force

echo "==> Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
