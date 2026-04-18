<?php

use App\Http\Controllers\AccreditationOfficer\CollegeController;
use App\Http\Controllers\AccreditationOfficer\DepartmentController;
use App\Http\Controllers\AccreditationOfficer\ProgramController;
use Illuminate\Support\Facades\Route;

// Dashboard route for the accreditation officer
Route::get('/dashboard', function () {
    return view('accreditation_officer.dashboard');
})->name('dashboard');

// Colleges
// College management routes
Route::get('/colleges', [CollegeController::class, 'index'])->name('colleges');
Route::post('/colleges', [CollegeController::class, 'store'])->name('colleges.store');
Route::put('/colleges/{college}', [CollegeController::class, 'update'])->name('colleges.update');
Route::delete('/colleges/{college}', [CollegeController::class, 'destroy'])->name('colleges.destroy');

// Departments
// Department management routes
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

// Programs
// Program management routes
Route::get('/programs', [ProgramController::class, 'index'])->name('programs');
Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
Route::get('/programs/departments/{college}', [ProgramController::class, 'getDepartments'])->name('programs.departments');
// Create a draft accreditation request for a program
Route::post('/programs/{program}/requests', [ProgramController::class, 'storeRequest'])->name('programs.requests.store');
