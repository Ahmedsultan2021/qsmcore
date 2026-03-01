<?php

namespace App\Imports;

use App\Models\FormField;
use App\Models\Form;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class FormFieldsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $form;
    protected $maxOrder;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->maxOrder = FormField::where('form_id', $form->id)->max('order') ?? -1;
    }

    public function model(array $row)
    {
        // Normalize column names (handle different formats)
        $fieldType = $this->getValue($row, ['field_type', 'fieldtype', 'type', 'field-type']);
        $label = $this->getValue($row, ['label', 'question', 'question_label', 'question-label']);
        $fieldName = $this->getValue($row, ['field_name', 'fieldname', 'name', 'field-name']);
        $placeholder = $this->getValue($row, ['placeholder', 'hint', 'help_text', 'help-text']);
        $required = $this->getValue($row, ['required', 'is_required', 'mandatory', 'is-required']);
        $options = $this->getValue($row, ['options', 'option', 'choices']);

        // Skip instruction rows (rows that don't have valid field types)
        $validFieldTypes = ['text', 'textarea', 'email', 'number', 'date', 'select', 'radio', 'checkbox', 'file', 'signature'];
        if (!empty($fieldType) && !in_array(strtolower(trim($fieldType)), $validFieldTypes)) {
            return null;
        }

        // Skip if essential fields are missing
        if (empty($fieldType) || empty($label)) {
            return null;
        }

        // Generate field name if not provided
        if (empty($fieldName)) {
            $fieldName = Str::slug($label, '_');
        }

        // Ensure field name is valid (lowercase, underscores only)
        $fieldName = strtolower(preg_replace('/[^a-z0-9_]/', '_', $fieldName));
        $fieldName = preg_replace('/^_+|_+$/', '', $fieldName);

        // Check if field name already exists
        $existingField = FormField::where('form_id', $this->form->id)
            ->where('name', $fieldName)
            ->first();

        if ($existingField) {
            // Append number to make it unique
            $counter = 1;
            $originalName = $fieldName;
            while (FormField::where('form_id', $this->form->id)
                ->where('name', $fieldName)
                ->exists()) {
                $fieldName = $originalName . '_' . $counter;
                $counter++;
            }
        }

        // Process required field
        $isRequired = false;
        if (!empty($required)) {
            $required = strtolower(trim($required));
            $isRequired = in_array($required, ['1', 'yes', 'true', 'y', 'required']);
        }

        // Process options for select/radio/checkbox
        $optionsArray = [];
        if (!empty($options) && in_array($fieldType, ['select', 'radio', 'checkbox'])) {
            $optionsArray = array_map('trim', explode(',', $options));
            $optionsArray = array_filter($optionsArray); // Remove empty values
        }

        $this->maxOrder++;

        return new FormField([
            'form_id' => $this->form->id,
            'field_type' => $fieldType,
            'label' => $label,
            'name' => $fieldName,
            'placeholder' => $placeholder ?: null,
            'required' => $isRequired,
            'options' => !empty($optionsArray) ? $optionsArray : null,
            'order' => $this->maxOrder,
        ]);
    }

    public function rules(): array
    {
        // Validation is handled in the model method since WithHeadingRow uses different column names
        return [];
    }

    public function customValidationMessages(): array
    {
        return [];
    }

    /**
     * Get value from row using multiple possible column names
     */
    protected function getValue(array $row, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            // Try exact match
            if (isset($row[$key])) {
                return $row[$key];
            }

            // Try case-insensitive match
            foreach ($row as $rowKey => $value) {
                if (strtolower($rowKey) === strtolower($key)) {
                    return $value;
                }
            }

            // Try with spaces replaced by underscores
            $keyWithUnderscore = str_replace(' ', '_', $key);
            if (isset($row[$keyWithUnderscore])) {
                return $row[$keyWithUnderscore];
            }
        }

        return null;
    }
}

