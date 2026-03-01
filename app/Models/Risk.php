<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'risk_owner_id',
        'risk_code',
        'title',
        'description',
        'category',
        'likelihood',
        'impact',
        'current_controls',
        'treatment_strategy',
        'action_plan',
        'target_date',
        'status',
        'date_identified',
        'last_review_date',
        'residual_likelihood',
        'residual_impact',
    ];

    protected $casts = [
        'target_date' => 'date',
        'date_identified' => 'date',
        'last_review_date' => 'date',
        'likelihood' => 'integer',
        'impact' => 'integer',
        'residual_likelihood' => 'integer',
        'residual_impact' => 'integer',
    ];

    protected $appends = ['risk_level', 'residual_risk_level'];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function riskOwner()
    {
        return $this->belongsTo(Employee::class, 'risk_owner_id');
    }

    // Accessors
    public function getRiskLevelAttribute()
    {
        $score = $this->likelihood * $this->impact;
        
        if ($score >= 15) {
            return 'critical';
        } elseif ($score >= 10) {
            return 'high';
        } elseif ($score >= 5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getResidualRiskLevelAttribute()
    {
        if (!$this->residual_likelihood || !$this->residual_impact) {
            return null;
        }

        $score = $this->residual_likelihood * $this->residual_impact;
        
        if ($score >= 15) {
            return 'critical';
        } elseif ($score >= 10) {
            return 'high';
        } elseif ($score >= 5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getRiskScoreAttribute()
    {
        return $this->likelihood * $this->impact;
    }

    public function getResidualRiskScoreAttribute()
    {
        if (!$this->residual_likelihood || !$this->residual_impact) {
            return null;
        }
        return $this->residual_likelihood * $this->residual_impact;
    }

    // Helper Methods
    public static function categories()
    {
        return [
            'operational' => 'Operational',
            'financial' => 'Financial',
            'strategic' => 'Strategic',
            'compliance' => 'Compliance',
            'reputational' => 'Reputational',
            'technological' => 'Technological',
            'environmental' => 'Environmental',
            'human_resources' => 'Human Resources',
        ];
    }

    public static function treatmentStrategies()
    {
        return [
            'accept' => 'Accept',
            'mitigate' => 'Mitigate',
            'transfer' => 'Transfer',
            'avoid' => 'Avoid',
        ];
    }

    public static function statuses()
    {
        return [
            'identified' => 'Identified',
            'assessed' => 'Assessed',
            'in_progress' => 'In Progress',
            'mitigated' => 'Mitigated',
            'monitoring' => 'Monitoring',
            'closed' => 'Closed',
        ];
    }

    public static function likelihoodLevels()
    {
        return [
            1 => 'Very Low',
            2 => 'Low',
            3 => 'Medium',
            4 => 'High',
            5 => 'Very High',
        ];
    }

    public static function impactLevels()
    {
        return [
            1 => 'Insignificant',
            2 => 'Minor',
            3 => 'Moderate',
            4 => 'Major',
            5 => 'Severe',
        ];
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereRaw('(likelihood * impact) >= 10');
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
                     ->whereNotIn('status', ['closed', 'mitigated']);
    }
}
