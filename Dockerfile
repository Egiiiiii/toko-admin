# Dockerfile PHP-FPM + Node.js + Composer yang dioptimasi
FROM php:8.4-fpm

# -----------------------------
# 1️⃣ Install system dependencies + Node.js
# -----------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    openssh-client \
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
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -----------------------------
# 2️⃣ Install PHP Extensions
# -----------------------------
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# -----------------------------
# 3️⃣ Install Composer
# -----------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Force https for GitHub (stabilkan saat build Docker)
RUN composer config -g github-protocols https
# Perpanjang timeout agar tidak timeout di jaringan lambat
RUN composer config -g process-timeout 2000

# -----------------------------
# 4️⃣ Set working directory
# -----------------------------
WORKDIR /var/www

# -----------------------------
# 5️⃣ Copy Composer files dulu
# -----------------------------
COPY composer.json composer.lock ./

# -----------------------------
# 6️⃣ Install PHP dependencies (cached jika composer.json tidak berubah)
# -----------------------------
# Gunakan prefer-source untuk mengurangi masalah download timeout
RUN composer install --no-interaction --prefer-source --optimize-autoloader --no-dev --no-scripts \
    || composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# -----------------------------
# 7️⃣ Copy NPM files dulu
# -----------------------------
COPY package.json package-lock.json ./

# -----------------------------
# 8️⃣ Install Node.js dependencies
# -----------------------------
RUN npm install --legacy-peer-deps --no-audit --no-fund

# -----------------------------
# 9️⃣ Copy sisa source code
# -----------------------------
COPY . .

# -----------------------------
# 🔟 Build frontend (misal Laravel Mix / React / Vue)
# -----------------------------
RUN npm run build

# -----------------------------
# 1️⃣1️⃣ Final setup permissions & dump autoload
# -----------------------------
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# -----------------------------
# 1️⃣2️⃣ Expose port dan CMD
# -----------------------------
EXPOSE 9000
CMD ["php-fpm"]
