<?php

/*
*
*   ======================================== IMPORTANTE ========================================
*
*   Esta es la documentación de el controlador
*   App\Http\Controllers\Auth\AuthController
*
*   Puedes generar mas rapido la documentación de los enpoinds
*   pasando a la IA tu metodo y con el siguiente prompt:
*
*
*
*
*   Genera la documentación de Swagger para mi metodo [Nombre del metodo/funcion que deseas documentar]
*
*   documenta usando atributos de PHP 8+ (como #[OA\Post(...)], #[OA\Property(...)], etc.)
*
*   [Tu metodo/funcion]
*
*   ============================================================================================
*
*/

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Dashboard', description: 'Endpoints para gestión de dashboard')]
class DashDocumentation
{
    #[OA\Get(
        path: "/api/dash/getStats",
        tags: ["Dashboard"],
        summary: "Obtener estadísticas generales del sistema",
        description: "Devuelve métricas clave del sistema: total de usuarios activos, puntos acumulados, escaneos realizados y recompensas canjeadas, comparadas con el mes anterior.",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Estadísticas obtenidas correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Dashboard obtenido exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "users", properties: [
                                new OA\Property(property: "total", type: "integer", example: 1250),
                                new OA\Property(property: "lastMonthTotal", type: "integer", example: 1100),
                                new OA\Property(property: "growthPercentage", type: "number", format: "float", example: 13.64),
                            ]),
                            new OA\Property(property: "totalPoints", properties: [
                                new OA\Property(property: "total", type: "integer", example: 25000),
                                new OA\Property(property: "lastMonthTotal", type: "integer", example: 22000),
                                new OA\Property(property: "growthPercentage", type: "number", format: "float", example: 13.64),
                            ]),
                            new OA\Property(property: "totalScans", properties: [
                                new OA\Property(property: "total", type: "integer", example: 8500),
                                new OA\Property(property: "lastMonthTotal", type: "integer", example: 7800),
                                new OA\Property(property: "growthPercentage", type: "number", format: "float", example: 8.97),
                            ]),
                            new OA\Property(property: "totalRewards", properties: [
                                new OA\Property(property: "total", type: "integer", example: 320),
                                new OA\Property(property: "lastMonthTotal", type: "integer", example: 290),
                                new OA\Property(property: "growthPercentage", type: "number", format: "float", example: 10.34),
                            ]),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token de autenticación no proporcionado."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 401),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al obtener las estadísticas",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener el dashboard."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getStats(Request $request) {}
}
