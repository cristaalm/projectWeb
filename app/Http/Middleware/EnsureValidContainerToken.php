<?php

namespace App\Http\Middleware;

use App\Enums\ContainerStatus;
use App\Repositories\ContainerRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autenticación de un contenedor físico por su token propio (header
 * X-Container-Token). Deja el contenedor resuelto en el request para que el
 * controlador nunca tenga que confiar en un identificador enviado en el body:
 * un contenedor solo puede operar a su propio nombre.
 */
class EnsureValidContainerToken
{
    public function __construct(
        private readonly ContainerRepository $containers,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Container-Token');
        $container = $token ? $this->containers->findByToken($token) : null;

        if (! $container) {
            return $this->reject('Token de contenedor ausente o inválido.', 401);
        }

        if ($container->status !== ContainerStatus::ACTIVE) {
            return $this->reject('El contenedor no está activo.', 403);
        }

        $request->attributes->set('container', $container);

        return $next($request);
    }

    private function reject(string $message, int $status): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => null,
            'code' => $status,
        ], $status);
    }
}
