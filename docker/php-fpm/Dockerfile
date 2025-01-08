FROM php:8.4-fpm-alpine as base

ARG project_root=.

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --update --no-cache --virtual .build-dependencies \
    $PHPIZE_DEPS \
    linux-headers \
    && \
    apk add --no-cache \
    bash git nano vim curl \
    && \
    pecl install xdebug-3.4.0beta1 && \
    docker-php-ext-enable xdebug \
    && \
    docker-php-ext-install mysqli pdo pdo_mysql \
    && \
    apk del .build-dependencies && \
    rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www/html

########################################################################################################################

FROM base as development

USER root

COPY ${project_root}/docker/php-fpm/php.development.ini /usr/local/etc/php/php.ini
