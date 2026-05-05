<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeReport;
use App\Models\ReportScore;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            // Add specific logic if needed to prevent editing in certain sub-statuses of Stage 8.
            // For now, we allow editing if they are the chair and in Stage 8.
        }
    }
}
