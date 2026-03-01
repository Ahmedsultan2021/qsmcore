<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'department_id',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_form');
    }

    public function formFields()
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function formResponses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
