<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', function () {
    return view('welcome');
});

// Unified dashboard route that redirects users to their role-specific dashboard
Route::get('/dashboard', function () {
    return match (Auth::user()->role) {
        'accreditation_officer' => redirect()->route('accreditation_officer.dashboard'),
        'council_secretariat' => redirect()->route('council_secretariat.dashboard'),
        'program_coordinator' => redirect()->route('program_coordinator.dashboard'),
        'council_coordinator' => redirect()->route('council_coordinator.dashboard'),
        'evaluator' => redirect()->route('evaluator.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Standard user profile management routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// UI design playground / testing routes
Route::get('/app', function () {
    return view('partials.app');
});
Route::get('/blank', function () {
    return view('partials.blank');
});
Route::get('/test/visit-schedule', function () {
    return view('test.visit_schedule_design');
})->name('test.visit_schedule');
require __DIR__.'/auth.php';
require __DIR__.'/council_coordinator.php';

// ------------------------------------------------------------------
// Accreditation Request Dashboard — accessible to multiple roles
// ------------------------------------------------------------------
use App\Http\Controllers\RequestDashboardController;
use App\Http\Controllers\stages\StageFiveController;
use App\Http\Controllers\stages\StageFourController;
use App\Http\Controllers\stages\StageOneController;
use App\Http\Controllers\stages\StageThreeController;
use App\Http\Controllers\stages\StageTwoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Dashboard and stage navigation routes for accreditation requests
Route::middleware('auth')->group(function () {
    // Generic Temp Files Cleanup
    Route::post('/temp-files/cleanup', function (Request $request) {
        $paths = $request->input('paths', []);
        foreach ($paths as $path) {
            if (str_starts_with($path, 'temp_files/') && Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        return response()->json(['success' => true]);
    })->name('temp_files.cleanup');
    // requests routes
    Route::get('/requests/{accreditationRequest}', [RequestDashboardController::class, 'show'])
        ->name('requests.show');
    Route::get('/requests/{accreditationRequest}/stage/{stage}', [RequestDashboardController::class, 'stage'])
        ->name('requests.stage');

    //  Stage one actions
    Route::post('/requests/{accreditationRequest}/stage-one', [StageOneController::class, 'store'])
        ->name('requests.stage_one.store');
    Route::patch('/requests/{accreditationRequest}/stage-one/{formSubmission}/reject', [StageOneController::class, 'reject'])
        ->name('requests.stage_one.reject');
    Route::patch('/requests/{accreditationRequest}/stage-one/{formSubmission}/approve', [StageOneController::class, 'approve'])
        ->name('requests.stage_one.approve');

    // Stage Two actions
    Route::post('/requests/{accreditationRequest}/stage-two/draft', [StageTwoController::class, 'createDraft'])
        ->name('requests.stage_two.draft');
    Route::get('/requests/{accreditationRequest}/stage-two/{formSubmission}/edit', [StageTwoController::class, 'edit'])
        ->name('requests.stage_two.edit');
    Route::get('/requests/{accreditationRequest}/stage-two/{formSubmission}/show', [StageTwoController::class, 'show'])
        ->name('requests.stage_two.show');
    Route::post('/requests/{accreditationRequest}/stage-two/{formSubmission}/save', [StageTwoController::class, 'saveDraft'])
        ->name('requests.stage_two.save');
    Route::get('/requests/{accreditationRequest}/stage-two/{formSubmission}/view-file/{decisionIndex}', [StageTwoController::class, 'viewFile'])
        ->name('requests.stage_two.view_file');
    Route::post('/requests/{accreditationRequest}/stage-two/{formSubmission}/upload-file/{decisionIndex}', [StageTwoController::class, 'uploadFile'])
        ->name('requests.stage_two.upload_file');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/submit', [StageTwoController::class, 'submit'])
        ->name('requests.stage_two.submit');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/reject', [StageTwoController::class, 'reject'])
        ->name('requests.stage_two.reject');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/approve', [StageTwoController::class, 'approve'])
        ->name('requests.stage_two.approve');

    // Stage Three actions
    Route::post('/requests/{accreditationRequest}/stage-three/draft', [StageThreeController::class, 'createDraft'])
        ->name('requests.stage_three.draft');
    Route::get('/requests/{accreditationRequest}/stage-three/{formSubmission}/edit', [StageThreeController::class, 'edit'])
        ->name('requests.stage_three.edit');
    Route::get('/requests/{accreditationRequest}/stage-three/{formSubmission}/show', [StageThreeController::class, 'show'])
        ->name('requests.stage_three.show');
    Route::post('/requests/{accreditationRequest}/stage-three/{formSubmission}/save', [StageThreeController::class, 'saveDraft'])
        ->name('requests.stage_three.save');
    Route::post('/requests/{accreditationRequest}/stage-three/{formSubmission}/upload-evidence-temp', [StageThreeController::class, 'uploadEvidenceTemp'])
        ->name('requests.stage_three.upload_evidence_temp');
    Route::get('/stage-three/view-file', [StageThreeController::class, 'viewFile'])
        ->name('requests.stage_three.view_file');
    Route::patch('/requests/{accreditationRequest}/stage-three/{formSubmission}/submit', [StageThreeController::class, 'submit'])
        ->name('requests.stage_three.submit');
    Route::patch('/requests/{accreditationRequest}/stage-three/{formSubmission}/reject', [StageThreeController::class, 'reject'])
        ->name('requests.stage_three.reject');
    Route::patch('/requests/{accreditationRequest}/stage-three/{formSubmission}/approve', [StageThreeController::class, 'approve'])
        ->name('requests.stage_three.approve');

    // Stage Four actions
    Route::patch('/requests/{accreditationRequest}/stage-four/coordinator', [StageFourController::class, 'assignCoordinator'])
        ->name('requests.stage_four.assign_coordinator');
    Route::get('/requests/{accreditationRequest}/stage-four/search-evaluators', [StageFourController::class, 'searchEvaluators'])
        ->name('requests.stage_four.search_evaluators');
    Route::post('/requests/{accreditationRequest}/stage-four/invite-member', [StageFourController::class, 'inviteMember'])
        ->name('requests.stage_four.invite_member');
    Route::patch('/requests/{accreditationRequest}/stage-four/replace-member/{committeeMember}', [StageFourController::class, 'replaceMember'])
        ->name('requests.stage_four.replace_member');
    Route::patch('/requests/{accreditationRequest}/stage-four/cancel-member/{committeeMember}', [StageFourController::class, 'cancelMember'])
        ->name('requests.stage_four.cancel_member');
    Route::post('/requests/{accreditationRequest}/stage-four/reinvite-member/{committeeMember}', [StageFourController::class, 'reinviteMember'])
        ->name('requests.stage_four.reinvite_member');
    Route::patch('/requests/{accreditationRequest}/stage-four/approve-committee', [StageFourController::class, 'approveCommittee'])
        ->name('requests.stage_four.approve_committee');

    // Stage Five actions
    Route::post('/requests/{accreditationRequest}/stage-five/draft', [StageFiveController::class, 'createDraft'])
        ->name('requests.stage_five.draft');
    Route::get('/requests/{accreditationRequest}/stage-five/{visitSchedule}/edit', [StageFiveController::class, 'edit'])
        ->name('requests.stage_five.edit');
    Route::get('/requests/{accreditationRequest}/stage-five/{visitSchedule}/show', [StageFiveController::class, 'show'])
        ->name('requests.stage_five.show');
    Route::post('/requests/{accreditationRequest}/stage-five/{visitSchedule}/save', [StageFiveController::class, 'saveDraft'])
        ->name('requests.stage_five.save');
    Route::patch('/requests/{accreditationRequest}/stage-five/{visitSchedule}/submit', [StageFiveController::class, 'submit'])
        ->name('requests.stage_five.submit');
    Route::post('/requests/{accreditationRequest}/stage-five/{visitSchedule}/council-forward', [StageFiveController::class, 'councilForward'])
        ->name('requests.stage_five.council_forward');
    Route::patch('/requests/{accreditationRequest}/stage-five/{visitSchedule}/university-reject', [StageFiveController::class, 'universityReject'])
        ->name('requests.stage_five.university_reject');
    Route::patch('/requests/{accreditationRequest}/stage-five/{visitSchedule}/university-accept', [StageFiveController::class, 'universityAccept'])
        ->name('requests.stage_five.university_accept');
    Route::get('/requests/{accreditationRequest}/stage-five/{visitSchedule}/view-pdf', [StageFiveController::class, 'viewPdf'])
        ->name('requests.stage_five.view_pdf');

    // Stage Six actions
    Route::get('/requests/{accreditationRequest}/stage-six/edit', [App\Http\Controllers\stages\StageSixController::class, 'edit'])
        ->name('requests.stage_six.edit');
    Route::get('/requests/{accreditationRequest}/stage-six/show', [App\Http\Controllers\stages\StageSixController::class, 'show'])
        ->name('requests.stage_six.show');
    Route::post('/requests/{accreditationRequest}/stage-six/save', [App\Http\Controllers\stages\StageSixController::class, 'save'])
        ->name('requests.stage_six.save');

});

Route::view('test-rubrics', 'requests.stage_six_rubrics_form');
