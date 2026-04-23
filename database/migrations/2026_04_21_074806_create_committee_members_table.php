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
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained()->cascadeOnDelete();
            $table->enum('member_status', [
                'pending_invite',
                'pending_uni',
                'declined_by_member',
                'declined_by_uni',
                'accepted',
                'canceled',
            ])->default('pending_invite');
            $table->timestamp('invite_sent_at')->nullable();
            $table->timestamp('member_responded_at')->nullable();
            $table->timestamp('university_responded_at')->nullable();
            $table->json('reject_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
