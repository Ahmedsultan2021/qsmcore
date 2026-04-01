<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sector_form_template', function (Blueprint $table) {
            $table->foreignId('sector_id')->constrained()->onDelete('cascade');
            $table->foreignId('form_template_id')->constrained()->onDelete('cascade');
            $table->primary(['sector_id', 'form_template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sector_form_template');
    }
};
