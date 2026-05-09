<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\Evaluator;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Display all pending and historical invitations for the authenticated evaluator.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Ensure the user has an evaluator profile
        $evaluator = Evaluator::where('user_id', $user->id)->firstOrFail();

        // Load active invitations with full committee/request/program chain
        // Also eagerly load other committee members for the "View Members" feature
        $invitations = CommitteeMember::where('evaluator_id', $evaluator->id)
            ->with([
                'committee.accreditationRequest.program.department.college.university',
                'committee.members' => function ($q) {
                    $q->where('member_status', 'accepted')
                        ->with('evaluator.user');
                },
            ])
            ->orderByDesc('invite_sent_at')
            ->get();

        return view('evaluator.invitations', compact('invitations', 'evaluator'));
    }

    /**
     * Accept a committee invitation — moves status to pending_uni awaiting university approval.
     */
    public function accept(Request $request, CommitteeMember $committeeMember)
    {
        $this->authorizeEvaluatorOwnership($committeeMember, $request->user());

        if ($committeeMember->member_status !== 'pending_invite') {
            return back()->with('error', 'لا يمكن القبول في هذه المرحلة.');
        }

        $committeeMember->update([
            'member_status' => 'pending_uni',
            'member_responded_at' => now(),
        ]);

        // Notify stakeholders
        $accreditationRequest = $committeeMember->committee->accreditationRequest;
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $evaluatorName = $request->user()->name;

        // 1. Notify Council Secretariat
        $secretariat = User::where('role', 'council_secretariat')->get();
        Notification::send($secretariat, new RealTimeNotification(
            title: 'موافقة عضو لجنة',
            message: "قام المقيم ({$evaluatorName}) بالموافقة على الانضمام للجنة البرنامج ({$programName}). الطلب بانتظار موافقة الجامعة.",
            type: 'info',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
        ));

        // 2. Notify Program Coordinator
        $coordinator = $accreditationRequest->programCoordinator;
        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'ترشيح عضو لجنة جديد',
                message: "تمت موافقة مقيم جديد ({$evaluatorName}) على الانضمام للجنة تقييم البرنامج ({$programName}). يرجى المراجعة والرد بالقبول أو الرفض.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
            ));
        }

        return back()->with('success', 'تم قبول الدعوة. سيتم إشعارك بعد مراجعة الجامعة.');
    }

    /**
     * Decline a committee invitation with reasons — records the rejection.
     */
    public function decline(Request $request, CommitteeMember $committeeMember)
    {
        $this->authorizeEvaluatorOwnership($committeeMember, $request->user());

        if ($committeeMember->member_status !== 'pending_invite') {
            return back()->with('error', 'لا يمكن الرفض في هذه المرحلة.');
        }

        $validated = $request->validate([
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['required', 'string', 'max:500'],
        ]);

        $committeeMember->update([
            'member_status' => 'declined_by_member',
            'member_responded_at' => now(),
            'reject_reason' => $validated['reasons'],
        ]);

        // Notify Council Secretariat
        $accreditationRequest = $committeeMember->committee->accreditationRequest;
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $evaluatorName = $request->user()->name;

        $secretariat = User::where('role', 'council_secretariat')->get();
        Notification::send($secretariat, new RealTimeNotification(
            title: 'رفض دعوة لجنة',
            message: "قام المقيم ({$evaluatorName}) برفض دعوة الانضمام للجنة البرنامج ({$programName}).",
            type: 'error',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
        ));

        return back()->with('success', 'تم تسجيل رفضك للدعوة.');
    }

    /**
     * Ensure the authenticated user owns the committee member record.
     */
    private function authorizeEvaluatorOwnership(CommitteeMember $committeeMember, $user): void
    {
        $evaluator = Evaluator::where('user_id', $user->id)->first();
        if (! $evaluator || $committeeMember->evaluator_id !== $evaluator->id) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الطلب.');
        }
    }

    /**
     * Display the list of active evaluations (Approved only).
     */
    public function myEvaluations(): View
    {
        $evaluatorId = Auth::user()->evaluator->id;

        $evaluations = CommitteeMember::where('evaluator_id', $evaluatorId)
            ->where('member_status', 'accepted')
            ->whereHas('committee', function ($query) {
                $query->where('status', 'approved');
            })
            ->with([
                'committee.accreditationRequest.program.department.college.university',
                'committee.members.evaluator.user',
            ])
            ->latest()
            ->get();

        return view('evaluator.evaluations', compact('evaluations'));
    }
}
