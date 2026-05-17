<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeApproval;
use App\Models\CommitteeReport;
use App\Models\ReportScore;
use App\Models\ReportSignature;
use App\Models\Standard;
use App\Models\User;
use App\Notifications\RealTimeNotification;
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
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        $committee = $accreditationRequest->committee;

        // Build members data for signatures (same logic as Final Report but form_type is 'form_5')
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee?->chairEvaluator;
        if ($chairEvaluator) {
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_5')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Get Members
        $members = $committee ? $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id) : collect();

        foreach ($members as $member) {
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage6')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_5')
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

        $form5Data = $report->form5_data ?? [];

        return view('requests.visit_report_show', compact(
            'accreditationRequest',
            'report',
            'program',
            'department',
            'college',
            'university',
            'membersData',
            'form5Data'
        ));
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

    // Validate that all indicators are scored (Initial assessment) and Form 5 is complete
    public function validateIndicators(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            return response()->json(['valid' => false, 'nullScoredIndicators' => [], 'form5Issues' => ['التقرير غير موجود']]);
        }

        // --- Form 6 Validation (Indicators) ---
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'initial')
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

        // --- Form 5 Validation (Visit Report) ---
        $form5Issues = [];
        $form5Info = [];
        $f5 = $report->form5_data ?? [];

        // 1. General Notes (7 points)
        $genNotes = $f5['general_notes'] ?? [];
        if (count($genNotes) < 7) {
            $form5Issues[] = 'يجب تقييم جميع النقاط السبعة في قسم الملاحظات العامة.';
        }

        // 2. Interviews (At least one)
        $interviews = $f5['interviews'] ?? [];
        $validInterviews = array_filter($interviews, fn ($i) => ! empty($i['name']) && ! empty($i['date']));
        if (empty($validInterviews)) {
            $form5Issues[] = 'يجب تسجيل مقابلة واحدة على الأقل (الاسم والتاريخ مطلوبان).';
        }

        // 3. General Results (Info only)
        $pos = count($f5['interview_positives'] ?? []);
        $neg = count($f5['interview_negatives'] ?? []);
        $form5Info[] = "النتائج العامة للمقابلات: تم تسجيل ({$pos}) إيجابيات و ({$neg}) سلبيات.";

        // 4. Site Tours (Date and Count for all)
        if (empty($f5['tours_date'])) {
            $form5Issues[] = 'تاريخ الجولات الميدانية مطلوب.';
        }
        $tours = $f5['tours'] ?? [];
        $missingTours = array_filter($tours, fn ($t) => empty($t['count']));
        if (! empty($missingTours)) {
            $form5Issues[] = 'يجب تحديد العدد لجميع المرافق في الجولات الميدانية.';
        }

        // 5. Document Review (Date and at least one row)
        if (empty($f5['docs_date'])) {
            $form5Issues[] = 'تاريخ الإطلاع على الوثائق مطلوب.';
        }
        $docPos = count($f5['docs_positives'] ?? []);
        $docNeg = count($f5['docs_negatives'] ?? []);
        if (($docPos + $docNeg) === 0) {
            $form5Issues[] = 'يجب إضافة ملاحظة واحدة على الأقل في قسم الإطلاع على الوثائق.';
        }

        return response()->json([
            'valid' => empty($nullScoredIndicators) && empty($form5Issues),
            'nullScoredIndicators' => $nullScoredIndicators,
            'form5Issues' => $form5Issues,
            'form5Info' => $form5Info,
        ]);
    }

    // Authorize that the current user is the committee chair evaluator and optionally check if report is editable.
    private function authorizeAccess(AccreditationRequest $accreditationRequest, bool $checkEditable = false): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        if (! $committee || $committee->chair_evaluator_id !== $user->evaluator?->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        // If checkEditable is true, prevent modification if not in draft or returned_for_edit status
        if ($checkEditable) {
            $report = $accreditationRequest->committeeReport;
            if ($report && ! in_array($report->status, ['draft', 'returned_for_edit'])) {
                $statusMsg = match ($report->status) {
                    'under_review' => 'لا يمكن تعديل النماذج أثناء وجود طلب موافقة معلق للأعضاء. يجب سحب الطلب أولاً في حال الرغبة بالتعديل.',
                    'submitted_to_council' => 'لا يمكن تعديل النماذج بعد رفع التقرير للمجلس.',
                    'council_responded' => 'لا يمكن تعديل النماذج بعد صدور قرار المجلس.',
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
                    'review_round' => 'stage6',
                ]);
            }

            $report->update([
                'current_iteration' => $newIteration,
                'status' => 'under_review',
            ]);

            // Notify committee members
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            foreach ($members as $member) {
                $memberUser = $member->evaluator->user;
                if ($memberUser) {
                    $memberUser->notify(new RealTimeNotification(
                        title: 'طلب مراجعة تقرير',
                        message: "قام رئيس اللجنة بطلب مراجعة واعتماد تقرير الزيارة للبرنامج ({$programName}).",
                        type: 'info',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
                    ));
                }
            }
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

        // Notify Chair
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $chairUser = $accreditationRequest->committee->chairEvaluator->user;
        if ($chairUser) {
            $chairUser->notify(new RealTimeNotification(
                title: 'رفض مسودة التقرير',
                message: "قام العضو ({$request->user()->name}) برفض مسودة التقرير للبرنامج ({$programName}) مع ذكر الملاحظات.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
            ));
        }

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six'])
            ->with('error', 'تم إرسال الرفض والملاحظات لرئيس اللجنة.');
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
                ->where('review_round', 'stage6')
                ->where('status', 'pending')
                ->update(['status' => 'canceled']);

            // Delete any existing signatures for initial report forms
            ReportSignature::where('report_id', $report->id)
                ->whereIn('form_type', ['form_5', 'form_6_initial'])
                ->delete();

            $report->update(['status' => 'returned_for_edit']);

            // Notify members
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $members = $accreditationRequest->committee->acceptedMembers()->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)->get();
            foreach ($members as $member) {
                $memberUser = $member->evaluator->user;
                if ($memberUser) {
                    $memberUser->notify(new RealTimeNotification(
                        title: 'سحب طلب مراجعة',
                        message: "قام رئيس اللجنة بسحب طلب المراجعة للتعديل على التقرير الخاص بالبرنامج ({$programName}).",
                        type: 'warning',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
                    ));
                }
            }
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

            // Notify Chair
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $chairUser = $accreditationRequest->committee->chairEvaluator->user;
            if ($chairUser) {
                $chairUser->notify(new RealTimeNotification(
                    title: 'موافقة عضو على التقرير',
                    message: "قام العضو ({$request->user()->name}) بالموافقة والتوقيع على تقرير البرنامج ({$programName}).",
                    type: 'success',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
                ));
            }
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

            // Notify Council Coordinator
            $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
            $councilCoordinator = $accreditationRequest->councilCoordinator;
            if ($councilCoordinator) {
                $councilCoordinator->notify(new RealTimeNotification(
                    title: 'رفع التقرير النهائي للزيارة',
                    message: "تم رفع التقرير النهائي للزيارة للبرنامج ({$programName}) من قبل رئيس اللجنة.",
                    type: 'info',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
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
                        title: 'رفع التقرير للمجلس',
                        message: "تم رفع التقرير النهائي للزيارة للبرنامج ({$programName}) إلى المجلس بنجاح.",
                        type: 'success',
                        actionUrl: route('requests.stage', [$accreditationRequest, 'stage_six'])
                    ));
                }
            }
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

        // Notify stakeholders
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer', 'programCoordinator');
        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;

        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'صدور خطاب التوصيات',
                message: "تم صدور خطاب توصيات لجنة التقييم للبرنامج ({$programName}). يرجى الاطلاع والرد في المرحلة السابعة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_seven'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'صدور خطاب التوصيات',
                message: "تم صدور خطاب توصيات لجنة التقييم للبرنامج ({$programName}). انتقل الطلب للمرحلة السابعة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_seven'])
            ));
        }

        // Notify Chair and Committee Members
        $chair = $accreditationRequest->committee->chairEvaluator->user ?? null;
        if ($chair) {
            $chair->notify(new RealTimeNotification(
                title: 'صدور خطاب التوصيات',
                message: "تم صدور خطاب توصيات المجلس للبرنامج ({$programName}). انتقل الطلب للمرحلة السابعة لانتظار رد الجامعة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_seven'])
            ));
        }

        $members = $accreditationRequest->committee->acceptedMembers()
            ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
            ->get();
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $memberUser->notify(new RealTimeNotification(
                    title: 'صدور خطاب التوصيات',
                    message: "تم صدور خطاب توصيات المجلس للبرنامج ({$programName}). بانتظار رد الجامعة في المرحلة السابعة.",
                    type: 'info',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_seven'])
                ));
            }
        }

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

    // Show the recommendations letter (Form 8).
    public function showRecommendationsLetter(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $program = $accreditationRequest->program;
        $department = $program->department;
        $college = $department->college;
        $university = $college->university;

        // Build detailed standards data for the assessment table.
        $detailedStandards = $this->buildDetailedStandards($report);

        return view('requests.eightForm', compact(
            'accreditationRequest',
            'program',
            'department',
            'college',
            'university',
            'detailedStandards'
        ));
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

        // Build members data for the table/signatures
        $membersData = [];

        // 1. Get Chair
        $chairEvaluator = $committee->chairEvaluator;
        if ($chairEvaluator) {
            // 1. Add Chair (Signature where approval_id is NULL)
            $chairSig = ReportSignature::where('report_id', $report->id)
                ->whereNull('approval_id')
                ->where('form_type', 'form_6_initial')
                ->latest()
                ->first();

            $membersData[] = [
                'name' => $chairEvaluator->user->name,
                'signature_path' => $chairSig?->signature_path,
                'is_chair' => true,
            ];
        }

        // 2. Add Members (Latest approved signature for stage6)
        $members = $committee->activeMembers->filter(fn ($m) => $m->evaluator_id !== $committee->chair_evaluator_id);

        foreach ($members as $member) {
            // Find latest approved record for stage6
            $latestApproval = CommitteeApproval::where('report_id', $report->id)
                ->where('member_id', $member->evaluator_id)
                ->where('review_round', 'stage6')
                ->where('status', 'approved')
                ->latest()
                ->first();

            $sigPath = null;
            if ($latestApproval) {
                $sig = ReportSignature::where('approval_id', $latestApproval->id)
                    ->where('form_type', 'form_6_initial')
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

        return view('requests.formSeven', compact(
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
     * Build detailed standards data for Form 7 assessment table.
     *
     * Returns an array of standards, each with their sub-standards including:
     *   - sub-standard name & number
     *   - average score (indicators 1-5 only; 0 = non-compliant, excluded)
     *   - strengths  (from form6_initial_data JSON, filtered by sub-standard id)
     *   - improvements (from form6_initial_data JSON, filtered by sub-standard id)
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildDetailedStandards(CommitteeReport $report): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all initial scores for this report keyed by indicator_id.
        $scores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        // Parse form6_initial_data – structure: { standards: { [stdId]: { strengths: [{text, subId}], improvements: [{text, subId}] } } }
        $formData = $report->form6_initial_data ?? [];
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
                    // 0 (non-compliant) is excluded from average per business rules
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
