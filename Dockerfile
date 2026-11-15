FROM php:8.2-apache

# PHP extension used by the app (mysqli) and Apache rewrite module
RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# The app is served under /clinicdesk (same path used with XAMPP)
WORKDIR /var/www/html/clinicdesk
COPY src/ /var/www/html/clinicdesk/

# Redirect the site root to the app
RUN echo '<?php header("Location: /clinicdesk/");' > /var/www/html/index.php

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD php -r 'exit(@file_get_contents("http://localhost/clinicdesk/") === false ? 1 : 0);'
