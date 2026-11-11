<?php

namespace App\Http\Requests\Nearby\Concerns;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Carbon;

/**
 * Ubicación del usuario y `limit` compartidos por los endpoints "cerca de ti".
 *
 * Se separan dos niveles de error:
 * - Formato (coordenadas fuera de rango, fecha que no es fecha, campos sueltos):
 *   siempre es 422 por validación.
 * - Calidad (precisión peor que `nearby.max_accuracy_m`, lectura vieja o con el
 *   reloj adelantado): cada endpoint decide. El de recompensas la trata como
 *   "sin ubicación"; los de comercios y contenedores responden 422.
 */
trait ValidatesUserLocation
{
    protected function locationRules(bool $required): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            'latitude' => [$presence, 'required_with:longitude,accuracy,captured_at', 'numeric', 'between:-90,90'],
            'longitude' => [$presence, 'required_with:latitude,accuracy,captured_at', 'numeric', 'between:-180,180'],
            'accuracy' => [$presence, 'required_with:latitude,longitude', 'numeric', 'min:0'],
            'captured_at' => [$presence, 'required_with:latitude,longitude', 'date'],
            'limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function locationMessages(): array
    {
        return [
            'latitude.required_with' => 'La latitud es obligatoria cuando se envía la ubicación.',
            'longitude.required_with' => 'La longitud es obligatoria cuando se envía la ubicación.',
            'accuracy.required_with' => 'La precisión es obligatoria cuando se envía la ubicación.',
            'captured_at.required_with' => 'La hora de captura es obligatoria cuando se envía la ubicación.',
            'limit.min' => 'El límite debe ser al menos 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'accuracy' => 'precisión',
            'captured_at' => 'hora de captura',
            'limit' => 'límite',
        ];
    }

    /**
     * Motivo por el que la ubicación (ya con formato válido) no sirve para
     * calcular distancias, o null si sirve o no se mandó.
     *
     * @return array{0: string, 1: string}|null [campo, mensaje]
     */
    protected function locationQualityProblem(): ?array
    {
        if (! $this->filled('latitude')) {
            return null;
        }

        $maxAccuracy = config('nearby.max_accuracy_m');
        if ((float) $this->input('accuracy') > $maxAccuracy) {
            return ['accuracy', "La ubicación es poco precisa (más de {$maxAccuracy} m). Intenta de nuevo con mejor señal."];
        }

        $capturedAt = Carbon::parse($this->input('captured_at'));
        if ($capturedAt->gt(now()->addMinutes(config('nearby.future_tolerance_minutes')))) {
            return ['captured_at', 'La hora de captura de la ubicación está en el futuro. Revisa el reloj del teléfono.'];
        }

        $maxAge = config('nearby.max_location_age_minutes');
        if ($capturedAt->lt(now()->subMinutes($maxAge))) {
            return ['captured_at', "La ubicación tiene más de {$maxAge} minutos. Vuelve a obtenerla."];
        }

        return null;
    }

    /**
     * Para los endpoints donde la ubicación es obligatoria: convierte un
     * problema de calidad en error de validación (422). Solo si el formato ya
     * es válido: con una fecha mal escrita no hay nada que revisar.
     */
    protected function rejectUnusableLocation(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if ($validator->errors()->isEmpty() && ($problem = $this->locationQualityProblem())) {
                $validator->errors()->add(...$problem);
            }
        });
    }

    /**
     * Coordenadas utilizables, o null si no se mandaron o no pasan el control
     * de calidad. Llamar solo después de validar.
     *
     * @return array{latitude: float, longitude: float}|null
     */
    public function usableLocation(): ?array
    {
        if (! $this->filled('latitude') || $this->locationQualityProblem() !== null) {
            return null;
        }

        return [
            'latitude' => (float) $this->validated('latitude'),
            'longitude' => (float) $this->validated('longitude'),
        ];
    }

    /** `limit` pedido, con el default y topado al máximo (pasarse no es error). */
    public function limit(): int
    {
        return min(
            (int) ($this->validated('limit') ?? config('nearby.default_limit')),
            (int) config('nearby.max_limit'),
        );
    }
}
