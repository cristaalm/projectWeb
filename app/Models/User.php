<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

use App\Notifications\CustomResetPassword;

// ENUMS
use App\Enums\UserStatus;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'tour',
        'avatar',
        'two_factor_status',
        'code_identity',
        'status',
        'google2fa_secret',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_status' => 'boolean',
        'status' => UserStatus::class,
        'tour' => 'boolean',
        'avatar' => 'string',
        'google2fa_secret' => 'string',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function merchant(): HasOne
    {
        return $this->hasOne(Merchant::class);
    }

    public function organizationMember(): HasOne
    {
        return $this->hasOne(OrganizationMember::class);
    }

    /**
     * A user is linked to an alliance through exactly one of merchant/organizationMember,
     * never both — if both are populated it's a data-integrity bug, not a case to silently pick from.
     */
    public function currentAlliance(): ?Alliance
    {
        if ($this->merchant && $this->organizationMember) {
            report(new \RuntimeException("User {$this->id} has both a merchant and an organization_member row."));
        }

        return $this->merchant?->alliance ?? $this->organizationMember?->alliance;
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(RewardUser::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token, $this->email));
    }
    
    public static function generatePassword($longitud = 16)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=[]{}|;:,.<>?';
        return Str::random($longitud, $caracteres);
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
