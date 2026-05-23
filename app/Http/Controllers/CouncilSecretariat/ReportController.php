<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Models\AccreditationCertificate;
use App\Models\AccreditationRequest;
use App\Models\Evaluator;
use App\Models\FinalDecision;
use App\Models\Indicator;
use App\Models\Program;
use App\Models\Standard;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard index page.
     */
    public function index(Request $request): View
    {
        // Fetch quick numbers for report cards
        $totalUniversities = University::count();
        $totalEvaluators = Evaluator::count();
        $totalRequests = AccreditationRequest::count();
        $totalCertificates = AccreditationCertificate::where('is_active', true)->count();

        // Universities list for dropdown filter
        $universities = University::orderBy('name')->get();

        // Get unique general specialties for filter dropdown
        $specialties = Evaluator::whereNotNull('general_specialty')
            ->where('general_specialty', '!=', '')
            ->distinct()
            ->orderBy('general_specialty')
            ->pluck('general_specialty');

        return view('council_secretariat.reports.index', compact(
            'totalUniversities',
            'totalEvaluators',
            'totalRequests',
            'totalCertificates',
            'universities',
            'specialties'
        ));
    }

    /**
     * Generate and download the requested PDF report.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => ['required', 'string', 'in:university_status,evaluator_stats,issued_decisions,general_summary,criteria_analysis'],
            'university_id' => ['nullable', 'integer', 'exists:universities,id'],
            'current_stage' => ['nullable', 'string'],
            'request_status' => ['nullable', 'string'],
            'specialty' => ['nullable', 'string'],
            'academic_rank' => ['nullable', 'string'],
            'decision_type' => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $reportType = $request->input('report_type');

        try {
            switch ($reportType) {
                case 'university_status':
                    return $this->generateUniversityStatusReport($request);
                case 'evaluator_stats':
                    return $this->generateEvaluatorStatsReport($request);
                case 'issued_decisions':
                    return $this->generateIssuedDecisionsReport($request);
                case 'general_summary':
                    return $this->generateGeneralSummaryReport();
                case 'criteria_analysis':
                    return $this->generateCriteriaAnalysisReport();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء توليد ملف التقرير: '.$e->getMessage());
        }

        return back()->with('error', 'نوع التقرير غير مدعوم.');
    }

    /**
     * Generate University Status PDF report.
     */
    private function generateUniversityStatusReport(Request $request)
    {
        $query = AccreditationRequest::with(['program.department.college.university']);

        // Apply filters
        if ($request->filled('university_id')) {
            $query->whereHas('program.department.college.university', function ($q) use ($request) {
                $q->where('id', $request->input('university_id'));
            });
        }

        if ($request->filled('current_stage') && $request->input('current_stage') !== 'all') {
            $query->where('current_stage', $request->input('current_stage'));
        }

        if ($request->filled('request_status') && $request->input('request_status') !== 'all') {
            $query->where('request_status', $request->input('request_status'));
        }

        $requests = $query->get();

        // Sort requests by university name for consistent ordering
        $requests = $requests->sortBy(function ($req) {
            return optional($req->program->department->college->university)->name;
        })->values();

        // Calculate KPI values
        $totalRequestsCount = $requests->count();
        $activeRequestsCount = $requests->where('request_status', 'Active')->count();
        $completedRequestsCount = $requests->where('request_status', 'completed')->count();
        $draftRequestsCount = $requests->where('request_status', 'draft')->count();

        $pdf = Pdf::view('print_templates.reports.university_status', compact(
            'requests',
            'totalRequestsCount',
            'activeRequestsCount',
            'completedRequestsCount',
            'draftRequestsCount'
        ))
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->waitUntilNetworkIdle();
            })
            ->name('تقرير_حالة_الاعتماد_للجامعات_'.Carbon::now()->format('YmdHis').'.pdf');

        return $pdf->download();
    }

    /**
     * Generate Evaluator Stats PDF report.
     */
    private function generateEvaluatorStatsReport(Request $request)
    {
        $query = Evaluator::with(['user', 'city', 'currentUniversity'])
            ->withCount(['committeeMemberships as active_memberships_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('specialty') && $request->input('specialty') !== 'all') {
            $query->where('general_specialty', $request->input('specialty'));
        }

        if ($request->filled('academic_rank') && $request->input('academic_rank') !== 'all') {
            $query->where('academic_rank', $request->input('academic_rank'));
        }

        $evaluators = $query->get();

        // Stats
        $totalEvaluatorsCount = $evaluators->count();
        $activeEvaluatorsInCommittees = $evaluators->where('active_memberships_count', '>', 0)->count();
        $totalCitiesCount = $evaluators->pluck('city_id')->unique()->count();

        $pdf = Pdf::view('print_templates.reports.evaluator_stats', compact(
            'evaluators',
            'totalEvaluatorsCount',
            'activeEvaluatorsInCommittees',
            'totalCitiesCount'
        ))
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->waitUntilNetworkIdle();
            })
            ->name('تقرير_بيانات_المقيمين_'.Carbon::now()->format('YmdHis').'.pdf');

        return $pdf->download();
    }

    /**
     * Generate Issued Decisions and Certificates PDF report.
     */
    private function generateIssuedDecisionsReport(Request $request)
    {
        $query = FinalDecision::with(['accreditationRequest.program.department.college.university', 'certificate', 'issuedBy'])
            ->orderBy('issued_at', 'desc');

        // Apply filters
        if ($request->filled('decision_type') && $request->input('decision_type') !== 'all') {
            $query->where('decision_type', $request->input('decision_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issued_at', '>=', Carbon::parse($request->input('date_from')));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issued_at', '<=', Carbon::parse($request->input('date_to')));
        }

        $decisions = $query->get();

        // Stats
        $totalDecisionsCount = $decisions->count();
        $approvedDecisionsCount = $decisions->filter(fn ($d) => $d->isApproved())->count();
        $rejectedDecisionsCount = $totalDecisionsCount - $approvedDecisionsCount;
        $activeCertificatesCount = $decisions->filter(fn ($d) => $d->certificate && $d->certificate->isValid())->count();

        $pdf = Pdf::view('print_templates.reports.issued_decisions', compact(
            'decisions',
            'totalDecisionsCount',
            'approvedDecisionsCount',
            'rejectedDecisionsCount',
            'activeCertificatesCount'
        ))
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->waitUntilNetworkIdle();
            })
            ->name('تقرير_القرارات_والشهادات_'.Carbon::now()->format('YmdHis').'.pdf');

        return $pdf->download();
    }

    /**
     * Generate General Statistical Summary PDF report.
     */
    private function generateGeneralSummaryReport()
    {
        $totalUniversities = University::count();
        $totalPrograms = Program::count();
        $totalRequests = AccreditationRequest::count();
        $activeRequests = AccreditationRequest::where('request_status', 'Active')->count();
        $totalCertificates = AccreditationCertificate::where('is_active', true)->count();

        // Request distribution by stage
        $stages = [
            'stage_one', 'stage_two', 'stage_three', 'stage_four',
            'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine',
        ];
        $stagesDistribution = [];
        foreach ($stages as $stage) {
            $stagesDistribution[$stage] = AccreditationRequest::where('current_stage', $stage)->count();
        }

        // Top 5 universities with most accreditation requests
        $topUniversities = University::withCount(['colleges as requests_count' => function ($q) {
            $q->join('departments', 'colleges.id', '=', 'departments.college_id')
                ->join('programs', 'departments.id', '=', 'programs.department_id')
                ->join('accreditation_requests', 'programs.id', '=', 'accreditation_requests.program_id');
        }])
            ->orderBy('requests_count', 'desc')
            ->take(5)
            ->get();

        // Top 5 specialties of evaluators
        $topSpecialties = Evaluator::select('general_specialty', DB::raw('count(*) as count'))
            ->whereNotNull('general_specialty')
            ->where('general_specialty', '!=', '')
            ->groupBy('general_specialty')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $pdf = Pdf::view('print_templates.reports.general_summary', compact(
            'totalUniversities',
            'totalPrograms',
            'totalRequests',
            'activeRequests',
            'totalCertificates',
            'stagesDistribution',
            'topUniversities',
            'topSpecialties'
        ))
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->waitUntilNetworkIdle();
            })
            ->name('التقرير_الإحصائي_الشامل_'.Carbon::now()->format('YmdHis').'.pdf');

        return $pdf->download();
    }

    /**
     * Generate Criteria Analysis PDF report (highest/lowest scored standards & indicators).
     */
    private function generateCriteriaAnalysisReport()
    {
        // Average score per indicator across all evaluations
        $indicatorScores = Indicator::select(
            'indicators.id',
            'indicators.number',
            'indicators.name',
            'indicators.sub_standard_id',
            DB::raw('ROUND(AVG(indicator_evaluations.score), 2) as avg_score'),
            DB::raw('COUNT(indicator_evaluations.id) as eval_count'),
            DB::raw('MAX(indicator_evaluations.score) as max_score'),
            DB::raw('MIN(indicator_evaluations.score) as min_score')
        )
            ->join('indicator_evaluations', 'indicators.id', '=', 'indicator_evaluations.indicator_id')
            ->groupBy('indicators.id', 'indicators.number', 'indicators.name', 'indicators.sub_standard_id')
            ->with(['subStandard.standard'])
            ->orderBy('avg_score', 'desc')
            ->get();

        // Average score per standard (aggregated via sub_standards → indicators)
        $standardScores = Standard::select(
            'standards.id',
            'standards.number',
            'standards.name',
            DB::raw('ROUND(AVG(indicator_evaluations.score), 2) as avg_score'),
            DB::raw('COUNT(indicator_evaluations.id) as eval_count')
        )
            ->join('sub_standards', 'standards.id', '=', 'sub_standards.standard_id')
            ->join('indicators', 'sub_standards.id', '=', 'indicators.sub_standard_id')
            ->join('indicator_evaluations', 'indicators.id', '=', 'indicator_evaluations.indicator_id')
            ->groupBy('standards.id', 'standards.number', 'standards.name')
            ->orderBy('avg_score', 'desc')
            ->get();

        $totalEvaluationsCount = $indicatorScores->sum('eval_count');
        $totalIndicatorsEvaluated = $indicatorScores->count();
        $overallAvgScore = $indicatorScores->isNotEmpty()
            ? round($indicatorScores->avg('avg_score'), 2)
            : 0;

        // Top 10 highest and lowest indicators
        $topIndicators = $indicatorScores->take(10);
        $bottomIndicators = $indicatorScores->sortBy('avg_score')->values()->take(10);

        $pdf = Pdf::view('print_templates.reports.criteria_analysis', compact(
            'standardScores',
            'topIndicators',
            'bottomIndicators',
            'totalEvaluationsCount',
            'totalIndicatorsEvaluated',
            'overallAvgScore'
        ))
            ->format('a4')
            ->withBrowsershot(function ($browsershot) {
                $browsershot->waitUntilNetworkIdle();
            })
            ->name('تقرير_تحليل_المعايير_'.Carbon::now()->format('YmdHis').'.pdf');

        return $pdf->download();
    }
}
