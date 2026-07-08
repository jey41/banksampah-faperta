<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrashPrice extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'category_type', 'price_buy', 'price_sell', 'unit', 'carbon_factor'];

    protected $casts = [
        'price_buy' => 'integer',
        'price_sell' => 'integer',
        'carbon_factor' => 'decimal:2',
    ];

    public function depositItems(): HasMany
    {
        return $this->hasMany(DepositItem::class);
    }
}
