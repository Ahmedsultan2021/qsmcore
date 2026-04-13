<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $fillable = [
        'title',
        'department',
        'location',
        'type',
        'description',
        'requirements',
        'benefits',
        'salary_range',
        'closing_date',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'closing_date' => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('closing_date')
                           ->orWhere('closing_date', '>=', now()->toDateString());
                     });
    }
}
