<?php

use App\Models\AccreditationRequest;
use App\Models\City;
use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Evaluator;
use App\Models\Program;
use App\Models\University;
use App\Models\User;

it('displays the contacts button and modal with correct members data', function () {
    // 1. Create a city (since EvaluatorFactory requires it)
    $city = City::create(['city_name' => 'صنعاء']);

    // 2. Create the university
    $university = University::factory()->create();

    // 3. Create the program coordinator user
    $programCoordinator = User::factory()->create([
        'name' => 'أحمد منسق البرنامج',
        'email' => 'program_coord@test.com',
        'role' => 'program_coordinator',
    ]);

    // 4. Create the council coordinator user
    $councilCoordinator = User::factory()->create([
        'name' => 'خالد منسق المجلس',
        'email' => 'council_coord@test.com',
        'role' => 'council_coordinator',
    ]);

    // 5. Create a program
    $program = Program::factory()->create();

    // 6. Create the Accreditation Request
    $accreditationRequest = AccreditationRequest::create([
        'program_id' => $program->id,
        'current_stage' => 'stage_four',
        'council_coord_id' => $councilCoordinator->id,
        'program_coord_id' => $programCoordinator->id,
        'request_status' => 'Active',
    ]);

    // 7. Create evaluators and committee
    $chairUser = User::factory()->create([
        'name' => 'عمر رئيس اللجنة',
        'email' => 'chair@test.com',
        'role' => 'evaluator',
    ]);
    $chairEvaluator = Evaluator::factory()->create([
        'user_id' => $chairUser->id,
        'city_id' => $city->id,
    ]);

    $memberUser = User::factory()->create([
        'name' => 'سارة عضو اللجنة',
        'email' => 'member@test.com',
        'role' => 'evaluator',
    ]);
    $memberEvaluator = Evaluator::factory()->create([
        'user_id' => $memberUser->id,
        'city_id' => $city->id,
    ]);

    // Create the committee
    $committee = Committee::create([
        'accreditation_request_id' => $accreditationRequest->id,
        'status' => 'approved',
        'chair_evaluator_id' => $chairEvaluator->id,
    ]);

    // Create active committee member links
    CommitteeMember::create([
        'committee_id' => $committee->id,
        'evaluator_id' => $chairEvaluator->id,
        'member_status' => 'accepted',
        'is_active' => true,
    ]);

    CommitteeMember::create([
        'committee_id' => $committee->id,
        'evaluator_id' => $memberEvaluator->id,
        'member_status' => 'accepted',
        'is_active' => true,
    ]);

    // 8. Act as the program coordinator to view the request page
    $response = $this->actingAs($programCoordinator)
        ->get(route('requests.show', $accreditationRequest));

    // 9. Assertions
    $response->assertStatus(200);

    // Verify contacts button exists in response
    $response->assertSee('id="btn-request-contacts"', false);

    // Verify program coordinator info
    $response->assertSee('أحمد منسق البرنامج');
    $response->assertSee('program_coord@test.com');

    // Verify council coordinator info
    $response->assertSee('خالد منسق المجلس');
    $response->assertSee('council_coord@test.com');

    // Verify committee evaluators and their designations
    $response->assertSee('عمر رئيس اللجنة');
    $response->assertSee('chair@test.com');
    $response->assertSee('رئيس اللجنة');

    $response->assertSee('سارة عضو اللجنة');
    $response->assertSee('member@test.com');
    $response->assertSee('عضو');
});
