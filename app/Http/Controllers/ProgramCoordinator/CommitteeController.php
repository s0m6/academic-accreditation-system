<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeMember;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        // Notify stakeholders
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $evaluatorUser = $committeeMember->evaluator->user;

        // 1. Notify Council Secretariat
        $secretariat = User::where('role', 'council_secretariat')->get();
        Notification::send($secretariat, new RealTimeNotification(
            title: 'موافقة الجامعة على عضو لجنة',
            message: "وافقت الجامعة على انضمام المقيم ({$evaluatorUser->name}) للجنة البرنامج ({$programName}).",
            type: 'success',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
        ));

        // 2. Notify Evaluator
        if ($evaluatorUser) {
            $evaluatorUser->notify(new RealTimeNotification(
                title: 'موافقة الجامعة على ترشيحك',
                message: "تمت موافقة الجامعة على مشاركتك في لجنة تقييم البرنامج ({$programName}). يرجى الانتظار للاعتماد النهائي للجنة.",
                type: 'success',
                actionUrl: route('evaluator.invitations')
            ));
        }

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

        // Notify stakeholders
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $evaluatorUser = $committeeMember->evaluator->user;

        // 1. Notify Council Secretariat
        $secretariat = User::where('role', 'council_secretariat')->get();
        Notification::send($secretariat, new RealTimeNotification(
            title: 'رفض الجامعة لعضو لجنة',
            message: "قامت الجامعة برفض انضمام المقيم ({$evaluatorUser->name}) للجنة البرنامج ({$programName}).",
            type: 'error',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
        ));

        // 2. Notify Evaluator
        if ($evaluatorUser) {
            $evaluatorUser->notify(new RealTimeNotification(
                title: 'رفض ترشيحك من الجامعة',
                message: "نعتذر، لقد تم رفض مشاركتك في لجنة تقييم البرنامج ({$programName}) من قبل الجامعة.",
                type: 'error',
                actionUrl: route('evaluator.invitations')
            ));
        }

        return back()->with('success', 'تم تسجيل رفض الجامعة.');
    }
}
