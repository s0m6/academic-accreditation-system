<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\Evidence;
use App\Models\FormSubmission;
use App\Models\Indicator;
use App\Models\IndicatorEvaluation;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($accreditationRequest, $user, $lastRejected) {

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
        });

        return back()->with('success', 'تم إنشاء مسودة جديدة بنجاح.');
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
                            if (!empty($evData['file_name']) && $existing->file_name !== $evData['file_name']) {
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
                            Storage::disk('local')->copy($evData['temp_path'], $newPath);

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

        return back()->with('success', 'تم رفع تقرير الدراسة الذاتية للأمانة بنجاح.');
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

        $formSubmission->update([
            'status' => 'approved',
            'decided_by' => $user->id,
            'decision_at' => Carbon::now(),
        ]);

        // Advance the request to stage four
        $accreditationRequest->update([
            'current_stage' => 'stage_four',
        ]);

        return back()->with('success', 'تمت الموافقة على التقرير بنجاح. الطلب الآن في المرحلة الرابعة.');
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
