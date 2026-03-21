FROM php:8.2-apache

# Install PHP MySQL extension
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy site files
COPY index.php user/ css/ js/ images/ /var/www/html/
WORKDIR /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable mod_rewrite
RUN a2enmod rewrite

# Expose port for Render
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
