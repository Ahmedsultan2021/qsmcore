<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\FormTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * `template_category` is the legacy label; we slug it to match `form_templates.library_key`
         * and attach via `department_form_template` for the company’s sector.
         */
        $map = [
            // ── AVIATION / Airlines ───────────────────────────────────────────────
            'SkyLine Airlines' => [
                ['name' => 'Flight Operations', 'template_category' => 'Aviation - Flight Ops',    'description' => 'Crew scheduling, flight dispatch, and airborne safety management'],
                ['name' => 'Ground Operations', 'template_category' => 'Aviation - Ground Safety', 'description' => 'Ramp services, turnaround coordination, and ground safety'],
                ['name' => 'OCC',               'template_category' => 'Aviation - OCC',           'description' => 'Operations Control Center — disruption management and crew coordination'],
                ['name' => 'Maintenance',       'template_category' => 'Aviation - Maintenance',   'description' => 'Line and base aircraft maintenance and MEL control'],
                ['name' => 'Training',          'template_category' => 'Aviation - Training',      'description' => 'Crew training, simulator sessions, and competency management'],
                ['name' => 'Safety',            'template_category' => 'Aviation - Safety',        'description' => 'Safety Management System, hazard reporting, and investigations'],
                ['name' => 'Quality',           'template_category' => 'Aviation - Quality',       'description' => 'Quality audits, NCR management, and compliance monitoring'],
            ],

            // ── AVIATION / MRO ────────────────────────────────────────────────────
            'AeroTech MRO' => [
                ['name' => 'Safety Reports',      'template_category' => 'Aviation - MRO Safety',  'description' => 'MRO workplace safety, incident reporting, and risk assessment'],
                ['name' => 'Quality Reports',     'template_category' => 'Aviation - MRO Quality', 'description' => 'MRO audits, NCR/CAR/PAR, calibration, and supplier evaluation'],
                ['name' => 'Operational Reports', 'template_category' => 'Aviation - MRO',         'description' => 'Technical defects, MEL control, scheduled maintenance, and manpower utilization'],
            ],

            // ── AVIATION / Airport ────────────────────────────────────────────────
            'Central Airport Authority' => [
                ['name' => 'Safety Reports',     'template_category' => 'Aviation - Airport Safety',    'description' => 'Airside incidents, ground vehicle incidents, wildlife strikes, and runway hazards'],
                ['name' => 'Quality Reports',    'template_category' => 'Aviation - Airport Quality',   'description' => 'Internal audits, terminal inspections, compliance audits, NCR/CAR/PAR, and contractor evaluation'],
                ['name' => 'Operations Reports', 'template_category' => 'Aviation - Airport Operations', 'description' => 'Turnaround oversight, fuel handling, GSE checks, and lost & found audits'],
            ],

            // ── OGE / Safety ──────────────────────────────────────────────────────
            'Gulf Petroleum HSE Ltd.' => [
                ['name' => 'Safety', 'template_category' => 'OGE Safety', 'description' => 'Incident/accident, near miss, hazard, personal injury, spill, fire, equipment failure, process safety, unsafe act, risk assessment'],
            ],

            // ── OGE / Quality ─────────────────────────────────────────────────────
            'Petroleum Quality Corp.' => [
                ['name' => 'Quality', 'template_category' => 'OGE Quality', 'description' => 'Internal audits, NCR, CAR, PAR, inspection, material defect, supplier, change management, calibration, quality improvement'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Maritime ─────────────────────────────
            'SeaRoute Maritime Ltd.' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Maritime incident, near miss, safety observations, man overboard, pollution/spill'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Rail ────────────────────────────────
            'RailConnect Operations' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Track incidents, near miss, safety observations, level crossing incidents'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Road Transport ───────────────────────
            'FreightLink Transport Co.' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Road incidents, near miss, driver behavior, load/vehicle inspections'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
        ];

        foreach ($map as $companyName => $departments) {
            $company = Company::where('name', $companyName)->first();
            if (! $company) {
                continue;
            }

            foreach ($departments as $deptSpec) {
                $department = Department::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name'       => $deptSpec['name'],
                    ],
                    [
                        'description' => $deptSpec['description'],
                    ]
                );

                $sectorId = $company->sector_id;
                if (! $sectorId) {
                    $department->formTemplates()->sync([]);

                    continue;
                }

                $libraryKey = Str::slug($deptSpec['template_category']);

                $templateIds = FormTemplate::query()
                    ->where('library_key', $libraryKey)
                    ->whereHas('sectors', fn ($q) => $q->where('sectors.id', $sectorId))
                    ->pluck('id')
                    ->all();

                $department->formTemplates()->sync($templateIds);
            }
        }
    }
}
