<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'RENOVA API',
    description: 'API JSON de RENOVA (backend Laravel + Sanctum). Documentación en reconstrucción módulo por módulo tras el rediseño del esquema de base de datos; por ahora están documentados Auth, Users (administración de usuarios) y el catálogo de Alliances.'
)]
#[OA\Server(
    url: '/api',
    description: 'Servidor actual (relativo al host donde se sirve la API)'
)]
#[OA\SecurityScheme(
    securityScheme: 'sessionCookie',
    type: 'apiKey',
    in: 'cookie',
    name: 'laravel_session',
    description: "Autenticación por sesión para la SPA web. Requiere primero pedir GET /sanctum/csrf-cookie y luego enviar el header X-XSRF-TOKEN (tomado de la cookie XSRF-TOKEN) en cada petición mutante. Sanctum activa este modo automáticamente para dominios listados en SANCTUM_STATEFUL_DOMAINS."
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerToken',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Personal Access Token',
    description: 'Autenticación por token para clientes no-navegador (apps móviles). Token emitido por POST /api/auth/login o /api/auth/register cuando la petición no trae sesión de Sanctum activa. Enviar como header Authorization: Bearer {token}.'
)]
class OpenApi
{
    // Contenedor de anotaciones globales de OpenAPI (Info, Servers, SecuritySchemes).
    // No se instancia; l5-swagger solo escanea los atributos de esta clase.
}
