# Etapa 1: Instalar dependencias PHP (Composer)
FROM php:8.2 as vendor
WORKDIR /app

# Instalar dependencias necesarias
RUN apt-get update \
    && apt-get install -y libpq-dev zip unzip git curl \
    && docker-php-ext-install bcmath

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock artisan ./
COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Etapa 2: Build de assets con Node y PNPM
FROM node:22.14 as nodebuild
WORKDIR /app

# Instalar PNPM globalmente
RUN npm install -g pnpm

COPY package*.json ./
COPY pnpm-lock.yaml* ./

COPY . .
RUN pnpm install --frozen-lockfile
RUN pnpm run build

# Etapa 3: Imagen final con Apache y PHP
FROM php:8.2-apache
WORKDIR /var/www/html

# Instala extensiones necesarias + cron y supervisor
RUN apt-get update \
    && apt-get install -y libpq-dev zip unzip git cron supervisor \
    && docker-php-ext-install pdo pdo_pgsql bcmath \
    && a2enmod rewrite headers negotiation \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Establece el DocumentRoot en public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Aplica el cambio en la configuración de Apache
RUN sed -ri "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri "s!<Directory /var/www/>!<Directory /var/www/html/public>!g" /etc/apache2/apache2.conf \
    && sed -ri "s!AllowOverride None!AllowOverride All!g" /etc/apache2/apache2.conf

# Copia dependencias y código
COPY --from=vendor /app/vendor ./vendor
COPY --from=nodebuild /app/public ./public
COPY --from=nodebuild /app/resources ./resources
COPY --from=nodebuild /app/routes ./routes
COPY --from=nodebuild /app/app ./app
COPY --from=nodebuild /app/storage ./storage
COPY --from=nodebuild /app/bootstrap ./bootstrap
COPY --from=nodebuild /app/config ./config
COPY --from=nodebuild /app/database ./database
COPY --from=nodebuild /app/artisan ./artisan
COPY --from=nodebuild /app/tests ./tests
COPY --from=nodebuild /app/composer.json ./composer.json
COPY --from=nodebuild /app/composer.lock ./composer.lock

# Copia el archivo .htaccess
COPY --from=nodebuild /app/public/.htaccess /var/www/html/public/.htaccess

# Crear los directorios necesarios y asignar permisos correctos
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Crear el symlink de public/storage → storage/app/public
RUN php artisan storage:link

# Asegura permisos (solo para desarrollo)
RUN chmod -R 777 /var/www/html \
    && chown -R www-data:www-data /var/www/html

# --- CONFIGURACIÓN DE SUPERVISOR Y CRON ---

# Crea el archivo de configuración de supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Crea el archivo de cron (ejecuta schedule:run cada minuto)
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/laravel-schedule
RUN chmod 0644 /etc/cron.d/laravel-schedule
RUN touch /var/log/cron.log

# Asegúrate de que cron se inicie con el usuario correcto
RUN crontab /etc/cron.d/laravel-schedule

# Expone el puerto que Render usará
EXPOSE 8080

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]   
