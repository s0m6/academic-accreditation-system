<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Http\Request;

class StageSixController extends Controller
{
    /**
     * Show the edit view for the visit report form.
     */
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

    /**
     * Show the read-only view for the visit report form.
     */
    public function show(AccreditationRequest $accreditationRequest)
    {
        $report = $accreditationRequest->committeeReport;
        $isEditMode = false;

        return view('requests.stage_six_visit_report', compact('accreditationRequest', 'report', 'isEditMode'));
    }

    /**
     * Save the visit report form data as JSON.
     */
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

    /**
     * Authorize that the current user is the committee chair.
     */
    private function authorizeAccess(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();
        $committee = $accreditationRequest->committee;

        if (!$committee || $committee->chair_evaluator_id !== $user->evaluator?->id) {
            abort(403, 'غير مصرح لك بتعديل هذا النموذج.');
        }
    }
}
