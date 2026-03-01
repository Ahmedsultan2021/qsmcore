<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'image',
        'attached_file',
        'audit_date',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'audit_report');
    }

    /**
     * Get the audit completion status.
     * Returns 'complete' if all attached reports are complete, otherwise 'pending'.
     * External audits (no reports) are considered complete.
     */
    public function getCompletionStatusAttribute()
    {
        $reports = $this->reports;
        
        // External audit (no reports attached) - considered complete
        if ($reports->isEmpty()) {
            return 'complete';
        }
        
        // Internal audit - check if all reports are complete
        $allReportsComplete = $reports->every(function ($report) {
            return $report->general_report_status === 'complete';
        });
        
        return $allReportsComplete ? 'complete' : 'pending';
    }

    /**
     * Check if audit is internal (has reports attached)
     */
    public function getIsInternalAttribute()
    {
        return $this->reports()->exists();
    }
}
