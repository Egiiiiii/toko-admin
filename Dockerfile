# --- STAGE 1: Build PHP Dependencies (Composer) ---
FROM composer:2 AS deps
WORKDIR /app
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --prefer-dist

# --- STAGE 2: Build Frontend Assets (Node.js) ---
FROM node:20-alpine AS node_build
WORKDIR /app
# Copy source code
COPY src .

# ### PERBAIKAN DISINI ###
# Kita butuh folder vendor agar Ziggy bisa ditemukan oleh Vite
COPY --from=deps /app/vendor ./vendor
# ######################

# Install dependensi node & build assets
RUN npm ci
RUN npm run build

# --- STAGE 3: Runtime (PHP-FPM) ---
FROM php:8.4-fpm-alpine

# Install library sistem
RUN apk add --no-cache postgresql-dev libzip-dev zip unzip bash

# Install Ekstensi PHP
RUN docker-php-ext-install pdo pdo_pgsql zip opcache pcntl

# Konfigurasi PHP-FPM
RUN sed -i 's|listen = 127.0.0.1:9000|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# 1. Copy Vendor dari Stage 1
COPY --from=deps /app/vendor ./vendor

# 2. Copy Source Code Aplikasi
COPY src .

# 3. Copy Hasil Build Frontend dari Stage 2
COPY --from=node_build /app/public/build ./public/build

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]