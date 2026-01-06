# ------------------------------------------------------------------------------
# Stage 1: Build PHP Dependencies (Base Debian untuk Network Stability)
# ------------------------------------------------------------------------------
FROM php:8.4-fpm AS deps

RUN echo "precedence ::ffff:0:0/96  100" >> /etc/gai.conf

RUN apt-get update && apt-get install -y git unzip zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer config --global process-timeout 2000 \
    && git config --global http.postBuffer 524288000

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts --prefer-dist


# ------------------------------------------------------------------------------
# Stage 2: Build Frontend (Node) - Dengan Cache & Vendor Ziggy
# ------------------------------------------------------------------------------
FROM node:20-slim AS node_build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline --no-audit

COPY . .
COPY --from=deps /var/www/vendor ./vendor


RUN npm run build


# ------------------------------------------------------------------------------
# Stage 3: Runtime (PHP-FPM + NGINX)
# ------------------------------------------------------------------------------
# ... (Stage 1 deps sama) ...
# ... (Stage 2 node_build sama) ...

# ------------------------------------------------------------------------------
# Stage 3: Runtime (PHP-FPM + NGINX)
# ------------------------------------------------------------------------------
FROM php:8.4-fpm

# Install dependencies & clean up
RUN apt-get update && apt-get install -y nginx gettext-base \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

# PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache pcntl intl gd bcmath

# Opcache Config (Performance Tuning)
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

WORKDIR /var/www

# --- PERBAIKAN URUTAN COPY (Agar aman menimpa file lokal) ---
# 1. Copy App Code dulu
COPY . .

# 2. Copy Vendor (Menimpa vendor lokal jika ada)
COPY --from=deps /var/www/vendor ./vendor

# 3. Copy Assets Frontend
COPY --from=node_build /app/public/build ./public/build
# ------------------------------------------------------------

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf.template

ENV PHP_UPSTREAM="127.0.0.1:9000"

# Permission & Laravel Optimization (Optional tapi recommended)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

# Healthcheck (Optional, biar Docker tahu container sehat)
HEALTHCHECK --interval=30s --timeout=3s \
  CMD curl -f http://localhost/up || exit 1

CMD ["sh", "-c", "envsubst '${PHP_UPSTREAM}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf && php-fpm -D && nginx -g 'daemon off;'"]