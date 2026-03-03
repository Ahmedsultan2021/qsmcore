<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            IndustrySeeder::class,
            SectorSeeder::class,
            CompanySeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            FormTemplateSeeder::class,
            RiskSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
