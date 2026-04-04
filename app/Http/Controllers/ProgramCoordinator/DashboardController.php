<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the coordinator's dashboard.
     */
    public function index()
    {
        return view('program_coordinator.dashboard');
    }

    /**
     * Display the accreditation requests belonging to this coordinator.
     */
    public function requests()
    {
        $user = Auth::user();
        $requests = AccreditationRequest::where('program_coord_id', $user->id)
            ->with(['program.department.college'])
            ->get();

        return view('program_coordinator.requests', compact('requests'));
    }
}
