<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'field_type',
        'label',
        'name',
        'placeholder',
        'required',
        'options',
        'order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
