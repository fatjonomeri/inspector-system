FROM dunglas/frankenphp:1-php8.4

# Install extensions required by Symfony
RUN install-php-extensions \
    pdo_pgsql \
    intl \
    zip \
    opcache

# Set working directory
WORKDIR /app

# Copy Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.* ./

# Install dependencies
RUN composer install --no-dev --no-scripts --prefer-dist

# Copy application files
COPY . .

# Finish composer setup
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative