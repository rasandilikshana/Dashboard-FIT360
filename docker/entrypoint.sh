#!/bin/bash

set -e

echo "Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Wait for MySQL to be ready
echo "Waiting for database..."
until mysql -h db -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
  sleep 5
done

# Run migrations ONLY if the 'migrations' table is empty
if ! mysql -h db -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SELECT COUNT(*) FROM migrations;" | grep -q '[1-9]'; then
  echo "Running migrations and seeding..."
  php artisan migrate --seed --force
else
  echo "Migrations already applied, skipping..."
fi

echo "Running storage:link..."
php artisan storage:link || true

# Ensure npm dependencies are installed (only if missing)
if [ ! -d "node_modules" ]; then
  echo "Installing npm dependencies..."
  npm install
fi

# Run production build (only if missing)
if [ ! -f "public/mix-manifest.json" ]; then
  echo "Building frontend assets..."
  npm run production
else
  echo "Frontend assets already built, skipping..."
fi

# Start Laravel Scheduler
echo "Starting Laravel Scheduler..."
if command -v crontab > /dev/null 2>&1; then
  (crontab -l ; echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1") | crontab -
  service cron start
else
  echo "Crontab not found, skipping..."
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
