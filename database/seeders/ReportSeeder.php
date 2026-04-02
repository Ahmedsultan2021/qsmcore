<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormTemplate;
use App\Models\Report;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Map department name to form template library_key slugs (matches FormTemplateSeeder).
     */
    protected function departmentLibraryKeys(): array
    {
        return [
            'Production' => ['aviation-quality', 'aviation-maintenance'],
            'Sales' => ['aviation-quality'],
            'Human Resources' => ['aviation-safety', 'aviation-training'],
            'Development' => ['aviation-safety'],
            'Technical Support' => ['aviation-safety'],
            'Investment' => ['aviation-quality'],
            'Accounting' => ['aviation-quality'],
            'Emergency' => ['aviation-safety', 'aviation-quality'],
            'Surgery' => ['aviation-safety', 'aviation-quality'],
            'Projects' => ['aviation-maintenance', 'aviation-safety', 'aviation-quality'],
            'Procurement' => ['aviation-maintenance', 'aviation-safety', 'aviation-quality'],
            'Academic Affairs' => ['aviation-training', 'aviation-quality'],
            'Student Affairs' => ['aviation-training', 'aviation-quality'],
            'Warehouse' => ['aviation-safety', 'aviation-maintenance'],
            'Operations' => ['aviation-maintenance', 'aviation-safety'],
            'Maintenance' => ['aviation-maintenance', 'aviation-safety'],
        ];
    }

    /**
     * Ensure a Form exists for this department from the given template; create if not.
     */
    protected function formFromTemplate(Department $department, FormTemplate $template): ?Form
    {
        $form = Form::where('company_id', $department->company_id)
            ->where('department_id', $department->id)
            ->where('name', $template->name)
            ->first();

        if ($form) {
            return $form;
        }

        $form = Form::create([
            'company_id' => $department->company_id,
            'department_id' => $department->id,
            'name' => $template->name,
        ]);

        $template->load('formTemplateFields');
        foreach ($template->formTemplateFields as $order => $tf) {
            FormField::create([
                'form_id' => $form->id,
                'field_type' => $tf->field_type,
                'label' => $tf->label,
                'name' => $tf->name,
                'placeholder' => $tf->placeholder,
                'required' => $tf->required,
                'options' => $tf->options,
                'order' => $order,
            ]);
        }

        return $form;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $libraryKeysMap = $this->departmentLibraryKeys();
        $now = Carbon::now();
        $reportStatuses = ['draft', 'submitted', 'submitted', 'approved', 'approved']; // bias to submitted/approved

        Department::with('company')->get()->each(function (Department $department) use ($libraryKeysMap, $now, $reportStatuses) {
            $libraryKeys = $libraryKeysMap[$department->name] ?? ['aviation-quality', 'aviation-safety'];
            $templates = FormTemplate::whereIn('library_key', $libraryKeys)
                ->with('formTemplateFields')
                ->orderBy('library_key')
                ->orderBy('name')
                ->limit(4)
                ->get();

            if ($templates->isEmpty()) {
                return;
            }

            $forms = collect();
            foreach ($templates as $template) {
                $form = $this->formFromTemplate($department, $template);
                if ($form) {
                    $forms->push($form);
                }
            }

            if ($forms->isEmpty()) {
                return;
            }

            $creator = Employee::where('company_id', $department->company_id)->inRandomOrder()->first();
            if (!$creator) {
                return;
            }

            $titles = [
                'Monthly ' . $department->name . ' Summary',
                $department->name . ' Compliance Report',
                $now->format('F Y') . ' – ' . $department->name . ' Review',
                'Quarterly ' . $department->name . ' Report',
            ];

            foreach (array_slice($titles, 0, 2) as $title) {
                $reportDate = $now->copy()->subDays(rand(1, 60));
                $report = Report::firstOrCreate(
                    [
                        'department_id' => $department->id,
                        'title' => $title,
                    ],
                    [
                        'department_id' => $department->id,
                        'title' => $title,
                        'description' => 'Report generated for ' . $department->name . ' covering relevant metrics and compliance.',
                        'status' => $reportStatuses[array_rand($reportStatuses)],
                        'report_date' => $reportDate,
                        'created_by' => $creator->id,
                    ]
                );

                $attach = $forms->random(min(2, $forms->count()));
                foreach ($attach as $form) {
                    $report->forms()->syncWithoutDetaching([$form->id]);
                }
            }
        });
    }
}
