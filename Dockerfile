# -----------------------------
# Stage 0: Base PHP + Node.js (OPTIMIZED)
# -----------------------------
FROM php:8.4-fpm AS base

# 1. Gunakan installer extension yang efisien (mlocati)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 2. Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip zip ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions (Pre-compiled / Cepat)
RUN install-php-extensions \
    pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- FIX UTAMA DISINI ---
# Set Environment Variable agar berlaku global di semua stage
# COMPOSER_PROCESS_TIMEOUT: Waktu tunggu (detik). 2000s = 33 menit.
# COMPOSER_MEMORY_LIMIT: Unlimited memory (-1) agar tidak crash saat unzip file besar
ENV COMPOSER_PROCESS_TIMEOUT=2000 \
    COMPOSER_MEMORY_LIMIT=-1

RUN composer config -g github-protocols https

WORKDIR /var/www

# -----------------------------
# Stage 1: PHP Dependencies
# -----------------------------
FROM base AS php-deps

ARG GITHUB_TOKEN
# Copy composer files
COPY composer.json composer.lock ./

# Pasang token GitHub jika ada (Sangat membantu speed jika punya token)
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com $GITHUB_TOKEN; fi

# Install dependencies
# Tambahkan flag:
# --no-interaction: Jangan tanya yes/no
# --prefer-dist: Paksa download file zip (lebih cepat drpd git clone)
RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --classmap-authoritative

# -----------------------------
# Stage 2: Node Dependencies
# -----------------------------
FROM base AS node-deps

COPY package.json package-lock.json ./

# Install node deps
RUN npm ci --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Final Image
# -----------------------------
FROM base AS final

WORKDIR /var/www

# Copy vendor & node_modules
COPY --from=php-deps /var/www/vendor ./vendor
COPY --from=node-deps /var/www/node_modules ./node_modules

# Copy source code
COPY . .

# Build frontend
RUN npm run build

# Final cleanup & permission
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]