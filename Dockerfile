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

# Install Composer (latest)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g github-protocols https \
    && composer config -g process-timeout 2000

WORKDIR /var/www

# -----------------------------
# Stage 1: PHP Dependencies with Cache
# -----------------------------
FROM base AS php-deps

WORKDIR /var/www

# Copy only composer files
COPY composer.json composer.lock ./

# Arg untuk GitHub token (opsional, untuk rate-limit)
ARG GITHUB_TOKEN
RUN if [ -n "$GITHUB_TOKEN" ]; then composer config -g github-oauth.github.com $GITHUB_TOKEN; fi

# Set memory unlimited & timeout tinggi
ENV COMPOSER_MEMORY_LIMIT=-1

# Install deps pakai prefer-dist & cache
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    --no-scripts \
    --timeout=300 \
    --prefer-stable \
    --verbose

# -----------------------------
# Stage 2: Node Dependencies with Cache
# -----------------------------
FROM base AS node-deps

WORKDIR /var/www

# Copy package files only
COPY package.json package-lock.json ./

# Cache npm
RUN mkdir -p /root/.npm

# Install npm deps
RUN npm ci --cache /root/.npm --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Final Build
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

# Dump composer autoload & set permissions
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
