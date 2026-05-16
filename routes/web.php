<?php

use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicCertificateController;
use App\Http\Controllers\RequestDashboardController;
use App\Http\Controllers\stages\StageEightController;
use App\Http\Controllers\stages\StageFiveController;
use App\Http\Controllers\stages\StageFourController;
use App\Http\Controllers\stages\StageNineController;
use App\Http\Controllers\stages\StageOneController;
use App\Http\Controllers\stages\StageSevenController;
use App\Http\Controllers\stages\StageSixController;
use App\Http\Controllers\stages\StageThreeController;
use App\Http\Controllers\stages\StageTwoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Public landing page
Route::get('/', function () {
    $latestCertificates = app(PublicCertificateController::class)->getLatest();

    return view('public.index', compact('latestCertificates'));
})->name('welcome');

// Public certificates explorer
Route::get('/certificates/explorer', [PublicCertificateController::class, 'index'])->name('certificates.explorer');
Route::get('/api/certificates/search', [PublicCertificateController::class, 'search'])->name('api.certificates.search');

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
});

// Import Notification Routes
require __DIR__.'/notifications.php';
// UI design playground / testing routes

Route::get('/blank', function () {
    return view('partials.blank');
});

// Public certificate verification (no auth required)
Route::get('/certificate/{certificateNumber}', [StageNineController::class, 'showCertificate'])
    ->name('certificate.show');
require __DIR__.'/auth.php';
require __DIR__.'/council_coordinator.php';

