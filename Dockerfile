FROM php:8.4-fpm

# 1. Install system dependencies + Node.js + Git + SSH
RUN apt-get update && apt-get install -y \
    git \
    openssh-client \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP Extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Force Composer use HTTPS (no SSH, no host verification error)
RUN composer config -g github-protocols https

# 5. Set working directory
WORKDIR /var/www

# 6. Copy project files
COPY . .

# 7. Install PHP Dependencies (CI-safe)
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev

# 8. Install & Build Frontend
RUN npm install && npm run build

# 9. Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# 10. Expose port & run
EXPOSE 9000
CMD ["php-fpm"]
