<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsTarget extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'target_amount',
        'is_achieved',
    ];

    protected $casts = [
        'target_amount' => 'integer',
        'is_achieved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
