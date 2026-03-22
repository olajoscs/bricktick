FROM php:8.4-fpm

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
  && docker-php-ext-install -j$(nproc) pdo_mysql zip intl \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && rm -rf /var/lib/apt/lists/*

COPY infra/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /var/www/html

