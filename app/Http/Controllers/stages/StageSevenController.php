<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\CommitteeReport;
use App\Models\ReportScore;
use App\Models\Standard;
use App\Notifications\RealTimeNotification;
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
     * Show the editable Form 9 (Response to Recommendations) for program coordinator.
     */
    public function editForm9(AccreditationRequest $accreditationRequest)
    {
        $user = request()->user();

        if ($user->role !== 'program_coordinator' || $accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        $report = $accreditationRequest->committeeReport;

        if (! $report || $report->status !== 'council_responded') {
            abort(403, 'لا يمكن تعديل النموذج في هذه المرحلة.');
        }

        $detailedStandards = $this->buildDetailedStandards($report);
        $savedForm9Data = $report->form9_data ?? [];
        $isEditMode = true;

        return view('requests.recommendations-feedback-form', compact(
            'accreditationRequest',
            'report',
            'detailedStandards',
            'savedForm9Data',
            'isEditMode'
        ));
    }

    /**
     * Show the read-only Form 9 (Response to Recommendations).
     */
    public function showForm9(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $report = $accreditationRequest->committeeReport;

        if (! $report) {
            abort(404, 'التقرير غير موجود.');
        }

        $detailedStandards = $this->buildDetailedStandards($report);
        $savedForm9Data = $report->form9_data ?? [];
        $isEditMode = false;

        return view('requests.recommendations-feedback-form', compact(
            'accreditationRequest',
            'report',
            'detailedStandards',
            'savedForm9Data',
            'isEditMode'
        ));
    }

    /**
     * Save Form 9 JSON data into committee_reports.form9_data.
     */
    public function saveForm9(Request $request, AccreditationRequest $accreditationRequest)
    {
        $user = $request->user();

        if ($user->role !== 'program_coordinator' || $accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'غير مصرح لك بإجراء هذه العملية.');
        }

        $report = $accreditationRequest->committeeReport;

        if (! $report || $report->status !== 'council_responded') {
            return response()->json(['success' => false, 'message' => 'لا يمكن حفظ البيانات في هذه المرحلة.'], 403);
        }

        $data = json_decode($request->input('form9_data', '[]'), true);

        if (! is_array($data)) {
            return response()->json(['success' => false, 'message' => 'بيانات غير صالحة.'], 422);
        }

        $report->update(['form9_data' => $data]);

        return response()->json(['success' => true, 'message' => 'تم حفظ البيانات بنجاح.']);
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

        // Notify stakeholders
        $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
        $accreditationRequest->loadMissing([
            'program.department.college.university.officer',
            'councilCoordinator',
            'committee.chairEvaluator.user',
            'committee.acceptedMembers.evaluator.user',
        ]);

        $councilCoordinator = $accreditationRequest->councilCoordinator;
        $officer = $accreditationRequest->program->department->college->university->officer;
        $chair = $accreditationRequest->committee->chairEvaluator->user ?? null;

        if ($councilCoordinator) {
            $councilCoordinator->notify(new RealTimeNotification(
                title: 'رفع الرد على التوصيات',
                message: "قام منسق البرنامج برفع الرد على التوصيات (نموذج 9) للبرنامج ({$programName}).",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
            ));
        }

        if ($officer) {
            $officer->notify(new RealTimeNotification(
                title: 'رفع الرد على التوصيات',
                message: "قام منسق البرنامج برفع الرد على التوصيات للبرنامج ({$programName}). انتقل الطلب للمرحلة الثامنة.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
            ));
        }

        if ($chair) {
            $chair->notify(new RealTimeNotification(
                title: 'وصول رد البرنامج على التوصيات',
                message: "قام منسق برنامج ({$programName}) بالرد على التوصيات. يمكنك الآن البدء في إعداد التقرير النهائي.",
                type: 'info',
                actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
            ));
        }

        // Notify other committee members
        $members = $accreditationRequest->committee->acceptedMembers()
            ->where('evaluator_id', '!=', $accreditationRequest->committee->chair_evaluator_id)
            ->get();
        foreach ($members as $member) {
            $memberUser = $member->evaluator->user;
            if ($memberUser) {
                $memberUser->notify(new RealTimeNotification(
                    title: 'وصول رد البرنامج على التوصيات',
                    message: "قام منسق برنامج ({$programName}) بالرد على التوصيات. انتقل الطلب للمرحلة الثامنة للمشاركة في التقرير النهائي.",
                    type: 'info',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_eight'])
                ));
            }
        }

        return redirect()->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight'])
            ->with('success', 'تم إرسال الرد بنجاح وانتقل الطلب للمرحلة الثامنة.');
    }

    /**
     * Build detailed standards data for Form 9, reusing the same
     * calculation logic as StageSixController::buildDetailedStandards().
     *
     * Returns per-standard array with sub-standards containing:
     *   - id, number, name
     *   - average score (indicators 1-5 only)
     *   - improvements (from form6_initial_data JSON)
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildDetailedStandards(CommitteeReport $report): array
    {
        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        // Load all initial scores keyed by indicator_id.
        $scores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'Initial')
            ->pluck('score', 'indicator_id');

        // Parse form6_initial_data JSON structure:
        // { standards: { [stdId]: { improvements: [{text, subId}] } } }
        $formData = $report->form6_initial_data ?? [];
        $stdComments = $formData['standards'] ?? [];

        $result = [];

        foreach ($standards as $stdIndex => $standard) {
            $subRows = [];
            $stdCommentBlock = $stdComments[(string) $standard->id] ?? [];
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
                    // Score 0 (non-compliant) excluded from average per business rules
                }

                $subAverage = $subCount > 0 ? round($subSum / $subCount, 2) : null;

                // Filter improvement points that belong to this sub-standard
                $subImprovements = array_values(
                    array_filter($allImprovements, fn ($p) => (string) ($p['subId'] ?? '') === (string) $subStandard->id)
                );

                $subRows[] = [
                    'id' => $subStandard->id,
                    'number' => $subStandard->number ?? ($stdIndex + 1),
                    'name' => $subStandard->name,
                    'average' => $subAverage,
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
