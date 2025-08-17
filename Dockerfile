FROM php:8.3-fpm-alpine

# системные пакеты
RUN apk add --no-cache \
    bash git unzip libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    libzip-dev oniguruma-dev icu-dev postgresql-dev

# PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg \
 && docker-php-ext-install gd exif pdo pdo_pgsql intl zip bcmath

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app
USER app

WORKDIR /var/www

CMD ["php-fpm"]
