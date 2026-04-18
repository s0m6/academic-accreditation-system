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
        Schema::create('indicator_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained('form_submissions')->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('indicators')->onDelete('cascade');
            $table->tinyInteger('score')->nullable();

            $table->unique(['form_submission_id', 'indicator_id'], 'sub_ind_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_evaluations');
    }
};
