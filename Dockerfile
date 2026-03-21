FROM php:8.2-apache

# Install PostgreSQL + required libs
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Copy all files
COPY . /var/www/html/

WORKDIR /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable rewrite
RUN a2enmod rewrite

EXPOSE 10000

CMD ["apache2-foreground"]
