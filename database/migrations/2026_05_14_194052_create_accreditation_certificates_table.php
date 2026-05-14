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
        Schema::create('accreditation_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_decision_id')->constrained('final_decisions')->cascadeOnDelete();
            $table->uuid('certificate_number')->unique();
            $table->json('certificate_data');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_certificates');
    }
};
