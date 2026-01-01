# -----------------------------
# Stage 0: Base PHP + Node.js
# -----------------------------
FROM php:8.4-fpm AS base

# Install system dependencies + Node.js
RUN apt-get update && apt-get install -y \
    git curl unzip zip libpng-dev libonig-dev libxml2-dev \
    libpq-dev libzip-dev libicu-dev ca-certificates \
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
# Stage 1: PHP Dependencies
# -----------------------------
FROM base AS php-deps

ARG GITHUB_TOKEN
# Copy composer files only
COPY composer.json composer.lock ./

# Pasang token GitHub jika ada
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com $GITHUB_TOKEN; fi

# Cache composer
RUN composer install --prefer-dist --no-dev --no-scripts --optimize-autoloader --classmap-authoritative

# -----------------------------
# Stage 2: Node Dependencies
# -----------------------------
FROM base AS node-deps

# Copy package files only
COPY package.json package-lock.json ./

# Install dependencies dengan npm ci dan cache
RUN mkdir -p /root/.npm \
    && npm ci --cache /root/.npm --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Final Image
# -----------------------------
FROM base AS final

WORKDIR /var/www

# Copy PHP deps
COPY --from=php-deps /var/www/vendor ./vendor

# Copy Node deps
COPY --from=node-deps /var/www/node_modules ./node_modules

# Copy full source code
COPY . .

# Build frontend Vue
RUN npm run build

# Composer dump-autoload + set permissions
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
