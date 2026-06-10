# ============================================================
# Stage 1: Base PHP image dengan semua extension yang dibutuhkan
# ============================================================
FROM php:8.4-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    nodejs \
    npm

# Install PHP extensions
RUN apk add --no-cache sqlite-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_sqlite \
    pgsql \
    zip \
    gd \
    bcmath \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ============================================================
# Stage 2: Dependencies (terpisah agar layer di-cache)
# ============================================================
FROM base AS deps

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci

# ============================================================
# Stage 3: Production image
# ============================================================
FROM base AS production

WORKDIR /var/www

# Copy composer vendor dari stage deps
COPY --from=deps /var/www/vendor ./vendor

# Copy semua source code
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Build frontend assets
COPY --from=deps /var/www/node_modules ./node_modules
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

# ============================================================
# Stage 4: Test image
# Berisi dev dependencies (PHPUnit, Pint, Mockery, dll)
# Dipakai oleh Jenkins untuk menjalankan test, BUKAN untuk production
# ============================================================
FROM base AS test

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-scripts --prefer-dist

COPY . .

RUN composer dump-autoload

CMD ["php", "artisan", "test"]
