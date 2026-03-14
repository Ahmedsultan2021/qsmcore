<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Form;
use App\Models\FormResponse;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyFormSubmissionController extends Controller
{
    /**
     * Show the form for submitting responses.
     */
    public function create(Department $department, Report $report, Form $form)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Ensure report belongs to department
        if ($report->department_id !== $department->id) {
            abort(404);
        }
        
        // Ensure form is attached to report
        $report->load('forms');
        if (!$report->forms->contains($form->id)) {
            abort(404, 'Form is not attached to this report');
        }
        
        $form->load('formFields');
        
        // Check if user already submitted this form for this report
        $existingResponse = FormResponse::where('report_id', $report->id)
            ->where('form_id', $form->id)
            ->where('submitted_by', $authEmployee->id)
            ->first();
        
        return Inertia::render('Companies/FormSubmissions/Create', [
            'department' => $department,
            'report' => $report,
            'form' => $form,
            'existingResponse' => $existingResponse,
        ]);
    }

    /**
     * Store form submission.
     */
    public function store(Request $request, Department $department, Report $report, Form $form)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Ensure report belongs to department
        if ($report->department_id !== $department->id) {
            abort(404);
        }
        
        // Ensure form is attached to report
        $report->load('forms');
        if (!$report->forms->contains($form->id)) {
            abort(404, 'Form is not attached to this report');
        }
        
        $form->load('formFields');
        
        // Build validation rules from form fields
        $rules = [];
        $fieldNames = [];
        
        foreach ($form->formFields as $field) {
            $fieldName = 'responses.' . $field->name;
            $fieldNames[] = $fieldName;
            
            if ($field->required) {
                $rules[$fieldName] = 'required';
            } else {
                $rules[$fieldName] = 'nullable';
            }
            
            // Add type-specific validation
            switch ($field->field_type) {
                case 'email':
                    $rules[$fieldName] .= '|email';
                    break;
                case 'number':
                    $rules[$fieldName] .= '|numeric';
                    break;
                case 'date':
                    $rules[$fieldName] .= '|date';
                    break;
                case 'file':
                    $rules[$fieldName] .= '|file';
                    break;
                case 'signature':
                    $rules[$fieldName] .= '|string|nullable';
                    break;
            }
        }
        
        $validated = $request->validate($rules);
        
        // Check if user already submitted this form for this report
        $existingResponse = FormResponse::where('report_id', $report->id)
            ->where('form_id', $form->id)
            ->where('submitted_by', $authEmployee->id)
            ->first();
        
        if ($existingResponse) {
            // Update existing response
            $existingResponse->update([
                'responses' => $validated['responses'],
            ]);
            
            return redirect()->route('companies.departments.reports.show', ['department' => $report->department->id, 'report' => $report->id])
                ->with('success', 'Form response updated successfully');
        } else {
            // Create new response
            FormResponse::create([
                'report_id' => $report->id,
                'form_id' => $form->id,
                'submitted_by' => $authEmployee->id,
                'responses' => $validated['responses'],
            ]);
            
            return redirect()->route('companies.departments.reports.show', ['department' => $report->department->id, 'report' => $report->id])
                ->with('success', 'Form submitted successfully');
        }
    }

    /**
     * Show submitted form response.
     */
    public function show(Department $department, Report $report, Form $form)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Ensure report belongs to department
        if ($report->department_id !== $department->id) {
            abort(404);
        }
        
        // Ensure form is attached to report
        $report->load('forms');
        if (!$report->forms->contains($form->id)) {
            abort(404, 'Form is not attached to this report');
        }
        
        $form->load('formFields');
        
        $response = FormResponse::where('report_id', $report->id)
            ->where('form_id', $form->id)
            ->where('submitted_by', $authEmployee->id)
            ->with('submitter')
            ->first();
        
        if (!$response) {
            return redirect()->route('companies.departments.reports.forms.create', ['department' => $department->id, 'report' => $report->id, 'form' => $form->id]);
        }
        
        return Inertia::render('Companies/FormSubmissions/Show', [
            'department' => $department,
            'report' => $report,
            'form' => $form,
            'response' => $response,
        ]);
    }
}

