<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'name',
        'description',
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
