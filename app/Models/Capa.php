<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capa extends Model
{
    use HasFactory;

    protected $appends = [
        'is_overdue',
    ];

    protected $fillable = [
        'company_id',
        'department_id',
        'assigned_to',
        'created_by',
        'title',
        'type',
        'status',
        'priority',
        'description',
        'root_cause',
        'corrective_action',
        'preventive_action',
        'due_date',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'closed') {
            return false;
        }
        if (!$this->due_date) {
            return false;
        }
        return $this->due_date->isPast();
    }
}

