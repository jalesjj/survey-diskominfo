<?php
// database/migrations/2026_06_13_000002_update_survey_questions_add_criteria_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            // Tambah kolom criteria_id sebagai foreign key (nullable)
            $table->foreignId('criteria_id')->nullable()->after('enable_saw')->constrained('criterias')->nullOnDelete();
        });

        // Hapus kolom lama criteria inline (data lama di-reset sesuai kesepakatan)
        Schema::table('survey_questions', function (Blueprint $table) {
            // Hapus index lama dulu sebelum drop kolom
            $table->dropIndex(['enable_saw', 'criteria_name']);
            $table->dropColumn(['criteria_name', 'criteria_weight', 'criteria_type']);
        });
    }

    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropForeign(['criteria_id']);
            $table->dropColumn('criteria_id');

            // Kembalikan kolom lama
            $table->string('criteria_name')->nullable()->after('enable_saw');
            $table->decimal('criteria_weight', 5, 3)->nullable()->after('criteria_name');
            $table->enum('criteria_type', ['benefit', 'cost'])->nullable()->after('criteria_weight');
            $table->index(['enable_saw', 'criteria_name']);
        });
    }
};