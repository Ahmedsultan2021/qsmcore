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
        'department_id' => 'integer',
        'created_by' => 'integer',
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

    public function scopeForTheme($query, ?string $slug)
    {
        if (!in_array($slug, ['safety', 'quality'], true)) {
            return $query;
        }

        return $query->whereHas(
            'department.formTemplates.themes',
            fn ($theme) => $theme->where('form_themes.slug', $slug)
        );
    }

    /**
     * Safety / quality label from the report's forms, then its department templates.
     */
    public function themeLabel(): string
    {
        $fromForms = $this->forms
            ->map(fn ($form) => $form->formTemplate)
            ->filter()
            ->flatMap(fn ($template) => $template->themes)
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();

        if ($fromForms->isNotEmpty()) {
            return $fromForms->join(' · ');
        }

        $fromDepartment = $this->department?->formTemplates
            ?->flatMap(fn ($template) => $template->themes)
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();

        return $fromDepartment && $fromDepartment->isNotEmpty()
            ? $fromDepartment->join(' · ')
            : '—';
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
        
        // Distinct form IDs that have at least one saved response (any submitter)
        $completedFormIds = $this->formResponses()
            ->distinct()
            ->pluck('form_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        // Check if all attached forms have at least one response (normalize types for DB/PDO)
        $allFormsCompleted = $forms->every(
            fn ($form) => in_array((int) $form->id, $completedFormIds, true)
        );
        
        return $allFormsCompleted ? 'complete' : 'pending';
    }
}
