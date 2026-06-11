<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\VisitSchedule;
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
                'stats' => [
                    'active_count' => 0,
                    'completed_count' => 0,
                    'upcoming_count' => 0,
                    'pending_invitations_count' => 0,
                ],
            ]);
        }
        $evaluatorId = $user->evaluator->id;

        // Fetch active evaluations (Accepted member AND Approved committee, and request not completed)
        $activeEvaluations = CommitteeMember::where('evaluator_id', $evaluatorId)
            ->where('member_status', 'accepted')
            ->whereHas('committee', function ($query) {
                $query->where('status', 'approved')
                    ->whereHas('accreditationRequest', function ($q) {
                        $q->where('request_status', '!=', 'completed');
                    });
            })
            ->with(['committee.accreditationRequest.program.department.college.university'])
            ->get();

        // Completed evaluations (Accepted member AND Approved committee AND request is completed)
        $completedCount = CommitteeMember::where('evaluator_id', $evaluatorId)
            ->where('member_status', 'accepted')
            ->whereHas('committee', function ($query) {
                $query->where('status', 'approved')
                    ->whereHas('accreditationRequest', function ($q) {
                        $q->where('request_status', 'completed');
                    });
            })
            ->count();

        // Upcoming count (visit schedules that are approved or submitted, for committees this evaluator is part of)
        $upcomingCount = VisitSchedule::whereIn('status', ['submitted_to_council', 'approved_uni', 'pending_uni'])
            ->whereIn('committee_id', function ($query) use ($evaluatorId) {
                $query->select('committee_id')
                    ->from('committee_members')
                    ->where('evaluator_id', $evaluatorId)
                    ->where('member_status', 'accepted');
            })
            ->count();

        // Pending invitations count (invitations where status is pending_invite)
        $pendingInvitationsCount = CommitteeMember::where('evaluator_id', $evaluatorId)
            ->where('is_active', true)
            ->where('member_status', 'pending_invite')
            ->count();

        $stats = [
            'active_count' => $activeEvaluations->count(),
            'completed_count' => $completedCount,
            'upcoming_count' => $upcomingCount,
            'pending_invitations_count' => $pendingInvitationsCount,
        ];

        return view('evaluator.dashboard', compact('activeEvaluations', 'stats'));
    }
}
