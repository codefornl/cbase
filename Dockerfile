FROM php:7.3-apache-buster

ENV DB_HOST 127.0.0.1
ENV DB_NAME test
ENV DB_USER user
ENV DB_PASS password
ENV ROOT_PASS danger
ENV DEBUG_MODE false

RUN a2enmod rewrite

RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get update -y && \
    apt-get install zip -y

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf

USER www-data

RUN composer install -d /var/www/html

USER root
