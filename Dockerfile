FROM php:8.2-fpm

# 1. Install dependencies sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev

# 2. Bersihkan cache apt agar image lebih kecil
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install Ekstensi PHP yang dibutuhkan Laravel & Filament
# pdo_pgsql: untuk koneksi ke PostgreSQL
# mbstring, exif, pcntl, bcmath, gd: standar Laravel
# intl: untuk format mata uang/tanggal
# zip: untuk manajemen file zip
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# 4. Install & Enable Redis (PENTING: Karena Anda pakai Redis)
RUN pecl install redis && docker-php-ext-enable redis

# 5. Install Composer (Copy dari image composer resmi)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Set working directory
WORKDIR /var/www

# 7. Copy seluruh file project ke dalam container
COPY . /var/www

# 8. Ubah kepemilikan folder agar www-data bisa menulis (penting untuk storage/logs)
RUN chown -R www-data:www-data /var/www

# 9. Ganti user ke www-data
USER www-data

# 10. Expose port 9000 (Port default PHP-FPM)
EXPOSE 9000

# 11. Perintah jalan
CMD ["php-fpm"]