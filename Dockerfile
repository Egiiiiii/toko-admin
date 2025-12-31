FROM php:8.4-fpm

# 1. Install system dependencies + Node.js (Sama seperti sebelumnya)
RUN apt-get update && apt-get install -y \
    git openssh-client curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libpq-dev libzip-dev libicu-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install PHP Extensions (Sama seperti sebelumnya)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g github-protocols https

WORKDIR /var/www

# --- PERUBAHAN UTAMA DI SINI ---

# 4. Copy Composer Files DULU
COPY composer.json composer.lock ./

# 5. Install PHP Dependencies (Layer ini akan dicache kalau composer.json tidak berubah)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# 6. Copy NPM Files DULU
COPY package.json package-lock.json ./

# 7. Install JS Dependencies (Layer ini akan dicache kalau package.json tidak berubah)
RUN npm install

# 8. BARU Copy sisa source code (Disini cache baru akan "bust" jika ada perubahan kode)
COPY . .

# 9. Build Frontend (Harus setelah COPY . . karena butuh file resources/)
RUN npm run build

# 10. Final setup permissions & dump autoload (untuk generate classmap final)
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]