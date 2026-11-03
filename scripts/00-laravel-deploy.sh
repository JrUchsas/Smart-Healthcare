#!/usr/bin/env bash
set -e

echo "=== Smart Healthcare Deployment Starting ==="

# Fallback: if APP_KEY is empty, generate one
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is not set. Generating key for this container..."
    php artisan key:generate --force
fi

echo "Step 1: Creating storage symlink..."
php artisan storage:link --force || true

echo "Step 2: Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Step 3: Running database migrations..."
php artisan migrate --force

if [ "$SEED_ON_DEPLOY" = "true" ]; then
    echo "Step 4: Running database seeders (Roles, Admin, Doctor)..."
    php artisan db:seed --force
fi

echo "=== Smart Healthcare Deployment Completed Successfully ==="
