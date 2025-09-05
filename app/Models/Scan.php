<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ENUMS
use App\Enums\ScanStatus;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'container_id',
        'material_type_id',
        'image_url',
        'is_valid',
        'points_awarded',
        'scan_status',
        'rejection_reason',
        'scanned_at',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'scan_status' => ScanStatus::class,
        'points_awarded' => 'integer',
        'scanned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }
}
