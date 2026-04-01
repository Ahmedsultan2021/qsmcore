<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            [
                'name'        => 'Aviation',
                'description' => 'Airlines, MRO facilities, and airport operations',
            ],
            [
                'name'        => 'OGE',
                'description' => 'Oil, Gas & Energy upstream and downstream operations',
            ],
            [
                'name'        => 'Logistics & Transportation',
                'description' => 'Maritime, rail, and road transport logistics',
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
