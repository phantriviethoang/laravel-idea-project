# Stage 1: Build frontend assets
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP + Nginx runtime
FROM php:8.4-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    sqlite-dev \
    && docker-php-ext-install \
    pdo \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create database file & set permissions
RUN mkdir -p database storage bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data database storage bootstrap/cache \
    && chmod -R 775 database storage bootstrap/cache

# Copy configuration files & entrypoint
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
