<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Evaluator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommitteeMember>
 */
class CommitteeMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'committee_id' => Committee::factory(),
            'evaluator_id' => Evaluator::factory(),
            'member_status' => 'pending_invite',
            'invite_sent_at' => now(),
            'member_responded_at' => null,
            'university_responded_at' => null,
            'reject_reason' => null,
            'is_active' => true,
        ];
    }
}
