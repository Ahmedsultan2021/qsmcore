<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Industry;
use App\Models\Sector;

function createCompanyFormContext(): array
{
    $industry = Industry::create(['name' => 'Test Industry']);
    $sector = Sector::create([
        'industry_id' => $industry->id,
        'name' => 'Test Sector',
    ]);
    $company = Company::create([
        'sector_id' => $sector->id,
        'name' => 'Test Company',
    ]);
    $employee = Employee::create([
        'company_id' => $company->id,
        'fname' => 'Test',
        'lname' => 'User',
        'email' => 'test-' . uniqid() . '@example.com',
        'password' => 'password',
    ]);
    $form = Form::create([
        'company_id' => $company->id,
        'name' => 'Test Form',
    ]);

    return compact('employee', 'form');
}

it('reorders form fields with an inertia-compatible redirect response', function () {
    ['employee' => $employee, 'form' => $form] = createCompanyFormContext();

    $fieldA = FormField::create([
        'form_id' => $form->id,
        'field_type' => 'text',
        'label' => 'Field A',
        'name' => 'field_a',
        'order' => 0,
    ]);
    $fieldB = FormField::create([
        'form_id' => $form->id,
        'field_type' => 'text',
        'label' => 'Field B',
        'name' => 'field_b',
        'order' => 1,
    ]);

    $response = $this->actingAs($employee, 'employee')
        ->from(route('companies.forms.show', $form))
        ->withHeaders([
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->post(route('companies.forms.fields.update-order', $form), [
            'fields' => [
                ['id' => $fieldA->id, 'order' => 1],
                ['id' => $fieldB->id, 'order' => 0],
            ],
        ]);

    $response->assertRedirect();
    expect($response->headers->get('Content-Type'))->not->toContain('application/json');

    expect(FormField::find($fieldA->id)->order)->toBe(1);
    expect(FormField::find($fieldB->id)->order)->toBe(0);
});

it('stores form fields with long placeholder text', function () {
    ['employee' => $employee, 'form' => $form] = createCompanyFormContext();

    $placeholder = str_repeat('A', 250);

    $response = $this->actingAs($employee, 'employee')
        ->from(route('companies.forms.show', $form))
        ->post(route('companies.forms.fields.store', $form), [
            'field_type' => 'signature',
            'label' => 'Just Culture Statement',
            'name' => 'just_culture_statement',
            'placeholder' => $placeholder,
            'required' => false,
            'options' => [],
            'order' => 0,
        ]);

    $response->assertRedirect();

    $field = FormField::where('form_id', $form->id)
        ->where('name', 'just_culture_statement')
        ->first();

    expect($field)->not->toBeNull();
    expect($field->placeholder)->toBe($placeholder);
});

it('updates form fields with long placeholder text', function () {
    ['employee' => $employee, 'form' => $form] = createCompanyFormContext();

    $field = FormField::create([
        'form_id' => $form->id,
        'field_type' => 'signature',
        'label' => 'Just Culture Statement',
        'name' => 'just_culture_statement',
        'placeholder' => 'Short text',
        'order' => 0,
    ]);

    $placeholder = str_repeat('B', 250);

    $response = $this->actingAs($employee, 'employee')
        ->from(route('companies.forms.show', $form))
        ->put(route('companies.forms.fields.update', [$form, $field]), [
            'field_type' => 'signature',
            'label' => 'Just Culture Statement',
            'name' => 'just_culture_statement',
            'placeholder' => $placeholder,
            'required' => false,
            'options' => [],
            'order' => 0,
        ]);

    $response->assertRedirect();
    expect($field->fresh()->placeholder)->toBe($placeholder);
});
