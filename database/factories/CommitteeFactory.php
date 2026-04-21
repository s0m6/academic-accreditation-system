<?php

namespace Database\Factories;

use App\Models\AccreditationRequest;
use App\Models\Committee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Committee>
 */
class CommitteeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'accreditation_request_id' => AccreditationRequest::factory(),
            'status' => 'forming',
            'chair_evaluator_id' => null,
        ];
    }
}
