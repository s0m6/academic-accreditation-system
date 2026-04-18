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
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // College belongs to a university and a city
            $table->foreignId('university_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnDelete();

            // College dean's information
            $table->string('dean_name');
            $table->string('dean_email');
            $table->string('dean_mobile');
            $table->string('dean_phone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
