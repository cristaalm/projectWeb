<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function apiResponse($success, $message, $data = null, $errors = null, $code = 200)
    {
        // Determinar si mostrar detalles técnicos
        $showTechnical = config('app.debug');
        // Si errors es un array con claves de validación, se permite mostrarlo siempre
        $isValidation = is_array($errors) && array_keys($errors) !== range(0, count($errors) - 1);
        if (!$showTechnical && $errors && !$isValidation) {
            if ($code >= 300 && $code < 400) {
                $errors = 'La solicitud fue redirigida o requiere acciones adicionales.';
            } elseif ($code >= 400 && $code < 500) {
                $errors = 'La solicitud no pudo ser procesada. Por favor revise los datos enviados.';
            } elseif ($code >= 500 && $code < 600) {
                $errors = 'Ocurrió un error interno. Por favor contacte al administrador.';
            } else {
                $errors = 'Ocurrió un error inesperado.';
            }
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'code' => $code,
        ], $code);
    }

    protected function unsetDataPagination($dataPaginate)
    {
        $data = $dataPaginate->toArray();
        unset($data['links']);
        unset($data['first_page_url']);
        unset($data['last_page_url']);
        unset($data['prev_page_url']);
        unset($data['next_page_url']);
        unset($data['path']);
        unset($data['current_page']);
        unset($data['per_page']);
        unset($data['from']);
        unset($data['to']);
        return $data;
    }
}
