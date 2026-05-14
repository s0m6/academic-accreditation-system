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
        Schema::create('final_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_request_id')->constrained('accreditation_requests')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->enum('decision_type', [
                'approved_achieved',         // محقق — 3 سنوات
                'approved_with_mastery',     // محقق بإتقان — 4 سنوات
                'approved_with_excellence',  // محقق بتميز — 5 سنوات
                'rejected_partial',          // محقق جزئياً — مهلة سنة
                'rejected_not_achieved',     // غير محقق — مهلة سنتين
            ]);
            $table->text('notes')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_decisions');
    }
};
