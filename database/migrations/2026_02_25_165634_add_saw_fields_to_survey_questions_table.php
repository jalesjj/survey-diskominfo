<?php
// database/migrations/2026_02_25_165634_add_saw_fields_to_survey_questions_table.php
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
        Schema::table('survey_questions', function (Blueprint $table) {
            // Fields untuk SAW (Simple Additive Weighting)
            $table->boolean('enable_saw')->default(false)->after('is_active');
            $table->string('criteria_name')->nullable()->after('enable_saw');
            $table->decimal('criteria_weight', 5, 3)->nullable()->after('criteria_name');
            $table->enum('criteria_type', ['benefit', 'cost'])->nullable()->after('criteria_weight');
            
            // Index untuk performance
            $table->index(['enable_saw', 'criteria_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropIndex(['enable_saw', 'criteria_name']);
            $table->dropColumn(['enable_saw', 'criteria_name', 'criteria_weight', 'criteria_type']);
        });
    }
};
