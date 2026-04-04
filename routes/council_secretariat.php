<?php

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
