<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FormTheme extends Model
{
    protected $fillable = [
        'slug',
        'name',
    ];

    public function formTemplates(): BelongsToMany
    {
        return $this->belongsToMany(FormTemplate::class, 'form_theme_form_template');
    }
}
