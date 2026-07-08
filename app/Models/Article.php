<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'image_path', 'status'];

    protected $appends = ['image_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::deleting(function ($article) {
            // Only delete if it's a local file (not external URL)
            if ($article->image_path && !str_starts_with($article->image_path, 'http')) {
                if (\Storage::disk('public')->exists($article->image_path)) {
                    \Storage::disk('public')->delete($article->image_path);
                }
            }
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->image_path) {
                    return null;
                }
                
                // If it's an external URL, return as-is
                if (str_starts_with($this->image_path, 'http')) {
                    return $this->image_path;
                }
                
                // Otherwise, it's a local file - add storage URL
                return \Storage::url($this->image_path);
            },
        );
    }
}

