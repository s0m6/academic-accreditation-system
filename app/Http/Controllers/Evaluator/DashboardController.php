<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display the evaluator dashboard.
     */
    public function index()
    {
        return view('evaluator.dashboard');
    }
}
