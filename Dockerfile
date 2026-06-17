FROM php:8.2-fpm

# Instal dependensi sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    libgmp-dev \
    && docker-php-ext-install bcmath gmp