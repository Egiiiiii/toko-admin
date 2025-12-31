# -----------------------------
# Stage 0: Base Image PHP + Node.js
# -----------------------------
FROM php:8.4-fpm AS base

# Install system dependencies + Node.js
RUN apt-get update && apt-get install -y \
    git curl openssh-client libpng-dev libonig-dev libxml2-dev \
    zip unzip libpq-dev libzip-dev libicu-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g github-protocols https \
    && composer config -g process-timeout 2000

WORKDIR /var/www

# -----------------------------
# Stage 1: Install PHP dependencies
# -----------------------------
FROM base AS php-deps

# Copy composer files only
COPY composer.json composer.lock ./

# Cache composer dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# -----------------------------
# Stage 2: Install Node.js dependencies
# -----------------------------
FROM base AS node-deps

# Copy package files only
COPY package.json package-lock.json ./

# Use npm ci with cache
RUN mkdir -p /root/.npm \
    && npm ci --cache /root/.npm --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Build final image
# -----------------------------
FROM base AS final

WORKDIR /var/www

# Copy PHP deps
COPY --from=php-deps /var/www/vendor ./vendor

# Copy Node deps
COPY --from=node-deps /var/www/node_modules ./node_modules

# Copy full source code
COPY . .

# Build frontend
RUN npm run build

# Dump composer autoload + set permissions
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Expose port and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
