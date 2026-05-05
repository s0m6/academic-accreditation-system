<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeApproval;
use App\Models\ReportScore;
use App\Models\ReportSignature;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StageEightController extends Controller
{
    // Show the edit view for the final program evaluation rubrics form (Stage 8).
    public function editRubrics(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'uni_responded', // Stage 8 typically follows uni_responded
        ]);

        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all FINAL scores for this report keyed by indicator_id.
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        $savedFormData = $report->form6_final_data ?? [];

        $isEditMode = true;

        return view('requests.stage_eight_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'savedFormData',
            'isEditMode'
        ));
    }

    // Show the read-only view for the final program evaluation rubrics form (Stage 8).
    public function showRubrics(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;

        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all final scores keyed by indicator_id.
        $savedScores = $report
            ? ReportScore::where('report_id', $report->id)
                ->where('score_type', 'final')
                ->pluck('score', 'indicator_id')
            : collect();

        $savedFormData = $report?->form6_final_data ?? [];

        $isEditMode = false;

        return view('requests.stage_eight_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'savedFormData',
            'isEditMode'
        ));
    }

    // Save the final rubrics form: indicator scores into report_scores and text comments into form6_final_data JSON.
    public function saveRubrics(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ]);

        $scores = $request->input('scores', []);
        $formData = $request->input('form_data', []);

        if (is_string($scores)) {
            $scores = json_decode($scores, true) ?? [];
        }

        if (is_string($formData)) {
            $formData = json_decode($formData, true) ?? [];
        }

        DB::transaction(function () use ($report, $scores, $formData) {
            // Upsert indicator scores into report_scores with score_type = 'final'.
            foreach ($scores as $indicatorId => $score) {
                ReportScore::updateOrCreate(
                    [
                        'report_id' => $report->id,
                        'indicator_id' => (int) $indicatorId,
                        'score_type' => 'final',
                    ],
                    [
                        'score' => ($score === '' || $score === null) ? null : (int) $score,
                    ]
                );
            }

            // Persist the structured text comments as JSON into form6_final_data.
            $report->update([
                'form6_final_data' => $formData,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'تم حفظ التغييرات الختامية بنجاح.']);
    }

    // Authorize that the current user is the committee chair evaluator.
    private function authorizeAccess(AccreditationRequest $accreditationRequest, bool $checkEditable = false): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        if (! $committee || $committee->chair_evaluator_id !== $user->evaluator?->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        if ($checkEditable) {
            $report = $accreditationRequest->committeeReport;
            if ($report && ! in_array($report->status, ['uni_responded', 'returned_for_edit'])) {
                $statusMsg = match ($report->status) {
                    'final_under_review' => 'لا يمكن تعديل النماذج أثناء وجود طلب موافقة معلق للأعضاء. يجب سحب الطلب أولاً في حال الرغبة بالتعديل.',
                    'completed' => 'لا يمكن تعديل النماذج بعد إكمال التقرير النهائي.',
                    default => 'لا يمكن تعديل النماذج في الحالة الحالية للتقرير.',
                };
                abort(403, $statusMsg);
            }
        }
    }

    // Request approval from committee members (PATCH)
    public function requestMemberApproval(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        DB::transaction(function () use ($report, $accreditationRequest) {
            // Increment iteration explicitly
            $newIteration = ($report->current_iteration ?? 0) + 1;

            // Get all committee members except chair
            $committee = $accreditationRequest->committee;
            $members = $committee->acceptedMembers()->where('evaluator_id', '!=', $committee->chair_evaluator_id)->get();

            foreach ($members as $member) {
                CommitteeApproval::create([
                    'report_id' => $report->id,
                    'member_id' => $member->evaluator_id,
                    'iteration_number' => $newIteration,
                    'status' => 'pending',
                    'review_round' => 'stage8',
                ]);
            }

            $report->update([
                'current_iteration' => $newIteration,
                'status' => 'final_under_review',
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم طلب موافقة الأعضاء (المراجعة النهائية) بنجاح.');
    }

    // Member approve with signatures (POST)
    public function memberApprove(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAsMember($accreditationRequest);

        $request->validate([
            'form_6_signature' => 'required|string', // Base64 SVG data
            'final_decision_signature' => 'required|string', // Base64 SVG data
        ]);

        $report = $accreditationRequest->committeeReport;
        $evaluatorId = request()->user()->evaluator->id;

        $approval = CommitteeApproval::where('report_id', $report->id)
            ->where('member_id', $evaluatorId)
            ->where('iteration_number', $report->current_iteration)
            ->where('review_round', 'stage8')
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($request, $report, $approval, $accreditationRequest) {
            // Save form 6 final signature
            $this->saveSignature($request->form_6_signature, $report->id, $accreditationRequest->id, $approval->id, 'form_6_final');
            // Save final decision signature
            $this->saveSignature($request->final_decision_signature, $report->id, $accreditationRequest->id, $approval->id, 'form_10');

            $approval->update([
                'status' => 'approved',
                'responded_at' => now(),
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تمت الموافقة وحفظ التوقيعات الختامية بنجاح.');
    }

    // Reject approval (POST)
    public function memberReject(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAsMember($accreditationRequest);

        $request->validate([
            'reject_reasons' => 'required|array',
            'reject_reasons.*' => 'required|string|max:1000',
        ]);

        $report = $accreditationRequest->committeeReport;
        $evaluatorId = request()->user()->evaluator->id;

        $approval = CommitteeApproval::where('report_id', $report->id)
            ->where('member_id', $evaluatorId)
            ->where('iteration_number', $report->current_iteration)
            ->where('review_round', 'stage8')
            ->where('status', 'pending')
            ->firstOrFail();

        $approval->update([
            'status' => 'rejected',
            'reject_reason' => json_encode($request->reject_reasons),
            'responded_at' => now(),
        ]);

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('error', 'تم إرسال الملاحظات الختامية لرئيس اللجنة.');
    }

    // Withdraw for edit (PATCH)
    public function withdrawForEdit(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        DB::transaction(function () use ($report) {
            // Cancel pending approvals for current iteration
            CommitteeApproval::where('report_id', $report->id)
                ->where('iteration_number', $report->current_iteration)
                ->where('review_round', 'stage8')
                ->where('status', 'pending')
                ->update(['status' => 'canceled']);

            $report->update(['status' => 'returned_for_edit']);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم سحب طلب الموافقة الختامية للتعديل.');
    }

    // Final submit by chair (POST)
    public function finalSubmit(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $request->validate([
            'form_6_signature' => 'required|string', // Base64 SVG data
            'final_decision_signature' => 'required|string', // Base64 SVG data
        ]);

        $report = $accreditationRequest->committeeReport;

        // Ensure all members have approved
        $pendingOrRejectedCount = CommitteeApproval::where('report_id', $report->id)
            ->where('iteration_number', $report->current_iteration)
            ->where('review_round', 'stage8')
            ->where('status', '!=', 'approved')
            ->count();

        if ($pendingOrRejectedCount > 0) {
            return back()->with('error', 'لا يمكن الاعتماد النهائي قبل موافقة جميع أعضاء اللجنة.');
        }

        DB::transaction(function () use ($request, $report, $accreditationRequest) {
            // Save chair signatures (approval_id = null)
            $this->saveSignature($request->form_6_signature, $report->id, $accreditationRequest->id, null, 'form_6_final');
            $this->saveSignature($request->final_decision_signature, $report->id, $accreditationRequest->id, null, 'final_decision');

            $report->update([
                'status' => 'completed',
                'stage8_submitted_at' => now(),
            ]);

            $accreditationRequest->update([
                'current_stage' => 'stage_nine',
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم الاعتماد النهائي للتقرير وانتقل الطلب للمرحلة التاسعة.');
    }

    // Helper: Save SVG signature and create ReportSignature record
    private function saveSignature(string $base64Svg, int $reportId, int $requestId, ?int $approvalId, string $formType): void
    {
        if (strpos($base64Svg, 'data:image/svg+xml;base64,') === 0) {
            $base64Svg = substr($base64Svg, strpos($base64Svg, ',') + 1);
        }

        $svgContent = base64_decode($base64Svg);
        $fileName = uniqid("sig_{$formType}_").'.svg';

        $path = "req_{$requestId}/signatures/{$fileName}";

        Storage::disk('local')->put($path, $svgContent);

        ReportSignature::create([
            'report_id' => $reportId,
            'approval_id' => $approvalId,
            'form_type' => $formType,
            'signature_path' => $path,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Authorize that the current user is a committee member (not chair)
    private function authorizeAsMember(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        $isMember = $user->role === 'evaluator' &&
                    $committee &&
                    $committee->chair_evaluator_id !== $user->evaluator?->id &&
                    $committee->acceptedMembers->pluck('evaluator_id')->contains($user->evaluator?->id);

        if (! $isMember) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }
    }
}
