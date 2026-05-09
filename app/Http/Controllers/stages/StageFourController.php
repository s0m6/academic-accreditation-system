<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeMember;
use App\Models\Evaluator;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;


class StageFourController extends Controller
{
    /**
     * Assign a council coordinator to the accreditation request.
     */
    public function assignCoordinator(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeSecretariat();

        $validated = $request->validate([
            'coordinator_id' => ['required', 'exists:users,id'],
        ]);

        // Verify the selected user has the council_coordinator role
        $coordinator = User::findOrFail($validated['coordinator_id']);
        if ($coordinator->role !== 'council_coordinator') {
            return back()->with('error', 'المستخدم المختار ليس منسق مجلس.');
        }

        $accreditationRequest->update([
            'council_coord_id' => $coordinator->id,
        ]);

        // Notify the assigned Council Coordinator
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $coordinator->notify(new RealTimeNotification(
            title: 'تعيين كمنسق مجلس',
            message: "تم تعيينك كمنسق مجلس لطلب الاعتماد للبرنامج ({$programName}).",
            type: 'info',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
        ));

        return back()->with('success', 'تم تعيين منسق المجلس بنجاح.');
    }

    /**
     * Search evaluators with optional filters — returns JSON for the member-picker modal.
     */
    public function searchEvaluators(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeSecretariat();

        $query = Evaluator::with(['user', 'city', 'currentUniversity', 'conflicts'])
            ->whereHas('user', fn ($q) => $q->where('role', 'evaluator'));

        // Text search on user name or specialty
        if ($request->filled('search')) {
            $search = '%'.$request->string('search').'%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', $search))
                    ->orWhere('general_specialty', 'like', $search)
                    ->orWhere('detailed_specialty', 'like', $search);
            });
        }

        // Academic rank filter
        if ($request->filled('academic_rank')) {
            $query->where('academic_rank', $request->input('academic_rank'));
        }

        // Same city as the college filter
        if ($request->boolean('same_city')) {
            $collegeCity = $accreditationRequest->program->department->college->city_id;
            if ($collegeCity) {
                $query->where('city_id', $collegeCity);
            }
        }

        // No conflict of interest with the university
        if ($request->boolean('no_conflict')) {
            $universityId = $accreditationRequest->program->department->college->university_id;
            $query->whereDoesntHave('conflicts', fn ($q) => $q->where('university_id', $universityId));
        }

        $evaluators = $query->orderBy('id')->limit(50)->get();

        // Get already-active members for this committee to exclude them
        $committee = $accreditationRequest->committee;
        $activeEvaluatorIds = $committee
            ? $committee->members()->where('is_active', true)->pluck('evaluator_id')->toArray()
            : [];

        $collegeCity = $accreditationRequest->program->department->college->city_id;
        $universityId = $accreditationRequest->program->department->college->university_id;

        $result = $evaluators->map(function ($ev) use ($activeEvaluatorIds, $collegeCity, $universityId) {
            $conflicts = $ev->conflicts->where('university_id', $universityId);
            $hasConflict = $conflicts->isNotEmpty();
            $sameCity = $ev->city_id === $collegeCity;

            return [
                'id' => $ev->id,
                'name' => $ev->user->name,
                'email' => $ev->user->email,
                'academic_rank' => $ev->academic_rank,
                'general_specialty' => $ev->general_specialty,
                'detailed_specialty' => $ev->detailed_specialty,
                'city' => $ev->city?->city_name ?? '—',
                'same_city' => $sameCity,
                'has_conflict' => $hasConflict,
                'conflict_details' => $conflicts->pluck('conflict_text')->toArray(),
                'already_selected' => in_array($ev->id, $activeEvaluatorIds),
            ];
        });

        return response()->json($result);
    }

    /**
     * Invite an evaluator to the committee — creates a pending_invite record.
     */
    public function inviteMember(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeSecretariat();

        $validated = $request->validate([
            'evaluator_id' => ['required', 'exists:evaluators,id'],
        ]);

        $committee = $accreditationRequest->committee;
        if (! $committee) {
            return back()->with('error', 'لم يتم إنشاء اللجنة بعد.');
        }

        // Prevent adding beyond 3 active members (backend guard)
        $activeCount = $committee->members()->where('is_active', true)->count();
        if ($activeCount >= 3) {
            return back()->with('error', 'اللجنة اكتملت بالفعل. لا يمكن إضافة أكثر من 3 أعضاء.');
        }

        // Prevent duplicate active invitation for the same evaluator
        $exists = $committee->members()
            ->where('evaluator_id', $validated['evaluator_id'])
            ->where('is_active', true)
            ->exists();
        if ($exists) {
            return back()->with('error', 'هذا المقيم مضاف بالفعل إلى اللجنة.');
        }

        $committee->members()->create([
            'evaluator_id' => $validated['evaluator_id'],
            'member_status' => 'pending_invite',
            'invite_sent_at' => now(),
            'is_active' => true,
            'member_responded_at' => null,
            'university_responded_at' => null,
            'reject_reason' => null,
        ]);

        // Notify the Evaluator
        $accreditationRequest->load(['program.department.college.university']);
        $evaluatorUser = Evaluator::with('user')->find($validated['evaluator_id'])->user;
        $program = $accreditationRequest->program;
        $programName = $program->program_name ?? 'البرنامج';
        $universityName = $program->department?->college?->university?->name ?? 'الجامعة';

        if ($evaluatorUser) {
            $evaluatorUser->notify(new RealTimeNotification(
                title: 'دعوة للمشاركة في لجنة تقييم',
                message: "تمت دعوتك للمشاركة في لجنة تقييم البرنامج ({$programName}) مع جامعة ({$universityName}). يرجى مراجعة الطلب واتخاذ قرار.",
                type: 'info',
                actionUrl: route('evaluator.invitations')
            ));
        }

        return back()->with('success', 'تم إرسال الدعوة للمقيم بنجاح.');
    }

    /**
     * Replace a declined/rejected member with a new evaluator.
     */
    public function replaceMember(Request $request, AccreditationRequest $accreditationRequest, CommitteeMember $committeeMember)
    {
        $this->authorizeSecretariat();

        $validated = $request->validate([
            'evaluator_id' => ['required', 'exists:evaluators,id'],
        ]);

        $committee = $accreditationRequest->committee;

        if ($committee->status === 'approved') {
            return back()->with('error', 'لا يمكن استبدال الأعضاء بعد اعتماد اللجنة.');
        }

        DB::transaction(function () use ($committeeMember, $committee, $validated) {
            // Mark the old record as canceled and inactive
            $committeeMember->update([
                'member_status' => 'canceled',
                'is_active' => false,
            ]);

            // Create a new invitation for the replacement evaluator
            $committee->members()->create([
                'evaluator_id' => $validated['evaluator_id'],
                'member_status' => 'pending_invite',
                'invite_sent_at' => now(),
                'is_active' => true,
                'member_responded_at' => null,
                'university_responded_at' => null,
                'reject_reason' => null,
            ]);
        });

        // Notify the NEW Evaluator
        $accreditationRequest->load(['program.department.college.university']);
        $newEvaluatorUser = Evaluator::with('user')->find($validated['evaluator_id'])->user;
        $program = $accreditationRequest->program;
        $programName = $program->program_name ?? 'البرنامج';
        $universityName = $program->department?->college?->university?->name ?? 'الجامعة';

        if ($newEvaluatorUser) {
            $newEvaluatorUser->notify(new RealTimeNotification(
                title: 'دعوة للمشاركة في لجنة تقييم (بديل)',
                message: "تمت دعوتك للمشاركة في لجنة تقييم البرنامج ({$programName}) مع جامعة ({$universityName}). يرجى مراجعة الطلب واتخاذ قرار.",
                type: 'info',
                actionUrl: route('evaluator.invitations')
            ));
        }

        return back()->with('success', 'تم استبدال العضو بنجاح وإرسال دعوة للمقيم الجديد.');
    }

    /**
     * Cancel an invitation without replacing it.
     */
    public function cancelMember(AccreditationRequest $accreditationRequest, CommitteeMember $committeeMember)
    {
        $this->authorizeSecretariat();

        $committee = $accreditationRequest->committee;
        if ($committee->status === 'approved') {
            return back()->with('error', 'لا يمكن إلغاء الأعضاء بعد اعتماد اللجنة.');
        }

        $oldStatus = $committeeMember->member_status;
        $evaluatorUser = $committeeMember->evaluator->user;
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';

        $committeeMember->update([
            'member_status' => 'canceled',
            'is_active' => false,
        ]);

        // Notify Evaluator
        if ($evaluatorUser) {
            $evaluatorUser->notify(new RealTimeNotification(
                title: 'إلغاء دعوة لجنة',
                message: "تم إلغاء دعوتك للمشاركة في لجنة تقييم البرنامج ({$programName}).",
                type: 'warning',
                actionUrl: route('evaluator.invitations')
            ));
        }

        // Notify Program Coordinator if they knew about the member (status was pending_uni or accepted)
        if (in_array($oldStatus, ['pending_uni', 'accepted'])) {
            $coordinator = $accreditationRequest->programCoordinator;
            if ($coordinator) {
                $coordinator->notify(new RealTimeNotification(
                    title: 'تعديل في أعضاء اللجنة',
                    message: "تم إلغاء دعوة العضو ({$evaluatorUser->name}) من لجنة تقييم البرنامج ({$programName}).",
                    type: 'warning',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
                ));
            }
        }

        return back()->with('success', 'تم إلغاء طلب المشاركة للمقيم بنجاح.');
    }

    /**
     * Re-invite the same member after they declined (keeps record history).
     */
    public function reinviteMember(AccreditationRequest $accreditationRequest, CommitteeMember $committeeMember)
    {
        $this->authorizeSecretariat();

        // Check if the member had actually declined
        if (! in_array($committeeMember->member_status, ['declined_by_member', 'declined_by_uni'])) {
            return back()->with('error', 'لا يمكن إعادة دعوة هذا العضو حالياً.');
        }

        $committee = $accreditationRequest->committee;

        DB::transaction(function () use ($committeeMember, $committee) {
            // Deactivate old record
            $committeeMember->update(['is_active' => false]);

            // Create new record for same evaluator
            $committee->members()->create([
                'evaluator_id' => $committeeMember->evaluator_id,
                'member_status' => 'pending_invite',
                'invite_sent_at' => now(),
                'is_active' => true,
                'member_responded_at' => null,
                'university_responded_at' => null,
                'reject_reason' => null,
            ]);
        });

        // Notify the Evaluator
        $accreditationRequest->load(['program.department.college.university']);
        $committeeMember->load('evaluator.user');
        $evaluatorUser = $committeeMember->evaluator->user;
        $program = $accreditationRequest->program;
        $programName = $program->program_name ?? 'البرنامج';
        $universityName = $program->department?->college?->university?->name ?? 'الجامعة';

        if ($evaluatorUser) {
            $evaluatorUser->notify(new RealTimeNotification(
                title: 'إعادة دعوة للمشاركة في لجنة تقييم',
                message: "تمت إعادة دعوتك للمشاركة في لجنة تقييم البرنامج ({$programName}) مع جامعة ({$universityName}). يرجى مراجعة الطلب واتخاذ قرار.",
                type: 'info',
                actionUrl: route('evaluator.invitations')
            ));
        }

        return back()->with('success', 'تمت إعادة إرسال الدعوة للمقيم بنجاح.');
    }

    /**
     * Approve the committee and assign a chair — advances request to stage five.
     */
    public function approveCommittee(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeSecretariat();

        $validated = $request->validate([
            'chair_evaluator_id' => ['required', 'exists:evaluators,id'],
        ]);

        $committee = $accreditationRequest->committee;
        if (! $committee) {
            return back()->with('error', 'لم يتم إنشاء اللجنة بعد.');
        }

        // Ensure all 3 active members are accepted before approving
        $acceptedCount = $committee->members()
            ->where('is_active', true)
            ->where('member_status', 'accepted')
            ->count();

        if ($acceptedCount < 3) {
            return back()->with('error', 'يجب أن يوافق 3 أعضاء قبل اعتماد اللجنة.');
        }

        // Chair must be one of the active accepted members
        $chairIsValidMember = $committee->members()
            ->where('evaluator_id', $validated['chair_evaluator_id'])
            ->where('is_active', true)
            ->where('member_status', 'accepted')
            ->exists();

        if (! $chairIsValidMember) {
            return back()->with('error', 'يجب اختيار الرئيس من الأعضاء الثلاثة المعتمدين.');
        }

        DB::transaction(function () use ($committee, $accreditationRequest, $validated) {
            $committee->update([
                'status' => 'approved',
                'chair_evaluator_id' => $validated['chair_evaluator_id'],
            ]);

            $accreditationRequest->update([
                'current_stage' => 'stage_five',
            ]);
        });

        // Notify stakeholders
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer', 'programCoordinator', 'committee.members.evaluator.user');

        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;
        $chair = $committee->chairEvaluator->user;
        $members = $committee->members()->where('is_active', true)->where('member_status', 'accepted')->get();

        // 1. Notify Council Secretariat
        $secretariat = User::where('role', 'council_secretariat')->get();
        Notification::send($secretariat, new RealTimeNotification(
            title: 'الاعتماد النهائي للجنة',
            message: "تم الاعتماد النهائي للجنة تقييم البرنامج ({$programName}).",
            type: 'success',
            actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
        ));

        // 2. Notify Program Coordinator
        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'اعتماد لجنة التقييم',
                message: "تم اعتماد لجنة التقييم للبرنامج ({$programName}). يرجى الاستعداد للمرحلة الخامسة (جدول الزيارة).",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        // 3. Notify Accreditation Officer
        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'اعتماد لجنة التقييم',
                message: "تم اعتماد لجنة التقييم للبرنامج ({$programName}). انتقل الطلب للمرحلة الخامسة.",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        // 4. Notify Committee Members
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $isChair = $member->evaluator_id === $committee->chair_evaluator_id;
                $title = $isChair ? 'اعتمادك كرئيس لجنة' : 'اعتمادك كعضو لجنة';
                $message = $isChair
                    ? "تم اعتمادك كرئيس للجنة التقييم للبرنامج ({$programName}). يرجى البدء في إعداد جدول الزيارة."
                    : "تم اعتمادك كعضو في لجنة التقييم للبرنامج ({$programName}).";

                $memberUser->notify(new RealTimeNotification(
                    title: $title,
                    message: $message,
                    type: 'success',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
                ));
            }
        }

        return back()->with('success', 'تم اعتماد اللجنة بنجاح وانتقل الطلب إلى المرحلة الخامسة.');
    }

    /**
     * Ensure the current user is a council secretariat member.
     */
    private function authorizeSecretariat(): void
    {
        if (request()->user()->role !== 'council_secretariat') {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }
    }
}
