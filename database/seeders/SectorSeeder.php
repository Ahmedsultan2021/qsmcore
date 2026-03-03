<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            'Manufacturing' => [
                ['name' => 'Food Manufacturing', 'description' => 'Food processing and packaging'],
                ['name' => 'Pharmaceuticals', 'description' => 'Pharmaceutical manufacturing'],
                ['name' => 'Automotive', 'description' => 'Vehicle assembly and components'],
                ['name' => 'Textiles', 'description' => 'Textiles and apparel'],
            ],
            'Services' => [
                ['name' => 'Financial Services', 'description' => 'Banking and insurance'],
                ['name' => 'Consulting', 'description' => 'Management and legal consulting'],
                ['name' => 'Logistics', 'description' => 'Transport and warehousing'],
            ],
            'Technology' => [
                ['name' => 'Software', 'description' => 'Software and application development'],
                ['name' => 'Telecommunications', 'description' => 'Networks and communications services'],
                ['name' => 'Cybersecurity', 'description' => 'Information and systems security'],
            ],
            'Healthcare' => [
                ['name' => 'Hospitals', 'description' => 'Institutional healthcare'],
                ['name' => 'Pharmaceutical Distribution', 'description' => 'Drug distribution and marketing'],
            ],
            'Construction' => [
                ['name' => 'Contracting', 'description' => 'Building and construction works'],
                ['name' => 'Real Estate', 'description' => 'Real estate development and management'],
            ],
            'Education' => [
                ['name' => 'Higher Education', 'description' => 'Universities and institutes'],
                ['name' => 'Training', 'description' => 'Training and development centers'],
            ],
            'Retail' => [
                ['name' => 'Retail', 'description' => 'Retail stores'],
                ['name' => 'Wholesale', 'description' => 'Wholesale and distribution'],
            ],
            'Energy' => [
                ['name' => 'Oil & Gas', 'description' => 'Oil and gas extraction and refining'],
                ['name' => 'Renewable Energy', 'description' => 'Solar and wind energy'],
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
                        'name' => $sector['name'],
                    ],
                    array_merge($sector, ['industry_id' => $industry->id])
                );
            }
        }
    }
}
