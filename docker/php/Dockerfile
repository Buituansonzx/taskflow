FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    postgresql-dev \
    linux-headers \
    autoconf \
    g++ \
    make \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    xml

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis \
    && apk del autoconf g++ make $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Fix permissions
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www
USER www

EXPOSE 9000
CMD ["php-fpm"]
