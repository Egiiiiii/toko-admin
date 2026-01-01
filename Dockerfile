# -----------------------------
# Stage 0: Base Image PHP + Node.js
# -----------------------------
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y \
    git curl openssh-client libpng-dev libonig-dev libxml2-dev \
    zip unzip libpq-dev libzip-dev libicu-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g github-protocols https \
    && composer config -g process-timeout 2000

WORKDIR /var/www

# -----------------------------
# Stage 1: Cache PHP dependencies
# -----------------------------
FROM base AS php-deps
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# -----------------------------
# Stage 2: Cache Node dependencies
# -----------------------------
FROM base AS node-deps
COPY package.json package-lock.json vite.config.js ./
RUN npm ci --cache /root/.npm --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Build frontend
# -----------------------------
FROM node-deps AS frontend-build
COPY resources/js resources/js
COPY resources/css resources/css
RUN npm run build

# -----------------------------
# Stage 4: Final image
# -----------------------------
FROM base AS final

WORKDIR /var/www

# Copy PHP vendor
COPY --from=php-deps /var/www/vendor ./vendor

# Copy Node modules (optional, needed if runtime JS like Inertia uses SSR)
COPY --from=node-deps /var/www/node_modules ./node_modules

# Copy built frontend
COPY --from=frontend-build /var/www/public/build ./public/build

# Copy rest of the source code
COPY . .

# Optimize composer autoload + permissions
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
