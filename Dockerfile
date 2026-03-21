# Use official PHP with Apache image
FROM php:8.2-apache

# Copy all files to the web server root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Expose port 10000 (Render will map to 80)
EXPOSE 10000

# Start Apache in the foreground
CMD ["apache2-foreground"]
