<?php

use App\Http\Controllers\ProgramCoordinator\CommitteeController;
use App\Http\Controllers\ProgramCoordinator\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Requests List for Program Coordinator
Route::get('/requests', [DashboardController::class, 'requests'])->name('requests');

// Committee member approval routes for program coordinator
Route::get('/committee/{accreditationRequest}', [CommitteeController::class, 'index'])->name('committee.index');
Route::patch('/committee-member/{committeeMemberId}/approve', [CommitteeController::class, 'approve'])->name('committee.approve');
Route::patch('/committee-member/{committeeMemberId}/decline', [CommitteeController::class, 'decline'])->name('committee.decline');
