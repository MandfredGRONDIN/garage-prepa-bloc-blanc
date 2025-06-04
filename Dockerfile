FROM php:8.2.19-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    a2enmod rewrite

# Supprimer les fichiers HTML par défaut
RUN rm -rf /var/www/html/*

WORKDIR /var/www/html
