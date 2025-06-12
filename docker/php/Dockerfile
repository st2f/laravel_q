FROM php:8.3-fpm

# Update package list and install dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libjpeg-dev \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libzip-dev \
  libicu-dev \
  zip \
  unzip \
  default-mysql-client \
  supervisor \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --enable-gd --with-jpeg

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

COPY ./docker/supervisor/supervisord.conf /etc/supervisord.conf

RUN mkdir -p /var/log/supervisor /var/run/supervisor

WORKDIR /var/www

# Use Supervisor as the container's entry point
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
# CMD ["php-fpm"]

EXPOSE 9000
