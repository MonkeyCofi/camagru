FROM php:8-fpm-alpine

# make the development .ini the default
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
# remove the comment from the line: ;extension=pdo_mysql
# RUN sed -i 's/;extension=pdo_mysql/extension=pdo_mysql/' /usr/local/etc/php/php.ini

RUN docker-php-ext-install pdo_mysql