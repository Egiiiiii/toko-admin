# ==============================================================================
# Stage 0: Base PHP (System Deps & Extensions)
# ==============================================================================
FROM php:8.4-fpm AS base

# 1. FIX JARINGAN (CRITICAL): Paksa Linux menggunakan IPv4
#    Ini mengatasi 'curl error 28' dan timeout saat connect ke GitHub/Packagist
RUN echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf

# 2. Install System Dependencies Dasar
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    ca-certificates \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP Extensions (via mlocati installer - lebih stabil)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
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

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ==============================================================================
# Stage 1: Node Builder (Frontend Build)
# ==============================================================================
# Menggunakan image node asli agar build lebih cepat & cache terpisah
FROM node:20-slim AS node-builder

WORKDIR /app

# Copy package files dulu untuk caching layer
COPY package.json package-lock.json ./

# Install npm dependencies (ci lebih cepat untuk environment CI/CD)
RUN npm ci --prefer-offline --no-audit

# Copy source code frontend dan build
COPY . .
RUN npm run build

# ==============================================================================
# Stage 2: PHP Builder (Backend Dependencies)
# ==============================================================================
FROM base AS php-builder

# Copy composer files dulu
COPY composer.json composer.lock ./

# 1. FIX GIT & COMPOSER TIMEOUT (CRITICAL)
#    - process-timeout: naikkan ke 2000 detik agar tidak putus saat download lambat
#    - git postBuffer: perbesar buffer agar tidak 'Connection reset'
#    - sslVerify false: cegah error sertifikat di environment corporate/proxy
#    - github-protocols: paksa HTTPS, jangan pernah coba SSH
RUN composer config --global process-timeout 2000 \
    && git config --global http.postBuffer 524288000 \
    && git config --global http.sslVerify false \
    && composer config -g github-protocols https

# 2. Install Dependencies Production
#    --no-dev: Buang library testing (phpunit, faker, dll) agar ringan
#    --ignore-platform-reqs: Mencegah error jika versi extension lokal beda sedikit
RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --no-progress \
    --ignore-platform-reqs

# ==============================================================================
# Stage 3: Final Image (Runtime)
# ==============================================================================
FROM base AS final

WORKDIR /var/www

# 1. Copy Vendor dari Stage PHP Builder
COPY --from=php-builder /var/www/vendor ./vendor

# 2. Copy Assets Public (CSS/JS) dari Stage Node Builder
#    HANYA folder public/ yang dicopy. node_modules DIBUANG.
COPY --from=node-builder /app/public ./public

# 3. Copy Source Code Aplikasi
COPY . .

# 4. Setup Permission & Optimization
#    Pastikan folder storage bisa ditulis oleh www-data
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache \
    && composer dump-autoload --optimize

EXPOSE 9000
CMD ["php-fpm"]