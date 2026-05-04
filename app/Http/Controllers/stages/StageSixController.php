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

class StageSixController extends Controller
{
    // Show the edit view for the visit report form (Form 5).
    public function edit(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
        ]);

        $isEditMode = true;

        return view('requests.stage_six_visit_report', compact('accreditationRequest', 'report', 'isEditMode'));
    }

    // Show the read-only view for the visit report form (Form 5).
    public function show(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        $isEditMode = false;

        return view('requests.stage_six_visit_report', compact('accreditationRequest', 'report', 'isEditMode'));
    }

    // Save the visit report form data as JSON into form5_data.
    public function save(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
        ]);

        $data = json_decode($request->input('report_data', '{}'), true);

        $report->update([
            'form5_data' => $data,
        ]);

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('success', 'تم حفظ النموذج بنجاح.');
    }

    // Show the edit view for the program evaluation rubrics form (Form 6).
    public function editRubrics(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
        ]);

        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all initial scores for this report keyed by indicator_id.
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $savedFormData = $report->form6_initial_data ?? [];

        $isEditMode = true;

        return view('requests.stage_six_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'savedFormData',
            'isEditMode'
        ));
    }

    // Show the read-only view for the program evaluation rubrics form (Form 6).
    public function showRubrics(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;

        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all initial scores keyed by indicator_id.
        $savedScores = $report
            ? ReportScore::where('report_id', $report->id)
                ->where('score_type', 'Initial')
                ->pluck('score', 'indicator_id')
            : collect();

        $savedFormData = $report?->form6_initial_data ?? [];

        $isEditMode = false;

        return view('requests.stage_six_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'savedFormData',
            'isEditMode'
        ));
    }

    // Save the rubrics form: indicator scores into report_scores and text comments into form6_initial_data JSON.
    public function saveRubrics(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, true);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
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
            // Upsert indicator scores into report_scores with score_type = 'initial'.
            foreach ($scores as $indicatorId => $score) {
                ReportScore::updateOrCreate(
                    [
                        'report_id' => $report->id,
                        'indicator_id' => (int) $indicatorId,
                        'score_type' => 'initial',
                    ],
                    [
                        'score' => ($score === '' || $score === null) ? null : (int) $score,
                    ]
                );
            }

            // Persist the structured text comments (strengths, improvements, priorities) as JSON.
            $report->update([
                'form6_initial_data' => $formData,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'تم حفظ التغييرات بنجاح.']);
    }

    // Authorize that the current user is the committee chair evaluator and optionally check if report is editable.
    private function authorizeAccess(AccreditationRequest $accreditationRequest, bool $checkEditable = false): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        if (! $committee || $committee->chair_evaluator_id !== $user->evaluator?->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        // If checkEditable is true, prevent modification if already under review by members
        if ($checkEditable) {
            $report = $accreditationRequest->committeeReport;
            if ($report && $report->status === 'under_review') {
                abort(403, 'لا يمكن تعديل النماذج أثناء وجود طلب موافقة معلق للأعضاء. يجب سحب الطلب أولاً في حال الرغبة بالتعديل.');
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
                    'review_round' => 'stage6',
                ]);
            }

            $report->update([
                'current_iteration' => $newIteration,
                'status' => 'under_review',
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('success', 'تم طلب موافقة الأعضاء بنجاح.');
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
            ->where('review_round', 'stage6')
            ->where('status', 'pending')
            ->firstOrFail();

        $approval->update([
            'status' => 'rejected',
            'reject_reason' => json_encode($request->reject_reasons), // Save as JSON string
            'responded_at' => now(),
        ]);

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('error', 'تم إرسال الرفض والملاحظات لرئيس اللجنة.');
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
                ->where('review_round', 'stage6')
                ->where('status', 'pending')
                ->update(['status' => 'canceled']);

            $report->update(['status' => 'returned_for_edit']);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('success', 'تم سحب الطلب للتعديل.');
    }

    // Member approve with signatures (POST)
    public function memberApprove(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAsMember($accreditationRequest);

        $request->validate([
            'form_5_signature' => 'required|string', // Base64 SVG data
            'form_6_signature' => 'required|string', // Base64 SVG data
        ]);

        $report = $accreditationRequest->committeeReport;
        $evaluatorId = request()->user()->evaluator->id;

        $approval = CommitteeApproval::where('report_id', $report->id)
            ->where('member_id', $evaluatorId)
            ->where('iteration_number', $report->current_iteration)
            ->where('review_round', 'stage6')
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($request, $report, $approval, $accreditationRequest) {
            // Save form 5 signature
            $this->saveSignature($request->form_5_signature, $report->id, $accreditationRequest->id, $approval->id, 'form_5');
            // Save form 6 initial signature
            $this->saveSignature($request->form_6_signature, $report->id, $accreditationRequest->id, $approval->id, 'form_6_initial');

            $approval->update([
                'status' => 'approved',
                'responded_at' => now(),
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('success', 'تمت الموافقة وحفظ التوقيعات بنجاح.');
    }

    // Submit to council by chair (POST)
    public function submitToCouncil(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $request->validate([
            'form_5_signature' => 'required|string', // Base64 SVG data
            'form_6_signature' => 'required|string', // Base64 SVG data
        ]);

        $report = $accreditationRequest->committeeReport;

        // Ensure all members have approved
        $pendingOrRejectedCount = CommitteeApproval::where('report_id', $report->id)
            ->where('iteration_number', $report->current_iteration)
            ->where('review_round', 'stage6')
            ->where('status', '!=', 'approved')
            ->count();

        if ($pendingOrRejectedCount > 0) {
            return back()->with('error', 'لا يمكن الرفع للمجلس قبل موافقة جميع أعضاء اللجنة.');
        }

        DB::transaction(function () use ($request, $report, $accreditationRequest) {
            // Save chair signatures (approval_id = null)
            $this->saveSignature($request->form_5_signature, $report->id, $accreditationRequest->id, null, 'form_5');
            $this->saveSignature($request->form_6_signature, $report->id, $accreditationRequest->id, null, 'form_6_initial');

            $report->update([
                'status' => 'submitted_to_council',
                'current_iteration' => 0, // Reset for stage 8
                'stage6_submitted_at' => now(), // Submission timestamp
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('success', 'تم رفع التقرير للمجلس بنجاح.');
    }

    // Council coordinator uploads recommendations letter (POST)
    public function uploadRecommendations(Request $request, AccreditationRequest $accreditationRequest)
    {
        // Authorize council coordinator
        if (request()->user()->role !== 'council_coordinator' || request()->user()->id !== $accreditationRequest->council_coord_id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        $report = $accreditationRequest->committeeReport;
        if (! $report || $report->status !== 'submitted_to_council') {
            return back()->with('error', 'لا يمكن رفع الخطاب في هذه الحالة.');
        }

        $validated = $request->validate([
            'recommendations_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $pdfPath = $validated['recommendations_pdf']->store("req_{$accreditationRequest->id}/council", 'local');

        DB::transaction(function () use ($accreditationRequest, $report, $pdfPath) {
            $report->update([
                'status' => 'council_responded',
                'form8_pdf_path' => $pdfPath,
                'council_responded_at' => now(),
            ]);

            $accreditationRequest->update([
                'current_stage' => 'stage_seven',
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_seven'])
            ->with('success', 'تم رفع خطاب التوصيات بنجاح وانتقل الطلب للمرحلة السابعة.');
    }

    // Helper: Save SVG signature and create ReportSignature record
    private function saveSignature(string $base64Svg, int $reportId, int $requestId, ?int $approvalId, string $formType): void
    {
        // Extract the actual SVG content from the base64 data URI (data:image/svg+xml;base64,...)
        if (strpos($base64Svg, 'data:image/svg+xml;base64,') === 0) {
            $base64Svg = substr($base64Svg, strpos($base64Svg, ',') + 1);
        }

        $svgContent = base64_decode($base64Svg);
        $fileName = uniqid("sig_{$formType}_").'.svg';

        // Path matches user request: req_{id}/signatures/
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

    // Show the final report of the reviewers committee (Form 7).
    public function showFinalReport(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        $membersData = [];

        // Determine the relevant iteration
        $currentIteration = $report->current_iteration;
        // If submitted to council, current_iteration might be reset to 0, so find the last one for stage 6
        if ($currentIteration == 0 && in_array($report->status, ['submitted_to_council', 'council_responded', 'uni_responded', 'final_under_review', 'completed'])) {
            $currentIteration = CommitteeApproval::where('report_id', $report->id)
                ->where('review_round', 'stage6')
                ->max('iteration_number') ?? 0;
        }

        // 1. Get Chair
        $chairEvaluator = $committee->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->where('form_type', 'form_6_initial')
                ->whereNull('approval_id')
                ->first();

            // Only show signature if not in draft/withdrawn state
            $showChairSig = ! in_array($report->status, ['draft', 'returned_for_edit']);

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $showChairSig ? $chairSig?->signature_path : null,
                'is_chair' => true,
            ];
        }

        // 2. Get ALL Accepted Members (excluding chair)
        $committeeMembers = $committee->acceptedMembers()
            ->where('evaluator_id', '!=', $committee->chair_evaluator_id)
            ->get();

        $canShowMemberSigs = ! in_array($report->status, ['draft', 'returned_for_edit']);

        foreach ($committeeMembers as $member) {
            $sigPath = null;

            if ($canShowMemberSigs) {
                // Find the signature for this member for Form 6 Initial IN THE CURRENT ITERATION
                $sig = ReportSignature::where('report_signatures.report_id', $report->id)
                    ->where('report_signatures.form_type', 'form_6_initial')
                    ->join('committee_approvals', 'report_signatures.approval_id', '=', 'committee_approvals.id')
                    ->where('committee_approvals.member_id', $member->evaluator_id)
                    ->where('committee_approvals.status', 'approved')
                    ->where('committee_approvals.review_round', 'stage6')
                    ->where('committee_approvals.iteration_number', $currentIteration)
                    ->select('report_signatures.*')
                    ->first();

                $sigPath = $sig?->signature_path;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'is_chair' => false,
            ];
        }

        $standardsScores = $this->calculateStandardsScores($report->id);

        return view('requests.formSeven', compact(
            'accreditationRequest',
            'program',
            'department',
            'college',
            'university',
            'membersData',
            'standardsScores'
        ));
    }

    /**
     * Calculate per-standard scores from report_scores (initial type, scores 1-5 only).
     *
     * Returns an array with:
     *   'standards' => [ ['id', 'name', 'sum', 'count', 'average', 'has_null_indicators'], ... ]
     *   'total'     => [ 'sum', 'count', 'average' ]
     *   'final_grade'         => int (1-5)
     *   'achievement_level'   => string (Arabic label)
     *
     * @return array<string, mixed>
     */
    private function calculateStandardsScores(int $reportId): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all initial scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $reportId)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $standardRows = [];
        $grandSum = 0;
        $grandCount = 0;

        foreach ($standards as $standard) {
            $stdSum = 0;
            $stdCount = 0;
            $hasNullIndicators = false;

            foreach ($standard->subStandards as $sub) {
                foreach ($sub->indicators as $indicator) {
                    $score = $scores->get($indicator->id); // null if not scored yet

                    if (is_null($score)) {
                        // Indicator exists but has no score → incomplete
                        $hasNullIndicators = true;
                    } elseif ($score >= 1 && $score <= 5) {
                        // Valid score 1-5: include in calculation
                        $stdSum += $score;
                        $stdCount += 1;
                    }
                    // Score 0 (non-compliant) is excluded from calculation per business rules
                }
            }

            $stdAverage = $stdCount > 0 ? round($stdSum / $stdCount, 2) : null;

            $standardRows[] = [
                'id' => $standard->id,
                'name' => $standard->name,
                'sum' => $stdSum,
                'count' => $stdCount,
                'average' => $stdAverage,
                'has_null_indicators' => $hasNullIndicators,
            ];

            $grandSum += $stdSum;
            $grandCount += $stdCount;
        }

        $grandAverage = $grandCount > 0 ? round($grandSum / $grandCount, 2) : null;

        // Determine final grade & achievement level using the specified rounding rules
        [$finalGrade, $achievementLevel] = $this->resolveGradeAndLevel($grandAverage);

        return [
            'standards' => $standardRows,
            'total' => [
                'sum' => $grandSum,
                'count' => $grandCount,
                'average' => $grandAverage,
            ],
            'final_grade' => $finalGrade,
            'achievement_level' => $achievementLevel,
        ];
    }

    /**
     * Resolve the final grade (1-5) and Arabic achievement level label
     * based on the grand average using the specified rounding thresholds.
     *
     * @return array{0: int|null, 1: string}
     */
    private function resolveGradeAndLevel(?float $average): array
    {
        if ($average === null) {
            return [null, '—'];
        }

        if ($average >= 4.5) {
            return [5, 'محقق بامتياز'];
        }

        if ($average >= 3.5) {
            return [4, 'محقق بإتقان'];
        }

        if ($average >= 2.5) {
            return [3, 'محقق'];
        }

        if ($average >= 1.5) {
            return [2, 'محقق جزئياً'];
        }

        return [1, 'غير محقق'];
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