// ------------------------------------------------------------------
// Accreditation Request Dashboard — accessible to multiple roles
// ------------------------------------------------------------------
// ------------------------------------------------------------------
// Accreditation Request Dashboard — accessible to multiple roles
// ------------------------------------------------------------------

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

    // Print actions
    Route::get('/requests/{accreditationRequest}/stage-two/{formSubmission}/print', [PrintController::class, 'printStageTwo'])
        ->name('requests.stage_two.print');

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
    Route::post('/requests/{accreditationRequest}/stage-three/{formSubmission}/validate', [StageThreeController::class, 'validateSubmission'])
        ->name('requests.stage_three.validate');
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
    Route::post('/requests/{accreditationRequest}/stage-five/{visitSchedule}/validate', [StageFiveController::class, 'validateSchedule'])
        ->name('requests.stage_five.validate');
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

    // Stage Six Visit Report actions
    Route::get('/requests/{accreditationRequest}/stage-six/visit-report/edit', [StageSixController::class, 'edit'])
        ->name('requests.stage_six.visit_report.edit');
    Route::get('/requests/{accreditationRequest}/stage-six/visit-report/show', [StageSixController::class, 'show'])
        ->name('requests.stage_six.visit_report.show');
    Route::post('/requests/{accreditationRequest}/stage-six/visit-report/save', [StageSixController::class, 'save'])
        ->name('requests.stage_six.visit_report.save');

    // Stage Six rubrics form actions
    Route::get('/requests/{accreditationRequest}/stage-six/rubrics/edit', [StageSixController::class, 'editRubrics'])
        ->name('requests.stage_six.rubrics_edit');
    Route::get('/requests/{accreditationRequest}/stage-six/rubrics/show', [StageSixController::class, 'showRubrics'])
        ->name('requests.stage_six.rubrics_show');
    Route::post('/requests/{accreditationRequest}/stage-six/rubrics/save', [StageSixController::class, 'saveRubrics'])
        ->name('requests.stage_six.rubrics_save');

    // Stage Six approval workflow actions
    Route::post('/requests/{accreditationRequest}/stage-six/validate', [StageSixController::class, 'validateIndicators'])
        ->name('requests.stage_six.validate');
    Route::patch('/requests/{accreditationRequest}/stage-six/request-approval', [StageSixController::class, 'requestMemberApproval'])
        ->name('requests.stage_six.request_approval');
    Route::post('/requests/{accreditationRequest}/stage-six/member-reject', [StageSixController::class, 'memberReject'])
        ->name('requests.stage_six.member_reject');
    Route::patch('/requests/{accreditationRequest}/stage-six/withdraw', [StageSixController::class, 'withdrawForEdit'])
        ->name('requests.stage_six.withdraw');
    Route::post('/requests/{accreditationRequest}/stage-six/member-approve', [StageSixController::class, 'memberApprove'])
        ->name('requests.stage_six.member_approve');
    Route::post('/requests/{accreditationRequest}/stage-six/submit-to-council', [StageSixController::class, 'submitToCouncil'])
        ->name('requests.stage_six.submit_to_council');
    Route::post('/requests/{accreditationRequest}/stage-six/council-upload', [StageSixController::class, 'uploadRecommendations'])
        ->name('requests.stage_six.council_upload');
    Route::get('/requests/{accreditationRequest}/stage-six/final-report', [StageSixController::class, 'showFinalReport'])
        ->name('requests.stage_six.final_report');
    Route::get('/requests/{accreditationRequest}/stage-six/recommendations-letter', [StageSixController::class, 'showRecommendationsLetter'])
        ->name('requests.stage_six.recommendations_letter');

    // Stage Seven actions
    Route::get('/requests/{accreditationRequest}/stage-seven/recommendations/view', [StageSevenController::class, 'viewRecommendations'])
        ->name('requests.stage_seven.recommendations.view');
    Route::get('/requests/{accreditationRequest}/stage-seven/recommendations/download', [StageSevenController::class, 'downloadRecommendations'])
        ->name('requests.stage_seven.recommendations.download');
    Route::post('/requests/{accreditationRequest}/stage-seven/recommendations/submit', [StageSevenController::class, 'submitResponse'])
        ->name('requests.stage_seven.recommendations.submit');

    // Stage Seven — Form 9 (Response to Recommendations)
    Route::get('/requests/{accreditationRequest}/stage-seven/form9/edit', [StageSevenController::class, 'editForm9'])
        ->name('requests.stage_seven.form9.edit');
    Route::get('/requests/{accreditationRequest}/stage-seven/form9/show', [StageSevenController::class, 'showForm9'])
        ->name('requests.stage_seven.form9.show');
    Route::post('/requests/{accreditationRequest}/stage-seven/form9/save', [StageSevenController::class, 'saveForm9'])
        ->name('requests.stage_seven.form9.save');

    // Stage Eight rubrics form actions
    Route::get('/requests/{accreditationRequest}/stage-eight/rubrics/edit', [StageEightController::class, 'editRubrics'])
        ->name('requests.stage_eight.rubrics_edit');
    Route::get('/requests/{accreditationRequest}/stage-eight/rubrics/show', [StageEightController::class, 'showRubrics'])
        ->name('requests.stage_eight.rubrics_show');
    Route::post('/requests/{accreditationRequest}/stage-eight/rubrics/save', [StageEightController::class, 'saveRubrics'])
        ->name('requests.stage_eight.rubrics_save');

    // Stage Eight approval workflow actions
    Route::post('/requests/{accreditationRequest}/stage-eight/validate', [StageEightController::class, 'validateIndicators'])
        ->name('requests.stage_eight.validate');
    Route::patch('/requests/{accreditationRequest}/stage-eight/request-approval', [StageEightController::class, 'requestMemberApproval'])
        ->name('requests.stage_eight.request_approval');
    Route::post('/requests/{accreditationRequest}/stage-eight/member-reject', [StageEightController::class, 'memberReject'])
        ->name('requests.stage_eight.member_reject');
    Route::patch('/requests/{accreditationRequest}/stage-eight/withdraw', [StageEightController::class, 'withdrawForEdit'])
        ->name('requests.stage_eight.withdraw');
    Route::post('/requests/{accreditationRequest}/stage-eight/member-approve', [StageEightController::class, 'memberApprove'])
        ->name('requests.stage_eight.member_approve');
    Route::post('/requests/{accreditationRequest}/stage-eight/final-submit', [StageEightController::class, 'finalSubmit'])
        ->name('requests.stage_eight.final_submit');
    Route::get('/requests/{accreditationRequest}/stage-eight/final-report', [StageEightController::class, 'showFinalReport'])
        ->name('requests.stage_eight.final_report');
    Route::get('/requests/{accreditationRequest}/stage-eight/final-decision', [StageEightController::class, 'showFinalDecision'])
        ->name('requests.stage_eight.final_decision');
    Route::get('/requests/{accreditationRequest}/stage-eight/comparison', [StageEightController::class, 'showComparison'])
        ->name('requests.stage_eight.comparison');

    // Stage Nine — Final Decision
    Route::post('/requests/{accreditationRequest}/stage-nine/issue-decision', [StageNineController::class, 'issueDecision'])
        ->name('requests.stage_nine.issue_decision');

});
