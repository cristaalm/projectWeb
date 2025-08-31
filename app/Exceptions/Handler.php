<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'No autenticado',
                'status' => 401
            ], 401);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => 'Ruta no encontrada',
                'status' => 404
            ], 404);
        });
    }
}
