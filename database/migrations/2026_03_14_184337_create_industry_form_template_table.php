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
        Schema::create('industry_form_template', function (Blueprint $table) {
            $table->foreignId('industry_id')->constrained()->onDelete('cascade');
            $table->foreignId('form_template_id')->constrained()->onDelete('cascade');
            $table->primary(['industry_id', 'form_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_form_template');
    }
};
