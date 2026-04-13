<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'vacancy_id',
        'name',
        'email',
        'phone',
        'cover_letter',
        'cv_path',
        'status',
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }

    protected $appends = ['cv_url'];
}
