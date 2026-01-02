# ---------------------------------------------------------------
# Stage 1: Base PHP (Hanya environment PHP dasar)
# ---------------------------------------------------------------
FROM php:8.4-fpm AS base

# Install System Dependencies & PHP Extensions
# Kita TIDAK install Nodejs di sini agar image final kecil
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y \
    git unzip zip ca-certificates \
    && install-php-extensions pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ---------------------------------------------------------------
# Stage 2: Node Build (Terpisah sepenuhnya)
# ---------------------------------------------------------------
# Menggunakan image node asli lebih cepat daripada install node di php container
FROM node:20-slim AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
# 'npm ci' lebih cepat dan reliable untuk CI daripada 'npm install'
RUN npm ci --no-audit --prefer-offline

COPY . .
# Compile asset (Vite/Mix). Hasilnya biasanya ada di folder /public
RUN npm run build

# ---------------------------------------------------------------
# Stage 3: PHP Vendor Build
# ---------------------------------------------------------------
FROM base AS php-builder

COPY composer.json composer.lock ./

# Install dependensi tanpa dev-tools untuk production
RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --no-progress

# ---------------------------------------------------------------
# Stage 4: Final Image (Runtime)
# ---------------------------------------------------------------
FROM base AS final

# 1. Copy Vendor PHP dari Stage 3
COPY --from=php-builder /var/www/vendor ./vendor

# 2. Copy Hasil Build Frontend (JS/CSS) dari Stage 2
# HANYA copy folder public (hasil compile), node_modules DIBUANG
COPY --from=node-builder /app/public ./public

# 3. Copy Source Code Sisanya
COPY . .

# 4. Setup Permission & Optimization
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache \
    && composer dump-autoload --optimize

EXPOSE 9000
CMD ["php-fpm"]