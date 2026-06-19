<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupRequest extends Model
{
    protected $fillable = [
        'user_id',
        'pickup_address',
        'pickup_phone',
        'latitude',
        'longitude',
        'estimated_distance',
        'pickup_date',
        'pickup_time',
        'notes',
        'status',
        'assigned_to',
        'deposit_id',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'estimated_distance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedPetugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }
}
