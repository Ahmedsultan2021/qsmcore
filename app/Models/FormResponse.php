<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'form_id',
        'submitted_by',
        'responses',
    ];

    protected $casts = [
        'responses' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function submitter()
    {
        return $this->belongsTo(Employee::class, 'submitted_by');
    }
}
