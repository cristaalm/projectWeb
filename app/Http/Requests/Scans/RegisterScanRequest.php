<?php

namespace App\Http\Requests\Scans;

use App\Http\Requests\Containers\Concerns\ValidatesFillLevels;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterScanRequest extends FormRequest
{
    use ValidatesFillLevels;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'uuid'],
            'code_identity' => ['required', 'string', 'max:30'],
            'material' => ['required', 'string', Rule::exists('material_types', 'slug')],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            // Opcional: nivel de llenado de los compartimentos al momento del
            // escaneo (misma forma que POST /containers/fill-levels).
            ...$this->fillLevelRules('fill_levels', required: false),
        ];
    }

    public function messages(): array
    {
        return [
            'material.exists' => 'El material indicado no existe en el catálogo.',
            ...$this->fillLevelMessages('fill_levels'),
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $this->validateDistinctSensors($validator, 'fill_levels');
    }
}
