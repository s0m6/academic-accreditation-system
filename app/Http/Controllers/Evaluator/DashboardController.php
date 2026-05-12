<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the evaluator dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user->evaluator) {
            return view('evaluator.dashboard', [
                'activeEvaluations' => collect(),
                'stats' => ['active_count' => 0, 'completed_count' => 0, 'upcoming_count' => 0],
            ]);
        }
        $evaluatorId = $user->evaluator->id;

        // Fetch active evaluations (Accepted member AND Approved committee)
        $activeEvaluations = CommitteeMember::where('evaluator_id', $evaluatorId)
            ->where('member_status', 'accepted')
            ->whereHas('committee', function ($query) {
                $query->where('status', 'approved');
            })
            ->with(['committee.accreditationRequest.program.department.college.university'])
            ->get();

        $stats = [
            'active_count' => $activeEvaluations->count(),
            'completed_count' => 0,
            'upcoming_count' => 0,
        ];

        return view('evaluator.dashboard', compact('activeEvaluations', 'stats'));
    }
}
