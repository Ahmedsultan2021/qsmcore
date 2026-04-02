<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Department;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormResponse;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyReportController extends Controller
{
    /**
     * Display all reports for the company (across all departments).
     */
    public function allReports(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        $query = Report::whereHas('department', function ($query) use ($authEmployee) {
                $query->where('company_id', $authEmployee->company_id);
            })
            ->with(['department', 'creator']);
        
        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        $reports = $query->latest()->paginate(10)->withQueryString();
        
        $departments = Department::where('company_id', $authEmployee->company_id)
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Companies/Reports/AllReports', [
            'reports' => $reports,
            'departments' => $departments,
            'filters' => $request->only(['department_id']),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $reports = Report::where('department_id', $department->id)
            ->with(['department', 'creator'])
            ->latest()
            ->paginate(10);
        
        $formsCount = Form::where('department_id', $department->id)->count();
        
        return Inertia::render('Companies/Reports/Index', [
            'department' => $department,
            'reports' => $reports,
            'formsCount' => $formsCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();

        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $formTemplates = $this->templatesForDepartment($authEmployee, $department);

        return Inertia::render('Companies/Reports/Create', [
            'department'    => $department,
            'formTemplates' => $formTemplates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'required|in:draft,submitted,reviewed,approved,rejected',
            'report_date'        => 'required|date',
            'form_template_ids'  => 'nullable|array',
            'form_template_ids.*'=> 'exists:form_templates,id',
        ]);

        $validated['department_id'] = $department->id;
        $validated['created_by']    = $authEmployee->id;

        $templateIds = $validated['form_template_ids'] ?? [];
        unset($validated['form_template_ids']);

        $report = Report::create($validated);

        if (!empty($templateIds)) {
            $formIds = $this->resolveFormIds($authEmployee->company_id, $department->id, $templateIds);
            $report->forms()->attach($formIds);
        }

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $department->load('company');
        
        // Report is already scoped to department via route model binding
        $report->load([
            'department.company.sector.industry',
            'creator',
            'forms',
            'formResponses',
            'reportFiles.uploader',
        ]);
        
        // Get form submission status for current user
        $authEmployee = Auth::guard('employee')->user();
        $submittedForms = FormResponse::where('report_id', $report->id)
            ->where('submitted_by', $authEmployee->id)
            ->pluck('form_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
        
        return Inertia::render('Companies/Reports/Show', [
            'department' => $department,
            'report' => $report,
            'submittedForms' => $submittedForms,
            'generalReportStatus' => $report->general_report_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $formTemplates = $this->templatesForDepartment($authEmployee, $department);
        $report->load('forms.formTemplate');

        return Inertia::render('Companies/Reports/Edit', [
            'department'    => $department,
            'report'        => $report,
            'formTemplates' => $formTemplates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'required|in:draft,submitted,reviewed,approved,rejected',
            'report_date'        => 'required|date',
            'form_template_ids'  => 'nullable|array',
            'form_template_ids.*'=> 'exists:form_templates,id',
        ]);

        $templateIds = $validated['form_template_ids'] ?? [];
        unset($validated['form_template_ids']);

        $report->update($validated);

        $formIds = $this->resolveFormIds($authEmployee->company_id, $department->id, $templateIds);
        $report->forms()->sync($formIds);

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report updated successfully');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return FormTemplate records for the company's sector that are linked to the department.
     */
    private function templatesForDepartment($authEmployee, Department $department): \Illuminate\Support\Collection
    {
        $company = $authEmployee->company;
        $sector  = $company->sector;

        if (!$sector) {
            return collect();
        }

        $query = $sector->formTemplates()
            ->linkedToDepartment($department)
            ->orderBy('name');

        return $query->get(['form_templates.id', 'form_templates.name', 'form_templates.library_key']);
    }

    /**
     * For each selected FormTemplate ID, find an existing Form record for this
     * company+department+template, or create one (copying fields from the template).
     * Returns an array of Form IDs ready to attach/sync to a report.
     */
    private function resolveFormIds(int $companyId, int $departmentId, array $templateIds): array
    {
        $formIds = [];

        foreach ($templateIds as $templateId) {
            $form = Form::firstOrCreate(
                [
                    'company_id'       => $companyId,
                    'department_id'    => $departmentId,
                    'form_template_id' => $templateId,
                ],
                [
                    'name' => FormTemplate::find($templateId)?->name ?? 'Form',
                ]
            );

            // If newly created, copy fields from the template
            if ($form->wasRecentlyCreated) {
                $template = FormTemplate::with('formTemplateFields')->find($templateId);
                if ($template) {
                    foreach ($template->formTemplateFields as $order => $tf) {
                        FormField::create([
                            'form_id'     => $form->id,
                            'field_type'  => $tf->field_type,
                            'label'       => $tf->label,
                            'name'        => $tf->name,
                            'placeholder' => $tf->placeholder,
                            'required'    => $tf->required,
                            'options'     => $tf->options,
                            'order'       => $order,
                        ]);
                    }
                }
            }

            $formIds[] = $form->id;
        }

        return $formIds;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $report->delete();

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report deleted successfully');
    }
}

