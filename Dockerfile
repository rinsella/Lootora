# syntax=docker/dockerfile:1.6
# =================================================================
# Lootora — multi-stage production Dockerfile
#   stage 1: composer dependencies
#   stage 2: node asset build (Laravel Mix)
#   stage 3: final PHP-FPM runtime image
# =================================================================

# ---------- Stage 1: composer dependencies ----------
# Pinned to a composer image bundling PHP 8.2 to match the runtime stage
# and avoid platform-requirement clashes with newer PHP.
FROM composer:2.7 AS vendor

WORKDIR /app
COPY composer.json composer.lock* ./
COPY database/ database/
COPY artisan artisan
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --ignore-platform-reqs

# ---------- Stage 2: node asset build ----------
FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json* webpack.mix.js* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY resources/ resources/
COPY public/ public/
COPY tailwind.config.js* postcss.config.js* ./
RUN npm run prod || npm run production || echo "no build script"

# ---------- Stage 3: PHP-FPM runtime ----------
FROM php:8.2-fpm-alpine AS app

# System deps + PHP extensions
RUN set -eux; \
    apk add --no-cache \
        bash \
        curl \
        git \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        libxml2-dev \
        mysql-client \
        nginx \
        supervisor \
        tzdata; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        zip; \
    rm -rf /var/cache/apk/*

# Composer binary (for in-container ops)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config
COPY docker/php/php.ini      /usr/local/etc/php/conf.d/zz-lootora.ini
COPY docker/php/opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini

# App workdir
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Inject built vendor + assets
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=assets /app/public /var/www/html/public

# Finalize composer autoloader (now that source + vendor are merged)
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Permissions
RUN set -eux; \
    mkdir -p storage/framework/{cache,sessions,views,testing} storage/logs bootstrap/cache; \
    chown -R www-data:www-data storage bootstrap/cache; \
    chmod -R ug+rwx storage bootstrap/cache

# Keep a pristine copy of public/ for the entrypoint to seed the shared volume on boot
RUN cp -aR /var/www/html/public /opt/lootora-public

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/lootora-entrypoint
RUN chmod +x /usr/local/bin/lootora-entrypoint

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD php artisan --version >/dev/null 2>&1 || exit 1

ENTRYPOINT ["lootora-entrypoint"]
CMD ["php-fpm", "-F"]
