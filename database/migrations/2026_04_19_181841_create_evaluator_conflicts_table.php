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
        Schema::create('evaluator_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('conflict_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluator_conflicts');
    }
};
