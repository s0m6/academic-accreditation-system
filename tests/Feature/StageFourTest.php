<?php

use App\Models\AccreditationRequest;
use App\Models\City;
use App\Models\College;
use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Department;
use App\Models\Evaluator;
use App\Models\Program;
use App\Models\University;
use App\Models\User;

// ─────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────
// Helpers: build a minimal accreditation request chain
// ─────────────────────────────────────────────────────

/**
 * Create a minimal university → college → department → program → request chain
 * with a committee in 'forming' status for stage_four testing.
 */
function makeStageForRequest(array $requestOverrides = []): array
{
    $city = City::factory()->create();
    $university = University::factory()->create();
    $college = College::factory()->create(['university_id' => $university->id, 'city_id' => $city->id]);
    $dept = Department::factory()->create(['college_id' => $college->id]);
    $program = Program::factory()->create(['department_id' => $dept->id]);

    $secretariat = User::factory()->create(['role' => 'council_secretariat', 'email_verified_at' => now()]);

    $progCoord = User::factory()->create(['role' => 'program_coordinator', 'email_verified_at' => now()]);

    $request = AccreditationRequest::factory()->create(array_merge([
        'program_id' => $program->id,
        'current_stage' => 'stage_four',
        'request_status' => 'Active',
        'program_coord_id' => $progCoord->id,
    ], $requestOverrides));

    $committee = Committee::factory()->create([
        'accreditation_request_id' => $request->id,
        'status' => 'forming',
    ]);

    return compact('secretariat', 'progCoord', 'request', 'committee', 'city', 'university');
}

/**
 * Create an evaluator user with an evaluator profile.
 */
