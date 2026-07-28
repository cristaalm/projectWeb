<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware de autenticación
        $middleware->alias([
            'auth' => \App\Http\Middleware\ApiAuthenticate::class,
            'ensureUserIsActive' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Middleware global para el grupo 'api': arranca la sesión/cookie para
        // requests que vienen de un dominio "stateful" (SANCTUM_STATEFUL_DOMAINS),
        // permitiendo que auth:sanctum autentique por cookie o por bearer token
        // según corresponda, sin lógica manual duplicada.
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Confía en el proxy que termina TLS en producción (Render) para que Laravel
        // detecte el esquema (https) vía X-Forwarded-Proto, en vez de forzarlo a mano.
        // En local no hay proxy delante, así que las URLs generadas siguen siendo http.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de excepciones para rutas API
        $exceptions->shouldRenderJsonWhen(function ($request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Personalizar respuesta para autenticación fallida
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'No autenticado',
                    'status' => 401,
                    'authenticated' => false
                ], 401);
            }
        });

        // FormRequest validation failures respetan el mismo envelope que Controller::apiResponse(),
        // ya que no pasan por el controlador (Laravel las intercepta antes de que se ejecute).
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Los datos enviados no son válidos.',
                    'data' => null,
                    'errors' => $e->errors(),
                    'code' => 422,
                ], 422);
            }
        });

        // Personalizar respuesta para rutas no encontradas
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Ruta no encontrada',
                    'status' => 404
                ], 404);
            }
        });
    })
    ->withCommands([
        \App\Console\Commands\ClearTokens::class,
    ])
    ->create();
