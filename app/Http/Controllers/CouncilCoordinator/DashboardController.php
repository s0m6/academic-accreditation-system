<?php

namespace App\Http\Controllers\CouncilCoordinator;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the council coordinator's dashboard.
     */
    public function index()
    {
        $assignedCount = AccreditationRequest::where('council_coord_id', Auth::id())->count();
        $recentRequests = AccreditationRequest::with(['program.department.college.university'])
            ->where('council_coord_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('council_coordinator.dashboard', compact('assignedCount', 'recentRequests'));
    }

    /**
     * Display the list of accreditation requests assigned to the coordinator.
     */
    public function requests(Request $request)
    {
        $requests = AccreditationRequest::with(['program.department.college.university'])
            ->where('council_coord_id', Auth::id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('program', function ($q) use ($request) {
                    $q->where('program_name', 'like', '%'.$request->search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('council_coordinator.requests.index', compact('requests'));
    }
}
