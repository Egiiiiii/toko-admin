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

COPY --from=deps /var/www/vendor ./vendor
COPY . .

RUN npm run build


# ------------------------------------------------------------------------------
# Stage 3: Runtime (PHP-FPM + NGINX)
# ------------------------------------------------------------------------------
FROM php:8.4-fpm

# 1. Install Nginx, Gettext-base, dan HAPUS CONFIG DEFAULT
# ---------------------------------------------------------
# Saya gabungkan di sini agar layer image tetap rapi.
# Perintah 'rm -f ...' akan menghapus config "Welcome to Nginx" segera setelah terinstall.
RUN apt-get update && apt-get install -y nginx gettext-base \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

# ---------------------------------------------------------

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache pcntl intl gd bcmath

WORKDIR /var/www

COPY --from=deps /var/www/vendor ./vendor
COPY . .
COPY --from=node_build /app/public/build ./public/build

# 2. Copy config mentah (yang ada variabel ${PHP_UPSTREAM}) jadi template
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf.template

# 3. Set Default Variabel (Biar langsung jalan di Kubernetes)
ENV PHP_UPSTREAM="127.0.0.1:9000"

# Permission
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

# 4. CMD "Sakti" (Pengganti entrypoint.sh)
# Logic: "Ambil template -> Ganti ${PHP_UPSTREAM} jadi 127.0.0.1 -> Simpan jadi config asli -> Jalankan App"
CMD ["sh", "-c", "envsubst '${PHP_UPSTREAM}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf && php-fpm -D && nginx -g 'daemon off;'"]