FROM php:8.2-fpm

# 1. Install system dependencies & Node.js (untuk Tailwind)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions (Wajib pdo_pgsql untuk Postgres)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# 4. Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set working directory
WORKDIR /var/www

# 6. Copy seluruh project
COPY . .

# 7. Install PHP Dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 8. Install & Build Tailwind CSS
RUN npm install && npm run build

# 9. Set Permissions (Agar Laravel bisa tulis log & cache)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# 10. Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]