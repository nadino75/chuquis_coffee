# syntax=docker/dockerfile:1

FROM php:8.2.12-apache

# Set working directory
WORKDIR /var/www/html

# System deps and PHP extensions commonly required by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        curl \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
	gd \
        mbstring \
        exif \
        pcntl \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules and set DocumentRoot to /public
RUN a2enmod rewrite && \ 
	sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf  &&\
	sed -ri 's!<Directory /var/www/>!<Directory /var/www/html/public/>!g' /etc/apache2/apache2.conf && \
	sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Install Composer (copy from official image for speed and reliability)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Expose port 80 (handled by base image)
# EXPOSE 80

# Default CMD is apache2-foreground from base image

RUN mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN mkdir -p /var/www/html/storage/framework/views \
	&& mkdir -p /var/www/html/storage/framework/cache \
	&& mkdir -p /var/www/html/storage/framework/sessions \
	&& mkdir -p /var/www/html/bootstrap/cache \
	&& chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
	&& chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
