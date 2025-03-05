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
# Install Node.js v16 and npm
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

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

# Git ownership
RUN git config --global --add safe.directory /var/www

# Update dependencies with specific version constraints
RUN composer update --no-interaction --no-scripts \
    && composer require maatwebsite/excel:"^3.1.48" --with-all-dependencies \
    && composer require yajra/laravel-datatables-oracle:"^9.0" --with-all-dependencies \
    && composer require yajra/laravel-datatables-buttons:"^4.13" --with-all-dependencies

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

RUN rm -rf node_modules package-lock.json && npm install
RUN npm run production

# Copy entrypoint script & give execute permissions
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && chown www-data:www-data /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]
