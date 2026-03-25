<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['government', 'private']);

            // Linking the university to the accreditation officer (from the users table)
            $table->foreignId('accreditation_officer_id')
                ->unique()
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // University president's information
            $table->string('president_name');
            $table->string('president_email');
            $table->string('president_mobile');
            $table->string('president_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};