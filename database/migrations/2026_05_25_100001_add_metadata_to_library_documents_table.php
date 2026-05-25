<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('library_documents', function (Blueprint $table) {
            $table->foreignId('library_category_id')->nullable()->after('uploaded_by')->constrained()->nullOnDelete();
            $table->foreignId('owner_employee_id')->nullable()->after('library_category_id')->constrained('employees')->nullOnDelete();
            $table->string('document_code')->nullable()->after('title');
            $table->string('version_label')->nullable()->after('document_code');
            $table->date('effective_date')->nullable()->after('description');
            $table->string('status', 32)->default('effective')->after('effective_date');
        });
    }

    public function down(): void
    {
        Schema::table('library_documents', function (Blueprint $table) {
            $table->dropForeign(['library_category_id']);
            $table->dropForeign(['owner_employee_id']);
            $table->dropColumn([
                'library_category_id',
                'owner_employee_id',
                'document_code',
                'version_label',
                'effective_date',
                'status',
            ]);
        });
    }
};
