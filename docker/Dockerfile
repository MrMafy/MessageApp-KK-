
FROM php:8.2-fpm


# Установка необходимых зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev

# Установка расширений
RUN docker-php-ext-install pdo_mysql mysqli

# Установка расширения Redis через PECL
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

CMD ["php-fpm"]
