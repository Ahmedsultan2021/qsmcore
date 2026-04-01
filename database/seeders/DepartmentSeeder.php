<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // form_category must exactly match the 'category' value used in FormTemplateSeeder
        // so that CompanyReportController can load the correct FormTemplate records per dept.
        $map = [
            // ── AVIATION / Airlines ───────────────────────────────────────────────
            'SkyLine Airlines' => [
                ['name' => 'Flight Operations', 'form_category' => 'Aviation - Flight Ops',    'description' => 'Crew scheduling, flight dispatch, and airborne safety management'],
                ['name' => 'Ground Operations', 'form_category' => 'Aviation - Ground Safety', 'description' => 'Ramp services, turnaround coordination, and ground safety'],
                ['name' => 'OCC',               'form_category' => 'Aviation - OCC',           'description' => 'Operations Control Center — disruption management and crew coordination'],
                ['name' => 'Maintenance',       'form_category' => 'Aviation - Maintenance',   'description' => 'Line and base aircraft maintenance and MEL control'],
                ['name' => 'Training',          'form_category' => 'Aviation - Training',      'description' => 'Crew training, simulator sessions, and competency management'],
                ['name' => 'Safety',            'form_category' => 'Aviation - Safety',        'description' => 'Safety Management System, hazard reporting, and investigations'],
                ['name' => 'Quality',           'form_category' => 'Aviation - Quality',       'description' => 'Quality audits, NCR management, and compliance monitoring'],
            ],

            // ── AVIATION / MRO ────────────────────────────────────────────────────
            'AeroTech MRO' => [
                ['name' => 'Safety Reports',      'form_category' => 'Aviation - MRO Safety',  'description' => 'MRO workplace safety, incident reporting, and risk assessment'],
                ['name' => 'Quality Reports',     'form_category' => 'Aviation - MRO Quality', 'description' => 'MRO audits, NCR/CAR/PAR, calibration, and supplier evaluation'],
                ['name' => 'Operational Reports', 'form_category' => 'Aviation - MRO',         'description' => 'Technical defects, MEL control, scheduled maintenance, and manpower utilization'],
            ],

            // ── AVIATION / Airport ────────────────────────────────────────────────
            'Central Airport Authority' => [
                ['name' => 'Safety Reports',     'form_category' => 'Aviation - Airport Safety', 'description' => 'Airside incidents, ground vehicle incidents, wildlife strikes, and runway hazards'],
                ['name' => 'Quality Reports',    'form_category' => null,                        'description' => 'Internal audits, terminal inspections, compliance audits, NCR/CAR/PAR, and contractor evaluation'],
                ['name' => 'Operations Reports', 'form_category' => null,                        'description' => 'Turnaround oversight, fuel handling, GSE checks, and lost & found audits'],
            ],

            // ── OGE / Safety ──────────────────────────────────────────────────────
            'Gulf Petroleum HSE Ltd.' => [
                ['name' => 'Safety', 'form_category' => 'OGE Safety', 'description' => 'Incident/accident, near miss, hazard, personal injury, spill, fire, equipment failure, process safety, unsafe act, risk assessment'],
            ],

            // ── OGE / Quality ─────────────────────────────────────────────────────
            'Petroleum Quality Corp.' => [
                ['name' => 'Quality', 'form_category' => 'OGE Quality', 'description' => 'Internal audits, NCR, CAR, PAR, inspection, material defect, supplier, change management, calibration, quality improvement'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Maritime ─────────────────────────────
            'SeaRoute Maritime Ltd.' => [
                ['name' => 'Safety',  'form_category' => 'Logistics & Transportation - Safety', 'description' => 'Maritime incident, near miss, safety observations, man overboard, pollution/spill'],
                ['name' => 'Quality', 'form_category' => null,                                  'description' => 'NCR, audit checklists, and CAPA forms'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Rail ────────────────────────────────
            'RailConnect Operations' => [
                ['name' => 'Safety',  'form_category' => 'Logistics & Transportation - Safety', 'description' => 'Track incidents, near miss, safety observations, level crossing incidents'],
                ['name' => 'Quality', 'form_category' => null,                                  'description' => 'NCR, audit checklists, and CAPA forms'],
            ],

            // ── LOGISTICS & TRANSPORTATION / Road Transport ───────────────────────
            'FreightLink Transport Co.' => [
                ['name' => 'Safety',  'form_category' => 'Logistics & Transportation - Safety', 'description' => 'Road incidents, near miss, driver behavior, load/vehicle inspections'],
                ['name' => 'Quality', 'form_category' => null,                                  'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
        ];

        foreach ($map as $companyName => $departments) {
            $company = Company::where('name', $companyName)->first();
            if (!$company) {
                continue;
            }

            foreach ($departments as $dept) {
                Department::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name'       => $dept['name'],
                    ],
                    [
                        'company_id'    => $company->id,
                        'name'          => $dept['name'],
                        'description'   => $dept['description'],
                        'form_category' => $dept['form_category'],
                    ]
                );
            }
        }
    }
}
