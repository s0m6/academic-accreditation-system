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
        $user = Auth::user();

        $requests = AccreditationRequest::where('program_coord_id', $user->id)
            ->with(['program.department.college.university'])
            ->get();

        $stats = [
            'total_count' => $requests->count(),
            'active_count' => $requests->whereNotIn('current_stage', ['stage_nine'])->count(),
            'completed_count' => $requests->where('current_stage', 'stage_nine')->count(),
        ];

        $latestRequests = $requests->sortByDesc('updated_at')->take(5);

        return view('program_coordinator.dashboard', compact('stats', 'requests', 'latestRequests'));
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
