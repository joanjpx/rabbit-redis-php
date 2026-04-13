FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    zip \
    unzip \
    libzip-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install bcmath sockets zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php", "-a"]
