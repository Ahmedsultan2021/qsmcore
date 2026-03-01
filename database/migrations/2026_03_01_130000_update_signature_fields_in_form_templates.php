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
        if (Schema::hasTable('form_template_fields')) {
            DB::table('form_template_fields')
                ->whereIn('name', [
                    'auditor_signature',
                    'auditee_signature',
                    'reviewer_signature',
                    'ame_signature',
                ])
                ->where('field_type', 'text')
                ->update(['field_type' => 'signature']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('form_template_fields')) {
            DB::table('form_template_fields')
                ->whereIn('name', [
                    'auditor_signature',
                    'auditee_signature',
                    'reviewer_signature',
                    'ame_signature',
                ])
                ->where('field_type', 'signature')
                ->update(['field_type' => 'text']);
        }
    }
};
