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
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'under_review',
                'returned_for_edit',
                'submitted_to_council',
                'council_responded',
                'uni_responded',
                'final_under_review',
                'completed'
            ])->default('draft')->change();

            $table->tinyInteger('current_iteration')->nullable();
            $table->json('form5_data')->nullable();
            $table->json('form6_initial_data')->nullable();
            $table->timestamp('stage6_submitted_at')->nullable();
            $table->string('form8_pdf_path')->nullable();
            $table->timestamp('council_responded_at')->nullable();
            $table->json('form9_data')->nullable();
            $table->string('form9_pdf_path')->nullable();
            $table->timestamp('uni_responded_at')->nullable();
            $table->json('form6_final_data')->nullable();
            $table->string('form10_pdf_path')->nullable();
            $table->timestamp('stage8_submitted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_reports', function (Blueprint $table) {
            $table->dropColumn([
                'current_iteration',
                'form5_data',
                'form6_initial_data',
                'stage6_submitted_at',
                'form8_pdf_path',
                'council_responded_at',
                'form9_data',
                'form9_pdf_path',
                'uni_responded_at',
                'form6_final_data',
                'form10_pdf_path',
                'stage8_submitted_at'
            ]);

            $table->enum('status', ['draft', 'submitted', 'returned', 'approved'])->change();
        });
    }
};
