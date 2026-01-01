# -----------------------------
# Stage 0: Base PHP + Node.js (OPTIMIZED)
# -----------------------------
FROM php:8.4-fpm AS base

# 1. COPY script installer "ajaib" (mlocati)
# Ini kunci agar install extension jadi ngebut karena pakai pre-compiled binaries
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 2. Install System Dependencies Dasar + Node.js
# Note: Kita TIDAK PERLU lagi manual install libpng-dev, libzip-dev, dll.
# Script 'install-php-extensions' akan otomatis mengurus library system yang dibutuhkan.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP Extensions (Sat-Set / Cepat)
RUN install-php-extensions \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache 
    # opcache sangat disarankan untuk production

# 4. Install Composer & Config
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g github-protocols https \
    && composer config -g process-timeout 2000

WORKDIR /var/www

# -----------------------------
# Stage 1: PHP Dependencies
# -----------------------------
FROM base AS php-deps

ARG GITHUB_TOKEN
# Copy composer files only to leverage Docker cache
COPY composer.json composer.lock ./

# Pasang token GitHub jika ada
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com $GITHUB_TOKEN; fi

# Install dependencies PHP
RUN composer install --prefer-dist --no-dev --no-scripts --optimize-autoloader --classmap-authoritative

# -----------------------------
# Stage 2: Node Dependencies
# -----------------------------
FROM base AS node-deps

# Copy package files only to leverage Docker cache
COPY package.json package-lock.json ./

# Install dependencies Node
RUN npm ci --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Final Image
# -----------------------------
FROM base AS final

WORKDIR /var/www

# Copy PHP vendors dari stage 1
COPY --from=php-deps /var/www/vendor ./vendor

# Copy Node modules dari stage 2
COPY --from=node-deps /var/www/node_modules ./node_modules

# Copy full source code
COPY . .

# Build frontend Vue
RUN npm run build

# Opsional: Hapus node_modules setelah build selesai untuk memperkecil ukuran image
# (Aktifkan baris di bawah jika aplikasi kamu tidak butuh nodejs saat runtime/SSR)
# RUN rm -rf node_modules

# Setup Permissions & Autoload
# Note: chmod diubah sedikit agar lebih clean path-nya
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]