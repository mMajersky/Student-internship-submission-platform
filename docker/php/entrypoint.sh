#!/usr/bin/env sh
set -e

cd /var/www/html

# Install composer dependencies if vendor directory doesn't exist
if [ -f "composer.json" ]; then
  if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
  fi

  # Use .env.example if .env doesn't exist
  if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
  fi

  # Generate APP_KEY if not set
  if ! grep -q "^APP_KEY=.\+" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force || true
  fi

  # Create storage directories
  echo "Creating storage directories..."
  mkdir -p storage/framework/{sessions,views,cache,testing} storage/app/public storage/logs
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache

  # Wait for database to be ready
  echo "Waiting for database..."
  sleep 5

  # Create storage symlink
  echo "Creating storage symlink..."
  php artisan storage:link || true

  # Remove duplicate Passport migrations if they exist
  rm -f database/migrations/2025_10_27_*oauth*.php

  # Run migrations
  echo "Running database migrations..."
  php artisan migrate --force || true

  # Install Passport keys if they don't exist
  if [ ! -f "storage/oauth-private.key" ] || [ ! -f "storage/oauth-public.key" ]; then
    echo "Installing Passport keys..."
    php artisan passport:install --force || true
  fi

  # Clear config and routes
  echo "Optimizing application..."
  php artisan config:clear || true
  php artisan route:clear || true

  echo "Application ready!"
fi

exec "$@"

