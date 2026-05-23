<?php

use App\Http\Controllers\CouncilSecretariat\CertificateController;
use App\Http\Controllers\CouncilSecretariat\CoordinatorController;
use App\Http\Controllers\CouncilSecretariat\DecisionController;
use App\Http\Controllers\CouncilSecretariat\EvaluatorController;
use App\Http\Controllers\CouncilSecretariat\RequestController;
use App\Http\Controllers\CouncilSecretariat\UniversityController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('council_secretariat.dashboard');
})->name('dashboard');

Route::get('/universities', [UniversityController::class, 'index'])->name('universities');
Route::post('/universities', [UniversityController::class, 'store'])->name('universities.store');
Route::post('/universities/{university}/officer', [UniversityController::class, 'storeOfficer'])->name('universities.storeOfficer');

// Council Coordinators management
Route::prefix('coordinators')->name('coordinators.')->group(function () {
    Route::get('/', [CoordinatorController::class, 'index'])->name('index');
    Route::post('/', [CoordinatorController::class, 'store'])->name('store');
});

Route::prefix('requests')->name('requests.')->group(function () {
    Route::get('/stage-one', [RequestController::class, 'stageOne'])->name('stage_one');
    Route::get('/stage-two', [RequestController::class, 'stageTwo'])->name('stage_two');
    Route::get('/stage-three', [RequestController::class, 'stageThree'])->name('stage_three');
    Route::get('/stage-four', [RequestController::class, 'stageFour'])->name('stage_four');
    Route::get('/stage-five', [RequestController::class, 'stageFive'])->name('stage_five');
    Route::get('/stage-six', [RequestController::class, 'stageSix'])->name('stage_six');
    Route::get('/stage-seven', [RequestController::class, 'stageSeven'])->name('stage_seven');
    Route::get('/stage-eight', [RequestController::class, 'stageEight'])->name('stage_eight');
    Route::get('/stage-nine', [RequestController::class, 'stageNine'])->name('stage_nine');
});

// Evaluators management routes
Route::prefix('evaluators')->name('evaluators.')->group(function () {
    Route::get('/', [EvaluatorController::class, 'index'])->name('index');
    Route::get('/search', [EvaluatorController::class, 'search'])->name('search');
    Route::get('/create', [EvaluatorController::class, 'create'])->name('create');
    Route::post('/', [EvaluatorController::class, 'store'])->name('store');
    Route::get('/{evaluator}', [EvaluatorController::class, 'show'])->name('show');
    Route::get('/{evaluator}/edit', [EvaluatorController::class, 'edit'])->name('edit');
    Route::put('/{evaluator}', [EvaluatorController::class, 'update'])->name('update');
    Route::get('/{evaluator}/attachments/{attachment}', [EvaluatorController::class, 'viewAttachment'])->name('attachments.view');
});

// Records (Decisions and Certificates)
Route::get('/decisions', [DecisionController::class, 'index'])->name('decisions.index');
Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');

// Reports Management
use App\Http\Controllers\CouncilSecretariat\ReportController;

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
});
