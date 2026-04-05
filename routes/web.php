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
require __DIR__.'/auth.php';

// ------------------------------------------------------------------
// Accreditation Request Dashboard — accessible to multiple roles
// ------------------------------------------------------------------
use App\Http\Controllers\RequestDashboardController;
use App\Http\Controllers\stages\StageOneController;
use App\Http\Controllers\stages\StageTwoController;

// Dashboard and stage navigation routes for accreditation requests
Route::middleware('auth')->group(function () {
    Route::get('/requests/{accreditationRequest}', [RequestDashboardController::class, 'show'])
        ->name('requests.show');
    Route::get('/requests/{accreditationRequest}/stage/{stage}', [RequestDashboardController::class, 'stage'])
        ->name('requests.stage');

    // Initial Accreditation Request (Stage One) submission and decision actions
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
    Route::get('/requests/{accreditationRequest}/stage-two/{formSubmission}/download-file/{decisionIndex}', [StageTwoController::class, 'downloadFile'])
        ->name('requests.stage_two.download_file');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/submit', [StageTwoController::class, 'submit'])
        ->name('requests.stage_two.submit');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/reject', [StageTwoController::class, 'reject'])
        ->name('requests.stage_two.reject');
    Route::patch('/requests/{accreditationRequest}/stage-two/{formSubmission}/approve', [StageTwoController::class, 'approve'])
        ->name('requests.stage_two.approve');
});