<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'sector_id',
        'name',
        'email',
        'phone',
        'address',
        'description',
        'logo_path',
    ];

    protected $hidden = [
        'logo_path',
    ];

    protected $appends = [
        'logo_url',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->logo_path) {
                return null;
            }

            return Storage::disk('public')->url($this->logo_path);
        });
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function industry()
    {
        return $this->hasOneThrough(Industry::class, Sector::class, 'id', 'id', 'sector_id', 'industry_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
