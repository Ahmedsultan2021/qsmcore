<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryDocument extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_EFFECTIVE = 'effective';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_EFFECTIVE,
    ];

    protected $fillable = [
        'company_id',
        'uploaded_by',
        'library_category_id',
        'owner_employee_id',
        'title',
        'document_code',
        'version_label',
        'description',
        'effective_date',
        'status',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size'       => 'integer',
        'effective_date'  => 'date',
    ];

    protected $appends = ['file_type'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'owner_employee_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'library_category_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(LibraryFavorite::class);
    }

    public function scopeVisibleToEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where(function (Builder $q) use ($employeeId) {
            $q->where('status', '!=', self::STATUS_DRAFT)
                ->orWhere('uploaded_by', $employeeId)
                ->orWhere('owner_employee_id', $employeeId);
        });
    }

    public function getFileTypeAttribute(): string
    {
        $name = strtolower($this->original_name ?? '');
        if (str_ends_with($name, '.pdf')) {
            return 'pdf';
        }
        if (str_ends_with($name, '.doc') || str_ends_with($name, '.docx')) {
            return 'word';
        }
        if (str_ends_with($name, '.xls') || str_ends_with($name, '.xlsx') || str_ends_with($name, '.csv')) {
            return 'excel';
        }
        if (str_ends_with($name, '.ppt') || str_ends_with($name, '.pptx')) {
            return 'powerpoint';
        }
        if (str_ends_with($name, '.jpg') || str_ends_with($name, '.jpeg') || str_ends_with($name, '.png')) {
            return 'image';
        }

        return 'file';
    }
}
