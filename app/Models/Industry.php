<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function sectors()
    {
        return $this->hasMany(Sector::class);
    }

    public function companies()
    {
        return $this->hasManyThrough(Company::class, Sector::class);
    }

    public function formTemplates()
    {
        return $this->belongsToMany(FormTemplate::class, 'industry_form_template');
    }
}
