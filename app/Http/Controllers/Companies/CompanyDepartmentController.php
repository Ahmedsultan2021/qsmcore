<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Audit;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CompanyDepartmentController extends Controller
{
    /**
     * @return list<array{group_key: string, group_label: string, templates: list<object>}>
     */
    protected function sectorTemplateGroups(Employee $employee): array
    {
        $employee->loadMissing('company.sector');
        $sector = $employee->company?->sector;
        if (! $sector) {
            return [];
        }

        return $sector->formTemplates()
            ->get(['form_templates.id', 'form_templates.name', 'form_templates.library_key'])
            ->sortBy(['library_key', 'name'])
            ->groupBy(fn ($t) => $t->library_key ?? '')
            ->sortKeys()
            ->map(fn ($group, $key) => [
                'group_key' => (string) $key,
                'group_label' => $key !== '' ? Str::title(str_replace('-', ' ', (string) $key)) : 'General',
                'templates' => $group->values()->all(),
            ])
            ->values()
            ->all();
    }

    protected function allowedTemplateIds(Employee $employee): \Illuminate\Support\Collection
    {
        $employee->loadMissing('company.sector');
        $sector = $employee->company?->sector;
        if (! $sector) {
            return collect();
        }

        return $sector->formTemplates()->pluck('form_templates.id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $departments = Department::where('company_id', $employee->company_id)
            ->with('company')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Companies/Departments/Index', [
            'departments' => $departments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = Auth::guard('employee')->user();

        return Inertia::render('Companies/Departments/Create', [
            'company_id' => $employee->company_id,
            'sectorTemplateGroups' => $this->sectorTemplateGroups($employee),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $allowed = $this->allowedTemplateIds($employee);
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'template_ids' => ['nullable', 'array'],
        ];
        if ($allowed->isNotEmpty()) {
            $rules['template_ids.*'] = ['integer', Rule::in($allowed->all())];
        }
        $validated = $request->validate($rules);

        $validated['company_id'] = $employee->company_id;
        $templateIds = $validated['template_ids'] ?? null;
        unset($validated['template_ids']);

        $department = Department::create($validated);

        if (! is_array($templateIds) || $templateIds === []) {
            $templateIds = $allowed->all();
        }
        $department->formTemplates()->sync(array_values(array_unique($templateIds)));

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }
        
        $department->load('company.sector.industry');
        
        // Get reports count for statistics
        $reportsCount = $department->reports()->count();
        $reportsByStatus = $department->reports()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Real form statistics for the department
        $departmentForms = $department->forms;
        $totalForms = $departmentForms->count();
        
        // Get all form IDs that have at least one response (completed)
        $completedFormIds = FormResponse::whereHas('form', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->distinct()
            ->pluck('form_id')
            ->toArray();
        
        // Get all reports for this department with their forms
        $departmentReports = $department->reports()->with('forms')->get();
        
        // Track forms attached to reports (but not completed)
        $formsAttachedToReports = [];
        
        foreach ($departmentReports as $report) {
            foreach ($report->forms as $form) {
                $formId = $form->id;
                
                // Only track if form is not already completed
                if (!in_array($formId, $completedFormIds) && !in_array($formId, $formsAttachedToReports)) {
                    $formsAttachedToReports[] = $formId;
                }
            }
        }
        
        // Calculate statistics
        $completed = count(array_unique($completedFormIds));
        $inProgress = count($formsAttachedToReports); // Forms attached to reports but not completed
        $pending = $totalForms - $completed - $inProgress; // Forms not attached to any report
        
        $formsStats = [
            'total' => $totalForms,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => max(0, $pending), // Ensure non-negative
        ];
        
        // Real risk statistics
        $risks = $department->risks;
        $riskStats = [
            'total' => $risks->count(),
            'low' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) < 5)->count(),
            'medium' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 5 && ($r->likelihood * $r->impact) < 10)->count(),
            'high' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 10 && ($r->likelihood * $r->impact) < 15)->count(),
            'critical' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 15)->count(),
        ];
        
        // Real audit statistics - get audits related to this department's reports
        $departmentReportIds = $department->reports()->pluck('id')->toArray();
        
        // Get audits that have at least one report from this department
        $audits = Audit::where('company_id', $authEmployee->company_id)
            ->whereHas('reports', function ($query) use ($departmentReportIds) {
                $query->whereIn('reports.id', $departmentReportIds);
            })
            ->with(['reports.forms', 'reports.formResponses'])
            ->get();
        
        // Calculate audit statistics based on completion status and date
        $completed = 0;
        $inProgress = 0;
        $pending = 0;
        $today = now()->startOfDay();
        
        foreach ($audits as $audit) {
            $auditDate = $audit->audit_date->startOfDay();
            $status = $audit->completion_status;
            
            // If audit date is in the future, it's pending
            if ($auditDate->isFuture()) {
                $pending++;
            } elseif ($status === 'complete') {
                $completed++;
            } elseif ($status === 'pending') {
                // Internal audit with incomplete reports = in progress
                if ($audit->reports->isNotEmpty()) {
                    $inProgress++;
                } else {
                    // This shouldn't happen (external audits are always complete), but handle it
                    $pending++;
                }
            }
        }
        
        $auditStats = [
            'total' => $audits->count(),
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $pending,
        ];
        
        // Fake statistics for functions
        $functionsStats = [
            'audits' => $auditStats,
            'kpis' => [
                'total' => 28,
                'on_target' => 22,
                'below_target' => 4,
                'above_target' => 2,
            ],
            'risk_register' => $riskStats,
        ];
        
        return Inertia::render('Companies/Departments/Show', [
            'department' => $department,
            'reportsCount' => $reportsCount,
            'reportsByStatus' => $reportsByStatus,
            'formsStats' => $formsStats,
            'functionsStats' => $functionsStats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }
        
        $department->load(['formTemplates:id,name,library_key']);

        return Inertia::render('Companies/Departments/Edit', [
            'department' => $department,
            'sectorTemplateGroups' => $this->sectorTemplateGroups($authEmployee),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }
        
        $allowed = $this->allowedTemplateIds($authEmployee);
        foreach ($department->formTemplates()->pluck('form_templates.id') as $id) {
            if (! $allowed->contains($id)) {
                $allowed = $allowed->push($id);
            }
        }
        $allowed = $allowed->unique()->values();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'template_ids' => ['required', 'array', 'min:1'],
            'template_ids.*' => ['integer', Rule::in($allowed->all())],
        ]);

        $templateIds = $validated['template_ids'];
        unset($validated['template_ids']);

        $department->update($validated);
        $department->formTemplates()->sync(array_values(array_unique($templateIds)));

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * Seed default departments for the authenticated company based on its sector.
     */
    public function seedDefaults()
    {
        $employee = Auth::guard('employee')->user();
        $employee->loadMissing('company.sector');
        $company = $employee->company;
        $sector  = $company?->sector;

        if (! $sector) {
            return redirect()->route('companies.departments.index')
                ->with('error', 'Your company has no sector assigned.');
        }

        $sectorMap = [
            'Airlines' => [
                ['name' => 'Flight Operations', 'template_category' => 'Aviation - Flight Ops',         'description' => 'Crew scheduling, flight dispatch, and airborne safety management'],
                ['name' => 'Ground Operations', 'template_category' => 'Aviation - Ground Safety',      'description' => 'Ramp services, turnaround coordination, and ground safety'],
                ['name' => 'OCC',               'template_category' => 'Aviation - OCC',                'description' => 'Operations Control Center — disruption management and crew coordination'],
                ['name' => 'Maintenance',       'template_category' => 'Aviation - Maintenance',        'description' => 'Line and base aircraft maintenance and MEL control'],
                ['name' => 'Training',          'template_category' => 'Aviation - Training',           'description' => 'Crew training, simulator sessions, and competency management'],
                ['name' => 'Safety',            'template_category' => 'Aviation - Safety',             'description' => 'Safety Management System, hazard reporting, and investigations'],
                ['name' => 'Quality',           'template_category' => 'Aviation - Quality',            'description' => 'Quality audits, NCR management, and compliance monitoring'],
            ],
            'MRO' => [
                ['name' => 'Safety Reports',      'template_category' => 'Aviation - MRO Safety',       'description' => 'MRO workplace safety, incident reporting, and risk assessment'],
                ['name' => 'Quality Reports',     'template_category' => 'Aviation - MRO Quality',      'description' => 'MRO audits, NCR/CAR/PAR, calibration, and supplier evaluation'],
                ['name' => 'Operational Reports', 'template_category' => 'Aviation - MRO',              'description' => 'Technical defects, MEL control, scheduled maintenance, and manpower utilization'],
            ],
            'Airport' => [
                ['name' => 'Safety Reports',     'template_category' => 'Aviation - Airport Safety',    'description' => 'Airside incidents, ground vehicle incidents, wildlife strikes, and runway hazards'],
                ['name' => 'Quality Reports',    'template_category' => 'Aviation - Airport Quality',   'description' => 'Internal audits, terminal inspections, compliance audits, NCR/CAR/PAR, and contractor evaluation'],
                ['name' => 'Operations Reports', 'template_category' => 'Aviation - Airport Operations','description' => 'Turnaround oversight, fuel handling, GSE checks, and lost & found audits'],
            ],
            'OGE Sector' => [
                ['name' => 'Safety',  'template_category' => 'OGE Safety',  'description' => 'Incident/accident, near miss, hazard, personal injury, spill, fire, equipment failure, process safety, unsafe act, risk assessment'],
                ['name' => 'Quality', 'template_category' => 'OGE Quality', 'description' => 'Internal audits, NCR, CAR, PAR, inspection, material defect, supplier, change management, calibration, quality improvement'],
            ],
            'Maritime' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Maritime incident, near miss, safety observations, man overboard, pollution/spill'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
            'Rail' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Track incidents, near miss, safety observations, level crossing incidents'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
            'Road Transport' => [
                ['name' => 'Safety',  'template_category' => 'Logistics & Transportation - Safety',  'description' => 'Road incidents, near miss, driver behavior, load/vehicle inspections'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
        ];

        $deptSpecs = $sectorMap[$sector->name] ?? null;

        if (! $deptSpecs) {
            return redirect()->route('companies.departments.index')
                ->with('error', 'No default departments are defined for your sector.');
        }

        foreach ($deptSpecs as $spec) {
            $department = Department::updateOrCreate(
                ['company_id' => $company->id, 'name' => $spec['name']],
                ['description' => $spec['description']]
            );

            $libraryKey  = Str::slug($spec['template_category']);
            $templateIds = \App\Models\FormTemplate::query()
                ->where('library_key', $libraryKey)
                ->whereHas('sectors', fn ($q) => $q->where('sectors.id', $sector->id))
                ->pluck('id')
                ->all();

            $department->formTemplates()->sync($templateIds);
        }

        return redirect()->route('companies.departments.index')
            ->with('success', 'Default departments seeded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }
        
        $department->delete();

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}

