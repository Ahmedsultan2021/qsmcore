<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\University;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = [
            [
                'name' => 'جامعة أسيوط التكنولوجية',
                'code' => 'ASU_TECH',
                'description' => 'جامعة أسيوط التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة أكتوبر التكنولوجية',
                'code' => 'OCT_TECH',
                'description' => 'جامعة أكتوبر التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة الدلتا التكنولوجية',
                'code' => 'DELTA_TECH',
                'description' => 'جامعة الدلتا التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة القاهرة الجديدة التكنولوجية',
                'code' => 'NC_TECH',
                'description' => 'جامعة القاهرة الجديدة التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة بني سويف التكنولوجية',
                'code' => 'BS_TECH',
                'description' => 'جامعة بني سويف التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة برج العرب التكنولوجية',
                'code' => 'BA_TECH',
                'description' => 'جامعة برج العرب التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة بورسعيد التكنولوجية',
                'code' => 'PS_TECH',
                'description' => 'جامعة بورسعيد التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة سمنود التكنولوجية بالغربية',
                'code' => 'SAM_TECH',
                'description' => 'جامعة سمنود التكنولوجية بالغربية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة طيبة التكنولوجية',
                'code' => 'TAIBA_TECH',
                'description' => 'جامعة طيبة التكنولوجية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة حلوان التكنولوجية الدولية',
                'code' => 'HELWAN_INT_TECH',
                'description' => 'جامعة حلوان التكنولوجية الدولية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة الفيوم التكنولوجية الدولية',
                'code' => 'FAYOUM_INT_TECH',
                'description' => 'جامعة الفيوم التكنولوجية الدولية',
                'is_active' => true,
            ],
            [
                'name' => 'جامعة أسيوط التكنولوجية الدولية',
                'code' => 'ASU_INT_TECH',
                'description' => 'جامعة أسيوط التكنولوجية الدولية',
                'is_active' => true,
            ],
        ];

        foreach ($universities as $university) {
            // Check if university exists by code
            $existingUniversity = University::where('code', $university['code'])->first();
            
            if ($existingUniversity) {
                // Update existing university
                $existingUniversity->update([
                    'name' => $university['name'],
                    'description' => $university['description'],
                    'is_active' => $university['is_active']
                ]);
                $this->command->info("Updated: {$university['name']}");
            } else {
                // Create new university
                University::create($university);
                $this->command->info("Created: {$university['name']}");
            }
        }
    }
}
