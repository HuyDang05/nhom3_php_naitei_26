FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache \
        nginx \
        libpq \
        icu-libs \
        libzip \
        libxml2 \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        icu-dev \
        libzip-dev \
        libxml2-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        intl \
        zip \
        dom \
        opcache \
    && apk del .build-deps

FROM php-base AS composer-dependencies

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM node:22-alpine AS frontend-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php-base AS production

WORKDIR /var/www/html

COPY . .
COPY --from=composer-dependencies /app/vendor ./vendor
COPY --from=frontend-build /app/public/build ./public/build

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/start.sh /usr/local/bin/start-app

RUN chmod +x /usr/local/bin/start-app \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 10000

CMD ["/usr/local/bin/start-app"]