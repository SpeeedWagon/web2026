# php/Dockerfile

# Use an official PHP image with Apache as the base
FROM php:8.1-apache

# Install necessary PHP extensions (example: pdo_mysql for MySQL access)
# Other common ones: mysqli, gd, zip, intl
RUN docker-php-ext-install pdo_mysql mysqli && docker-php-ext-enable pdo_mysql mysqli

# Optional: Install other dependencies (like Composer)
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Optional: Copy custom PHP configurations
# COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# The base image already sets the working directory to /var/www/html
# and runs Apache in the foreground.
# Your code will be mounted via the volume in docker-compose.yml