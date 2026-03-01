<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'excerpt',
        'featured_image',
        'is_published',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blogPost) {
            if (empty($blogPost->slug)) {
                $blogPost->slug = Str::slug($blogPost->title);
            }
        });

        static::updating(function ($blogPost) {
            if ($blogPost->isDirty('title') && empty($blogPost->slug)) {
                $blogPost->slug = Str::slug($blogPost->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
