<?php

namespace App\Http\Controllers;

use App\Models\AccreditationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RequestDashboardController extends Controller
{
    /**
     * The stages available in the system.
     *
     * @var array<string, string>
     */
    public const STAGES = [
        'stage_one' => 'طلب الاعتماد الأولي',
        'stage_two' => 'البيانات الأساسية',
        'stage_three' => 'تقرير الدراسة الذاتية',
        'stage_four' => 'اختيار لجنة التقييم',
        'stage_five' => 'تحديد جدول الزيارة',
        'stage_six' => 'تقارير نتائج التقييم(الأولية)',
        'stage_seven' => 'توصيات اللجنة والرد عليها',
        'stage_eight' => 'تقارير نتائج التقييم(الختامية)',
    ];

    /**
     * Show the request dashboard, defaulting to the current stage view.
     */
    public function show(AccreditationRequest $accreditationRequest)
    {
        $this->authorizeAccess($accreditationRequest);

        $accreditationRequest->load('program.department.college.university');

        return view('requests.show', $this->viewData($accreditationRequest, $accreditationRequest->current_stage));
    }

    /**
     * Display a specific stage for the given accreditation request.
     */
    public function stage(Request $request, AccreditationRequest $accreditationRequest, string $stage)
    {
        $this->authorizeAccess($accreditationRequest);

        if (! array_key_exists($stage, self::STAGES)) {
            abort(404);
        }

        $accreditationRequest->load('program.department.college.university');

        return view('requests.show', $this->viewData($accreditationRequest, $stage));
    }

    /**
     * Construct the view data array for the request dashboard.
     */
    private function viewData(AccreditationRequest $accreditationRequest, string $activeStage): array
    {
        $program = $accreditationRequest->program;
        $dept = $program->department;
        $college = $dept->college;
        $univ = $college->university;
        $officer = User::find($univ->accreditation_officer_id);
        $details = $program->program_details ?? [];

        // Pre-fill data for stage one modal
        $prefill = [
            'degree_level' => $program->degree_level,
            'program_name' => $program->program_name,
            'department_name' => $dept->name,
            'university_name' => $univ->name,
            'college_name' => $college->name,
            'language' => $details['language'] ?? '',
            'credit_hours' => $details['credit_hours'] ?? '',
            'establishment_date' => $details['establishment_date'] ?? '',
            'study_duration' => $details['study_duration'] ?? '',
            'website_url' => $details['website_url'] ?? '',
            'president_name' => $univ->president_name,
            'president_phone' => $univ->president_phone,
            'president_mobile' => $univ->president_mobile,
            'president_email' => $univ->president_email,
            'officer_name' => $officer?->name ?? '',
            'officer_phone' => $officer?->phone ?? '',
            'officer_mobile' => $officer?->mobile ?? '',
            'officer_email' => $officer?->email ?? '',
            'dean_name' => $college->dean_name,
            'dean_phone' => $college->dean_phone,
            'dean_mobile' => $college->dean_mobile,
            'dean_email' => $college->dean_email,
            'head_name' => $dept->head_name,
            'head_phone' => $dept->head_phone,
            'head_mobile' => $dept->head_mobile,
            'head_email' => $dept->head_email,
        ];

        // Load active stage submissions ordered newest first
        $activeStageSubmissions = $accreditationRequest
            ->formSubmissions()
            ->where('stage', $activeStage)
            ->with(['submitter', 'decider'])
            ->orderByDesc('id')
            ->when(auth()->user()->role === 'evaluator', function ($q) {
                return $q->where('status', 'approved');
            })
            ->get();

        // Load committee data with active members for stage four panel
        $committee = $accreditationRequest->committee;
        if ($committee) {
            $committee->load([
                'activeMembers.evaluator.user',
                'activeMembers.evaluator.city',
                'chairEvaluator.user',
            ]);
        }

        // Load council coordinators list for the coordinator selector modal
        $coordinators = User::where('role', 'council_coordinator')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Load visit schedules for stage five
        $visitSchedules = $accreditationRequest->visitSchedules()->orderByDesc('id')->get();

        return [
            'accreditationRequest' => $accreditationRequest,
            'stages' => self::STAGES,
            'activeStage' => $activeStage,
            'prefill' => $prefill,
            'activeStageSubmissions' => $activeStageSubmissions,
            'committee' => $committee,
            'coordinators' => $coordinators,
            'visitSchedules' => $visitSchedules,
        ];
    }

    /**
     * Authorize that the current user has access to view the request based on their role.
     */
    private function authorizeAccess(AccreditationRequest $accreditationRequest): void
    {
        $user = request()->user();

        $allowed = match ($user->role) {
            'council_secretariat' => true,
            'accreditation_officer' => $accreditationRequest->program->department->college->university->accreditation_officer_id === $user->id,
            'program_coordinator' => $accreditationRequest->program_coord_id === $user->id,
            'council_coordinator' => $accreditationRequest->council_coord_id === $user->id,
            'evaluator' => $accreditationRequest->committee && $accreditationRequest->committee->members()
                ->where('evaluator_id', $user->evaluator->id ?? 0)
                ->where('member_status', 'accepted')
                ->exists(),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الطلب.');
        }
    }
}
