<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeReport;
use App\Models\Indicator;
use App\Models\User;
use App\Models\VisitSchedule;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StageFiveController extends Controller
{
    /**
     * Create a new draft visit schedule for the accreditation request.
     */
    public function createDraft(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeChairEvaluator($accreditationRequest);

        $committee = $accreditationRequest->committee;
        if (! $committee) {
            return back()->with('error', 'اللجنة غير موجودة.');
        }

        $visitSchedule = $accreditationRequest->visitSchedules()->create([
            'committee_id' => $committee->id,
            'status' => 'draft',
            'schedule_data' => null,
        ]);

        return redirect()->route('requests.stage_five.edit', [
            'accreditationRequest' => $accreditationRequest->id,
            'visitSchedule' => $visitSchedule->id,
        ])->with('success', 'تم إنشاء مسودة جدول الزيارة بنجاح.');
    }

    /**
     * Show the edit form for the visit schedule.
     */
    public function edit(AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        $this->authorizeChairEvaluator($accreditationRequest);

        // Ensure the visit schedule belongs to the request and is a draft
        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'draft') {
            abort(403, 'لا يمكن تعديل هذا الجدول.');
        }

        $program = $accreditationRequest->program;

        return view('requests.visit_schedule', [
            'accreditationRequest' => $accreditationRequest,
            'visitSchedule' => $visitSchedule,
            'program' => $program,
        ]);
    }

    /**
     * Show the visit schedule data (PDF style view).
     */
    public function show(AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        // Allowed roles: Chair Evaluator, Council Coordinator, Program Coordinator, Council Secretariat
        $this->authorizeViewSchedule($accreditationRequest);

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id) {
            abort(404);
        }

        $program = $accreditationRequest->program;

        return view('requests.visit_schedule', [
            'accreditationRequest' => $accreditationRequest,
            'visitSchedule' => $visitSchedule,
            'program' => $program,
            'readonly' => true,
        ]);
    }

    /**
     * Save the draft schedule data via AJAX.
     */
    public function saveDraft(Request $request, AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        $this->authorizeChairEvaluator($accreditationRequest);

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل هذا الجدول.'], 403);
        }

        $validated = $request->validate([
            'days' => ['required', 'array'],
        ]);

        $visitSchedule->update([
            'schedule_data' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ مسودة الجدول بنجاح.',
            'redirect' => route('requests.stage', ['accreditationRequest' => $accreditationRequest->id, 'stage' => 'stage_five']),
        ]);
    }

    /**
     * Submit the schedule to the council.
     */
    public function submit(Request $request, AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        $this->authorizeChairEvaluator($accreditationRequest);

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'draft') {
            return back()->with('error', 'لا يمكن إرسال هذا الجدول.');
        }

        // Validate that data is not empty
        $data = $visitSchedule->schedule_data;
        if (! $data || empty($data['days'])) {
            return back()->with('error', 'لا يوجد بيانات في الجدول لإرسالها.');
        }

        $visitSchedule->update([
            'status' => 'submitted_to_council',
            'submitted_at' => now(),
        ]);

        // Notify Council Coordinator
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $councilCoordinator = $accreditationRequest->councilCoordinator;
        if ($councilCoordinator) {
            $councilCoordinator->notify(new RealTimeNotification(
                title: 'رفع جدول الزيارة',
                message: "قام رئيس اللجنة برفع جدول الزيارة للبرنامج ({$programName}). يرجى المراجعة والتحويل للجامعة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        // Notify other committee members
        $members = $accreditationRequest->committee->acceptedMembers()
            ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
            ->get();
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $memberUser->notify(new RealTimeNotification(
                    title: 'رفع جدول الزيارة للمراجعة',
                    message: "قام رئيس اللجنة برفع جدول الزيارة للبرنامج ({$programName}) إلى المجلس للمراجعة.",
                    type: 'info',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
                ));
            }
        }

        return back()->with('success', 'تم إرسال جدول الزيارة إلى منسق المجلس بنجاح.');
    }

    /**
     * Council coordinator attaches PDF and forwards to university.
     */
    public function councilForward(Request $request, AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        if (request()->user()->role !== 'council_coordinator' || request()->user()->id !== $accreditationRequest->council_coord_id) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'submitted_to_council') {
            return back()->with('error', 'حالة الجدول لا تسمح بهذا الإجراء.');
        }

        $validated = $request->validate([
            'council_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $pdfPath = $validated['council_pdf']->store("req_{$accreditationRequest->id}/visits", 'local');

        $visitSchedule->update([
            'status' => 'pending_uni',
            'council_processed_at' => now(),
            'council_pdf_path' => $pdfPath,
        ]);

        // Notify Program Coordinator and Accreditation Officer
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer', 'programCoordinator');
        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;

        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'وصول جدول الزيارة',
                message: "تم إرسال جدول الزيارة للبرنامج ({$programName}) من قبل المجلس. يرجى المراجعة والرد بالقبول أو الرفض.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'وصول جدول الزيارة',
                message: "تم إرسال جدول الزيارة للبرنامج ({$programName}) للجامعة للمراجعة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        return back()->with('success', 'تم إرفاق النسخة الموقعة وتحويل الجدول إلى الجامعة بنجاح.');
    }

    /**
     * View the private PDF attachment.
     */
    public function viewPdf(AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        $this->authorizeViewSchedule($accreditationRequest);

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || ! $visitSchedule->council_pdf_path) {
            abort(404);
        }

        if (! str_starts_with($visitSchedule->council_pdf_path, "req_{$accreditationRequest->id}/visits/")) {
            abort(403, 'غير مصرح للوصول إلى هذا الملف.');
        }

        if (! Storage::disk('local')->exists($visitSchedule->council_pdf_path)) {
            return back()->with('error', 'عذراً، الملف المرفق غير موجود في نظام التخزين. يرجى التواصل مع الدعم الفني.');
        }

        return response()->download(Storage::disk('local')->path($visitSchedule->council_pdf_path), "visit_schedule_req_{$accreditationRequest->id}.pdf");
    }

    /**
     * Program coordinator rejects the schedule.
     */
    public function universityReject(Request $request, AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        if (request()->user()->role !== 'program_coordinator' || request()->user()->id !== $accreditationRequest->program_coord_id) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'pending_uni') {
            return back()->with('error', 'حالة الجدول لا تسمح بهذا الإجراء.');
        }

        $validated = $request->validate([
            'reject_reasons' => ['required', 'string'],
        ]);

        $visitSchedule->update([
            'status' => 'rejected_uni',
            'university_responded_at' => now(),
            'rejection_reason' => [
                'reason' => $validated['reject_reasons'],
                'date' => now()->toDateTimeString(),
            ],
        ]);

        // Notify Chair and Council Coordinator
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $chair = $accreditationRequest->committee->chairEvaluator->user ?? null;
        $councilCoordinator = $accreditationRequest->councilCoordinator;

        if ($chair) {
            $chair->notify(new RealTimeNotification(
                title: 'رفض جدول الزيارة',
                message: "تم رفض جدول الزيارة للبرنامج ({$programName}) من قبل الجامعة. يرجى مراجعة الأسباب والتعديل.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        if ($councilCoordinator) {
            $councilCoordinator->notify(new RealTimeNotification(
                title: 'رفض جدول الزيارة',
                message: "تم رفض جدول الزيارة للبرنامج ({$programName}) من قبل الجامعة.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
            ));
        }

        // Notify other committee members
        $members = $accreditationRequest->committee->acceptedMembers()
            ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
            ->get();
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $memberUser->notify(new RealTimeNotification(
                    title: 'رفض جدول الزيارة من الجامعة',
                    message: "قامت الجامعة برفض جدول الزيارة للبرنامج ({$programName}). سيقوم رئيس اللجنة بالتعديل عليه.",
                    type: 'error',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_five'])
                ));
            }
        }

        return back()->with('success', 'تم رفض جدول الزيارة وإرسال الأسباب بنجاح.');
    }

    /**
     * Program coordinator accepts the schedule.
     */
    public function universityAccept(Request $request, AccreditationRequest $accreditationRequest, VisitSchedule $visitSchedule)
    {
        if (request()->user()->role !== 'program_coordinator' || request()->user()->id !== $accreditationRequest->program_coord_id) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء.');
        }

        if ($visitSchedule->accreditation_request_id !== $accreditationRequest->id || $visitSchedule->status !== 'pending_uni') {
            return back()->with('error', 'حالة الجدول لا تسمح بهذا الإجراء.');
        }

        DB::transaction(function () use ($accreditationRequest, $visitSchedule) {
            $visitSchedule->update([
                'status' => 'approved_uni',
                'university_responded_at' => now(),
            ]);

            $accreditationRequest->update([
                'current_stage' => 'stage_six',
            ]);

            // Create or update a draft committee report
            $committeeReport = CommitteeReport::updateOrCreate(
                ['accreditation_request_id' => $accreditationRequest->id],
                ['status' => 'draft']
            );

            // Get all indicators to create initial report scores
            $indicators = Indicator::all();

            foreach ($indicators as $indicator) {
                // Use updateOrInsert to prevent duplicates if the university accepts multiple times
                DB::table('report_scores')->updateOrInsert(
                    [
                        'report_id' => $committeeReport->id,
                        'indicator_id' => $indicator->id,
                    ],
                    [
                        'score' => null,
                        'score_type' => 'Initial',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });

        // Notify Chair, Council Coordinator and Officer
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $chair = $accreditationRequest->committee->chairEvaluator->user ?? null;
        $councilCoordinator = $accreditationRequest->councilCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;

        if ($chair) {
            $chair->notify(new RealTimeNotification(
                title: 'الموافقة على جدول الزيارة',
                message: "تمت الموافقة على جدول الزيارة للبرنامج ({$programName}). يرجى الاستعداد للزيارة الميدانية وإعداد التقرير.",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
            ));
        }

        if ($councilCoordinator) {
            $councilCoordinator->notify(new RealTimeNotification(
                title: 'الموافقة على جدول الزيارة',
                message: "تمت الموافقة على جدول الزيارة للبرنامج ({$programName}) من قبل الجامعة.",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'الموافقة على جدول الزيارة',
                message: "تمت الموافقة على جدول الزيارة للبرنامج ({$programName}). انتقل الطلب للمرحلة السادسة (تقرير اللجنة).",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
            ));
        }

        // Notify other committee members
        $members = $accreditationRequest->committee->acceptedMembers()
            ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
            ->get();
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $memberUser->notify(new RealTimeNotification(
                    title: 'الموافقة على جدول الزيارة',
                    message: "تمت الموافقة على جدول الزيارة للبرنامج ({$programName}). يرجى الاستعداد للزيارة الميدانية والمشاركة في إعداد التقرير.",
                    type: 'success',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
                ));
            }
        }

        return back()->with('success', 'تم الموافقة على جدول الزيارة وانتقل الطلب للمرحلة السادسة.');
    }

    /**
     * Ensure the user is the Chair Evaluator of the committee.
     */
    private function authorizeChairEvaluator(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();
        if ($user->role !== 'evaluator') {
            abort(403, 'يجب أن تكون مقيماً.');
        }

        $committee = $accreditationRequest->committee;
        if (! $committee || $committee->chair_evaluator_id !== $user->evaluator->id) {
            abort(403, 'أنت لست رئيس اللجنة لهذا الطلب.');
        }
    }

    /**
     * Authorize viewing the schedule details.
     */
    private function authorizeViewSchedule(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();

        $allowed = match ($user->role) {
            'council_secretariat' => true,
            'program_coordinator' => $accreditationRequest->program_coord_id === $user->id,
            'council_coordinator' => $accreditationRequest->council_coord_id === $user->id,
            'evaluator' => $accreditationRequest->committee && $accreditationRequest->committee->members()
                ->where('evaluator_id', $user->evaluator->id ?? 0)
                ->where('member_status', 'accepted')
                ->exists(),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'ليس لديك صلاحية لمشاهدة هذا الجدول.');
        }
    }
}
