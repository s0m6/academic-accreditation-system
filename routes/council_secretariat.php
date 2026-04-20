<?php

use App\Http\Controllers\CouncilSecretariat\EvaluatorController;
use App\Http\Controllers\CouncilSecretariat\RequestController;
use App\Http\Controllers\CouncilSecretariat\UniversityController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('council_secretariat.dashboard');
})->name('dashboard');

Route::get('/universities', [UniversityController::class, 'index'])->name('universities');
Route::post('/universities/{university}/officer', [UniversityController::class, 'storeOfficer'])->name('universities.storeOfficer');

Route::prefix('requests')->name('requests.')->group(function () {
    Route::get('/stage-one', [RequestController::class, 'stageOne'])->name('stage_one');
    Route::get('/stage-two', [RequestController::class, 'stageTwo'])->name('stage_two');
    Route::get('/stage-three', [RequestController::class, 'stageThree'])->name('stage_three');
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
