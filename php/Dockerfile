FROM php:7.4.0-fpm-buster

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && docker-php-ext-install pdo_mysql

COPY ./php.ini /usr/local/etc/php/php.ini

WORKDIR /work