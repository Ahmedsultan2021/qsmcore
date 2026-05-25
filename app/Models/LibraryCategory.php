<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryCategory extends Model
{
    public const DEFAULT_NAMES = [
        'Operations',
        'Safety',
        'Maintenance',
        'Ground Operations',
        'Cabin Operations',
        'Quality',
        'Security',
        'Training',
    ];

    protected $fillable = [
        'company_id',
        'name',
        'sort_order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LibraryDocument::class);
    }

    public static function ensureDefaultsForCompany(int $companyId): void
    {
        if (self::where('company_id', $companyId)->exists()) {
            return;
        }

        foreach (self::DEFAULT_NAMES as $index => $name) {
            self::create([
                'company_id' => $companyId,
                'name'       => $name,
                'sort_order' => $index,
            ]);
        }
    }
}
