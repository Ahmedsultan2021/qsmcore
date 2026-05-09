<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpDocument extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['file_url', 'file_type'];

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
        return 'file';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
