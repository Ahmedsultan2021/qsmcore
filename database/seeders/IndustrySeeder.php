<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Manufacturing',
                'description' => 'Production of goods and raw materials',
            ],
            [
                'name' => 'Services',
                'description' => 'Financial, commercial and consulting services',
            ],
            [
                'name' => 'Technology',
                'description' => 'Information technology, software and communications',
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Hospitals, clinics and pharmaceuticals',
            ],
            [
                'name' => 'Construction',
                'description' => 'Contracting, real estate and infrastructure',
            ],
            [
                'name' => 'Education',
                'description' => 'Educational institutions and training',
            ],
            [
                'name' => 'Retail',
                'description' => 'Retail and wholesale trade',
            ],
            [
                'name' => 'Energy',
                'description' => 'Oil, gas and renewable energy',
            ],
        ];

        foreach ($industries as $industry) {
            Industry::firstOrCreate(
                ['name' => $industry['name']],
                $industry
            );
        }
    }
}
