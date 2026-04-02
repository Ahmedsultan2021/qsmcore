<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('form_theme_form_template', function (Blueprint $table) {
            $table->foreignId('form_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_theme_id')->constrained('form_themes')->cascadeOnDelete();
            $table->primary(['form_theme_id', 'form_template_id']);
        });

        Schema::table('form_templates', function (Blueprint $table) {
            $table->string('library_key')->nullable()->after('name');
        });

        $now = now();
        DB::table('form_themes')->insert([
            ['slug' => 'safety', 'name' => 'Safety', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'quality', 'name' => 'Quality', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $safetyId = DB::table('form_themes')->where('slug', 'safety')->value('id');
        $qualityId = DB::table('form_themes')->where('slug', 'quality')->value('id');

        foreach (DB::table('form_templates')->select('id', 'category')->cursor() as $row) {
            $slug = Str::slug($row->category);

            DB::table('form_templates')->where('id', $row->id)->update(['library_key' => $slug]);

            $lc = strtolower($row->category);
            if (str_contains($lc, 'safety')) {
                DB::table('form_theme_form_template')->updateOrInsert(
                    ['form_template_id' => $row->id, 'form_theme_id' => $safetyId]
                );
            }
            if (str_contains($lc, 'quality')) {
                DB::table('form_theme_form_template')->updateOrInsert(
                    ['form_template_id' => $row->id, 'form_theme_id' => $qualityId]
                );
            }
        }

        Schema::table('form_templates', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('form_templates', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
        });

        foreach (DB::table('form_templates')->select('id', 'library_key')->cursor() as $row) {
            $label = $row->library_key
                ? Str::title(str_replace('-', ' ', $row->library_key))
                : 'General';
            DB::table('form_templates')->where('id', $row->id)->update(['category' => $label]);
        }

        Schema::table('form_templates', function (Blueprint $table) {
            $table->dropColumn('library_key');
        });

        Schema::dropIfExists('form_theme_form_template');
        Schema::dropIfExists('form_themes');
    }
};
