<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'contact_email',
        'contact_phone',
        'contact_address',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'contact_email'   => 'support@qsm.com',
            'contact_phone'   => '+1 (555) 123-4567',
            'contact_address' => '123 Business St, Suite 100',
        ]);
    }

    public static function forFrontend(): array
    {
        $settings = static::current();

        return [
            'email'   => $settings->contact_email,
            'phone'   => $settings->contact_phone,
            'address' => $settings->contact_address,
        ];
    }
}
