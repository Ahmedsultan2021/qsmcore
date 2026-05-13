<?php

namespace App\Http\Controllers\Companies;

use App\Exports\CompanyReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Department;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormResponse;
use App\Models\FormTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

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
     * Download all company reports as an Excel spreadsheet.
     */
    public function exportExcel(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        $companyId    = (int) $authEmployee->company_id;

        $departmentId = null;
        if ($request->filled('department_id')) {
            $departmentId = (int) $request->department_id;
            $owns = Department::where('id', $departmentId)
                ->where('company_id', $companyId)
                ->exists();
            if (!$owns) {
                abort(403);
            }
        }

        $filename = 'reports-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new CompanyReportsExport($companyId, $departmentId), $filename);
    }

    /**
     * Render a printable HTML view of all company reports (browser → save as PDF).
     */
    public function exportPdf(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        $companyId    = (int) $authEmployee->company_id;

        $department = null;
        if ($request->filled('department_id')) {
            $department = Department::where('id', (int) $request->department_id)
                ->where('company_id', $companyId)
                ->firstOrFail();
        }

        $query = Report::query()
            ->with(['department', 'creator'])
            ->whereHas('department', fn ($q) => $q->where('company_id', $companyId))
            ->latest();

        if ($department) {
            $query->where('department_id', $department->id);
        }

        return response()->view('exports.reports-pdf', [
            'reports'     => $query->get(),
            'company'     => $authEmployee->company,
            'department'  => $department,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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

        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
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

    /**
     * Download a single report as PDF or Word document.
     */
    public function downloadReport(Request $request, Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();

        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }

        $request->validate([
            'format' => 'required|in:pdf,word',
            'mode'   => 'required|in:full,content',
        ]);

        $format = $request->input('format');
        $mode   = $request->input('mode');

        $report->load([
            'department.company',
            'creator',
            'forms.formFields',
        ]);

        $company    = $department->company ?? $report->department?->company;
        $creator    = $report->creator ? trim($report->creator->fname . ' ' . $report->creator->lname) : null;
        $formsData  = $this->buildFormsData($report, $mode);

        if ($format === 'pdf') {
            return $this->generatePdf($report, $company, $department, $creator, $formsData, $mode);
        }

        return $this->generateWord($report, $company, $department, $creator, $formsData, $mode);
    }

    private function buildFormsData(Report $report, string $mode): array
    {
        $forms = [];

        foreach ($report->forms as $form) {
            $formData = [
                'name'         => $form->name,
                'fields'       => [],
                'submitted_at' => null,
                'submitter'    => null,
            ];

            $response = null;
            if ($mode === 'full') {
                $response = FormResponse::where('report_id', $report->id)
                    ->where('form_id', $form->id)
                    ->with('submitter')
                    ->latest()
                    ->first();

                if ($response) {
                    $formData['submitted_at'] = $response->created_at?->format('d-M-Y H:i');
                    $sub = $response->submitter;
                    $formData['submitter'] = $sub ? trim($sub->fname . ' ' . $sub->lname) : null;
                }
            }

            $fields = $form->formFields()->orderBy('order')->get();

            foreach ($fields as $field) {
                $fieldData = [
                    'label'      => $field->label,
                    'type'       => $field->field_type,
                    'required'   => (bool) $field->required,
                    'options'    => $field->options ?? [],
                    'answer'     => null,
                ];

                if ($mode === 'full' && $response) {
                    $fieldData['answer'] = $response->responses[$field->name] ?? null;
                }

                $formData['fields'][] = $fieldData;
            }

            $forms[] = $formData;
        }

        return $forms;
    }

    private function generatePdf(Report $report, $company, $department, $creator, array $forms, string $mode)
    {
        $pdf = Pdf::loadView('exports.report-download', [
            'report'      => $report,
            'company'     => $company,
            'department'  => $department,
            'creator'     => $creator,
            'forms'       => $forms,
            'mode'        => $mode,
            'generatedAt' => now()->format('d-M-Y H:i'),
        ]);

        $pdf->setPaper('a4');

        $slug = str()->slug($report->title);
        $filename = "report-{$report->id}-{$slug}.pdf";

        return $pdf->download($filename);
    }

    private function sanitize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '-';
        }
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);
        return str_replace(
            ["\xC2\xA0", "\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\x98", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D"],
            [' ', '-', '-', "'", "'", '"', '"'],
            $text
        );
    }

    private function generateWord(Report $report, $company, $department, $creator, array $forms, string $mode)
    {
        $phpWord = new PhpWord();
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        $phpWord->addTitleStyle(1, ['size' => 22, 'bold' => true, 'color' => '059669'], ['spaceAfter' => 80]);
        $phpWord->addTitleStyle(2, ['size' => 15, 'bold' => true, 'color' => '059669'], ['spaceBefore' => 200, 'spaceAfter' => 80]);

        $section = $phpWord->addSection(['marginTop' => 600, 'marginBottom' => 600, 'marginLeft' => 800, 'marginRight' => 800]);

        $section->addTitle($this->sanitize($report->title), 1);

        $subtitle = $this->sanitize($company?->name) . ' - ' . $this->sanitize($department?->name);
        $section->addText($subtitle, ['size' => 11, 'color' => '6b7280'], ['spaceAfter' => 120]);

        // Metadata table
        $metaTable = $section->addTable([
            'borderSize' => 4, 'borderColor' => 'e5e7eb',
            'cellMargin' => 60,
        ]);

        $metaCellStyle = ['valign' => 'top'];
        $labelStyle = ['size' => 8, 'bold' => true, 'color' => '6b7280'];
        $valueStyle = ['size' => 11, 'bold' => true, 'color' => '111827'];

        $metaTable->addRow();
        $cell = $metaTable->addCell(4800, $metaCellStyle);
        $cell->addText('REPORT DATE', $labelStyle);
        $cell->addText($this->sanitize(optional($report->report_date)->format('d-M-Y')), $valueStyle);

        $cell = $metaTable->addCell(4800, $metaCellStyle);
        $cell->addText('STATUS', $labelStyle);
        $cell->addText(ucfirst($report->status), $valueStyle);

        $metaTable->addRow();
        $cell = $metaTable->addCell(4800, $metaCellStyle);
        $cell->addText('CREATED BY', $labelStyle);
        $cell->addText($this->sanitize($creator), $valueStyle);

        $cell = $metaTable->addCell(4800, $metaCellStyle);
        $cell->addText('DEPARTMENT', $labelStyle);
        $cell->addText($this->sanitize($department?->name), $valueStyle);

        if ($report->description) {
            $metaTable->addRow();
            $cell = $metaTable->addCell(9600, array_merge($metaCellStyle, ['gridSpan' => 2]));
            $cell->addText('DESCRIPTION', $labelStyle);
            $cell->addText($this->sanitize($report->description), ['size' => 11, 'color' => '374151']);
        }

        $section->addTextBreak(1);

        // Forms
        if (empty($forms)) {
            $section->addText('No forms attached to this report.', ['size' => 11, 'color' => '9ca3af', 'italic' => true], ['alignment' => Jc::CENTER]);
        }

        foreach ($forms as $form) {
            $section->addTitle($this->sanitize($form['name']), 2);

            if ($mode === 'full' && !empty($form['submitted_at'])) {
                $subInfo = 'Submitted ' . $form['submitted_at'];
                if (!empty($form['submitter'])) {
                    $subInfo .= ' by ' . $this->sanitize($form['submitter']);
                }
                $section->addText($subInfo, ['size' => 9, 'color' => '6b7280', 'italic' => true], ['spaceAfter' => 80]);
            }

            $fieldTable = $section->addTable([
                'borderSize' => 4, 'borderColor' => 'e5e7eb',
                'cellMargin' => 60,
            ]);

            foreach ($form['fields'] as $field) {
                $fieldTable->addRow();

                $lCell = $fieldTable->addCell(3200, ['valign' => 'top', 'bgColor' => 'f9fafb']);
                $labelText = $this->sanitize($field['label']);
                if ($field['required']) {
                    $labelText .= ' *';
                }
                $lCell->addText($labelText, ['size' => 10, 'bold' => true, 'color' => '374151']);

                $vCell = $fieldTable->addCell(6400, ['valign' => 'top']);

                if ($mode === 'full') {
                    $answer = $field['answer'];
                    if ($field['type'] === 'signature' && !empty($answer) && is_string($answer) && str_starts_with($answer, 'data:image')) {
                        $vCell->addText('[Signature on file]', ['size' => 10, 'italic' => true, 'color' => '6b7280']);
                    } elseif (!empty($answer)) {
                        $text = is_array($answer) ? implode(', ', $answer) : (string) $answer;
                        $vCell->addText($this->sanitize($text), ['size' => 11, 'color' => '111827']);
                    } else {
                        $vCell->addText('No response provided', ['size' => 10, 'italic' => true, 'color' => '9ca3af']);
                    }
                } else {
                    if (!empty($field['options'])) {
                        $opts = is_array($field['options']) ? implode(', ', $field['options']) : (string) $field['options'];
                        $vCell->addText('Options: ' . $this->sanitize($opts), ['size' => 9, 'color' => '6b7280']);
                    }
                    $vCell->addText(' ', ['size' => 11]);
                }
            }

            $section->addTextBreak(1);
        }

        // Footer
        $section->addText(
            'QSMCore - Report #' . $report->id . '  |  Generated ' . now()->format('d-M-Y H:i'),
            ['size' => 8, 'color' => '9ca3af'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 200]
        );

        $slug     = str()->slug($report->title);
        $filename = "report-{$report->id}-{$slug}.docx";
        $tempPath = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $filename);

        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');

        if (ob_get_level()) {
            ob_end_clean();
        }

        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
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
        if ((int) $department->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $report->delete();

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report deleted successfully');
    }
}

