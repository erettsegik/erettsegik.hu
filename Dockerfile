FROM php:5.5-apache

RUN a2enmod rewrite
RUN apt-get update && apt-get install -y git-core mysql-client libmcrypt-dev libpng12-dev php5-gd php5-mcrypt
RUN docker-php-ext-install gd mcrypt mbstring pdo_mysql

COPY . /var/www/html
WORKDIR /var/www/html
RUN curl -sS https://getcomposer.org/installer | php && php composer.phar install
