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
        Schema::create('committee_reports', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('accreditation_request_id')->constrained('accreditation_requests')->cascadeOnDelete();
            $table->enum('status', [
                'draft',
                'under_review',
                'returned_for_edit',
                'submitted_to_council',
                'council_responded',
                'uni_responded',
                'final_under_review',
                'completed',
            ])->default('draft');
            $table->tinyInteger('current_iteration')->default(0);
            $table->json('form5_data')->nullable();
            $table->json('form6_initial_data')->nullable();
            $table->timestamp('stage6_submitted_at')->nullable();
            $table->string('form8_pdf_path')->nullable();
            $table->timestamp('council_responded_at')->nullable();
            $table->json('form9_data')->nullable();
            $table->string('form9_pdf_path')->nullable();
            $table->timestamp('uni_responded_at')->nullable();
            $table->json('form6_final_data')->nullable();
            $table->timestamp('stage8_submitted_at')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_reports');
    }
};