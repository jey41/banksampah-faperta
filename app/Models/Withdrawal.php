<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'withdrawal_method', 'admin_fee', 'bank_name', 'bank_type', 'account_number', 'account_name', 'status', 'validated_by', 'notes'];

    protected $casts = [
        'amount' => 'integer',
        'admin_fee' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function history(): HasMany
    {
        return $this->hasMany(WithdrawalHistory::class);
    }
}
