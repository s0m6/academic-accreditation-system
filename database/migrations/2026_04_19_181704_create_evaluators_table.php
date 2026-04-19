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
        Schema::create('evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('general_specialty');
            $table->string('detailed_specialty');
            $table->enum('academic_rank', [
                'Professor',
                'Associate Professor',
                'Assistant Professor',
                'Lecturer',
                'Expert',
            ]);
            $table->foreignId('current_university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluators');
    }
};
