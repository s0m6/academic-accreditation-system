<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:approve-pending-invitations')]
#[Description('Approve all active pending committee member invitations')]
class ApprovePendingInvitations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = DB::table('committee_members')
            ->where('is_active', 1)
            ->where('member_status', 'pending_invite')
            ->update([
                'member_status' => 'accepted',
                'member_responded_at' => now(),
                'university_responded_at' => now(),
            ]);

        $this->info("✅ تم قبول {$count} دعوة بنجاح.");

        return self::SUCCESS;
    }
}
