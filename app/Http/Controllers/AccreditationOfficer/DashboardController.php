<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the accreditation officer dashboard.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $university = $user->university;

        if (! $university) {
            // Fallback if no university is attached
            return view('accreditation_officer.dashboard', [
                'collegesCount' => 0,
                'departmentsCount' => 0,
                'programsCount' => 0,
                'requestsCount' => 0,
                'activeRequestsCount' => 0,
                'recentRequests' => collect(),
            ]);
        }

        $collegesCount = $university->colleges()->count();

        $departmentsCount = $university->colleges()
            ->join('departments', 'colleges.id', '=', 'departments.college_id')
            ->count('departments.id');

        $programsCount = $university->colleges()
            ->join('departments', 'colleges.id', '=', 'departments.college_id')
            ->join('programs', 'departments.id', '=', 'programs.department_id')
            ->count('programs.id');

        $requestsQuery = AccreditationRequest::whereHas('program.department.college', function ($query) use ($university) {
            $query->where('university_id', $university->id);
        });

        $requestsCount = (clone $requestsQuery)->count();

        $activeRequestsCount = (clone $requestsQuery)
            ->whereIn('request_status', ['Active'])
            ->count();

        $recentRequests = (clone $requestsQuery)
            ->with(['program.department'])
            ->latest()
            ->take(5)
            ->get();

        return view('accreditation_officer.dashboard', compact(
            'collegesCount',
            'departmentsCount',
            'programsCount',
            'requestsCount',
            'activeRequestsCount',
            'recentRequests'
        ));
    }
}
