# Proyecto: PI5E

Aplicación web con Laravel 10+, diseñada como API RESTful con autenticación por tokens (Sanctum), soporte multilingüe (español) y frontend moderno con Vite + Tailwind CSS.

![PHP](https://img.shields.io/badge/PHP-%5E8.1-777BB4?logo=php) ![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel) ![Node](https://img.shields.io/badge/Node-%5E18-339933?logo=node.js) ![pnpm](https://img.shields.io/badge/pnpm-%5E8-F69220?logo=pnpm) ![PostgreSQL](https://img.shields.io/badge/PostgreSQL-%5E16-336791?logo=postgresql)

- 🚀 Desarrollo rápido y moderno
- 🔐 Autenticación segura con Laravel Sanctum
- 🌎 Internacionalización lista (español)

## Índice

- [Requisitos](#requisitos)
- [Instalación y Configuración](#instalacion-y-configuracion)
  - [1. Clonar el repositorio](#1-clonar-el-repositorio)
  - [2. Instalar dependencias de PHP](#2-instalar-dependencias-de-php)
  - [3. Configurar variables de entorno](#3-configurar-variables-de-entorno)
  - [4. Generar la clave de la aplicación](#4-generar-la-clave-de-la-aplicacion)
  - [5. Ejecutar migraciones y publicar configuraciones](#5-ejecutar-migraciones-y-publicar-configuraciones)
  - [6. Configurar traducciones (español)](#6-configurar-traducciones-espanol)
  - [7. Instalar dependencias del frontend](#7-instalar-dependencias-del-frontend)
  - [8. Compilar assets (CSS/JS)](#8-compilar-assets-cssjs)
  - [9. Crear enlace simbólico para storage](#9-crear-enlace-simbolico-para-storage)
  - [10. Limpiar caché (opcional)](#10-limpiar-cache-opcional)
- [Iniciar el servidor](#iniciar-el-servidor)
- [Autenticación con Laravel Sanctum](#autenticacion-con-laravel-sanctum)
- [Internacionalización (i18n)](#internacionalizacion-i18n)
- [Comandos útiles](#comandos-utiles)
- [Estructura clave del proyecto](#estructura-clave-del-proyecto)

Este proyecto está listo para desarrollo local y puede desplegarse fácilmente en producción.

## Requisitos

Antes de comenzar, asegúrate de tener instalado:

- PHP >= 8.1
- Composer
- Node.js >= 18 + pnpm
- PostgreSQL >= 12
- Git

## Instalación y Configuración

Sigue estos pasos para clonar y configurar el proyecto localmente.

### 1. Clonar el repositorio

```bash
git clone https://github.com/cristaalm/projectWeb.git
cd pi5e
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

Esto instala todas las dependencias de Laravel y paquetes adicionales (Sanctum, Laravel Lang, etc.).

### 3. Configurar variables de entorno

Copia el archivo de ejemplo .env.example:

```bash
cp .env.example .env
```

Edita el archivo .env con tus credenciales de base de datos:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pi5e
DB_USERNAME=postgres # Se recomienda usar un usuario con permisos limitados
DB_PASSWORD=tu_password # Cambia esto por tu contraseña real

# Idioma predeterminado
APP_LOCALE=es

# Zona horaria de México
APP_TIMEZONE=America/Mexico_City
```

### 4. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y publicar configuraciones

Publicar migraciones de Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Ejecutar migraciones

```bash
php artisan migrate
```

Esto creará las tablas necesarias, incluyendo users, personal_access_tokens, etc.

### 6. Configurar traducciones (español)

El proyecto incluye traducciones completas en español. Asegúrate de tener la carpeta lang/es/.

Si necesitas actualizarlas con la versión oficial:

```bash
php artisan lang:publish es
```

### 7. Instalar dependencias del frontend

```bash
pnpm install
```

También puedes usar npm install si prefieres.

### 8. Compilar assets (CSS/JS)

Inicia el compilador Vite en modo desarrollo:

```bash
npm run dev
```

Esto genera los archivos estáticos en public/build/.

### 9. Crear enlace simbólico para storage

```bash
php artisan storage:link
```

Permite acceder a archivos subidos via storage/app/public.

### 10. Limpiar caché (opcional, pero recomendado)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Iniciar el servidor

```bash
php artisan serve
```

Accede a la aplicación en:
http://localhost:8000

Ten en cuenta que debes tener corriendo vite con `npm run dev`, para acceder a http://localhost:8000.

## Autenticación con Laravel Sanctum

Este proyecto utiliza Sanctum para autenticación API. Características:

- Tokens de acceso personal.
- Validación de estado del usuario (status).
- Expiración personalizada de tokens.
- Respuestas JSON consistentes ante errores.

## Internacionalización (i18n)

- Idioma predeterminado: español (`es`)
- Traducciones cargadas desde `lang/es/`
- Mensajes de validación, autenticación y sistema en español

## Comandos útiles

```bash
# Reinicia BD y ejecuta seeders
php artisan migrate:fresh --seed

# Carga datos iniciales
php artisan db:seed

# Consola interactiva de Laravel
php artisan tinker

# Limpia tokens expirados (comando personalizado)
php artisan clear:tokens
```

## Estructura clave del proyecto

```text
pi5e/
├── app/
├── bootstrap/app.php            # Configuración centralizada
├── config/
├── database/
│   └── migrations/              # Migraciones (incluye Sanctum)
├── lang/                        # Traducciones en español
├── resources/
│   ├── js/                      # Frontend con Vite
│   └── views/                   # Blade templates
├── routes/
│   ├── api.php                  # Rutas API
│   └── web.php                  # Rutas web
├── storage/
│   └── app/public/              # Archivos subidos (Archivos que se generan en producción por usuarios)
├── .env                         # Variables de entorno
└── composer.json                # Dependencias PHP
```
