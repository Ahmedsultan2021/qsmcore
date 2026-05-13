<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryDocument extends Model
{
    protected $fillable = [
        'company_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url', 'file_type'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFileTypeAttribute(): string
    {
        $name = strtolower($this->original_name ?? '');
        if (str_ends_with($name, '.pdf')) return 'pdf';
        if (str_ends_with($name, '.doc') || str_ends_with($name, '.docx')) return 'word';
        if (str_ends_with($name, '.xls') || str_ends_with($name, '.xlsx') || str_ends_with($name, '.csv')) return 'excel';
        if (str_ends_with($name, '.ppt') || str_ends_with($name, '.pptx')) return 'powerpoint';
        if (str_ends_with($name, '.jpg') || str_ends_with($name, '.jpeg') || str_ends_with($name, '.png')) return 'image';
        return 'file';
    }
}
