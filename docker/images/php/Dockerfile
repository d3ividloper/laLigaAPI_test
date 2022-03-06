# ./docker/php/Dockerfile
FROM php:7.4.1-fpm-alpine3.10
MAINTAINER David

WORKDIR /app

RUN apk --update upgrade \
    && apk add --no-cache autoconf automake make gcc g++ icu-dev rabbitmq-c rabbitmq-c-dev \
    && pecl install amqp-1.9.4 \
    && pecl install apcu-5.1.17 \
    && pecl install xdebug-2.8.1 \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        opcache \
        intl \
        pdo_mysql \
    && docker-php-ext-enable \
        amqp \
        apcu \
        opcache \
        xdebug
RUN apk add libpng libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev gd && docker-php-ext-install gd
RUN apk add --no-cache libzip-dev && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*

COPY ./docker/etc/php/ /usr/local/etc/php/

#COPY . /app
#RUN composer install
