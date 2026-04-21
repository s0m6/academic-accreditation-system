<?php

use App\Http\Controllers\Evaluator\DashboardController;
use App\Http\Controllers\Evaluator\InvitationController;
use Illuminate\Support\Facades\Route;

/**
 * Dashboard route for the evaluator
 */
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Evaluation active management routes
Route::get('/evaluations', [InvitationController::class, 'myEvaluations'])->name('evaluations');

// Evaluation invitation management routes
Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations');
Route::patch('/invitations/{committeeMember}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::patch('/invitations/{committeeMember}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');
