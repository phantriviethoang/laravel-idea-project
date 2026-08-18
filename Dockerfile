FROM php:8.3-fpm-bookworm

# System dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    git \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        intl \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@11.16.0 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Composer dependencies
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Frontend dependencies
COPY package.json package-lock.json ./

RUN npm ci

# Application
COPY . .

# Build Vite / Alpine.js
RUN npm run build

# Laravel directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/data

# Permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    /var/data

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# PHP-FPM configuration
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Start script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
