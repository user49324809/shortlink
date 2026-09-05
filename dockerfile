FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-req=ext-gd

FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev libpng-dev libzip-dev unzip \
    && docker-php-ext-install curl gd pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Включаем mod_rewrite (нужно для Yii2)
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY . .
COPY --from=composer /app/vendor ./vendor
RUN cp config/db.php.example config/db.php

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
