<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormTemplate;
use App\Exports\FormFieldsTemplateExport;
use App\Imports\FormFieldsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CompanyFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        $forms = Form::where('company_id', $employee->company_id)
            ->with('department')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Companies/Forms/Index', [
            'forms' => $forms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all departments for the company to populate the dropdown
        $departments = \App\Models\Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Companies/Forms/Create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // If department is specified, ensure it belongs to the employee's company
        if (!empty($validated['department_id'])) {
            $department = \App\Models\Department::find($validated['department_id']);
            if (!$department || $department->company_id !== $employee->company_id) {
                return back()->withErrors(['department_id' => 'Invalid department selected.']);
            }
        }

        // Ensure name is unique within the company
        $exists = Form::where('company_id', $employee->company_id)
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A form with this name already exists.']);
        }

        Form::create([
            'company_id' => $employee->company_id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
        ]);

        return redirect()->route('companies.forms.index')
            ->with('success', 'Form created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        $employee = Auth::guard('employee')->user();
        
        // Ensure the form belongs to the employee's company
        if ($form->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $form->load(['reports', 'formFields', 'department']);
        
        return Inertia::render('Companies/Forms/Show', [
            'form' => $form,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        $employee = Auth::guard('employee')->user();
        
        // Ensure the form belongs to the employee's company
        if ($form->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get all departments for the company to populate the dropdown
        $departments = \App\Models\Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        $form->load('department');
        
        return Inertia::render('Companies/Forms/Edit', [
            'form' => $form,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        $employee = Auth::guard('employee')->user();
        
        // Ensure the form belongs to the employee's company
        if ($form->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // If department is specified, ensure it belongs to the employee's company
        if (!empty($validated['department_id'])) {
            $department = \App\Models\Department::find($validated['department_id']);
            if (!$department || $department->company_id !== $employee->company_id) {
                return back()->withErrors(['department_id' => 'Invalid department selected.']);
            }
        }

        // Ensure name is unique within the company (excluding current form)
        $exists = Form::where('company_id', $employee->company_id)
            ->where('name', $validated['name'])
            ->where('id', '!=', $form->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A form with this name already exists.']);
        }

        $form->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'] ?? null,
        ]);
        $form->load(['reports', 'formFields', 'department']);

        return back()->with('success', 'Form updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        $employee = Auth::guard('employee')->user();
        
        // Ensure the form belongs to the employee's company
        if ($form->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $form->delete();

        return redirect()->route('companies.forms.index')
            ->with('success', 'Form deleted successfully');
    }

    /**
     * Display the form template bank for adding templates to company forms.
     */
    public function templateBank()
    {
        $employee = Auth::guard('employee')->user();

        $templates = FormTemplate::with('formTemplateFields')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $departments = \App\Models\Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Companies/Forms/TemplateBank', [
            'templates' => $templates,
            'departments' => $departments,
        ]);
    }

    /**
     * Add a form from template to the company's form list.
     */
    public function addFromTemplate(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $validated = $request->validate([
            'form_template_id' => 'required|exists:form_templates,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $template = FormTemplate::with('formTemplateFields')->findOrFail($validated['form_template_id']);

        // If department is specified, ensure it belongs to the employee's company
        if (!empty($validated['department_id'])) {
            $department = \App\Models\Department::find($validated['department_id']);
            if (!$department || $department->company_id !== $employee->company_id) {
                return back()->withErrors(['department_id' => 'Invalid department selected.']);
            }
        }

        // Create the form
        $form = Form::create([
            'company_id' => $employee->company_id,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $template->name,
        ]);

        // Copy template fields to form fields
        foreach ($template->formTemplateFields as $order => $templateField) {
            FormField::create([
                'form_id' => $form->id,
                'field_type' => $templateField->field_type,
                'label' => $templateField->label,
                'name' => $templateField->name,
                'placeholder' => $templateField->placeholder,
                'required' => $templateField->required,
                'options' => $templateField->options,
                'order' => $order,
            ]);
        }

        return redirect()->route('companies.forms.index')
            ->with('success', 'Form "' . $template->name . '" has been added from template.');
    }

    /**
     * Download Excel template for form fields.
     */
    public function downloadTemplate()
    {
        return Excel::download(new FormFieldsTemplateExport(), 'form_fields_template.xlsx');
    }

    /**
     * Import form fields from Excel file.
     */
    public function importFields(Request $request, Form $form)
    {
        $employee = Auth::guard('employee')->user();
        
        // Ensure the form belongs to the employee's company
        if ($form->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new FormFieldsImport($form), $request->file('file'));

            return back()->with('success', 'Form fields imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return back()->withErrors(['import' => $errors])->with('error', 'Some rows failed to import. Please check the errors below.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import file: ' . $e->getMessage());
        }
    }
}

