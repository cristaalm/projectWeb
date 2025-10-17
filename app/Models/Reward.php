<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'rewards';
    protected $primaryKey = 'id';
    protected $keyType = 'integer';
    public $incrementing = true;

    protected $fillable = [
        'alliance_id',
        'name',
        'description',
        'points_required',
        'image',
        'stock',
        'code',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'image' => 'boolean',
        'stock' => 'integer',
        'code' => 'string',
        'expires_at' => 'datetime',
    ];

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
    
    public static function calculateEan13CheckDigit(string $digits12): string
    {
        if (strlen($digits12) !== 12 || !ctype_digit($digits12)) {
            throw new \InvalidArgumentException('Se requieren 12 dígitos numéricos.');
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $digits12[$i];
            // Posición 1 (índice 0) es impar → multiplicar por 1
            // Posición 2 (índice 1) es par → multiplicar por 3
            if ($i % 2 === 0) {
                $sum += $digit;        // posiciones impares (1ª, 3ª, ...)
            } else {
                $sum += $digit * 3;    // posiciones pares (2ª, 4ª, ...)
            }
        }

        $checksum = (10 - ($sum % 10)) % 10;
        return (string) $checksum;
    }
}
