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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('risk_owner_id')->nullable()->constrained('employees')->onDelete('set null');
            
            // Risk Identification
            $table->string('risk_code')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('category'); // operational, financial, strategic, compliance, reputational, etc.
            
            // Risk Assessment
            $table->integer('likelihood')->default(1); // 1-5 scale
            $table->integer('impact')->default(1); // 1-5 scale
            $table->integer('risk_score')->virtualAs('likelihood * impact'); // Calculated field
            
            // Risk Treatment
            $table->text('current_controls')->nullable();
            $table->string('treatment_strategy')->nullable(); // accept, mitigate, transfer, avoid
            $table->text('action_plan')->nullable();
            $table->date('target_date')->nullable();
            $table->string('status')->default('identified'); // identified, assessed, in_progress, mitigated, closed
            
            // Additional Information
            $table->date('date_identified')->nullable();
            $table->date('last_review_date')->nullable();
            $table->integer('residual_likelihood')->nullable(); // After mitigation
            $table->integer('residual_impact')->nullable(); // After mitigation
            
            $table->timestamps();
            
            // Indexes
            $table->index('department_id');
            $table->index('status');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
