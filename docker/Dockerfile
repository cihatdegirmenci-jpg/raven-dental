FROM php:7.4-apache

# System deps
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        opcache \
        bcmath \
        mbstring

# Apache modules
RUN a2enmod rewrite headers expires deflate

# Storage dirs (will be on volume)
RUN mkdir -p /var/storage/cache /var/storage/modification /var/storage/logs \
             /var/storage/session /var/storage/upload /var/storage/download \
             /var/storage/backup \
    && chown -R www-data:www-data /var/storage \
    && chmod -R 775 /var/storage
