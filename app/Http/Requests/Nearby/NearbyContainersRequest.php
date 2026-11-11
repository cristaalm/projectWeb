<?php

namespace App\Http\Requests\Nearby;

use App\Http\Requests\Nearby\Concerns\ValidatesUserLocation;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

/**
 * La ubicación es obligatoria y tiene que pasar el control de calidad
 * (precisión/antigüedad): sin ella no hay "cerca".
 */
class NearbyContainersRequest extends FormRequest
{
    use ValidatesUserLocation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->locationRules(required: true);
    }

    public function messages(): array
    {
        return $this->locationMessages();
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $this->rejectUnusableLocation($validator);
    }
}
