FROM tunet/php:7.4.1-fpm

COPY --from=composer:1.9.1 /usr/bin/composer /usr/bin/composer
#ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./bin /var/www/app.loc/bin
COPY ./config /var/www/app.loc/config
COPY ./public /var/www/app.loc/public
COPY ./src /var/www/app.loc/src
COPY ./templates /var/www/app.loc/templates
COPY ./tests /var/www/app.loc/tests
COPY ./translations /var/www/app.loc/translations
COPY ./.env /var/www/app.loc/.env
COPY ./.env.test /var/www/app.loc/.env.test
COPY ./composer.json /var/www/app.loc/composer.json
COPY ./composer.lock /var/www/app.loc/composer.lock
COPY ./phpunit.xml.dist /var/www/app.loc/phpunit.xml.dist
COPY ./symfony.lock /var/www/app.loc/symfony.lock
COPY ./phpcs.xml /var/www/app.loc/phpcs.xml
COPY ./phpmd.xml /var/www/app.loc/phpmd.xml

# todo: remove
COPY ./cms-bundle /var/www/app.loc/cms-bundle

WORKDIR /var/www/app.loc

# user www-data
RUN chown -R 82:82 /var/www/app.loc
USER 82

RUN composer install
RUN bin/console cache:warmup --env=prod
#RUN bin/console assets:install --relative
