<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\Committee;
use App\Models\Evidence;
use App\Models\FormSubmission;
use App\Models\Indicator;
use App\Models\IndicatorEvaluation;
use App\Models\Standard;
use App\Models\User;
use App\Notifications\RealTimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Controller for handling Accreditation Stage Three (Self-Study Report).
 */
class StageThreeController extends Controller
{
    // Create a new draft form submission for stage three with all indicator evaluations.
    // If a previously rejected submission exists, clone its form_data, indicator scores, and evidences.
    public function createDraft(Request $request, AccreditationRequest $accreditationRequest)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest);

        // Prevent creating a draft when a pending or approved submission already exists
        $hasActive = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'يوجد طلب نشط المراجعة أو معتمد مسبقاً.');
        }

        // Prevent duplicate drafts
        $draftExists = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->where('status', 'draft')
            ->exists();

        if ($draftExists) {
            return back()->with('error', 'يوجد مسودة بالفعل يمكنك التعديل عليها.');
        }

        // Look for the most recent rejected submission to clone its data
        $lastRejected = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        $formSubmission = DB::transaction(function () use ($accreditationRequest, $user, $lastRejected) {

            // ──────────────────────────────────────────────────────────
            // Step 1: Create the new draft, copying form_data JSON from
            //         the rejected submission if one exists.
            // ──────────────────────────────────────────────────────────
            $formSubmission = FormSubmission::create([
                'accreditation_request_id' => $accreditationRequest->id,
                'stage' => 'stage_three',
                'status' => 'draft',
                'form_data' => $lastRejected?->form_data,
                'submitted_by' => $user->id,
                'submitted_at' => null,
                'decided_by' => null,
                'decision_at' => null,
                'decision_reasons' => null,
            ]);

            if ($lastRejected) {
                // ──────────────────────────────────────────────────────
                // Step 2: Clone indicator evaluations WITH their scores
                //         from the rejected submission.
                // ──────────────────────────────────────────────────────
                $oldEvaluations = IndicatorEvaluation::where('form_submission_id', $lastRejected->id)
                    ->with('evidences')
                    ->get();

                foreach ($oldEvaluations as $oldEval) {
                    // Create a new evaluation row copying the indicator_id and score
                    $newEval = IndicatorEvaluation::create([
                        'form_submission_id' => $formSubmission->id,
                        'indicator_id' => $oldEval->indicator_id,
                        'score' => $oldEval->score,
                    ]);

                    // ──────────────────────────────────────────────────
                    // Step 3: Clone evidence records for this indicator.
                    //         The file_path points to the same physical
                    //         file — no file duplication is needed.
                    //         The saveDraft deletion logic already checks
                    //         if other records reference the same file
                    //         before physically deleting it.
                    // ──────────────────────────────────────────────────
                    foreach ($oldEval->evidences as $oldEvidence) {
                        Evidence::create([
                            'indicator_evaluation_id' => $newEval->id,
                            'file_name' => $oldEvidence->file_name,
                            'file_path' => $oldEvidence->file_path,
                        ]);
                    }
                }
            } else {
                // ──────────────────────────────────────────────────────
                // No rejected submission: fresh draft with blank scores
                // ──────────────────────────────────────────────────────
                $indicatorIds = Indicator::pluck('id');
                $evaluations = [];
                foreach ($indicatorIds as $indicatorId) {
                    $evaluations[] = [
                        'form_submission_id' => $formSubmission->id,
                        'indicator_id' => $indicatorId,
                        'score' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (! empty($evaluations)) {
                    IndicatorEvaluation::insert($evaluations);
                }
            }

            return $formSubmission;
        });

        return redirect()->route('requests.stage_three.edit', [$accreditationRequest->id, $formSubmission->id])
            ->with('success', 'تم إنشاء مسودة جديدة بنجاح، يمكنك الآن التعديل والحفظ.');
    }

    // Show the stage three edit form, loading all required data.
    public function edit(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        // Load the accreditation request hierarchy for program info
        $accreditationRequest->loadMissing('program.department.college.university');
        $program = $accreditationRequest->program;
        $dept = $program->department;
        $college = $dept->college;
        $univ = $college->university;
        $details = $program->program_details ?? [];

        // Build the program info block for the view
        $programInfo = [
            'university_name' => $univ->name,
            'university_president' => $univ->president_name,
            'college_name' => $college->name,
            'department_name' => $dept->name,
            'program_name' => $program->program_name,
            'degree_level' => $program->degree_level,
            'website_url' => $details['website_url'] ?? '',
            'establishment_date' => $details['establishment_date'] ?? '',
        ];

        // Load all 7 standards with their sub-standards and indicators
        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all indicator evaluations for this submission (keyed by indicator_id)
        $indicatorEvaluations = $formSubmission->indicatorEvaluations()
            ->with('evidences')
            ->get()
            ->keyBy('indicator_id');

        // Build indicator scores map: indicator_id => score
        $indicatorScores = $indicatorEvaluations->map(fn ($ie) => $ie->score);

        // Build evidences map: indicator_evaluation_id => [evidence, ...]
        $evidencesByEvalId = $indicatorEvaluations->mapWithKeys(function ($ie) {
            return [$ie->id => $ie->evidences];
        });

        // Build a map: indicator_id => indicator_evaluation_id (needed for evidence upload route)
        $evalIdByIndicatorId = $indicatorEvaluations->map(fn ($ie) => $ie->id);

        // Current saved form_data (null on first open)
        $formData = $formSubmission->form_data ?? [];

        return view('requests.stageThreeForm', compact(
            'accreditationRequest',
            'formSubmission',
            'programInfo',
            'standards',
            'indicatorScores',
            'evidencesByEvalId',
            'evalIdByIndicatorId',
            'formData'
        ));
    }

    // Show the stage three data (view only).
    public function show(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        // Load the accreditation request hierarchy for program info
        $accreditationRequest->loadMissing('program.department.college.university');
        $program = $accreditationRequest->program;
        $dept = $program->department;
        $college = $dept->college;
        $univ = $college->university;
        $details = $program->program_details ?? [];

        $programInfo = [
            'university_name' => $univ->name,
            'university_president' => $univ->president_name,
            'college_name' => $college->name,
            'department_name' => $dept->name,
            'program_name' => $program->program_name,
            'degree_level' => $program->degree_level,
            'website_url' => $details['website_url'] ?? '',
            'establishment_date' => $details['establishment_date'] ?? '',
        ];

        // Load all 7 standards with their sub-standards and indicators
        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all indicator evaluations for this submission
        $indicatorEvaluations = $formSubmission->indicatorEvaluations()
            ->with('evidences')
            ->get()
            ->keyBy('indicator_id');

        $indicatorScores = $indicatorEvaluations->map(fn ($ie) => $ie->score);
        $evidencesByEvalId = $indicatorEvaluations->mapWithKeys(function ($ie) {
            return [$ie->id => $ie->evidences];
        });
        $evalIdByIndicatorId = $indicatorEvaluations->map(fn ($ie) => $ie->id);

        $formData = $formSubmission->form_data ?? [];
        $readonly = true;

        return view('requests.stageThreeForm', compact(
            'accreditationRequest',
            'formSubmission',
            'programInfo',
            'standards',
            'indicatorScores',
            'evidencesByEvalId',
            'evalIdByIndicatorId',
            'formData',
            'readonly'
        ));
    }

    // Save the stage three draft: form_data JSON + indicator scores.
    public function saveDraft(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {

        $user = $request->user();
        if ($user->role !== 'program_coordinator' || $formSubmission->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Unauthorized or non-draft state'], 403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        // Decode the main form data payload
        $formInput = $request->input('form_data', []);
        $jsonData = is_string($formInput) ? (json_decode($formInput, true) ?? []) : $formInput;

        // Decode and save indicator scores
        $scoresInput = $request->input('scores', []);
        $scores = is_string($scoresInput) ? (json_decode($scoresInput, true) ?? []) : $scoresInput;

        // Find relevant evaluations for evidence mapping
        $indicatorEvaluations = $formSubmission->indicatorEvaluations()->get()->keyBy('indicator_id');

        // Decode and process submitted evidences
        $evidencesInput = $request->input('evidences', []);
        $submittedEvidences = is_string($evidencesInput) ? (json_decode($evidencesInput, true) ?? []) : $evidencesInput;

        $submittedEvidenceIds = [];

        // Perform updates and deletions inside a transaction
        DB::transaction(function () use ($accreditationRequest, $formSubmission, $scores, $indicatorEvaluations, $submittedEvidences, &$submittedEvidenceIds, $jsonData) {
            // Save indicator scores
            foreach ($scores as $indicatorId => $score) {
                IndicatorEvaluation::where('form_submission_id', $formSubmission->id)
                    ->where('indicator_id', (int) $indicatorId)
                    ->update(['score' => $score === '' || $score === null ? null : (int) $score]);
            }

            // Process submitted evidences
            foreach ($submittedEvidences as $indicatorId => $evs) {
                $ie = $indicatorEvaluations->get($indicatorId);
                if (! $ie) {
                    continue;
                }

                foreach ($evs as $evData) {
                    if (! empty($evData['id'])) {
                        // Existing evidence, keep it
                        $existing = Evidence::where('id', $evData['id'])
                            ->where('indicator_evaluation_id', $ie->id)
                            ->first();
                        if ($existing) {
                            $submittedEvidenceIds[] = $existing->id;

                            // Update the file_name if it was modified in the UI
                            if (! empty($evData['file_name']) && $existing->file_name !== $evData['file_name']) {
                                $existing->update(['file_name' => $evData['file_name']]);
                            }
                        }
                    } elseif (! empty($evData['temp_path']) && ! empty($evData['file_name'])) {
                        // New evidence from temp - Use 'local' disk explicitly
                        if (Storage::disk('local')->exists($evData['temp_path']) && str_starts_with($evData['temp_path'], 'temp_files/')) {
                            $extension = pathinfo($evData['temp_path'], PATHINFO_EXTENSION);
                            // Save into req_{id}/stagethree/ with unique name
                            $newPath = "req_{$accreditationRequest->id}/stagethree/".uniqid('ev_').'_'.time().'.'.$extension;

                            // Ensure destination directory exists on local disk
                            Storage::disk('local')->makeDirectory("req_{$accreditationRequest->id}/stagethree");
                            Storage::disk('local')->move($evData['temp_path'], $newPath);

                            $newEv = Evidence::create([
                                'indicator_evaluation_id' => $ie->id,
                                'file_name' => $evData['file_name'],
                                'file_path' => $newPath,
                            ]);
                            $submittedEvidenceIds[] = $newEv->id;
                        }
                    }
                }
            }

            // Find and delete removed evidences
            $allCurrentEvidenceIds = Evidence::whereIn('indicator_evaluation_id', $indicatorEvaluations->pluck('id'))
                ->pluck('id')
                ->toArray();

            $toDeleteIds = array_diff($allCurrentEvidenceIds, $submittedEvidenceIds);

            if (! empty($toDeleteIds)) {
                $toDelete = Evidence::whereIn('id', $toDeleteIds)->get();

                // Process each evidence record for physical and database deletion
                $toDelete->each(function (Evidence $ev) {
                    if (! Evidence::where('id', '!=', $ev->id)->where('file_path', $ev->file_path)->exists()) {
                        Storage::disk('local')->delete($ev->file_path);
                    }
                    $ev->delete();
                });
            }

            // Persist the form_data
            $formSubmission->update(['form_data' => $jsonData]);
        });

        return response()->json(['success' => true, 'message' => 'تم حفظ المسودة بنجاح']);
    }

    // Upload an individual evidence file asynchronously to a temporary location.
    public function uploadEvidenceTemp(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator' || $formSubmission->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Unauthorized or non-draft state'], 403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        // Store in temporary folder immediately so if user leaves without saving, the actual DB isn't updated
        $path = $file->storeAs(
            'temp_files',
            'temp_'.uniqid().'_'.time().'.'.$file->getClientOriginalExtension(),
            'local'
        );

        return response()->json([
            'success' => true,
            'temp_path' => $path,
            'file_name' => $request->input('file_name', $file->getClientOriginalName()),
        ]);
    }

    // View a temporary or saved evidence file securely
    public function viewFile(Request $request)
    {
        $path = $request->query('path');

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        // Basic sanity check: only allow 'temp_files/' or 'req_*/stagethree/' paths
        if (! str_starts_with($path, 'temp_files/') && ! preg_match('/^req_\d+\/stagethree\//', $path)) {
            abort(403, 'غير مصرح للوصول إلى هذا المسار');
        }

        $user = $request->user();
        // Here we could add deeper authorization, but checking role is a good start
        if (! in_array($user->role, ['program_coordinator', 'accreditation_officer', 'council_secretariat'])) {
            abort(403);
        }

        return response()->file(Storage::disk('local')->path($path));
    }

    // Submit the stage three draft to the council.
    public function submit(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        if ($formSubmission->status !== 'draft') {
            return back()->with('error', 'لا يمكن رفع طلب ليس كمسودة.');
        }

        $formSubmission->update([
            'status' => 'pending',
            'submitted_by' => $user->id,
            'submitted_at' => Carbon::now(),
        ]);

        // Notify Council Secretariat and Accreditation Officer
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer');
        $officer = $accreditationRequest->program->department->college->university->officer;
        $secretaries = User::where('role', 'council_secretariat')->get();

        if ($secretaries->isNotEmpty()) {
            Notification::send($secretaries, new RealTimeNotification(
                title: 'رفع تقرير الدراسة الذاتية',
                message: "قام منسق البرنامج برفع تقرير الدراسة الذاتية للمرحلة الثالثة للبرنامج ({$programName}).",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_three'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'رفع تقرير الدراسة الذاتية',
                message: "قام منسق البرنامج برفع تقرير الدراسة الذاتية للمرحلة الثالثة للبرنامج ({$programName}) (للمراجعة فقط).",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_three'])
            ));
        }

        return back()->with('success', 'تم رفع تقرير الدراسة الذاتية للأمانة بنجاح.');
    }

    // Validate the stage three form data before submission.
    public function validateSubmission(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $formData = $formSubmission->form_data ?? [];
        $indicatorEvaluations = $formSubmission->indicatorEvaluations()->get();
        $missing = [];

        // 1. Part One: Basic Program Data (Mandatory)
        $section1 = [
            'general' => [
                'review_date' => 'تاريخ التقييم / المراجعة',
                'review_team_head' => 'رئيس فريق المراجعة',
            ],
            'program' => [
                'executive_summary' => 'الملخص التنفيذي للبرنامج',
                'coordinator_name' => 'اسم منسق إعداد التقرير',
                'coordinator_title' => 'صفة منسق إعداد التقرير',
                'coordinator_email' => 'البريد الإلكتروني للمنسق',
                'coordinator_phone' => 'رقم هاتف المنسق',
                'report_date' => 'تاريخ إعداد التقرير (بيانات المنسق)',
            ],
            'profile' => [
                'program_mission' => 'رسالة البرنامج',
                'program_objectives_list' => 'أهداف البرنامج',
                'program_system' => 'نظام البرنامج',
                'credit_hours' => 'عدد الساعات المعتمدة',
                'courses_total' => 'عدد المقررات',
                'male_students_count' => 'عدد الطلاب (الذكور)',
                'female_students_count' => 'عدد الطلاب (الإناث)',
                'dept_council_date' => 'تاريخ اعتماد مجلس القسم',
                'college_council_date' => 'تاريخ اعتماد مجلس الكلية',
                'academic_council_date' => 'تاريخ اعتماد المجلس الأكاديمي',
                'university_council_date' => 'تاريخ اعتماد مجلس الجامعة',
                'program_history' => 'موجز عن تاريخ البرنامج',
                'env_changes' => 'التغيرات في البيئة الداخلية والخارجية',
                'self_study_arrangements' => 'ترتيبات إجراء الدراسة الذاتية',
                'comparison_methodology' => 'منهجية المقارنة الداخلية والخارجية',
            ],
        ];

        foreach ($section1 as $sec => $fields) {
            foreach ($fields as $key => $label) {
                $val = $formData[$sec][$key] ?? '';

                // Special handling for program_objectives_list (JSON string)
                if ($key === 'program_objectives_list') {
                    if (is_string($val)) {
                        $val = json_decode($val, true) ?? [];
                    }
                    $nonEmptyPoints = array_filter((array) $val, fn ($p) => ! empty(trim($p)));
                    if (empty($nonEmptyPoints)) {
                        $missing[] = "الجزء الأول: حقل «{$label}» مطلوب (يجب إدخال نقطة واحدة على الأقل).";
                    }

                    continue;
                }

                if (empty($val) || $val === '[]' || $val === '[""]') {
                    $missing[] = "الجزء الأول: حقل «{$label}» مطلوب.";
                }
            }
        }

        // 1.1 Tables Validation (Summarized warnings)
        $tables = $formData['tables'] ?? [];

        // Graduates Table (15 numeric fields + 3 year labels = 18 fields)
        $gradYears = ['last_year', 'prev_year', 'two_years_ago'];
        $grades = ['excellent', 'very_good', 'good', 'pass', 'fail'];
        $gradMissing = false;

        foreach ($gradYears as $y) {
            // 1. Check Year Label (ft_graduates_last_year_year_display)
            $labelKey = "ft_graduates_{$y}_year_display";
            $labelVal = $tables[$labelKey] ?? '';
            if (empty($labelVal) || (is_string($labelVal) && trim($labelVal) === '')) {
                $gradMissing = true;
                break;
            }

            // 2. Check Grade Values (ft_graduates_last_year_excellent)
            foreach ($grades as $g) {
                $fieldKey = "ft_graduates_{$y}_{$g}";
                $val = $tables[$fieldKey] ?? '';

                // Check if the flat key is empty
                if ($val === null || (is_string($val) && trim($val) === '')) {
                    $gradMissing = true;
                    break 2;
                }
            }
        }

        if ($gradMissing) {
            $missing[] = 'الجزء الأول: بيانات «جدول تقديرات الخريجين» غير مكتملة (يرجى التأكد من ملء جميع الأعوام والتقديرات   ).';
        }

        // Research Table (9 indicators)
        $researchKeys = [
            'intl_journals_indexed', 'arabic_journals_reviewed', 'local_journals_reviewed',
            'faculty_publications', 'faculty_textbooks', 'faculty_translated_books',
            'master_theses_discussed', 'phd_dissertations_discussed', 'conferences_workshops_organized',
        ];
        $resMissing = false;
        foreach ($researchKeys as $rk) {
            $val = $tables["res_{$rk}_count"] ?? null;
            if ($val === null || ($val === '' && $val !== 0 && $val !== '0')) {
                $resMissing = true;
                break;
            }
        }
        if ($resMissing) {
            $missing[] = 'الجزء الأول: بيانات «جدول البحث العلمي والأنشطة البحثية» غير مكتملة.';
        }

        // Facilities Table (7 indicators * 4 fields = 28 fields)
        $facilityKeys = ['classrooms', 'spec_labs', 'comp_labs', 'library', 'admin_offices', 'student_lounges', 'sports'];
        $facFields = ['count', 'area', 'students', 'hours'];
        $facMissing = false;
        foreach ($facilityKeys as $fk) {
            foreach ($facFields as $ff) {
                $val = $tables["fac_{$fk}_{$ff}"] ?? null;
                if ($val === null || ($val === '' && $val !== 0 && $val !== '0')) {
                    $facMissing = true;
                    break 2;
                }
            }
        }
        if ($facMissing) {
            $missing[] = 'الجزء الأول: بيانات «جدول المرافق التعليمية والخدمية» غير مكتملة.';
        }

        // 2. Part Two: Indicator Ratings (Mandatory)
        $unratedCount = $indicatorEvaluations->whereNull('score')->count();
        if ($unratedCount > 0) {
            $missing[] = "الجزء الثاني: يوجد عدد ({$unratedCount}) مؤشرات لم يتم تقييمها بعد.";
        }

        // 3. Part Three: Evaluations & Results (At least one point)
        $section3 = [
            'evaluations' => [
                'evaluation_procedures' => 'إجراءات التقييم',
                'evaluator_recommendations' => 'توصيات المقيمين المستقلين',
                'actions_taken' => 'الإجراءات المتخذة حيالها',
            ],
            'results' => [
                'success_aspects' => 'أبرز جوانب النجاح',
                'priority_improvements' => 'أولويات التحسين',
            ],
        ];

        foreach ($section3 as $sec => $fields) {
            foreach ($fields as $key => $label) {
                $list = $formData[$sec][$key] ?? [];
                if (! is_array($list)) {
                    $list = json_decode($list, true) ?? [];
                }
                $nonEmpty = array_filter($list, fn ($p) => ! empty(trim($p)));
                if (empty($nonEmpty)) {
                    $missing[] = "الجزء الثالث: يجب إدخال نقطة واحدة على الأقل في «{$label}».";
                }
            }
        }

        // 4. Executive Proposals (Mandatory if listed, and at least one must exist)
        $proposals = $formData['tables']['proposals'] ?? [];
        if (empty($proposals)) {
            $missing[] = 'الجزء الثالث: يجب إضافة مقترح تنفيذي واحد على الأقل في خطة التحسين.';
        } else {
            foreach ($proposals as $index => $p) {
                if (empty($p['recommendation']) || empty($p['responsible']) || empty($p['timeline']) || empty($p['resources'])) {
                    $missing[] = 'الجزء الثالث: بيانات المقترح رقم ('.($index + 1).') غير مكتملة.';
                }
            }
        }

        if (count($missing) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد بيانات ناقصة يجب إكمالها قبل الرفع.',
                'missing' => $missing,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'البيانات مكتملة وجاهزة للرفع.',
        ]);
    }

    // Reject the stage three submission with reasons.
    public function reject(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'council_secretariat') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $validated = $request->validate([
            'reasons' => 'required|array|min:1',
            'reasons.*' => 'required|string|max:500',
        ]);

        $formSubmission->update([
            'status' => 'rejected',
            'decision_reasons' => $validated['reasons'],
            'decided_by' => $user->id,
            'decision_at' => Carbon::now(),
        ]);

        // Notify Coordinator and Accreditation Officer
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer', 'programCoordinator');
        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;

        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'رفض تقرير الدراسة الذاتية',
                message: "تم رفض تقرير الدراسة الذاتية للمرحلة الثالثة للبرنامج ({$programName}). يرجى مراجعة الأسباب والتعديل.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_three'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'رفض تقرير الدراسة الذاتية',
                message: "تم رفض تقرير الدراسة الذاتية للبرنامج ({$programName}) من قبل أمانة المجلس.",
                type: 'error',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_three'])
            ));
        }

        return back()->with('success', 'تم رفض التقرير وإعادته للمنسق للتعديل.');
    }

    // Approve the stage three submission and advance to stage four.
    public function approve(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'council_secretariat') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        DB::transaction(function () use ($formSubmission, $user, $accreditationRequest) {
            $formSubmission->update([
                'status' => 'approved',
                'decided_by' => $user->id,
                'decision_at' => Carbon::now(),
            ]);

            // Advance the request to stage four
            $accreditationRequest->update([
                'current_stage' => 'stage_four',
            ]);

            // Create the evaluation committee in "forming" status
            Committee::create([
                'accreditation_request_id' => $accreditationRequest->id,
                'status' => 'forming',
                'chair_evaluator_id' => null,
            ]);
        });

        // Notify Coordinator and Accreditation Officer
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing('program.department.college.university.officer', 'programCoordinator');
        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;

        if ($coordinator) {
            $coordinator->notify(new RealTimeNotification(
                title: 'الموافقة على المرحلة الثالثة',
                message: 'تم قبول الدراسة الذاتية وسيتم خلال الفترة القادمة اختيار لجنة التقييم واشعاركم بها من أجل الموافقة عليها.',
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'الموافقة على المرحلة الثالثة',
                message: "تمت الموافقة على تقرير الدراسة الذاتية للبرنامج ({$programName}). انتقل الطلب للمرحلة الرابعة (اختيار اللجنة).",
                type: 'success',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_four'])
            ));
        }

        return back()->with('success', 'تمت الموافقة على التقرير بنجاح, لجنة التقييم (قيد التشكيل). الطلب الآن في المرحلة الرابعة.');
    }

    // Ensure the submission belongs to the correct request and the user is authorized.
    private function ensureAuthorized(Request $request, AccreditationRequest $accreditationRequest, ?FormSubmission $formSubmission = null): void
    {
        if ($formSubmission) {
            if ($formSubmission->accreditation_request_id !== $accreditationRequest->id) {
                abort(404);
            }
            if ($formSubmission->stage !== 'stage_three') {
                abort(404);
            }
        }

        $user = $request->user();
        if ($user->role === 'program_coordinator' && $accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'غير مصرح لك بإدارة هذا الطلب.');
        }
    }
}
