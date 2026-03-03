<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'company' => 'United Foods Co.',
                'name' => 'Production',
                'description' => 'Production lines and quality management',
                'manager_name' => 'Ahmed Mahmoud',
                'phone' => '+20223456790',
                'email' => 'production@united-foods.com',
            ],
            [
                'company' => 'United Foods Co.',
                'name' => 'Sales',
                'description' => 'Sales and distribution',
                'manager_name' => 'Sara Ali',
                'phone' => '+20223456791',
                'email' => 'sales@united-foods.com',
            ],
            [
                'company' => 'United Foods Co.',
                'name' => 'Human Resources',
                'description' => 'Recruitment and development',
                'manager_name' => 'Mohamed Hassan',
                'phone' => '+20223456792',
                'email' => 'hr@united-foods.com',
            ],
            [
                'company' => 'Advanced Technology Inc.',
                'name' => 'Development',
                'description' => 'Software and application development',
                'manager_name' => 'Khaled Amr',
                'phone' => null,
                'email' => 'dev@advanced-tech.com',
            ],
            [
                'company' => 'Advanced Technology Inc.',
                'name' => 'Technical Support',
                'description' => 'Customer and operations support',
                'manager_name' => 'Nora Ahmed',
                'phone' => null,
                'email' => 'support@advanced-tech.com',
            ],
            [
                'company' => 'Investment Finance Corp.',
                'name' => 'Investment',
                'description' => 'Portfolio and investment management',
                'manager_name' => 'Omar Yusuf',
                'phone' => null,
                'email' => 'investment@invest-finance.com',
            ],
            [
                'company' => 'Investment Finance Corp.',
                'name' => 'Accounting',
                'description' => 'Finance and accounting',
                'manager_name' => 'Huda Ibrahim',
                'phone' => null,
                'email' => 'accounts@invest-finance.com',
            ],
            [
                'company' => 'Modern Hospitals Group',
                'name' => 'Emergency',
                'description' => 'Emergency and critical care',
                'manager_name' => 'Dr. Yasser Fathy',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'Modern Hospitals Group',
                'name' => 'Surgery',
                'description' => 'Surgical operations',
                'manager_name' => 'Dr. Mona Said',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'National Building Co.',
                'name' => 'Projects',
                'description' => 'Project and site management',
                'manager_name' => 'Tariq Abdullah',
                'phone' => null,
                'email' => 'projects@national-build.com',
            ],
            [
                'company' => 'National Building Co.',
                'name' => 'Procurement',
                'description' => 'Procurement and warehousing',
                'manager_name' => 'Fatima Mohamed',
                'phone' => null,
                'email' => 'procurement@national-build.com',
            ],
            [
                'company' => 'Renaissance Academy',
                'name' => 'Academic Affairs',
                'description' => 'Curriculum and examinations',
                'manager_name' => 'Dr. Karim Abdelrahman',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'Renaissance Academy',
                'name' => 'Student Affairs',
                'description' => 'Admissions, registration and activities',
                'manager_name' => 'Lamya Hussein',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'Loyalty Stores Chain',
                'name' => 'Warehouse',
                'description' => 'Inventory and supply management',
                'manager_name' => 'Ramy Saad',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'Eastern Energy Co.',
                'name' => 'Operations',
                'description' => 'Extraction and refining operations',
                'manager_name' => 'Waleed Gamal',
                'phone' => null,
                'email' => null,
            ],
            [
                'company' => 'Eastern Energy Co.',
                'name' => 'Maintenance',
                'description' => 'Facilities and equipment maintenance',
                'manager_name' => 'Emad Nasser',
                'phone' => null,
                'email' => null,
            ],
        ];

        foreach ($departments as $data) {
            $company = Company::where('name', $data['company'])->first();
            if (!$company) {
                continue;
            }

            Department::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $data['name'],
                ],
                [
                    'company_id' => $company->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'manager_name' => $data['manager_name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                ]
            );
        }
    }
}
