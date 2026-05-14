<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationCertificate;
use App\Models\AccreditationRequest;
use App\Models\FinalDecision;
use App\Models\ReportScore;
use App\Models\Standard;
use App\Notifications\RealTimeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StageNineController extends Controller
{
    /**
     * Arabic month names for date formatting.
     */
    private const ARABIC_MONTHS = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر',
    ];

    /**
     * Issue the final accreditation decision (POST).
     * Only council_secretariat role may do this.
     */
    public function issueDecision(Request $request, AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess();

        // Guard: must be at stage nine and not already decided
        if ($accreditationRequest->current_stage !== 'stage_nine') {
            return back()->with('error', 'الطلب ليس في المرحلة التاسعة.');
        }

        if ($accreditationRequest->finalDecision()->exists()) {
            return back()->with('error', 'تم إصدار القرار النهائي مسبقاً لهذا الطلب.');
        }

        $validated = $request->validate([
            'decision_type' => ['required', 'string', 'in:'.implode(',', array_keys(FinalDecision::$decisionMeta))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $isApproval = FinalDecision::$decisionMeta[$validated['decision_type']]['approved'];
        $durationYears = FinalDecision::$decisionMeta[$validated['decision_type']]['years'];

        DB::transaction(function () use ($validated, $accreditationRequest, $isApproval, $durationYears) {
            $issuedAt = now();

            // 1. Create the final decision record
            $decision = FinalDecision::create([
                'accreditation_request_id' => $accreditationRequest->id,
                'issued_by' => request()->user()->id,
                'decision_type' => $validated['decision_type'],
                'notes' => $validated['notes'] ?? null,
                'issued_at' => $issuedAt,
            ]);

            // 2. If approval → create certificate
            if ($isApproval) {
                $program = $accreditationRequest->program;
                $university = $program->department->college->university;
                $expiresAt = $issuedAt->copy()->addYears($durationYears);

                AccreditationCertificate::create([
                    'final_decision_id' => $decision->id,
                    'certificate_number' => (string) Str::uuid(),
                    'certificate_data' => [
                        'program_name' => $program->program_name,
                        'university_name' => $university->name,
                        'achievement_level' => FinalDecision::$decisionMeta[$validated['decision_type']]['label'],
                        'decision_type' => $validated['decision_type'],
                        'duration_years' => $durationYears,
                        'issued_at' => $this->formatArabicDate($issuedAt),
                        'expires_at' => $this->formatArabicDate($expiresAt),
                        'expires_at_raw' => $expiresAt->toDateTimeString(),
                    ],
                ]);
            }

            // 3. Update request status to completed
            $accreditationRequest->update(['request_status' => 'completed']);

            // 4. Notify program coordinator
            $programCoordinator = $accreditationRequest->programCoordinator;
            if ($programCoordinator) {
                $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
                $decisionLabel = FinalDecision::$decisionMeta[$validated['decision_type']]['label'];
                $programCoordinator->notify(new RealTimeNotification(
                    title: 'صدر القرار النهائي لطلب الاعتماد',
                    message: "صدر القرار النهائي للبرنامج ({$programName}) بمستوى: {$decisionLabel}.",
                    type: $isApproval ? 'success' : 'error',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_nine'])
                ));
            }

            // 5. Notify council coordinator
            $councilCoordinator = $accreditationRequest->councilCoordinator;
            if ($councilCoordinator) {
                $programName = $accreditationRequest->program->program_name ?? 'البرنامج';
                $councilCoordinator->notify(new RealTimeNotification(
                    title: 'اكتمل طلب الاعتماد',
                    message: "تم إصدار القرار النهائي وإغلاق طلب اعتماد البرنامج ({$programName}).",
                    type: 'info',
                    actionUrl: route('requests.stage', [$accreditationRequest, 'stage_nine'])
                ));
            }
        });

        return redirect()
            ->route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_nine'])
            ->with('success', 'تم إصدار القرار النهائي بنجاح وتحويل الطلب إلى مكتمل.');
    }

    /**
     * Show the public certificate page by certificate UUID.
     */
    public function showCertificate(string $certificateNumber)
    {
        $certificate = AccreditationCertificate::where('certificate_number', $certificateNumber)
            ->with('finalDecision.accreditationRequest.program.department.college.university')
            ->firstOrFail();

        $data = $certificate->certificate_data;
        $isValid = $certificate->isValid();

        // Generate QR code URL (points back to this page)
        $certificateUrl = route('certificate.show', $certificateNumber);

        return view('public.show', compact('certificate', 'data', 'isValid', 'certificateUrl'));
    }

    /**
     * Calculate achievement level from the committee report scores (same logic as Stage 8).
     */
    public static function resolveAchievementFromReport(AccreditationRequest $accreditationRequest): array
    {
        $report = $accreditationRequest->committeeReport;
        if (! $report) {
            return ['level' => null, 'average' => null, 'suggested_type' => null];
        }

        $standards = Standard::with(['subStandards.indicators'])->orderBy('id')->get();

        $scores = ReportScore::where('report_id', $report->id)
            ->where('score_type', 'final')
            ->pluck('score', 'indicator_id');

        $grandSum = 0;
        $grandCount = 0;

        foreach ($standards as $standard) {
            foreach ($standard->subStandards as $sub) {
                foreach ($sub->indicators as $indicator) {
                    $score = $scores->get($indicator->id);
                    if (! is_null($score) && $score >= 1 && $score <= 5) {
                        $grandSum += $score;
                        $grandCount += 1;
                    }
                }
            }
        }

        $average = $grandCount > 0 ? round($grandSum / $grandCount, 2) : null;

        [$grade, $level] = self::resolveGradeAndLevel($average);

        // Map Stage 8 level to the suggested Stage 9 decision type
        $suggestedType = match ($level) {
            'محقق بامتياز' => 'approved_with_excellence',
            'محقق بإتقان' => 'approved_with_mastery',
            'محقق' => 'approved_achieved',
            'محقق جزئياً' => 'rejected_partial',
            'غير محقق' => 'rejected_not_achieved',
            default => null,
        };

        return [
            'level' => $level,
            'average' => $average,
            'grade' => $grade,
            'suggested_type' => $suggestedType,
        ];
    }

    /**
     * Resolve grade and Arabic achievement level from a numeric average (mirrors Stage 8 logic).
     */
    private static function resolveGradeAndLevel(?float $average): array
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

    /**
     * Format a Carbon date as "DD MonthName YYYY م" in Arabic.
     */
    private function formatArabicDate(Carbon $date): string
    {
        $day = $date->day;
        $month = self::ARABIC_MONTHS[$date->month];
        $year = $date->year;

        return "{$day} {$month} {$year}م";
    }

    /**
     * Authorize that only council_secretariat can issue decisions.
     */
    private function authorizeAccess(): void
    {
        if (request()->user()->role !== 'council_secretariat') {
            abort(403, 'غير مصرح لك بإصدار القرار النهائي.');
        }
    }
}
