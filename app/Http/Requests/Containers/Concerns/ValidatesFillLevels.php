<?php

namespace App\Http\Requests\Containers\Concerns;

use App\Models\ContainerSensor;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\Rule;

/**
 * Reglas compartidas de las lecturas de llenado, que llegan con la misma forma
 * en POST /containers/fill-levels (`sensors`) y en POST /scans (`fill_levels`):
 * una lista de { material, fill_level, sensor_key? }.
 */
trait ValidatesFillLevels
{
    protected function fillLevelRules(string $field, bool $required): array
    {
        return [
            $field => [$required ? 'required' : 'nullable', 'array', 'min:1', 'max:20'],
            "{$field}.*" => ['required', 'array:material,fill_level,sensor_key'],
            "{$field}.*.material" => ['required', 'string', Rule::exists('material_types', 'slug')],
            "{$field}.*.fill_level" => ['required', 'numeric', 'between:0,100'],
            "{$field}.*.sensor_key" => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/'],
        ];
    }

    protected function fillLevelMessages(string $field): array
    {
        return [
            "{$field}.*.array" => 'Cada lectura solo puede tener material, fill_level y sensor_key.',
            "{$field}.*.material.exists" => 'El material indicado no existe en el catálogo.',
            "{$field}.*.fill_level.between" => 'El nivel de llenado debe estar entre 0 y 100.',
            "{$field}.*.sensor_key.regex" => 'El identificador del compartimento solo puede tener letras, números, guiones y guiones bajos.',
        ];
    }

    /**
     * Dos lecturas del mismo compartimento en una sola petición serían
     * ambiguas (¿cuál vale?), así que se rechazan. Sin sensor_key, el
     * compartimento es el material.
     */
    protected function validateDistinctSensors(ValidatorContract $validator, string $field): void
    {
        $validator->after(function (ValidatorContract $validator) use ($field) {
            $seen = [];

            foreach ((array) $this->input($field, []) as $index => $reading) {
                if (! is_array($reading) || ! is_string($reading['material'] ?? null)) {
                    continue;
                }

                $sensorKey = $reading['sensor_key'] ?? null;
                $key = ContainerSensor::keyFor(is_string($sensorKey) ? $sensorKey : null, $reading['material']);

                if (isset($seen[$key])) {
                    $validator->errors()->add(
                        "{$field}.{$index}.sensor_key",
                        "El compartimento «{$key}» viene repetido en la misma petición."
                    );
                }

                $seen[$key] = true;
            }
        });
    }
}
