<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('accreditation_officer.dashboard');
})->name('dashboard');
