<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Support\Facades\Storage;

class StageSevenController extends Controller
{
    /**
     * View the recommendations letter PDF in the browser.
     */
    public function viewRecommendations(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        if (! $report || ! $report->form8_pdf_path) {
            abort(404, 'خطاب التوصيات غير موجود.');
        }

        $path = $report->form8_pdf_path;

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'عذراً، الملف المطلوب غير موجود في خوادم النظام.');
        }

        $fullPath = Storage::disk('local')->path($path);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recommendations_letter.pdf"',
        ]);
    }

    /**
     * Download the recommendations letter PDF.
     */
    public function downloadRecommendations(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        if (! $report || ! $report->form8_pdf_path) {
            abort(404, 'خطاب التوصيات غير موجود.');
        }

        $path = $report->form8_pdf_path;

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'عذراً، الملف المطلوب غير موجود في خوادم النظام.');
        }

        $fullPath = Storage::disk('local')->path($path);
        $fileName = 'recommendations_letter_'.$accreditationRequest->id.'.pdf';

        return response()->download($fullPath, $fileName);
    }

    /**
     * Ensure the user is authorized to access this request's files.
     */
    private function authorizeAccess(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();

        // Allowed roles: Program Coordinator (if owner), Council Coordinator (if owner), Council Secretariat, Accreditation Officer
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
    }
}
