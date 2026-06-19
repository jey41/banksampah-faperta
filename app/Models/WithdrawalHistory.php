<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalHistory extends Model
{
    protected $table = 'withdrawal_history';

    protected $fillable = ['withdrawal_id', 'status', 'notes', 'processed_by', 'processed_at'];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
