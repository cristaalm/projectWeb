<?php

namespace App\Http\Controllers;

use App\Enums\AllianceStatus;
use App\Http\Controllers\OldControllers\Controller;
use App\Models\Alliance;

class AllianceController extends Controller
{
    public function catalog()
    {
        $alliances = Alliance::where('status', AllianceStatus::ACTIVE->value)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->apiResponse(true, 'Alianzas obtenidas correctamente.', [
            'alliances' => $alliances,
        ], null, 200);
    }
}
