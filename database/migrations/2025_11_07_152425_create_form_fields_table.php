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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->string('field_type'); // text, textarea, select, checkbox, radio, date, number, email, file
            $table->string('label');
            $table->string('name'); // field name (snake_case)
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // For select, radio, checkbox options
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['form_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
