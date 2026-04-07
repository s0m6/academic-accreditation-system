<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

/**
 * Controller for handling Accreditation Stage Three (Self-Study Report).
 */
class StageThreeController extends Controller
{
    /**
     * Create a new draft form submission for stage three.
     */
    public function createDraft(Request $request, AccreditationRequest $accreditationRequest)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest);

        // Ensure there are no active (pending) submissions
        $hasActive = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'يوجد طلب نشط المراجعة أو معتمد مسبقاً.');
        }

        // Check if there is an existing draft
        $draftExists = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->where('status', 'draft')
            ->exists();

        if ($draftExists) {
            return back()->with('error', 'يوجد مسودة بالفعل يمكنك التعديل عليها.');
        }

        // Check if there's a previously rejected submission to copy its data
        $lastRejected = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_three')
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        $formSubmission = FormSubmission::create([
            'accreditation_request_id' => $accreditationRequest->id,
            'stage' => 'stage_three',
            'status' => 'draft',
            'form_data' => null,
            'submitted_by' => $user->id,
            'submitted_at' => null,
            'decided_by' => null,
            'decision_at' => null,
            'decision_reasons' => null,
        ]);

        return back()->with('success', 'تم إنشاء مسودة جديدة بنجاح.');
    }

    /**
     * Ensure the submission belongs to the correct request and the user is authorized.
     */
    private function ensureAuthorized(Request $request, AccreditationRequest $accreditationRequest): void
    {
        $user = $request->user();
        if ($user->role === 'program_coordinator' && $accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'غير مصرح لك بإدارة هذا الطلب.');
        }
    }
}
