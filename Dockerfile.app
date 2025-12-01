# Start from the official PHP 8.2 FPM image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev

# Install required PHP extensions for Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer (globally)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Remove any default Debian Node.js (which is v18)
RUN apt-get remove -y nodejs npm || true

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Verify version (optional but helpful)
RUN node -v
RUN npm -v

# Set working directory
WORKDIR /var/www/html

# Copy existing application files
COPY . .

# Fix permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Install npm dependencies
RUN npm install

# Build frontend assets
RUN npm run build

# Expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]
