<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\ReportScore;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StageSixController extends Controller
{
    // Show the edit view for the visit report form (Form 5).
    public function edit(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
            'current_iteration' => 1,
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
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
            'current_iteration' => 1,
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
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
            'current_iteration' => 1,
        ]);

        $standards = Standard::with(['subStandards.indicators'])
            ->orderBy('id')
            ->get();

        // Load all initial scores for this report keyed by indicator_id.
        $savedScores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'initial')
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
                ->where('score_type', 'initial')
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
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport()->firstOrCreate([
            'accreditation_request_id' => $accreditationRequest->id,
        ], [
            'status' => 'draft',
            'current_iteration' => 1,
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

    // Authorize that the current user is the committee chair evaluator.
    private function authorizeAccess(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        if (! $committee || $committee->chair_evaluator_id !== $user->evaluator?->id) {
            abort(403, 'غير مصرح لك بتعديل هذا النموذج.');
        }
    }
}
