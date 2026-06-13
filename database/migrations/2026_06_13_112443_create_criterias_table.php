<?php
// database/migrations/2026_06_13_000001_create_criterias_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criterias', function (Blueprint $table) {
            $table->id();
            $table->string('criteria_name');
            $table->decimal('criteria_weight', 5, 3);
            $table->enum('criteria_type', ['benefit', 'cost']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criterias');
    }
};