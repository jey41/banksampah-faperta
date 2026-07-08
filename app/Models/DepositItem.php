<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositItem extends Model
{
    use HasFactory;

    protected $fillable = ['deposit_id', 'trash_price_id', 'weight', 'price_per_unit', 'total_price', 'total_carbon'];

    protected $casts = [
        'weight' => 'decimal:2',
        'price_per_unit' => 'integer',
        'total_price' => 'integer',
        'total_carbon' => 'decimal:2',
    ];

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function trashPrice(): BelongsTo
    {
        return $this->belongsTo(TrashPrice::class);
    }
}
