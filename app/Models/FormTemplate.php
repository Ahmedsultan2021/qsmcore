<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
    ];

    public function formTemplateFields()
    {
        return $this->hasMany(FormTemplateField::class)->orderBy('order');
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_form_template');
    }
}
