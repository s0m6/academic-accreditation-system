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
        Schema::create('report_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('committee_reports')->onDelete('cascade');
            $table->foreignId('approval_id')->nullable()->constrained('committee_approvals')->onDelete('cascade');
            $table->enum('form_type', ['form_5', 'form_6_initial', 'form_6_final', 'form_10']);
            $table->string('signature_path');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_signatures');
    }
};
