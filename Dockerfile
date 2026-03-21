# Use official PHP with Apache
FROM php:8.2-apache

# Copy all files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Make sure Apache can read all files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable mod_rewrite for routing if needed
RUN a2enmod rewrite

# Expose port 10000 (Render maps this automatically)
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
