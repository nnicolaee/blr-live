FROM php:8.2-apache
WORKDIR /var/www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli && \
    a2enmod rewrite

EXPOSE 80
