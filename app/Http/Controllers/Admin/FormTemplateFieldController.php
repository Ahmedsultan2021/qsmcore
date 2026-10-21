<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormTemplate;
use App\Models\FormTemplateField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormTemplateFieldController extends Controller
{
    public function store(Request $request, FormTemplate $formTemplate)
    {
        $validated = $this->validateField($request);

        $existingField = FormTemplateField::where('form_template_id', $formTemplate->id)
            ->where('name', $validated['name'])
            ->first();

        if ($existingField) {
            return back()->withErrors(['name' => 'Field name must be unique for this form.']);
        }

        if (! isset($validated['order'])) {
            $maxOrder = FormTemplateField::where('form_template_id', $formTemplate->id)->max('order') ?? -1;
            $validated['order'] = $maxOrder + 1;
        }

        $validated['form_template_id'] = $formTemplate->id;
        $validated['options'] = $validated['options'] ?? null;

        FormTemplateField::create($validated);

        return back()->with('success', 'Form field added successfully');
    }

    public function update(Request $request, FormTemplate $formTemplate, FormTemplateField $formTemplateField)
    {
        $this->ensureFieldBelongsToTemplate($formTemplate, $formTemplateField);

        $validated = $this->validateField($request);

        $existingField = FormTemplateField::where('form_template_id', $formTemplate->id)
            ->where('name', $validated['name'])
            ->where('id', '!=', $formTemplateField->id)
            ->first();

        if ($existingField) {
            return back()->withErrors(['name' => 'Field name must be unique for this form.']);
        }

        $validated['options'] = $validated['options'] ?? null;

        $formTemplateField->update($validated);

        return back()->with('success', 'Form field updated successfully');
    }

    public function destroy(FormTemplate $formTemplate, FormTemplateField $formTemplateField)
    {
        $this->ensureFieldBelongsToTemplate($formTemplate, $formTemplateField);

        $formTemplateField->delete();

        return back()->with('success', 'Form field deleted successfully');
    }

    public function updateOrder(Request $request, FormTemplate $formTemplate)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:form_template_fields,id',
            'fields.*.order' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $formTemplate) {
            foreach ($validated['fields'] as $fieldData) {
                FormTemplateField::where('id', $fieldData['id'])
                    ->where('form_template_id', $formTemplate->id)
                    ->update(['order' => $fieldData['order']]);
            }
        });

        return back();
    }

    private function validateField(Request $request): array
    {
        return $request->validate([
            'field_type' => 'required|string|in:text,textarea,select,checkbox,radio,date,number,email,file,signature',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-z_][a-z0-9_]*$/',
            'placeholder' => 'nullable|string|max:5000',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);
    }

    private function ensureFieldBelongsToTemplate(FormTemplate $formTemplate, FormTemplateField $field): void
    {
        if ((int) $field->form_template_id !== (int) $formTemplate->id) {
            abort(404);
        }
    }
}
