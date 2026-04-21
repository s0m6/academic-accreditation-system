<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeMember;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    /**
     * Show active committee members awaiting university approval for the given request.
     */
    public function index(Request $request, AccreditationRequest $accreditationRequest)
    {
        $user = $request->user();

        // Only the program coordinator assigned to this request can view
        if ($accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الطلب.');
        }

        $committee = $accreditationRequest->committee;
        if ($committee) {
            $committee->load(['activeMembers.evaluator.user', 'activeMembers.evaluator.city']);
        }

        return view('program_coordinator.committee', compact('accreditationRequest', 'committee'));
    }

    /**
     * Approve a committee member on behalf of the university.
     */
    public function approve(Request $request, $committeeMemberId)
    {
        $user = $request->user();
        $committeeMember = CommitteeMember::with('committee.accreditationRequest')->findOrFail($committeeMemberId);
        $accreditationRequest = $committeeMember->committee->accreditationRequest;

        if ($accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }

        if ($committeeMember->member_status !== 'pending_uni') {
            return back()->with('error', 'لا يمكن الموافقة في هذه المرحلة.');
        }

        $committeeMember->update([
            'member_status' => 'accepted',
            'university_responded_at' => now(),
        ]);

        return back()->with('success', 'تمت الموافقة على العضو بنجاح.');
    }

    /**
     * Reject a committee member on behalf of the university with reasons.
     */
    public function decline(Request $request, $committeeMemberId)
    {
        $user = $request->user();
        $committeeMember = CommitteeMember::with('committee.accreditationRequest')->findOrFail($committeeMemberId);
        $accreditationRequest = $committeeMember->committee->accreditationRequest;

        if ($accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }

        if ($committeeMember->member_status !== 'pending_uni') {
            return back()->with('error', 'لا يمكن الرفض في هذه المرحلة.');
        }

        $validated = $request->validate([
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['required', 'string', 'max:500'],
        ]);

        $committeeMember->update([
            'member_status' => 'declined_by_uni',
            'university_responded_at' => now(),
            'reject_reason' => $validated['reasons'],
        ]);

        return back()->with('success', 'تم تسجيل رفض الجامعة.');
    }
}
