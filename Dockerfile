#usa una imagen oficial de PHP con extensiones necesarias

FROM php:8.1-fpm

#Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
git \
curl \
zip \
unzip \
libonig-dev \
libxml2-dev \
libzip-dev \
libpng-dev \
libjpeg-dev \
libfreetype6-dev \
&& docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd 

#instalar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#Establece el directorio de trabajo
WORKDIR /var/www

#copa el proyecto
COPY . .
#Da permisos a la carpeta de storage y bootstrap
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

#Expone el puerto(usado por nginx en docker-compose)
EXPOSE 9000

#Ejecuta el comando de php-fpm
CMD ["php-fpm"]