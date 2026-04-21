<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\Evaluator;
use Illuminate\Http\Request;

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
        $invitations = CommitteeMember::where('evaluator_id', $evaluator->id)
            ->where('is_active', true)
            ->with([
                'committee.accreditationRequest.program.department.college.university',
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
}
