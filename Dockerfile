# Etapa 1: Dependencias de PHP y Composer
FROM php:8.2 AS vendor

# Instalar dependencias necesarias
RUN apt-get update \
    && apt-get install -y libpq-dev libgmp-dev zip unzip git curl \
    && docker-php-ext-configure gmp \
    && docker-php-ext-install bcmath gmp

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar archivos necesarios para Composer
COPY composer.json composer.lock artisan ./
COPY . .

# Instalar dependencias de PHP (sin dev)
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Etapa 2: Build de assets con Node y PNPM
FROM node:22.14 as nodebuild

WORKDIR /app

# Render inyecta las variables de entorno del dashboard al contenedor ya
# corriendo, pero NO al build de Docker en sí — sin declararlas acá con ARG,
# `pnpm run build` las ve vacías y Vite las hornea así en el bundle estático
# (import.meta.env.VITE_* se resuelve en build time, no en runtime).
ARG VITE_BASE_URL
ARG VITE_GOOGLE_CLIENT_ID_WEB
ARG VITE_PUSHER_APP_CLUSTER
ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_HOST
ARG VITE_PUSHER_PORT
ARG VITE_PUSHER_SCHEME
ENV VITE_BASE_URL=$VITE_BASE_URL \
    VITE_GOOGLE_CLIENT_ID_WEB=$VITE_GOOGLE_CLIENT_ID_WEB \
    VITE_PUSHER_APP_CLUSTER=$VITE_PUSHER_APP_CLUSTER \
    VITE_PUSHER_APP_KEY=$VITE_PUSHER_APP_KEY \
    VITE_PUSHER_HOST=$VITE_PUSHER_HOST \
    VITE_PUSHER_PORT=$VITE_PUSHER_PORT \
    VITE_PUSHER_SCHEME=$VITE_PUSHER_SCHEME

# Instalar PNPM globalmente para Node 22
RUN npm install -g pnpm

COPY package*.json ./
COPY pnpm-lock.yaml* ./
COPY . .

RUN pnpm install --frozen-lockfile
RUN pnpm run build

# Etapa 3: Imagen final de producción
FROM php:8.2-apache AS production

WORKDIR /var/www/html

# Instala extensiones necesarias + cron y supervisor
RUN apt-get update \
    && apt-get install -y libpq-dev libgmp-dev zip unzip git cron supervisor \
    && docker-php-ext-configure gmp \
    && docker-php-ext-install pdo pdo_pgsql bcmath gmp \
    && a2enmod rewrite headers negotiation \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar dependencias de PHP desde la etapa vendor
COPY --from=vendor /app/vendor ./vendor
COPY --from=nodebuild /app/composer.lock ./composer.lock
COPY --from=nodebuild /app/composer.json ./composer.json

# Copiar aplicación
COPY --from=nodebuild /app .

# Copiar assets compilados
COPY --from=nodebuild /app/public/build ./public/build

# Configurar Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Generar enlaces de storage
RUN php artisan storage:link || true


# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# --- CONFIGURACIÓN DE SUPERVISOR Y CRON ---
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/laravel-schedule
RUN chmod 0644 /etc/cron.d/laravel-schedule
RUN touch /var/log/cron.log
RUN crontab /etc/cron.d/laravel-schedule

# Expone el puerto que Render usará
EXPOSE 8080

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
