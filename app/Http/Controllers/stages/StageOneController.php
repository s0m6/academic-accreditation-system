<?php

/**
 * Controller for handling Accreditation Stage One (Initial Request).
 */

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Mail\ProgramCoordinatorCreated;
use App\Models\AccreditationRequest;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class StageOneController extends Controller
{
    /**
     * Record the initial stage submission for an accreditation request.
     */
    public function store(Request $request, AccreditationRequest $accreditationRequest)
    {
        $user = $request->user();
        $this->authorizeOfficer($accreditationRequest, $user);

        $validated = $request->validate([
            // Program coordinator contact info (only required fields not pre-filled)
            'coord_name' => 'required|string|max:255',
            'coord_phone' => 'nullable|string|max:30',
            'coord_mobile' => 'nullable|string|max:30',
            'coord_email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
                    if ($user && $user->role !== 'program_coordinator') {
                        $fail('البريد الإلكتروني مستخدم بالفعل لنوع حساب آخر.');
                    }
                },
            ],
        ]);

        $accreditationRequest->load('program.department.college.university');
        $program = $accreditationRequest->program;
        $dept = $program->department;
        $college = $dept->college;
        $univ = $college->university;
        $officer = $user;
        $details = $program->program_details ?? [];

        /**
         * Build the full JSON payload for form_data.
         * Section 1: Basic program data (pre-filled from DB).
         * Section 2: Contact info (pre-filled except program coordinator).
         */
        $formData = [
            'section_one' => [
                'degree_level' => $program->degree_level,
                'program_name' => $program->program_name,
                'department_name' => $dept->name,
                'university_name' => $univ->name,
                'college_name' => $college->name,
                'language' => $details['language'] ?? null,
                'credit_hours' => $details['credit_hours'] ?? null,
                'establishment_date' => $details['establishment_date'] ?? null,
                'study_duration' => $details['study_duration'] ?? null,
                'website_url' => $details['website_url'] ?? null,
            ],
            'section_two' => [
                'university_president' => [
                    'title' => 'رئيس الجامعة',
                    'name' => $univ->president_name,
                    'phone' => $univ->president_phone,
                    'mobile' => $univ->president_mobile,
                    'email' => $univ->president_email,
                ],
                'accreditation_officer' => [
                    'title' => 'مسؤول الاعتماد',
                    'name' => $officer->name,
                    'phone' => $officer->phone,
                    'mobile' => $officer->mobile,
                    'email' => $officer->email,
                ],
                'college_dean' => [
                    'title' => 'عميد الكلية',
                    'name' => $college->dean_name,
                    'phone' => $college->dean_phone,
                    'mobile' => $college->dean_mobile,
                    'email' => $college->dean_email,
                ],
                'department_head' => [
                    'title' => 'رئيس القسم',
                    'name' => $dept->head_name,
                    'phone' => $dept->head_phone,
                    'mobile' => $dept->head_mobile,
                    'email' => $dept->head_email,
                ],
                'program_coordinator' => [
                    'title' => 'منسق البرنامج',
                    'name' => $validated['coord_name'],
                    'phone' => $validated['coord_phone'] ?? null,
                    'mobile' => $validated['coord_mobile'] ?? null,
                    'email' => $validated['coord_email'],
                ],
            ],
        ];

        // Create the form submission record
        FormSubmission::create([
            'accreditation_request_id' => $accreditationRequest->id,
            'stage' => 'stage_one',
            'status' => 'pending',
            'form_data' => $formData,
            'submitted_by' => $user->id,
            'submitted_at' => Carbon::now(),
            'decided_by' => null,
            'decision_at' => null,
            'decision_reasons' => null,
        ]);

        // Activate the accreditation request
        $accreditationRequest->update(['request_status' => 'Active']);

        return back()->with('success', 'تم إرسال الطلب الأولي بنجاح وهو الآن قيد المراجعة.');
    }

    /**
     * Deny the initial accreditation request with stated reasons.
     */
    public function reject(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        $this->authorizeSecretary($user);
        $this->ensureBelongs($accreditationRequest, $formSubmission);

        $validated = $request->validate([
            'reasons' => 'required|array|min:1',
            'reasons.*' => 'required|string|max:500',
        ]);

        $formSubmission->update([
            'status' => 'rejected',
            'decision_reasons' => $validated['reasons'],
            'decided_by' => $user->id,
            'decision_at' => Carbon::now(),
        ]);

        return back()->with('success', 'تم رفض الطلب وإشعار مسؤول الاعتماد.');
    }

    /**
     * Approve the first stage of an accreditation request, creating the coordinator account.
     */
    public function approve(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        $this->authorizeSecretary($user);
        $this->ensureBelongs($accreditationRequest, $formSubmission);

        DB::beginTransaction();

        try {
            // Approve the submission
            $formSubmission->update([
                'status' => 'approved',
                'decided_by' => $user->id,
                'decision_at' => Carbon::now(),
            ]);

            // Extract coordinator data from the stored form_data
            $formData = $formSubmission->form_data;
            $coordData = $formData['section_two']['program_coordinator'] ?? [];
            $coordEmail = $coordData['email'] ?? null;
            $coordName = $coordData['name'] ?? 'منسق البرنامج';
            $coordPhone = $coordData['phone'] ?? null;
            $coordMobile = $coordData['mobile'] ?? null;

            // Check if a coordinator with this email already exists
            $existingCoord = User::where('email', $coordEmail)->first();

            if (! $existingCoord) {
                $password = Str::random(10);

                $coordinator = User::create([
                    'name' => $coordName,
                    'email' => $coordEmail,
                    'password' => Hash::make($password),
                    'role' => 'program_coordinator',
                    'phone' => $coordPhone,
                    'mobile' => $coordMobile,
                ]);

                // Generate email verification link
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                    [
                        'id' => $coordinator->getKey(),
                        'hash' => sha1($coordinator->getEmailForVerification()),
                    ]
                );

                $programName = $accreditationRequest->program->program_name ?? '';

                Mail::to($coordinator->email)->send(
                    new ProgramCoordinatorCreated($coordinator, $password, $verificationUrl, $programName)
                );
            } else {
                $coordinator = $existingCoord;
            }

            // Link coordinator to the request and advance to stage two
            $accreditationRequest->update([
                'program_coord_id' => $coordinator->id,
                'current_stage' => 'stage_two',
            ]);

            DB::commit();

            $message = $existingCoord
                ? 'تمت الموافقة على الطلب وإسناده للمنسق الحالي.'
                : 'تمت الموافقة على الطلب وتم إنشاء حساب منسق البرنامج وإرسال بريد التفعيل.';

            return redirect()->route('requests.stage', [$accreditationRequest, 'stage_one'])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ أثناء الموافقة على الطلب. قد تكون هناك مشكلة في إرسال البريد الإلكتروني للمنسق. تفاصيل الخطأ: '.$e->getMessage());
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeOfficer(AccreditationRequest $accreditationRequest, User $user): void
    {
        if ($user->role !== 'accreditation_officer') {
            abort(403);
        }

        $accreditationRequest->loadMissing('program.department.college.university');

        if ($accreditationRequest->program->department->college->university->accreditation_officer_id !== $user->id) {
            abort(403);
        }
    }

    private function authorizeSecretary(User $user): void
    {
        if ($user->role !== 'council_secretariat') {
            abort(403);
        }
    }

    private function ensureBelongs(AccreditationRequest $accreditationRequest, FormSubmission $formSubmission): void
    {
        if ($formSubmission->accreditation_request_id !== $accreditationRequest->id) {
            abort(404);
        }

        if ($formSubmission->stage !== 'stage_one') {
            abort(404);
        }
    }
}