function makeEvaluator(?int $cityId = null, ?int $universityId = null): array
{
    $user = User::factory()->create(['role' => 'evaluator', 'email_verified_at' => now()]);

    $cityId = $cityId ?? City::factory()->create()->id;
    $universityId = $universityId ?? University::factory()->create()->id;

    $evaluator = Evaluator::factory()->create([
        'user_id' => $user->id,
        'city_id' => $cityId,
        'current_university_id' => $universityId,
    ]);

    return compact('user', 'evaluator');
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 1: Assign Council Coordinator
// ─────────────────────────────────────────────────────────────────────────────

describe('assignCoordinator', function () {
    it('allows secretariat to assign a council coordinator', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
        ] = makeStageForRequest();

        $coordinator = User::factory()->create(['role' => 'council_coordinator', 'email_verified_at' => now()]);

        $this->actingAs($secretariat)
            ->patch(route('requests.stage_four.assign_coordinator', $request), [
                'coordinator_id' => $coordinator->id,
            ])
            ->assertRedirect();

        expect($request->fresh()->council_coord_id)->toBe($coordinator->id);
    });

    it('rejects assignment when user is not a council_coordinator role', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
        ] = makeStageForRequest();

        $nonCoord = User::factory()->create(['role' => 'program_coordinator', 'email_verified_at' => now()]);

        $this->actingAs($secretariat)
            ->patch(route('requests.stage_four.assign_coordinator', $request), [
                'coordinator_id' => $nonCoord->id,
            ])
            ->assertRedirect();

        expect($request->fresh()->council_coord_id)->toBeNull();
    });

    it('forbids non-secretariat from assigning coordinator', function () {
        [
            'progCoord' => $progCoord,
            'request' => $request,
        ] = makeStageForRequest();

        $coordinator = User::factory()->create(['role' => 'council_coordinator', 'email_verified_at' => now()]);

        $this->actingAs($progCoord)
            ->patch(route('requests.stage_four.assign_coordinator', $request), [
                'coordinator_id' => $coordinator->id,
            ])
            ->assertForbidden();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 2: Invite Member
// ─────────────────────────────────────────────────────────────────────────────

describe('inviteMember', function () {
    it('allows secretariat to invite an evaluator', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['evaluator' => $evaluator] = makeEvaluator();

        $this->actingAs($secretariat)
            ->post(route('requests.stage_four.invite_member', $request), [
                'evaluator_id' => $evaluator->id,
            ])
            ->assertRedirect();

        expect(
            $committee->members()
                ->where('evaluator_id', $evaluator->id)
                ->where('is_active', true)
                ->where('member_status', 'pending_invite')
                ->exists()
        )->toBeTrue();
    });

    it('prevents inviting when 3 active members already exist', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        // Create 3 active members
        for ($i = 0; $i < 3; $i++) {
            ['evaluator' => $ev] = makeEvaluator();
            CommitteeMember::factory()->create([
                'committee_id' => $committee->id,
                'evaluator_id' => $ev->id,
                'is_active' => true,
                'member_status' => 'pending_invite',
                'invite_sent_at' => now(),
            ]);
        }

        ['evaluator' => $newEv] = makeEvaluator();

        $this->actingAs($secretariat)
            ->post(route('requests.stage_four.invite_member', $request), [
                'evaluator_id' => $newEv->id,
            ])
            ->assertRedirect();

        // Total active members must remain 3
        expect($committee->members()->where('is_active', true)->count())->toBe(3);
    });

    it('forbids non-secretariat from inviting evaluators', function () {
        [
            'progCoord' => $progCoord,
            'request' => $request,
        ] = makeStageForRequest();

        ['evaluator' => $evaluator] = makeEvaluator();

        $this->actingAs($progCoord)
            ->post(route('requests.stage_four.invite_member', $request), [
                'evaluator_id' => $evaluator->id,
            ])
            ->assertForbidden();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 3: Replace Member
// ─────────────────────────────────────────────────────────────────────────────

describe('replaceMember', function () {
    it('deactivates declined member and creates new invitation', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['evaluator' => $oldEv] = makeEvaluator();
        $declinedMember = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $oldEv->id,
            'is_active' => true,
            'member_status' => 'declined_by_member',
            'invite_sent_at' => now(),
            'member_responded_at' => now(),
            'reject_reason' => ['لدي ارتباطات أخرى'],
        ]);

        ['evaluator' => $newEv] = makeEvaluator();

        $this->actingAs($secretariat)
            ->patch(route('requests.stage_four.replace_member', [$request, $declinedMember]), [
                'evaluator_id' => $newEv->id,
            ])
            ->assertRedirect();

        // Old member must be deactivated
        expect($declinedMember->fresh()->is_active)->toBeFalse();

        // New active member exists for the replacement evaluator
        expect(
            $committee->members()
                ->where('evaluator_id', $newEv->id)
                ->where('is_active', true)
                ->where('member_status', 'pending_invite')
                ->exists()
        )->toBeTrue();

        // Active count stays at 1
        expect($committee->members()->where('is_active', true)->count())->toBe(1);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 4: Evaluator Accept / Decline Invitation
// ─────────────────────────────────────────────────────────────────────────────

describe('evaluator invitation response', function () {
    it('evaluator can accept a pending_invite', function () {
        [
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['user' => $evUser, 'evaluator' => $evaluator] = makeEvaluator();

        $member = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $evaluator->id,
            'is_active' => true,
            'member_status' => 'pending_invite',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($evUser)
            ->patch(route('evaluator.invitations.accept', $member))
            ->assertRedirect();

        expect($member->fresh()->member_status)->toBe('pending_uni');
        expect($member->fresh()->member_responded_at)->not->toBeNull();
    });

    it('evaluator can decline with reasons', function () {
        [
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['user' => $evUser, 'evaluator' => $evaluator] = makeEvaluator();

        $member = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $evaluator->id,
            'is_active' => true,
            'member_status' => 'pending_invite',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($evUser)
            ->patch(route('evaluator.invitations.decline', $member), [
                'reasons' => ['لدي تعارض مصالح', 'لا أستطيع الالتزام بالوقت'],
            ])
            ->assertRedirect();

        $fresh = $member->fresh();
        expect($fresh->member_status)->toBe('declined_by_member');
        expect($fresh->reject_reason)->toBe(['لدي تعارض مصالح', 'لا أستطيع الالتزام بالوقت']);
        expect($fresh->member_responded_at)->not->toBeNull();
    });

    it('evaluator cannot accept another evaluator invitation', function () {
        [
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['user' => $evUser1] = makeEvaluator();
        ['evaluator' => $evaluator2] = makeEvaluator();

        $member = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $evaluator2->id,
            'is_active' => true,
            'member_status' => 'pending_invite',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($evUser1)
            ->patch(route('evaluator.invitations.accept', $member))
            ->assertForbidden();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 5: University (Program Coordinator) Approval
// ─────────────────────────────────────────────────────────────────────────────

describe('university approval', function () {
    it('program coordinator can approve a pending_uni member', function () {
        [
            'progCoord' => $progCoord,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['evaluator' => $evaluator] = makeEvaluator();

        $member = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $evaluator->id,
            'is_active' => true,
            'member_status' => 'pending_uni',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($progCoord)
            ->patch(route('program_coordinator.committee.approve', [$request, $member]))
            ->assertRedirect();

        $fresh = $member->fresh();
        expect($fresh->member_status)->toBe('accepted');
        expect($fresh->university_responded_at)->not->toBeNull();
    });

    it('program coordinator can decline with reasons', function () {
        [
            'progCoord' => $progCoord,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['evaluator' => $evaluator] = makeEvaluator();

        $member = CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $evaluator->id,
            'is_active' => true,
            'member_status' => 'pending_uni',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($progCoord)
            ->patch(route('program_coordinator.committee.decline', [$request, $member]), [
                'reasons' => ['يعمل في نفس الجامعة'],
            ])
            ->assertRedirect();

        $fresh = $member->fresh();
        expect($fresh->member_status)->toBe('declined_by_uni');
        expect($fresh->reject_reason)->toBe(['يعمل في نفس الجامعة']);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 6: Approve Committee
// ─────────────────────────────────────────────────────────────────────────────

describe('approveCommittee', function () {
    it('allows secretariat to approve committee and advance to stage_five when all 3 accepted', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        $evaluatorIds = [];
        for ($i = 0; $i < 3; $i++) {
            ['evaluator' => $ev] = makeEvaluator();
            CommitteeMember::factory()->create([
                'committee_id' => $committee->id,
                'evaluator_id' => $ev->id,
                'is_active' => true,
                'member_status' => 'accepted',
                'invite_sent_at' => now(),
                'member_responded_at' => now(),
                'university_responded_at' => now(),
            ]);
            $evaluatorIds[] = $ev->id;
        }

        $this->actingAs($secretariat)
            ->patch(route('requests.stage_four.approve_committee', $request), [
                'chair_evaluator_id' => $evaluatorIds[0],
            ])
            ->assertRedirect();

        expect($committee->fresh()->status)->toBe('approved');
        expect($committee->fresh()->chair_evaluator_id)->toBe($evaluatorIds[0]);
        expect($request->fresh()->current_stage)->toBe('stage_five');
    });

    it('prevents approval when fewer than 3 accepted members', function () {
        [
            'secretariat' => $secretariat,
            'request' => $request,
            'committee' => $committee,
        ] = makeStageForRequest();

        ['evaluator' => $ev] = makeEvaluator();
        CommitteeMember::factory()->create([
            'committee_id' => $committee->id,
            'evaluator_id' => $ev->id,
            'is_active' => true,
            'member_status' => 'accepted',
            'invite_sent_at' => now(),
        ]);

        $this->actingAs($secretariat)
            ->patch(route('requests.stage_four.approve_committee', $request), [
                'chair_evaluator_id' => $ev->id,
            ])
            ->assertRedirect();

        // Stage must NOT advance
        expect($request->fresh()->current_stage)->toBe('stage_four');
        expect($committee->fresh()->status)->toBe('forming');
    });
});
