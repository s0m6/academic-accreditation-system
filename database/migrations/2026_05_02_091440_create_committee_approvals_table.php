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
        Schema::create('committee_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('committee_reports')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('evaluators')->onDelete('cascade');
            $table->tinyInteger('iteration_number');
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->enum('review_round', ['stage6', 'stage8']);
            $table->string('reject_reason')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_approvals');
    }
};
