FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable apache rewrite module (common for PHP web apps)
RUN a2enmod rewrite

# Copy project files into the container (optional for production, overridden by mount in dev)
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set appropriate permissions for Apache
RUN chown -R www-data:www-data /var/www/html
