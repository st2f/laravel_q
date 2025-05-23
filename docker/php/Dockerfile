FROM php:8.3-fpm

# Update package list and install dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libzip-dev \
  libicu-dev \
  zip \
  unzip \
  default-mysql-client
  # php8.3-dom \
  # php8.3-mysql \
  # php8.3-xml \
  # php8.3-mbstring

# Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    xml \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache

# Redis for caching/sessions
RUN pecl install redis && docker-php-ext-enable redis

# Xdebug for debugging - REMOVE IN PRODUCTION!
# RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
