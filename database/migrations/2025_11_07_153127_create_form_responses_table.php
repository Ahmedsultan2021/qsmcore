<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('employees')->onDelete('cascade');
            $table->json('responses'); // Store all field responses as JSON
            $table->timestamps();
            
            // Ensure one response per user per form per report
            $table->unique(['report_id', 'form_id', 'submitted_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
