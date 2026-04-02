<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            // ── AVIATION / Airlines ───────────────────────────────────────────────
            [
                'industry' => 'Aviation',
                'sector'   => 'Airlines',
                'name'     => 'SkyLine Airlines',
                'email'    => 'info@skyline-airlines.com',
                'phone'    => '+1-800-100-1000',
                'address'  => 'Terminal 1, International Airport',
                'description' => 'Full-service commercial airline operating domestic and international routes',
            ],

            // ── AVIATION / MRO ────────────────────────────────────────────────────
            [
                'industry' => 'Aviation',
                'sector'   => 'MRO',
                'name'     => 'AeroTech MRO',
                'email'    => 'ops@aerotech-mro.com',
                'phone'    => '+1-800-200-2000',
                'address'  => 'Hangar Complex, Aviation Park',
                'description' => 'EASA/FAA-certified aircraft maintenance, repair & overhaul facility',
            ],

            // ── AVIATION / Airport ────────────────────────────────────────────────
            [
                'industry' => 'Aviation',
                'sector'   => 'Airport',
                'name'     => 'Central Airport Authority',
                'email'    => 'info@central-airport.com',
                'phone'    => '+1-800-300-3000',
                'address'  => 'Airport Boulevard, Central City',
                'description' => 'International airport operator managing airside and landside operations',
            ],

            // ── OGE ───────────────────────────────────────────────────────────────
            [
                'industry' => 'OGE',
                'sector'   => 'OGE Sector',
                'name'     => 'Gulf Petroleum HSE Ltd.',
                'email'    => 'hse@gulf-petroleum.com',
                'phone'    => '+966-11-400-4000',
                'address'  => 'Offshore Platform Zone, Gulf Region',
                'description' => 'Oil & gas operator with a dedicated HSE safety management division',
            ],

            [
                'industry' => 'OGE',
                'sector'   => 'OGE Sector',
                'name'     => 'Petroleum Quality Corp.',
                'email'    => 'quality@petro-qc.com',
                'phone'    => '+966-11-500-5000',
                'address'  => 'Industrial Zone, Energy City',
                'description' => 'Downstream petroleum processing company with ISO-certified quality division',
            ],

            // ── LOGISTICS & TRANSPORTATION / Maritime ─────────────────────────────
            [
                'industry' => 'Logistics & Transportation',
                'sector'   => 'Maritime',
                'name'     => 'SeaRoute Maritime Ltd.',
                'email'    => 'ops@searoute-maritime.com',
                'phone'    => '+44-20-600-6000',
                'address'  => 'Port Authority Building, Harbor District',
                'description' => 'International sea freight and maritime vessel management company',
            ],

            // ── LOGISTICS & TRANSPORTATION / Rail ────────────────────────────────
            [
                'industry' => 'Logistics & Transportation',
                'sector'   => 'Rail',
                'name'     => 'RailConnect Operations',
                'email'    => 'ops@railconnect.com',
                'phone'    => '+44-20-700-7000',
                'address'  => 'Central Rail Depot, Junction City',
                'description' => 'National rail freight and passenger operations company',
            ],

            // ── LOGISTICS & TRANSPORTATION / Road Transport ───────────────────────
            [
                'industry' => 'Logistics & Transportation',
                'sector'   => 'Road Transport',
                'name'     => 'FreightLink Transport Co.',
                'email'    => 'ops@freightlink.com',
                'phone'    => '+44-20-800-8000',
                'address'  => 'Logistics Hub, Motorway Park',
                'description' => 'Long-haul road haulage and last-mile freight distribution company',
            ],
        ];

        foreach ($companies as $data) {
            $sector = Sector::where('name', $data['sector'])
                ->whereHas('industry', fn ($q) => $q->where('name', $data['industry']))
                ->first();

            if (!$sector) {
                continue;
            }

            Company::firstOrCreate(
                [
                    'sector_id' => $sector->id,
                    'name'      => $data['name'],
                ],
                [
                    'sector_id'   => $sector->id,
                    'name'        => $data['name'],
                    'email'       => $data['email'],
                    'phone'       => $data['phone'],
                    'address'     => $data['address'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
