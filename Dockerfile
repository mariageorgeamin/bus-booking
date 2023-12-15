# Use PHP 8.2 FPM base image
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    libonig-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml ctype zip gd pdo_pgsql


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version

# Copy application files
COPY . .

# Attempt to install Composer dependencies
RUN composer install --optimize-autoloader --no-dev || \
    (composer clear-cache && composer install --optimize-autoloader --no-dev --ignore-platform-reqs) || \
    php -d memory_limit=-1 /usr/local/bin/composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN chmod -R 777 storage

# Copy the .env.example file to .env
COPY .env.example .env

# Expose port 9000
EXPOSE 9000

# Start Laravel server by default when the container starts
CMD php artisan serve --host=0.0.0.0 --port=9000
