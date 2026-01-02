# ------------------------------------------------------------------------------
# Stage 1: Build PHP Dependencies (Base Debian untuk Network Stability)
# ------------------------------------------------------------------------------
FROM php:8.4-fpm AS deps

# FIX JARINGAN (Wajib ada biar gak timeout lagi)
RUN echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf

RUN apt-get update && apt-get install -y git unzip zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY composer.json composer.lock ./

# Config Network Composer
RUN composer config --global process-timeout 2000 \
    && git config --global http.postBuffer 524288000

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --prefer-dist

# ------------------------------------------------------------------------------
# Stage 2: Build Frontend (Node) - Dengan Cache & Vendor Ziggy
# ------------------------------------------------------------------------------
FROM node:20-slim AS node_build

WORKDIR /app

# 1. Copy package files DULUAN (Biar Cache NPM jalan)
COPY package.json package-lock.json ./
RUN npm ci --prefer-offline --no-audit

# 2. Copy Vendor dari stage 1 (Agar Ziggy/Vite bisa baca route Laravel)
COPY --from=deps /var/www/vendor ./vendor

# 3. Baru Copy source code sisanya
COPY . .

# 4. Build
RUN npm run build

# ------------------------------------------------------------------------------
# Stage 3: Runtime
# ------------------------------------------------------------------------------
FROM php:8.4-fpm

# Install Extension dengan installer (Lebih mudah daripada manual)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache pcntl intl gd bcmath

WORKDIR /var/www

# Copy Vendor
COPY --from=deps /var/www/vendor ./vendor

# Copy Source Code
COPY . .

# Copy Hasil Build JS/CSS
COPY --from=node_build /app/public/build ./public/build

# Permission
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]