<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TansikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleData = [
            [
                'student_name' => 'أحمد محمد علي',
                'tanseq_no' => '123456789',
                'certificate_name' => 'الثانوية العامة',
                'school' => 'مدرسة النهضة الثانوية',
                'department' => 'علمي',
                'governorate' => 'القاهرة',
                'branch' => 'أ',
                'specialization' => 'رياضيات',
                'year' => 2005,
                'month' => 6,
                'day' => 15,
                'national_id' => '12345678901234',
                'total_mark_limit' => 410.00,
                'total_mark' => 385.50,
                'percent' => 94.02
            ],
            [
                'student_name' => 'فاطمة أحمد حسن',
                'tanseq_no' => '987654321',
                'certificate_name' => 'الثانوية العامة',
                'school' => 'مدرسة الأمل الثانوية',
                'department' => 'علمي',
                'governorate' => 'الجيزة',
                'branch' => 'ب',
                'specialization' => 'فيزياء',
                'year' => 2005,
                'month' => 8,
                'day' => 22,
                'national_id' => '98765432109876',
                'total_mark_limit' => 410.00,
                'total_mark' => 378.25,
                'percent' => 92.26
            ],
            [
                'student_name' => 'محمد علي إبراهيم',
                'tanseq_no' => '456789123',
                'certificate_name' => 'الثانوية العامة',
                'school' => 'مدرسة المستقبل الثانوية',
                'department' => 'علمي',
                'governorate' => 'الإسكندرية',
                'branch' => 'ج',
                'specialization' => 'كيمياء',
                'year' => 2005,
                'month' => 3,
                'day' => 10,
                'national_id' => '45678912345678',
                'total_mark_limit' => 410.00,
                'total_mark' => 365.75,
                'percent' => 89.21
            ]
        ];

        foreach ($sampleData as $data) {
            Tansik::create($data);
        }
    }
}
