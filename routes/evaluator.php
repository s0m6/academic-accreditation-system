<?php

use App\Http\Controllers\Evaluator\DashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Dashboard route for the evaluator
 */
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
