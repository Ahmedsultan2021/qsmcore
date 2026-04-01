<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('created_by')->constrained('employees')->onDelete('cascade');

            $table->string('title');
            $table->string('type')->default('corrective'); // corrective | preventive
            $table->string('status')->default('open'); // open | in_progress | closed
            $table->string('priority')->default('medium'); // low | medium | high

            $table->text('description')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();

            $table->date('due_date')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'department_id']);
            $table->index('assigned_to');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capas');
    }
};

