<?php

namespace App\Models;

use App\Enums\ContainerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Container extends Model
{
    use HasFactory;

    public const TOKEN_PREFIX = 'ect_';

    protected $table = 'containers';

    protected $fillable = [
        'name',
        'serial_number',
        'location',
        'latitude',
        'longitude',
        'status',
    ];

    // El token nunca sale en una respuesta serializada del modelo: solo lo
    // entregan los endpoints dedicados de ContainerController.
    protected $hidden = [
        'api_token',
        'api_token_hash',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'status' => ContainerStatus::class,
        'api_token' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::creating(function (Container $container) {
            if (! $container->api_token) {
                $container->assignNewToken();
            }
        });
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Genera un token nuevo y lo deja asignado (sin guardar). El token en
     * claro queda cifrado en `api_token`; `api_token_hash` es lo que se usa
     * para buscar el contenedor en cada petición.
     */
    public function assignNewToken(): string
    {
        $token = self::TOKEN_PREFIX.Str::random(48);

        $this->api_token = $token;
        $this->api_token_hash = self::hashToken($token);

        return $token;
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
