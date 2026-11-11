<?php

namespace App\Http\Requests\Nearby;

use App\Http\Requests\Nearby\Concerns\ValidatesUserLocation;
use Illuminate\Foundation\Http\FormRequest;

/**
 * La ubicación es opcional: si no viene, o viene pero no pasa el control de
 * calidad (precisión/antigüedad), las recompensas se eligen solo por puntos.
 */
class NearbyRewardsRequest extends FormRequest
{
    use ValidatesUserLocation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->locationRules(required: false);
    }

    public function messages(): array
    {
        return $this->locationMessages();
    }
}
