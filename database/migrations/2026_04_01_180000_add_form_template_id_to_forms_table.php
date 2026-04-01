<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            // Tracks which FormTemplate this form was created from so we can
            // find-or-create a form per template+department when attaching to reports.
            $table->foreignId('form_template_id')
                  ->nullable()
                  ->after('department_id')
                  ->constrained()
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\FormTemplate::class);
            $table->dropColumn('form_template_id');
        });
    }
};
