<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
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
            'checkTokenExpiration' => \App\Http\Middleware\CheckTokenExpiration::class,
            'apiAuthUser' => \App\Http\Middleware\ApiAuthUser::class,
        ]);

        // Middleware global para el grupo 'api'
        $middleware->group('api', [
            'apiAuthUser',
            'checkTokenExpiration',
        ]);
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
