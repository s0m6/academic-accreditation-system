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
        Schema::create('report_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('committee_reports')->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('indicators')->onDelete('cascade');
            $table->tinyInteger('score')->nullable();
            $table->enum('score_type', ['Initial', 'final']);
            $table->timestamps();

            $table->unique(['report_id', 'indicator_id', 'score_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_scores');
    }
};
