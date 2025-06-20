FROM php:8.2-apache

# 🔧 Install libreoffice, unzip, DAN PHP zip extension
RUN apt-get update && \
    apt-get install -y \
    libreoffice \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean

RUN a2enmod rewrite

# Copy file lo ke folder Apache
COPY . /var/www/html/
WORKDIR /var/www/html/