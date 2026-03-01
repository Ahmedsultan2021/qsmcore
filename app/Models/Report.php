<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'title',
        'description',
        'status',
        'report_date',
        'created_by',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function forms()
    {
        return $this->belongsToMany(Form::class, 'report_form');
    }

    public function formResponses()
    {
        return $this->hasMany(FormResponse::class);
    }

    /**
     * Get the general report status based on form completion.
     * Returns 'complete' if all forms have responses, otherwise 'pending'.
     */
    public function getGeneralReportStatusAttribute()
    {
        $forms = $this->forms;
        
        // If no forms attached, consider it pending
        if ($forms->isEmpty()) {
            return 'pending';
        }
        
        // Get all form IDs that have responses for this report
        $completedFormIds = $this->formResponses()
            ->distinct()
            ->pluck('form_id')
            ->toArray();
        
        // Check if all forms have responses
        $allFormsCompleted = $forms->every(function ($form) use ($completedFormIds) {
            return in_array($form->id, $completedFormIds);
        });
        
        return $allFormsCompleted ? 'complete' : 'pending';
    }
}
