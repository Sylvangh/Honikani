FROM php:8.2-apache

# Install MySQL extensions
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy everything
COPY . /var/www/html/

WORKDIR /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable rewrite
RUN a2enmod rewrite

EXPOSE 10000

CMD ["apache2-foreground"]
