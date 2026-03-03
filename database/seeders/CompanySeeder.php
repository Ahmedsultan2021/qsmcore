<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'sector' => 'Food Manufacturing',
                'industry' => 'Manufacturing',
                'name' => 'United Foods Co.',
                'email' => 'info@united-foods.com',
                'phone' => '+20223456789',
                'address' => 'Cairo, Industrial Zone',
                'description' => 'Leading company in food processing and packaging',
            ],
            [
                'sector' => 'Software',
                'industry' => 'Technology',
                'name' => 'Advanced Technology Inc.',
                'email' => 'contact@advanced-tech.com',
                'phone' => '+20234567890',
                'address' => 'Nasr City, Cairo',
                'description' => 'Software solutions and application development',
            ],
            [
                'sector' => 'Financial Services',
                'industry' => 'Services',
                'name' => 'Investment Finance Corp.',
                'email' => 'info@invest-finance.com',
                'phone' => '+20245678901',
                'address' => 'Maadi, Cairo',
                'description' => 'Financial and investment services',
            ],
            [
                'sector' => 'Hospitals',
                'industry' => 'Healthcare',
                'name' => 'Modern Hospitals Group',
                'email' => 'info@modern-hospitals.com',
                'phone' => '+20256789012',
                'address' => 'Sheikh Zayed, Giza',
                'description' => 'Network of hospitals and medical centers',
            ],
            [
                'sector' => 'Contracting',
                'industry' => 'Construction',
                'name' => 'National Building Co.',
                'email' => 'projects@national-build.com',
                'phone' => '+20267890123',
                'address' => '6th of October City',
                'description' => 'General contracting and infrastructure projects',
            ],
            [
                'sector' => 'Higher Education',
                'industry' => 'Education',
                'name' => 'Renaissance Academy',
                'email' => 'admission@renaissance-academy.edu',
                'phone' => '+20278901234',
                'address' => 'Alexandria',
                'description' => 'Higher education institution',
            ],
            [
                'sector' => 'Retail',
                'industry' => 'Retail',
                'name' => 'Loyalty Stores Chain',
                'email' => 'support@loyalty-stores.com',
                'phone' => '+20289012345',
                'address' => 'Multiple branches',
                'description' => 'Retail store network',
            ],
            [
                'sector' => 'Oil & Gas',
                'industry' => 'Energy',
                'name' => 'Eastern Energy Co.',
                'email' => 'info@eastern-energy.com',
                'phone' => '+20290123456',
                'address' => 'Suez',
                'description' => 'Oil and gas extraction and refining',
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
                    'name' => $data['name'],
                ],
                [
                    'sector_id' => $sector->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
