FROM php:8.2-apache

# Install LibreOffice & unzip (buat edit .docx)
RUN apt-get update && \
    apt-get install -y libreoffice libreoffice-writer unzip && \
    apt-get clean

# Aktifin mod_rewrite (kalau nanti lo butuh .htaccess)
RUN a2enmod rewrite

# Copy project ke container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/