FROM php:7.4-fpm-alpine

RUN apk add --update --no-cache libgd libpng-dev libjpeg-turbo-dev freetype-dev

RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo pdo_mysql
RUN apk add --update libxml2-dev && docker-php-ext-install xml

ADD php.ini /usr/local/etc/php.ini
