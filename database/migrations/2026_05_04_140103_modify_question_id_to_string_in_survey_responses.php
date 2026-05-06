<?php
// database/migrations/2026_05_04_140000_modify_question_id_to_string_in_survey_responses.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Gunakan raw SQL untuk lebih eksplisit
        
        // Step 1: Drop foreign key constraint jika ada
        try {
            DB::statement('ALTER TABLE survey_responses DROP FOREIGN KEY survey_responses_question_id_foreign');
        } catch (\Exception $e) {
            // Foreign key mungkin sudah tidak ada, skip error
            echo "Foreign key already dropped or doesn't exist\n";
        }
        
        // Step 2: Drop index jika ada
        try {
            DB::statement('ALTER TABLE survey_responses DROP INDEX survey_responses_question_id_foreign');
        } catch (\Exception $e) {
            // Index mungkin sudah tidak ada, skip error
            echo "Index already dropped or doesn't exist\n";
        }
        
        // Step 3: Modify column type dari BIGINT UNSIGNED ke VARCHAR(100)
        DB::statement('ALTER TABLE survey_responses MODIFY COLUMN question_id VARCHAR(100) NOT NULL');
        
        echo "Successfully modified question_id to VARCHAR(100)\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ubah kembali ke BIGINT UNSIGNED
        DB::statement('ALTER TABLE survey_responses MODIFY COLUMN question_id BIGINT UNSIGNED NOT NULL');
        
        // Restore foreign key
        DB::statement('
            ALTER TABLE survey_responses 
            ADD CONSTRAINT survey_responses_question_id_foreign 
            FOREIGN KEY (question_id) 
            REFERENCES survey_questions(id) 
            ON DELETE CASCADE
        ');
        
        echo "Successfully rolled back question_id to BIGINT UNSIGNED\n";
    }
};