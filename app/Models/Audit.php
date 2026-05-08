<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Audit extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
        'attached_file_url',
    ];

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'image',
        'attached_file',
        'audit_date',
        'status',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    protected $attributes = [
        'status' => 'pending',
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
     * Audit status is set explicitly by the user (defaults to pending on create,
     * editable from the edit form). The reports' own completion is shown
     * separately on the Show page.
     */
    public function getCompletionStatusAttribute()
    {
        return $this->status ?? 'pending';
    }

    /**
     * Check if audit is internal (has reports attached)
     */
    public function getIsInternalAttribute()
    {
        return $this->reports()->exists();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return Storage::url($this->image);
    }

    public function getAttachedFileUrlAttribute(): ?string
    {
        if (!$this->attached_file) {
            return null;
        }
        return Storage::url($this->attached_file);
    }
}
