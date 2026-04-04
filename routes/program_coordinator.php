<?php

use App\Http\Controllers\ProgramCoordinator\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Requests List for Program Coordinator
Route::get('/requests', [DashboardController::class, 'requests'])->name('requests');
