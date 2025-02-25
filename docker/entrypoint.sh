#!/bin/sh
set -e

# Ensure storage and cache directories have correct permissions
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Wait for MySQL to be ready
echo "Waiting for database..."
until mysql -h db -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
  sleep 5
done

# Check if the table exists before migrating
if ! mysql -h db -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SHOW TABLES LIKE 'app_settings';" | grep -q 'app_settings'; then
  echo "Running migrations and seeding..."
  php artisan migrate --seed --force
else
  echo "Migrations already applied, skipping..."
fi

echo "Running storage:link..."
php artisan storage:link

echo "Installing npm dependencies..."
if command -v npm > /dev/null 2>&1; then
  npm install && npm run prod
else
  echo "npm not installed, skipping..."
fi

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
