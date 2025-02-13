# POKEDEX LARAVEL
## Dockerfile
# Etapa 1: Usar PHP-FPM con Nginx como base
FROM php:8.2-fpm AS base

# Instalar dependencias del sistema necesarias para Laravel y Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto (excluyendo los no necesarios)
COPY . .

RUN mkdir -p storage/framework/views && mkdir -p storage/framework/cache && mkdir -p storage/framework/sessions && mkdir -p storage/framework/testing
# Instalar las dependencias de Composer
RUN composer install --no-dev --prefer-dist --no-interaction

# Copiar la configuración de Nginx
COPY nginx/default.conf /etc/nginx/sites-available/default



# Exponer los puertos 80 y 443 para Nginx
EXPOSE 80 443

# Iniciar Nginx y PHP-FPM
CMD service nginx start && php-fpm

## Archivos configurácion kubernetes (k8s)

