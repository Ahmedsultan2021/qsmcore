<?php

use App\Models\Department;
use App\Models\FormTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_form_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_template_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['department_id', 'form_template_id']);
        });

        if (! Schema::hasColumn('departments', 'form_category')) {
            return;
        }

        foreach (Department::query()->with('company.sector')->cursor() as $department) {
            $sector = $department->company?->sector;
            if (! $sector) {
                continue;
            }

            $query = FormTemplate::query()
                ->whereHas('sectors', fn ($q) => $q->where('sectors.id', $sector->id));

            if ($department->form_category) {
                $query->where('form_templates.category', $department->form_category);
            }

            $ids = $query->pluck('id')->all();
            if ($ids !== []) {
                $department->formTemplates()->sync($ids);
            }
        }

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('form_category');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('form_category')->nullable()->after('description');
        });

        Schema::dropIfExists('department_form_template');
    }
};
