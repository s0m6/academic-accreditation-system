<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeApproval;
use App\Models\ReportScore;
use App\Models\ReportSignature;
use App\Models\Standard;
use App\Models\User;
use App\Notifications\RealTimeNotification;
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

        // Load all INITIAL scores (from Stage 6) for comparison
        $initialScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $savedFormData = $report->form6_final_data ?? [];

        $isEditMode = true;

        return view('requests.stage_eight_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'initialScores',
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

        // Load all initial scores (from Stage 6) for comparison
        $initialScores = $report
            ? ReportScore::where('report_id', $report->id)
                ->where('score_type', 'Initial')
                ->pluck('score', 'indicator_id')
            : collect();

        $savedFormData = $report?->form6_final_data ?? [];

        $isEditMode = false;

        return view('requests.stage_eight_rubrics_form', compact(
            'accreditationRequest',
            'report',
            'standards',
            'savedScores',
            'initialScores',
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

    // Validate that all indicators are scored (Final assessment)
    public function validateIndicators(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            return response()->json(['valid' => false, 'nullScoredIndicators' => []]);
        }

        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        $nullScoredIndicators = [];

        foreach ($standards as $standard) {
            $missingIndicators = [];
            foreach ($standard->subStandards as $subStandard) {
                foreach ($subStandard->indicators as $indicator) {
                    if (! $savedScores->has($indicator->id) || $savedScores->get($indicator->id) === null) {
                        $missingIndicators[$subStandard->name][] = $indicator->indicator_name;
                    }
                }
            }

            if (! empty($missingIndicators)) {
                $subGroups = [];
                $totalMissing = 0;
                foreach ($missingIndicators as $subName => $inds) {
                    $subGroups[] = [
                        'sub_standard_name' => $subName,
                        'indicators' => $inds,
                    ];
                    $totalMissing += count($inds);
                }

                $nullScoredIndicators[] = [
                    'standard_name' => $standard->name,
                    'sub_groups' => $subGroups,
                    'total_missing' => $totalMissing,
                ];
            }
        }

        return response()->json([
            'valid' => empty($nullScoredIndicators),
            'nullScoredIndicators' => $nullScoredIndicators,
        ]);
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

        // Validation: Ensure all indicators are scored (final type)
        $standardsScores = $this->calculateStandardsScores($report->id);
        $isIncomplete = collect($standardsScores['standards'])->contains('has_null_indicators', true);

        if ($isIncomplete) {
            return back()->with('error', 'لا يمكن طلب موافقة الأعضاء قبل إكمال تقييم جميع المؤشرات في مقاييس تقييم البرنامج (التقييم الختامي).');
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

            // Notify committee members
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            foreach ($members as $member) {
                $memberUser = $member->evaluator->user;
                if ($memberUser) {
                    $memberUser->notify(new RealTimeNotification(
                        title: 'طلب مراجعة التقرير النهائي',
                        message: "قام رئيس اللجنة بطلب مراجعة واعتماد التقرير النهائي للبرنامج ({$programName}).",
                        type: 'info',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
                    ));
                }
            }
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

            // Notify Chair
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $chairUser = $accreditationRequest->committee->chairEvaluator->user;
            if ($chairUser) {
                $chairUser->notify(new RealTimeNotification(
                    title: 'موافقة عضو على التقرير النهائي',
                    message: "قام العضو ({$request->user()->name}) بالموافقة والتوقيع على التقرير النهائي للبرنامج ({$programName}).",
                    type: 'success',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
                ));
            }
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

        // Notify Chair
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $chairUser = $accreditationRequest->committee->chairEvaluator->user;
        if ($chairUser) {
            $chairUser->notify(new RealTimeNotification(
                title: 'رفض مسودة التقرير النهائي',
                message: "قام العضو ({$request->user()->name}) برفض مسودة التقرير النهائي للبرنامج ({$programName}) مع ذكر الملاحظات.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
            ));
        }

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('error', 'تم إرسال الملاحظات الختامية لرئيس اللجنة.');
    }

    // Withdraw for edit (PATCH)
    public function withdrawForEdit(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        DB::transaction(function () use ($report, $accreditationRequest) {
            // Cancel pending approvals for current iteration
            CommitteeApproval::where('report_id', $report->id)
                ->where('iteration_number', $report->current_iteration)
                ->where('review_round', 'stage8')
                ->where('status', 'pending')
                ->update(['status' => 'canceled']);

            // Delete any existing signatures for final report forms
            ReportSignature::where('report_id', $report->id)
                ->whereIn('form_type', ['form_6_final', 'form_10'])
                ->delete();

            $report->update(['status' => 'returned_for_edit']);

            // Notify members
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $members = $accreditationRequest->committee->acceptedMembers()->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)->get();
            foreach ($members as $member) {
                $memberUser = $member->evaluator->user;
                if ($memberUser) {
                    $memberUser->notify(new RealTimeNotification(
                        title: 'سحب طلب مراجعة نهائي',
                        message: "قام رئيس اللجنة بسحب طلب المراجعة للتعديل على التقرير النهائي للبرنامج ({$programName}).",
                        type: 'warning',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
                    ));
                }
            }
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
            $this->saveSignature($request->final_decision_signature, $report->id, $accreditationRequest->id, null, 'form_10');

            $report->update([
                'status' => 'completed',
                'stage8_submitted_at' => now(),
            ]);

            $accreditationRequest->update([
                'current_stage' => 'stage_nine',
            ]);

            // Notify Council Coordinator
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $councilCoordinator = $accreditationRequest->councilCoordinator;
            if ($councilCoordinator) {
                $councilCoordinator->notify(new RealTimeNotification(
                    title: 'مطلوب إصدار قرار نهائي',
                    message: "تم اعتماد التقرير النهائي للبرنامج ({$programName}) من قبل رئيس لجنة التقييم وانتقل الطلب للمرحلة التاسعة. يرجى مراجعة الطلب وإصدار القرار النهائي.",
                    type: 'warning',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_nine'])
                ));
            }

            // Notify committee members
            $members = $accreditationRequest->committee->acceptedMembers()
                ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
                ->get();
            foreach ($members as $member) {
                $memberUser = $member->evaluator->user;
                if ($memberUser) {
                    $memberUser->notify(new RealTimeNotification(
                        title: 'الاعتماد النهائي للتقرير',
                        message: "تم الاعتماد النهائي للتقرير للبرنامج ({$programName}) وإرساله للمجلس.",
                        type: 'success',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
                    ));
                }
            }
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم الاعتماد النهائي للتقرير وانتقل الطلب للمرحلة التاسعة.');
    }

    // Show the final report of the reviewers committee (Stage 8).
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

        // Build members data for the table/signatures
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee->chairEvaluator;
        if ($chairEvaluator) {
            // 1. Add Chair (Signature where approval_id is NULL)
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_6_final')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Add Members (Latest approved signature for stage8)
        $members = $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id);

        foreach ($members as $member) {
            // Find latest approved record for stage8
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage8')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_6_final')
                    ->latest()
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

        // Build detailed sub-standards data for the assessment table (Section 2 of Form 7).
        $detailedStandards = $this->buildDetailedStandards($report);

        return view('requests.formSevenFinal', compact(
            'accreditationRequest',
            'program',
            'department',
            'college',
            'university',
            'membersData',
            'standardsScores',
            'detailedStandards'
        ));
    }

    /**
     * Calculate per-standard scores from report_scores (final type, scores 1-5 only).
     */
    private function calculateStandardsScores(int $reportId): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all final scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $reportId)
            ->where('score_type', 'final')
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
     * Build detailed standards data for Form 7 assessment table.
     */
    private function buildDetailedStandards($report): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all final scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        // Parse form6_final_data
        $formData = $report->form6_final_data ?? [];
        $stdComments = $formData['standards'] ?? [];

        $result = [];

        foreach ($standards as $stdIndex => $standard) {
            $subRows = [];
            $stdCommentBlock = $stdComments[(string) $standard->id] ?? [];

            $allStrengths = $stdCommentBlock['strengths'] ?? [];
            $allImprovements = $stdCommentBlock['improvements'] ?? [];

            foreach ($standard->subStandards as $subStandard) {
                $subSum = 0;
                $subCount = 0;

                foreach ($subStandard->indicators as $indicator) {
                    $score = $scores->get($indicator->id);
                    if ($score !== null && $score >= 1 && $score <= 5) {
                        $subSum += $score;
                        $subCount += 1;
                    }
                }

                $subAverage = $subCount > 0 ? round($subSum / $subCount, 2) : null;

                // Filter strengths/improvements that belong to this sub-standard
                $subStrengths = array_values(array_filter($allStrengths, fn ($p) => (string) ($p['subId'] ?? '') === (string) $subStandard->id));
                $subImprovements = array_values(array_filter($allImprovements, fn ($p) => (string) ($p['subId'] ?? '') === (string) $subStandard->id));

                $subRows[] = [
                    'id' => $subStandard->id,
                    'number' => $subStandard->number ?? ($stdIndex + 1),
                    'name' => $subStandard->name,
                    'average' => $subAverage,
                    'strengths' => array_column($subStrengths, 'text'),
                    'improvements' => array_column($subImprovements, 'text'),
                ];
            }

            $result[] = [
                'id' => $standard->id,
                'number' => $standard->number ?? ($stdIndex + 1),
                'name' => $standard->name,
                'subs' => $subRows,
            ];
        }

        return $result;
    }

    /**
     * Resolve the final grade (1-5) and Arabic achievement level label
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

    // Show the final decision and recommendations of the evaluators committee (Form 10 - Stage 8).
    public function showFinalDecision(AccreditationRequest $accreditationRequest)
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

        // Calculate scores to get average and achievement level
        $standardsScores = $this->calculateStandardsScores($report->id);
        $grandAverage = $standardsScores['total']['average'];
        $achievementLevel = $standardsScores['achievement_level'];

        // Build members data for signatures (form_type = 'form_10')
        $membersData = [];

        // 1. Get Chair (Signature where approval_id is NULL)
        $chairEvaluator = $committee->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_10')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'signed_at' => $chairSig?->created_at,
                'is_chair' => true,
            ];
        }

        // 2. Add Members (Latest approved signature for stage8)
        $members = $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id);

        foreach ($members as $member) {
            // Find latest approved record for stage8
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage8')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            $signedAt = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_10')
                    ->latest()
                    ->first();
                $sigPath = $sig?->signature_path;
                $signedAt = $sig?->created_at;
            }

            $membersData[] = [
                'name' => $member->evaluator->user->name,
                'signature_path' => $sigPath,
                'signed_at' => $signedAt,
                'is_chair' => false,
            ];
        }

        return view('requests.final-decision-form', compact(
            'accreditationRequest',
            'program',
            'department',
            'college',
            'university',
            'grandAverage',
            'achievementLevel',
            'membersData'
        ));
    }

    // Show a comparison view between Stage 6 (Initial) and Stage 8 (Final) rubrics.
    public function showComparison(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest, false);

        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            return back()->with('error', 'تقرير اللجنة غير متوفر حالياً.');
        }

        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load scores
        $initialScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        $finalScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        // Narrative data
        $initialData = $report->form6_initial_data ?? [];
        $finalData = $report->form6_final_data ?? [];

        // Flatten sub-standard names for easy lookup in the view
        $subNames = $standards->flatMap->subStandards->pluck('name', 'id')->toArray();

        return view('requests.stage_eight_comparison', compact(
            'accreditationRequest',
            'report',
            'standards',
            'initialScores',
            'finalScores',
            'initialData',
            'finalData',
            'subNames'
        ));
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

    /**
     * Download the institution response to recommendations PDF (Form 9) from Stage 8.
     */
    public function downloadForm9Response(AccreditationRequest $accreditationRequest)
    {
        $user = request()->user();

        // Allow access to anyone involved in the request
        $allowed = match ($user->role) {
            'accreditation_officer', 'council_secretariat' => true,
            'program_coordinator' => $accreditationRequest->program_coord_id === $user->id,
            'council_coordinator' => $accreditationRequest->council_coord_id === $user->id,
            'evaluator' => $accreditationRequest->committee && $accreditationRequest->committee->members()
                ->where('evaluator_id', $user->evaluator->id ?? 0)
                ->where('member_status', 'accepted')
                ->exists(),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الملفات.');
        }

        $report = $accreditationRequest->committeeReport;

        if (! $report || ! $report->form9_pdf_path) {
            abort(404, 'ملف رد المؤسسة المرفق غير موجود.');
        }

        $path = $report->form9_pdf_path;

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'عذراً، الملف المطلوب غير موجود في خوادم النظام.');
        }

        $fullPath = Storage::disk('local')->path($path);
        $fileName = 'institution_response_signed_'.$accreditationRequest->id.'.pdf';

        return response()->download($fullPath, $fileName);
    }
}
