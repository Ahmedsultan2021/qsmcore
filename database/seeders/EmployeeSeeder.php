<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['company' => 'United Foods Co.', 'fname' => 'James', 'lname' => 'Wilson', 'email' => 'james.wilson@united-foods.com', 'position' => 'Production Manager', 'phone' => '+20223456793'],
            ['company' => 'United Foods Co.', 'fname' => 'Emma', 'lname' => 'Brown', 'email' => 'emma.brown@united-foods.com', 'position' => 'Quality Assurance Lead', 'phone' => '+20223456794'],
            ['company' => 'United Foods Co.', 'fname' => 'David', 'lname' => 'Taylor', 'email' => 'david.taylor@united-foods.com', 'position' => 'Sales Representative', 'phone' => null],
            ['company' => 'Advanced Technology Inc.', 'fname' => 'Sophie', 'lname' => 'Anderson', 'email' => 'sophie.anderson@advanced-tech.com', 'position' => 'Senior Developer', 'phone' => null],
            ['company' => 'Advanced Technology Inc.', 'fname' => 'Ryan', 'lname' => 'Martinez', 'email' => 'ryan.martinez@advanced-tech.com', 'position' => 'DevOps Engineer', 'phone' => null],
            ['company' => 'Investment Finance Corp.', 'fname' => 'Lisa', 'lname' => 'Garcia', 'email' => 'lisa.garcia@invest-finance.com', 'position' => 'Investment Analyst', 'phone' => null],
            ['company' => 'Investment Finance Corp.', 'fname' => 'Kevin', 'lname' => 'Lee', 'email' => 'kevin.lee@invest-finance.com', 'position' => 'Accountant', 'phone' => null],
            ['company' => 'Modern Hospitals Group', 'fname' => 'Jennifer', 'lname' => 'Clark', 'email' => 'jennifer.clark@modern-hospitals.com', 'position' => 'Operations Coordinator', 'phone' => null],
            ['company' => 'Modern Hospitals Group', 'fname' => 'Michael', 'lname' => 'White', 'email' => 'michael.white@modern-hospitals.com', 'position' => 'Facilities Manager', 'phone' => null],
            ['company' => 'National Building Co.', 'fname' => 'Sarah', 'lname' => 'Harris', 'email' => 'sarah.harris@national-build.com', 'position' => 'Project Manager', 'phone' => null],
            ['company' => 'National Building Co.', 'fname' => 'Daniel', 'lname' => 'King', 'email' => 'daniel.king@national-build.com', 'position' => 'Site Engineer', 'phone' => null],
            ['company' => 'Renaissance Academy', 'fname' => 'Amanda', 'lname' => 'Scott', 'email' => 'amanda.scott@renaissance-academy.edu', 'position' => 'Academic Advisor', 'phone' => null],
            ['company' => 'Renaissance Academy', 'fname' => 'Christopher', 'lname' => 'Green', 'email' => 'christopher.green@renaissance-academy.edu', 'position' => 'Registrar', 'phone' => null],
            ['company' => 'Loyalty Stores Chain', 'fname' => 'Nicole', 'lname' => 'Adams', 'email' => 'nicole.adams@loyalty-stores.com', 'position' => 'Store Manager', 'phone' => null],
            ['company' => 'Eastern Energy Co.', 'fname' => 'Andrew', 'lname' => 'Nelson', 'email' => 'andrew.nelson@eastern-energy.com', 'position' => 'Operations Supervisor', 'phone' => null],
            ['company' => 'Eastern Energy Co.', 'fname' => 'Rachel', 'lname' => 'Carter', 'email' => 'rachel.carter@eastern-energy.com', 'position' => 'Safety Officer', 'phone' => null],
        ];

        $password = Hash::make('password');

        foreach ($employees as $data) {
            $company = Company::where('name', $data['company'])->first();
            if (!$company) {
                continue;
            }

            Employee::firstOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'company_id' => $company->id,
                    'fname' => $data['fname'],
                    'lname' => $data['lname'],
                    'email' => $data['email'],
                    'password' => $password,
                    'phone' => $data['phone'] ?? null,
                    'position' => $data['position'],
                ]
            );
        }
    }
}
