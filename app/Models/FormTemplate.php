<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    use HasFactory;

    /**
     * Global blueprint: sector_form_template limits which sectors may use it; department_form_template
     * limits which company departments may use it. `library_key` is a stable slug for grouping and seeding.
     * KPI buckets use form_theme_form_template (safety / quality).
     */
    protected $fillable = [
        'name',
        'library_key',
        'description',
    ];

    /**
     * Whether this template is linked to the department (pivot). Sector is checked at query level.
     */
    public function appliesToDepartment(Department $department): bool
    {
        return $department->formTemplates()->where('form_templates.id', $this->id)->exists();
    }

    public function scopeLinkedToDepartment(Builder $query, Department $department): Builder
    {
        return $query->whereHas(
            'departments',
            fn (Builder $q) => $q->where('departments.id', $department->id)
        );
    }

    public function formTemplateFields()
    {
        return $this->hasMany(FormTemplateField::class)->orderBy('order');
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_form_template');
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_form_template');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_form_template')
            ->withTimestamps();
    }

    public function themes()
    {
        return $this->belongsToMany(FormTheme::class, 'form_theme_form_template');
    }
}
