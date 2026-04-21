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
        Schema::table('committee_members', function (Blueprint $table) {
            $table->enum('member_status', [
                'pending_invite',
                'pending_uni',
                'declined_by_member',
                'declined_by_uni',
                'accepted',
                'canceled',
            ])->default('pending_invite')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_members', function (Blueprint $table) {
            $table->enum('member_status', [
                'pending_invite',
                'pending_uni',
                'declined_by_member',
                'declined_by_uni',
                'accepted',
            ])->default('pending_invite')->change();
        });
    }
};
