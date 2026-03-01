<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'sector_id',
        'name',
        'email',
        'phone',
        'address',
        'description',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function industry()
    {
        return $this->hasOneThrough(Industry::class, Sector::class, 'id', 'id', 'sector_id', 'industry_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
