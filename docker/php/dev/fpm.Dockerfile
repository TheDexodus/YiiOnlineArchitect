FROM tunet/php:7.4.1-fpm

RUN apk update \
    && apk add --no-cache --virtual .build-deps \
           $PHPIZE_DEPS \
    && pecl install xdebug-2.8.0 \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

RUN addgroup -g 1000 1000
RUN adduser -u 1000 -G 1000 -D 1000
USER 1000

WORKDIR /var/www/app.loc
