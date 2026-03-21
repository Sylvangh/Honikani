FROM php:8.2-apache

# Install PostgreSQL driver
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Copy files
COPY . /var/www/html/

WORKDIR /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 🔥 THIS PART FIXES FORBIDDEN
RUN echo '<Directory /var/www/html>\
    Options Indexes FollowSymLinks\
    AllowOverride All\
    Require all granted\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# Enable rewrite
RUN a2enmod rewrite

EXPOSE 10000

CMD ["apache2-foreground"]
