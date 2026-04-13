<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    public function run(): void
    {
        Vacancy::firstOrCreate(
            ['title' => 'IT Specialist'],
            [
                'department'   => 'Information Technology',
                'location'     => 'Dubai, UAE',
                'type'         => 'full-time',
                'salary_range' => '$50,000 – $70,000 / year',
                'closing_date' => now()->addMonths(2)->toDateString(),
                'is_active'    => true,
                'description'  => "We are looking for a skilled IT Specialist to join our growing technology team at QSMCore. You will be responsible for maintaining and improving our software infrastructure, supporting internal teams, and ensuring the smooth operation of our Quality & Safety Management platform.\n\nKey Responsibilities:\n• Maintain and monitor server infrastructure and cloud environments\n• Troubleshoot hardware, software, and network issues\n• Support development and deployment pipelines (CI/CD)\n• Administer databases and ensure data integrity and security\n• Collaborate with the development team on system architecture and improvements\n• Document technical processes and procedures",
                'requirements' => "• Bachelor's degree in Computer Science, Information Technology, or related field\n• 3+ years of experience in IT support or system administration\n• Proficiency in Linux/Unix server environments\n• Experience with cloud platforms (AWS, Azure, or GCP)\n• Solid understanding of networking (TCP/IP, DNS, firewalls, VPN)\n• Experience with PHP/Laravel applications is a strong plus\n• Knowledge of MySQL/PostgreSQL database administration\n• Strong problem-solving skills and attention to detail\n• Excellent communication skills in English",
                'benefits'     => "• Competitive salary package\n• Health insurance coverage\n• Flexible working hours\n• Remote work options available\n• Annual performance bonus\n• Professional development budget\n• 25 days annual leave\n• Collaborative and innovative work environment",
            ]
        );
    }
}
