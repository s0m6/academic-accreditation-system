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
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_request_id')->constrained('accreditation_requests')->cascadeOnDelete();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete();
            $table->enum('status', ['draft', 'submitted_to_council', 'pending_uni', 'approved_uni', 'rejected_uni'])->default('draft');
            $table->json('schedule_data')->nullable();
            $table->string('council_pdf_path')->nullable();
            $table->json('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('council_processed_at')->nullable();
            $table->timestamp('university_responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_schedules');
    }
};
