<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            // ── AVIATION ─────────────────────────────────────────────────────────
            'Aviation' => [
                [
                    'name'        => 'Airlines',
                    'description' => 'Commercial airline operations including flight ops, ground ops, OCC, maintenance, training, safety, and quality',
                ],
                [
                    'name'        => 'MRO',
                    'description' => 'Maintenance, Repair & Overhaul facilities covering safety, quality, and operational reports',
                ],
                [
                    'name'        => 'Airport',
                    'description' => 'Airport management and operations covering safety, quality, and operations reports',
                ],
            ],

            // ── OGE ──────────────────────────────────────────────────────────────
            'OGE' => [
                [
                    'name'        => 'Safety',
                    'description' => 'HSE safety reporting for oil, gas & energy operations',
                ],
                [
                    'name'        => 'Quality',
                    'description' => 'Quality management reporting for oil, gas & energy operations',
                ],
            ],

            // ── LOGISTICS & TRANSPORTATION ────────────────────────────────────────
            'Logistics & Transportation' => [
                [
                    'name'        => 'Maritime',
                    'description' => 'Sea freight and maritime vessel operations',
                ],
                [
                    'name'        => 'Rail',
                    'description' => 'Rail transport and track operations',
                ],
                [
                    'name'        => 'Road Transport',
                    'description' => 'Road haulage, freight logistics, and driver operations',
                ],
            ],
        ];

        foreach ($sectors as $industryName => $sectorList) {
            $industry = Industry::where('name', $industryName)->first();
            if (!$industry) {
                continue;
            }

            foreach ($sectorList as $sector) {
                Sector::firstOrCreate(
                    [
                        'industry_id' => $industry->id,
                        'name'        => $sector['name'],
                    ],
                    array_merge($sector, ['industry_id' => $industry->id])
                );
            }
        }
    }
}
