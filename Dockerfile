# Use official PHP Apache image
FROM php:8.2-apache

# ------------------------
# Install system dependencies & PostgreSQL driver
# ------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# ------------------------
# Install Composer
# ------------------------
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# ------------------------
# Set working directory
# ------------------------
WORKDIR /var/www/html

# ------------------------
# Copy composer files and install dependencies first (Docker caching)
# ------------------------
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# ------------------------
# Copy rest of the application
# ------------------------
COPY . .

# ------------------------
# Fix permissions for Apache
# ------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# ------------------------
# Apache config: allow .htaccess overrides & enable rewrite
# ------------------------
RUN printf "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom \
    && a2enmod rewrite

# ------------------------
# Expose port Render expects
# ------------------------
EXPOSE 10000

# ------------------------
# Start Apache
# ------------------------
CMD ["apache2-foreground"]
