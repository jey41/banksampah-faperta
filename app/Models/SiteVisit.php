<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'ip_address',
        'user_agent',
        'referer',
        'visited_at',
    ];

    public $timestamps = false; // We use visited_at instead of created_at/updated_at

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year);
    }

    public function scopeUniqueVisitors($query)
    {
        return $query->distinct('ip_address');
    }
}
