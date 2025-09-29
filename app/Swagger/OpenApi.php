<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API RENOVA",
    description: "Documentación de la API de Laravel con Swagger"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Ingresa el token JWT sin la palabra 'Bearer'. El sistema lo añadirá automáticamente."
)]
#[OA\Server(
    url: "https://renova-3q4h.onrender.com",
    description: "Servidor principal (Render)"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Servidor local (php artisan serve)"
)]
class OpenApi {}
