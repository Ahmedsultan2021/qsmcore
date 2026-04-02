<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'manager_name',
        'phone',
        'email',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function risks()
    {
        return $this->hasMany(Risk::class);
    }

    /**
     * Form templates this department may use (reports, template bank). Also constrained by sector on each template.
     */
    public function formTemplates()
    {
        return $this->belongsToMany(FormTemplate::class, 'department_form_template')
            ->withTimestamps();
    }
}
