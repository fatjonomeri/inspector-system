FROM dunglas/frankenphp:1-php8.4

# Install extensions required by Symfony
RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    opcache \
    redis

# Set working directory
WORKDIR /app

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .