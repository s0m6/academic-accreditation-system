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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // Department belongs to a college
            $table->foreignId('college_id')
                ->constrained('colleges')
                ->cascadeOnDelete();

            // Department head's information
            $table->string('head_name');
            $table->string('head_email');
            $table->string('head_mobile');
            $table->string('head_phone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};