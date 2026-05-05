<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\ReportScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Submit the response to recommendations (Form 9).
     */
    public function submitResponse(Request $request, AccreditationRequest $accreditationRequest)
    {
        // 1. Authorize - only program coordinator who owns the request
        if ($request->user()->role !== 'program_coordinator' || $accreditationRequest->program_coord_id !== $request->user()->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        $report = $accreditationRequest->committeeReport;
        if (! $report || $report->status !== 'council_responded') {
            return back()->with('error', 'لا يمكن إرسال الرد في هذه المرحلة.');
        }

        // 2. Validate
        $request->validate([
            'response_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'response_pdf.required' => 'يرجى إرفاق ملف الرد.',
            'response_pdf.mimes' => 'يجب أن يكون الملف بصيغة PDF.',
            'response_pdf.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت.',
        ]);

        // 3. Store File with random name in the request folder
        $pdfPath = $request->file('response_pdf')->store("req_{$accreditationRequest->id}/recommendation_responses", 'local');

        // 4. Database Transaction
        DB::transaction(function () use ($accreditationRequest, $report, $pdfPath) {
            // A. Update Committee Report
            $report->update([
                'status' => 'uni_responded',
                'form9_pdf_path' => $pdfPath,
                'uni_responded_at' => now(),
                'form6_final_data' => $report->form6_initial_data, // Copy initial data to final
            ]);

            // B. Duplicate Scores (Initial -> final)
            $initialScores = ReportScore::where('report_id', $report->id)
                ->where('score_type', 'Initial')
                ->get();

            foreach ($initialScores as $score) {
                ReportScore::create([
                    'report_id' => $report->id,
                    'indicator_id' => $score->indicator_id,
                    'score' => $score->score,
                    'score_type' => 'final',
                ]);
            }

            // C. Advance Request Stage
            $accreditationRequest->update([
                'current_stage' => 'stage_eight',
            ]);
        });

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم إرسال الرد بنجاح وانتقل الطلب للمرحلة الثامنة.');
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
