FROM php:8.2-fpm

# Tentukan direktori kerja
WORKDIR /var/www/html

# Salin semua file dari folder tempat Dockerfile berada ke dalam container (.)
COPY . .

# Instal ekstensi dan dependensi
RUN apt-get update && apt-get install -y libgmp-dev && \
    docker-php-ext-install bcmath gmp

# Pastikan izin akses benar
RUN chown -R www-data:www-data /var/www/html