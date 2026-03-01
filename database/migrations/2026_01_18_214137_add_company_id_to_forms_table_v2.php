<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if column already exists and drop it if it does (from failed migration)
        if (Schema::hasColumn('forms', 'company_id')) {
            // Try to drop the foreign key if it exists
            try {
                Schema::table('forms', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop the column
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
        
        // Now add the column properly as nullable
        Schema::table('forms', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
