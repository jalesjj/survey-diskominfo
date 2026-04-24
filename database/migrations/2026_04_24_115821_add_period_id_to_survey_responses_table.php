<?php
// database/migrations/2026_04_23_000002_add_period_id_to_survey_responses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable()->after('survey_id');
            
            // Foreign key
            $table->foreign('period_id')
                  ->references('id')
                  ->on('survey_periods')
                  ->onDelete('set null');
            
            // Index untuk query cepat
            $table->index('period_id');
        });
    }

    public function down()
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });
    }
};