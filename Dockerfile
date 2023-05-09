FROM php:8.2-apache
WORKDIR /var/www/html

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli && \
    a2enmod rewrite

EXPOSE 80
