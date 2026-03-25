<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CouncilSecretariat\UniversityController;

Route::get('/dashboard', function () {
    return view('council_secretariat.dashboard');
})->name('dashboard');

Route::get('/universities', [UniversityController::class, 'index'])->name('universities');
Route::post('/universities/{university}/officer', [UniversityController::class, 'storeOfficer'])->name('universities.storeOfficer');