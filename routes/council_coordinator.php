<?php

use App\Http\Controllers\CouncilCoordinator\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:council_coordinator'])
    ->prefix('council-coordinator')
    ->name('council_coordinator.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/requests', [DashboardController::class, 'requests'])->name('requests');
    });
