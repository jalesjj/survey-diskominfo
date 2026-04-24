<?php
// database/migrations/2026_04_23_000001_create_survey_periods_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id');
            $table->string('period_name'); // "Tahun 2025", "Tahun 2026"
            $table->integer('year'); // 2025, 2026
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign key (kalau ada tabel surveys)
            // $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            
            // Index untuk query cepat
            $table->index(['survey_id', 'status']);
            $table->index('year');
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_periods');
    }
};