<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultDepartment extends Model
{
    protected $fillable = [
        'sector_id',
        'name',
        'description',
        'library_key',
        'sort_order',
    ];

    protected $casts = [
        'sector_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
