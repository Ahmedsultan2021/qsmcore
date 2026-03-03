<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Risk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $risks = [
            ['department' => 'Production', 'company' => 'United Foods Co.', 'title' => 'Equipment failure during peak production', 'description' => 'Risk of conveyor or packaging equipment breakdown affecting output and delivery schedules.', 'category' => 'operational', 'likelihood' => 3, 'impact' => 4, 'status' => 'in_progress', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Production', 'company' => 'United Foods Co.', 'title' => 'Raw material quality deviation', 'description' => 'Incoming raw materials may not meet specifications, leading to rework or batch rejection.', 'category' => 'operational', 'likelihood' => 2, 'impact' => 3, 'status' => 'assessed', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Sales', 'company' => 'United Foods Co.', 'title' => 'Key account loss', 'description' => 'Loss of a major retail or distributor account impacting revenue targets.', 'category' => 'financial', 'likelihood' => 2, 'impact' => 4, 'status' => 'monitoring', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Human Resources', 'company' => 'United Foods Co.', 'title' => 'Staff turnover in critical roles', 'description' => 'High turnover in quality and production supervision roles affecting consistency.', 'category' => 'human_resources', 'likelihood' => 3, 'impact' => 3, 'status' => 'identified', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Development', 'company' => 'Advanced Technology Inc.', 'title' => 'Security vulnerability in product', 'description' => 'Undetected vulnerability in software could lead to data breach or system compromise.', 'category' => 'technological', 'likelihood' => 2, 'impact' => 5, 'status' => 'in_progress', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Development', 'company' => 'Advanced Technology Inc.', 'title' => 'Third-party API dependency failure', 'description' => 'Critical features depend on external APIs; outage or change could affect service.', 'category' => 'technological', 'likelihood' => 3, 'impact' => 3, 'status' => 'assessed', 'treatment_strategy' => 'transfer'],
            ['department' => 'Technical Support', 'company' => 'Advanced Technology Inc.', 'title' => 'Insufficient documentation leading to support delays', 'description' => 'Lack of up-to-date docs increases resolution time and customer dissatisfaction.', 'category' => 'operational', 'likelihood' => 3, 'impact' => 2, 'status' => 'identified', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Investment', 'company' => 'Investment Finance Corp.', 'title' => 'Market volatility impact on portfolio', 'description' => 'Sharp market downturns could affect client portfolios and compliance limits.', 'category' => 'financial', 'likelihood' => 3, 'impact' => 4, 'status' => 'monitoring', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Accounting', 'company' => 'Investment Finance Corp.', 'title' => 'Regulatory reporting error', 'description' => 'Incorrect or late regulatory filing could result in penalties or reputational damage.', 'category' => 'compliance', 'likelihood' => 2, 'impact' => 4, 'status' => 'assessed', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Emergency', 'company' => 'Modern Hospitals Group', 'title' => 'Medical supply shortage', 'description' => 'Shortage of critical supplies during surge could affect patient care and safety.', 'category' => 'operational', 'likelihood' => 2, 'impact' => 5, 'status' => 'in_progress', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Surgery', 'company' => 'Modern Hospitals Group', 'title' => 'Surgical site infection rate', 'description' => 'Risk of healthcare-associated infections despite protocols.', 'category' => 'reputational', 'likelihood' => 2, 'impact' => 4, 'status' => 'monitoring', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Projects', 'company' => 'National Building Co.', 'title' => 'Contractor delay or default', 'description' => 'Key subcontractor delay or insolvency could impact project timeline and cost.', 'category' => 'operational', 'likelihood' => 3, 'impact' => 4, 'status' => 'identified', 'treatment_strategy' => 'transfer'],
            ['department' => 'Procurement', 'company' => 'National Building Co.', 'title' => 'Material price escalation', 'description' => 'Sudden increase in steel, cement, or other materials affecting project margins.', 'category' => 'financial', 'likelihood' => 3, 'impact' => 3, 'status' => 'assessed', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Academic Affairs', 'company' => 'Renaissance Academy', 'title' => 'Accreditation non-compliance', 'description' => 'Failure to meet accreditation standards could affect program recognition.', 'category' => 'compliance', 'likelihood' => 2, 'impact' => 4, 'status' => 'monitoring', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Warehouse', 'company' => 'Loyalty Stores Chain', 'title' => 'Inventory shrinkage (theft or damage)', 'description' => 'Shrinkage in warehouse or in-transit affecting profitability.', 'category' => 'operational', 'likelihood' => 3, 'impact' => 3, 'status' => 'identified', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Operations', 'company' => 'Eastern Energy Co.', 'title' => 'Process safety incident', 'description' => 'Potential release or fire in extraction or refining process.', 'category' => 'environmental', 'likelihood' => 2, 'impact' => 5, 'status' => 'in_progress', 'treatment_strategy' => 'mitigate'],
            ['department' => 'Maintenance', 'company' => 'Eastern Energy Co.', 'title' => 'Critical asset breakdown', 'description' => 'Unplanned failure of critical equipment leading to production stoppage.', 'category' => 'operational', 'likelihood' => 3, 'impact' => 4, 'status' => 'assessed', 'treatment_strategy' => 'mitigate'],
        ];

        $statuses = ['identified', 'assessed', 'in_progress', 'mitigated', 'monitoring', 'closed'];
        $now = Carbon::now();

        foreach ($risks as $i => $data) {
            $department = Department::where('name', $data['department'])
                ->whereHas('company', fn ($q) => $q->where('name', $data['company']))
                ->first();
            if (!$department) {
                continue;
            }

            $owner = Employee::where('company_id', $department->company_id)->inRandomOrder()->first();
            $dateIdentified = $now->copy()->subDays(rand(10, 180));
            $targetDate = $dateIdentified->copy()->addDays(rand(30, 90));

            Risk::firstOrCreate(
                [
                    'department_id' => $department->id,
                    'title' => $data['title'],
                ],
                [
                    'department_id' => $department->id,
                    'risk_owner_id' => $owner?->id,
                    'risk_code' => 'RISK-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'category' => $data['category'],
                    'likelihood' => $data['likelihood'],
                    'impact' => $data['impact'],
                    'current_controls' => 'Existing procedures and periodic reviews in place.',
                    'treatment_strategy' => $data['treatment_strategy'],
                    'action_plan' => 'Implement controls and review by target date.',
                    'target_date' => $targetDate,
                    'status' => $data['status'],
                    'date_identified' => $dateIdentified,
                    'last_review_date' => $dateIdentified->copy()->addDays(rand(14, 60)),
                ]
            );
        }
    }
}
