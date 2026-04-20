<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'verified', 'role:council_secretariat'])
                ->prefix('council-secretariat')
                ->name('council_secretariat.')
                ->group(base_path('routes/council_secretariat.php'));

            Route::middleware(['web', 'auth', 'verified', 'role:accreditation_officer'])
                ->prefix('accreditation-officer')
                ->name('accreditation_officer.')
                ->group(base_path('routes/accreditation_officer.php'));

            Route::middleware(['web', 'auth', 'verified', 'role:program_coordinator'])
                ->prefix('program-coordinator')
                ->name('program_coordinator.')
                ->group(base_path('routes/program_coordinator.php'));

            Route::middleware(['web', 'auth', 'verified', 'role:evaluator'])
                ->prefix('evaluator')
                ->name('evaluator.')
                ->group(base_path('routes/evaluator.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
