<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointEarning extends Model
{
    const UPDATED_AT = null;

    protected $table = 'point_earnings';

    protected $fillable = [
        'user_id',
        'scan_id',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}
