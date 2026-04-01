# --- Build stage: compile assets ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources/ ./resources/
RUN npm run build

# --- Production stage ---
FROM php:8.4-cli

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev unzip curl git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# PHP config for large OCR payloads
RUN echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/checkbatch.ini \
    && echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/checkbatch.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/checkbatch.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Copy built assets from node stage
COPY --from=assets /app/public/build ./public/build

# Re-run composer scripts (post-autoload-dump etc)
RUN composer dump-autoload --optimize

# Ensure storage directories exist and are writable
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
