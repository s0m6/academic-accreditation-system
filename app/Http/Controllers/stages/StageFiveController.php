<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\VisitSchedule;
use App\Models\CommitteeReport;
use App\Models\ReportScore;
use App\Models\Indicator;
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

        return Storage::disk('local')->download($visitSchedule->council_pdf_path, "visit_schedule_req_{$accreditationRequest->id}.pdf");
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

            // Create a draft committee report
            $committeeReport = CommitteeReport::create([
                'accreditation_request_id' => $accreditationRequest->id,
                'status' => 'draft',
            ]);

            // Get all indicators to create initial report scores
            $indicators = Indicator::all();
            $reportScores = [];

            foreach ($indicators as $indicator) {
                $reportScores[] = [
                    'report_id' => $committeeReport->id,
                    'indicator_id' => $indicator->id,
                    'score' => null,
                    'score_type' => 'Initial',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert all scores at once
            if (!empty($reportScores)) {
                ReportScore::insert($reportScores);
            }
        });

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
