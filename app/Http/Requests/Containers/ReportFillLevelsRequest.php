<?php

namespace App\Http\Requests\Containers;

use App\Http\Requests\Containers\Concerns\ValidatesFillLevels;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class ReportFillLevelsRequest extends FormRequest
{
    use ValidatesFillLevels;

    /** Margen para relojes de contenedor un poco adelantados. */
    private const FUTURE_TOLERANCE_MINUTES = 5;

    /** Lecturas guardadas sin conexión que se mandan después, como máximo. */
    private const MAX_AGE_DAYS = 30;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Una hora futura bloquearía las lecturas reales siguientes (solo
            // gana la más nueva), y una muy vieja suele ser un reloj sin
            // sincronizar (ej. 1970).
            'measured_at' => [
                'nullable',
                'date',
                'before_or_equal:'.now()->addMinutes(self::FUTURE_TOLERANCE_MINUTES)->toIso8601String(),
                'after_or_equal:'.now()->subDays(self::MAX_AGE_DAYS)->toIso8601String(),
            ],
            ...$this->fillLevelRules('sensors', required: true),
        ];
    }

    public function messages(): array
    {
        return [
            'measured_at.before_or_equal' => 'La hora de medición no puede estar en el futuro. Revisa el reloj del contenedor.',
            'measured_at.after_or_equal' => 'La hora de medición tiene más de '.self::MAX_AGE_DAYS.' días. Revisa el reloj del contenedor.',
            ...$this->fillLevelMessages('sensors'),
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $this->validateDistinctSensors($validator, 'sensors');
    }
}
