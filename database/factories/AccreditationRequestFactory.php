<?php

namespace Database\Factories;

use App\Models\AccreditationRequest;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccreditationRequest>
 */
class AccreditationRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'program_coord_id' => User::factory()->create(['role' => 'program_coordinator', 'email_verified_at' => now()])->id,
            'current_stage' => 'stage_four',
            'request_status' => 'Active',
            'council_coord_id' => null,
        ];
    }
}
