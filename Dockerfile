FROM php:8.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    nodejs \
    npm \
    cron

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . .

# Update dependencies with specific version constraints
RUN composer update --no-interaction --no-scripts \
    && composer require maatwebsite/excel:"^3.1.48" --with-all-dependencies \
    && composer require yajra/laravel-datatables-oracle:"^9.0" --with-all-dependencies \
    && composer require yajra/laravel-datatables-buttons:"^4.13" --with-all-dependencies

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Copy entrypoint script & give execute permissions
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && chown www-data:www-data /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
