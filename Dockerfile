FROM php:8.2-fpm-alpine

# Install system dependencies, Nginx, and build packages
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions required for Laravel and PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
RUN npm ci && npm run build

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx config and entrypoint script
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]