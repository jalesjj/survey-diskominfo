<?php
// database/migrations/2026_04_23_000003_create_saw_calculation_results_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saw_calculation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id')->nullable();
            $table->unsignedBigInteger('period_id');
            $table->string('criteria_name');
            $table->enum('criteria_type', ['benefit', 'cost'])->default('benefit');
            $table->decimal('criteria_weight', 5, 2)->default(0); // Bobot asli
            $table->decimal('average_score', 5, 2)->default(0); // Skor rata-rata (x)
            $table->decimal('normalized_score', 8, 4)->default(0); // Normalisasi (r)
            $table->decimal('weight_normalized', 8, 4)->default(0); // Bobot ternormalisasi (w)
            $table->decimal('weighted_score', 8, 4)->default(0); // Nilai terbobot (V = w × r)
            $table->integer('total_responses')->default(0); // Jumlah responden
            $table->integer('questions_count')->default(0); // Jumlah pertanyaan dalam kriteria ini
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('period_id')
                  ->references('id')
                  ->on('survey_periods')
                  ->onDelete('cascade');
            
            // Unique constraint: 1 kriteria hanya 1 hasil per periode
            $table->unique(['period_id', 'criteria_name']);
            
            // Index untuk query cepat
            $table->index(['period_id', 'criteria_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saw_calculation_results');
    }
};