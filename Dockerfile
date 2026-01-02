# -----------------------------
# Stage 0: Base PHP + Node.js (ULTRA STABLE)
# -----------------------------
FROM php:8.4-fpm AS base

# PHP extension installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# System deps + Node
RUN apt-get update && apt-get install -y \
    git curl unzip zip ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions (precompiled)
RUN install-php-extensions \
    pdo_pgsql mbstring exif pcntl bcmath gd zip intl opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 🚑 Composer Network Hardening (CRITICAL)
RUN composer config -g process-timeout 2000 \
    && composer config -g http.timeout 2000 \
    && composer config -g curl.timeout 2000 \
    && composer config -g github-protocols https

WORKDIR /var/www

# -----------------------------
# Stage 1: PHP Dependencies
# -----------------------------
FROM base AS php-deps

ARG GITHUB_TOKEN

COPY composer.json composer.lock ./

# Optional GitHub token (recommended)
RUN if [ -n "$GITHUB_TOKEN" ]; then \
      composer config -g github-oauth.github.com $GITHUB_TOKEN ; \
    fi

RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-progress \
    --no-ansi

# -----------------------------
# Stage 2: Node Dependencies
# -----------------------------
FROM base AS node-deps

COPY package.json package-lock.json ./

RUN npm ci --prefer-offline --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# Stage 3: Final Image
# -----------------------------
FROM base AS final

WORKDIR /var/www

COPY --from=php-deps /var/www/vendor ./vendor
COPY --from=node-deps /var/www/node_modules ./node_modules

COPY . .

RUN npm run build

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
