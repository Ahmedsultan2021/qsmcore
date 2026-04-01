<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'title',
        'kind',
        'description',
        'status',
        'report_date',
        'created_by',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Report $report) {
            $report->loadMissing('reportFiles');
            foreach ($report->reportFiles as $file) {
                Storage::disk('public')->delete($file->path);
            }
        });
    }

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

    public function reportFiles()
    {
        return $this->hasMany(ReportFile::class)->orderByDesc('created_at');
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
