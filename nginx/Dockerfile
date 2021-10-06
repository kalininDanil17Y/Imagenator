FROM php:7.4-fpm-alpine

# Use the default production configuration
# docker exec 0855b03e3e93 mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
RUN \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql \