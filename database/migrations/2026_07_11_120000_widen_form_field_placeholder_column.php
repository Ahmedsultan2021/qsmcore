<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('form_fields') && Schema::hasColumn('form_fields', 'placeholder')) {
            DB::statement('ALTER TABLE form_fields MODIFY placeholder TEXT NULL');
        }

        if (Schema::hasTable('form_template_fields') && Schema::hasColumn('form_template_fields', 'placeholder')) {
            DB::statement('ALTER TABLE form_template_fields MODIFY placeholder TEXT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('form_fields') && Schema::hasColumn('form_fields', 'placeholder')) {
            DB::statement('ALTER TABLE form_fields MODIFY placeholder VARCHAR(255) NULL');
        }

        if (Schema::hasTable('form_template_fields') && Schema::hasColumn('form_template_fields', 'placeholder')) {
            DB::statement('ALTER TABLE form_template_fields MODIFY placeholder VARCHAR(255) NULL');
        }
    }
};
