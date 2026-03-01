<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyFormFieldController extends Controller
{
    /**
     * Store a newly created form field.
     */
    public function store(Request $request, Form $form)
    {
        $validated = $request->validate([
            'field_type' => 'required|string|in:text,textarea,select,checkbox,radio,date,number,email,file,signature',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-z_][a-z0-9_]*$/',
            'placeholder' => 'nullable|string|max:255',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Check if field name is unique for this form
        $existingField = FormField::where('form_id', $form->id)
            ->where('name', $validated['name'])
            ->first();
        
        if ($existingField) {
            return back()->withErrors(['name' => 'Field name must be unique for this form.']);
        }

        // Get max order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = FormField::where('form_id', $form->id)->max('order') ?? -1;
            $validated['order'] = $maxOrder + 1;
        }

        $validated['form_id'] = $form->id;
        $validated['options'] = $validated['options'] ?? null;

        FormField::create($validated);

        return back()->with('success', 'Form field added successfully');
    }

    /**
     * Update the specified form field.
     */
    public function update(Request $request, Form $form, FormField $formField)
    {
        // Ensure field belongs to form
        if ($formField->form_id !== $form->id) {
            abort(404);
        }

        $validated = $request->validate([
            'field_type' => 'required|string|in:text,textarea,select,checkbox,radio,date,number,email,file,signature',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-z_][a-z0-9_]*$/',
            'placeholder' => 'nullable|string|max:255',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Check if field name is unique for this form (excluding current field)
        $existingField = FormField::where('form_id', $form->id)
            ->where('name', $validated['name'])
            ->where('id', '!=', $formField->id)
            ->first();
        
        if ($existingField) {
            return back()->withErrors(['name' => 'Field name must be unique for this form.']);
        }

        $validated['options'] = $validated['options'] ?? null;

        $formField->update($validated);

        return back()->with('success', 'Form field updated successfully');
    }

    /**
     * Remove the specified form field.
     */
    public function destroy(Form $form, FormField $formField)
    {
        // Ensure field belongs to form
        if ($formField->form_id !== $form->id) {
            abort(404);
        }

        $formField->delete();

        return back()->with('success', 'Form field deleted successfully');
    }

    /**
     * Update field order (for drag and drop sorting).
     */
    public function updateOrder(Request $request, Form $form)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:form_fields,id',
            'fields.*.order' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $form) {
            foreach ($validated['fields'] as $fieldData) {
                FormField::where('id', $fieldData['id'])
                    ->where('form_id', $form->id)
                    ->update(['order' => $fieldData['order']]);
            }
        });

        return response()->json(['success' => true]);
    }
}

