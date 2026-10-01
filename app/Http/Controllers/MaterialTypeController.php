<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OldControllers\Controller;
use App\Repositories\MaterialTypeRepository;

class MaterialTypeController extends Controller
{
    public function __construct(
        private readonly MaterialTypeRepository $materials,
    ) {}

    public function catalog()
    {
        return $this->apiResponse(true, 'Tipos de material obtenidos correctamente.', [
            'material_types' => $this->materials->catalog(),
        ], null, 200);
    }
}
