<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluatorRequest;
use App\Http\Requests\UpdateEvaluatorRequest;
use App\Mail\EvaluatorCreated;
use App\Models\City;
use App\Models\Evaluator;
use App\Models\EvaluatorAttachment;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EvaluatorController extends Controller
{
    // Display a paginated list of evaluators with search and filter support
    public function index(Request $request)
    {
        $cities = City::orderBy('city_name')->get();

        $evaluators = Evaluator::with(['user', 'city'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));
            })
            ->when($request->filled('city_id'), function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('council_secretariat.evaluators.index', compact('evaluators', 'cities'));
    }

    // Show the form to create a new evaluator
    public function create()
    {
        $cities = City::orderBy('city_name')->get();
        $universities = University::orderBy('name')->get();

        return view('council_secretariat.evaluators.insert_evaluator', compact('cities', 'universities'));
    }

    // Handle real-time Axios search/filter requests and return JSON
    public function search(Request $request)
    {
        $evaluators = Evaluator::with(['user', 'city'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));
            })
            ->when($request->filled('city_id'), function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'evaluators' => $evaluators->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->user->name,
                'email' => $e->user->email,
                'city' => $e->city->city_name,
                'academic_rank' => $e->academic_rank,
                'verified' => (bool) $e->user->email_verified_at,
            ]),
            'total' => $evaluators->total(),
            'current_page' => $evaluators->currentPage(),
            'last_page' => $evaluators->lastPage(),
        ]);
    }

    // Store a new evaluator: create user, evaluator profile, conflicts, and attachments
    public function store(StoreEvaluatorRequest $request)
    {
        $validated = $request->validated();
        $password = Str::random(12);

        try {
            $evaluator = DB::transaction(function () use ($validated, $password, $request) {
                // Create the user account for the evaluator
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($password),
                    'role' => 'evaluator',
                    'phone' => $validated['phone'] ?? null,
                    'mobile' => $validated['mobile'],
                ]);

                // Create the evaluator profile linked to the new user
                $evaluator = Evaluator::create([
                    'user_id' => $user->id,
                    'city_id' => $validated['city_id'],
                    'general_specialty' => $validated['general_specialty'],
                    'detailed_specialty' => $validated['detailed_specialty'],
                    'academic_rank' => $validated['academic_rank'],
                    'current_university_id' => $validated['current_university_id'] ?? null,
                ]);

                // Insert all submitted conflicts of interest
                if (! empty($validated['conflicts'])) {
                    foreach ($validated['conflicts'] as $conflict) {
                        $evaluator->conflicts()->create([
                            'university_id' => $conflict['university_id'],
                            'conflict_text' => $conflict['conflict_text'],
                        ]);
                    }
                }

                // Store uploaded attachments and record each path in the database
                if (! empty($validated['attachments'])) {
                    foreach ($validated['attachments'] as $index => $attachment) {
                        $file = $request->file("attachments.{$index}.file");
                        $filename = Str::slug($attachment['name']).'_'.time().'_'.$index.'.'.$file->getClientOriginalExtension();
                        $path = $file->storeAs("evaluator_attachments/{$evaluator->id}", $filename);

                        $evaluator->attachments()->create([
                            'name' => $attachment['name'],
                            'path' => $path,
                        ]);
                    }
                }

                return $evaluator;
            });
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حفظ البيانات، لم يتم إنشاء الحساب.')->withInput();
        }

        // Send a verification and welcome email to the newly created evaluator
        try {
            $user = $evaluator->user;
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            Mail::to($user->email)->send(new EvaluatorCreated($user, $password, $verificationUrl));

            return redirect()->route('council_secretariat.evaluators.index')
                ->with('success', 'تم إنشاء حساب المقيم وإرسال رسالة التفعيل بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('council_secretariat.evaluators.index')
                ->with('success', 'تم إنشاء الحساب بنجاح، ولكن تعذر إرسال بريد التفعيل.');
        }
    }

    // Show the form for editing the specified evaluator
    public function edit(Evaluator $evaluator)
    {
        $evaluator->load(['user', 'conflicts', 'attachments']);
        $cities = City::orderBy('city_name')->get();
        $universities = University::orderBy('name')->get();

        $mode = 'edit';

        return view('council_secretariat.evaluators.insert_evaluator', compact('evaluator', 'cities', 'universities', 'mode'));
    }

    // Update the specified evaluator in storage
    public function update(UpdateEvaluatorRequest $request, Evaluator $evaluator)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $evaluator) {
                // Update user details
                $evaluator->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'mobile' => $validated['mobile'],
                ]);

                // Update evaluator profile
                $evaluator->update([
                    'city_id' => $validated['city_id'],
                    'general_specialty' => $validated['general_specialty'],
                    'detailed_specialty' => $validated['detailed_specialty'],
                    'academic_rank' => $validated['academic_rank'],
                    'current_university_id' => $validated['current_university_id'] ?? null,
                ]);

                // Update conflicts: delete all old and recreate
                $evaluator->conflicts()->delete();
                if (! empty($validated['conflicts'])) {
                    foreach ($validated['conflicts'] as $conflict) {
                        $evaluator->conflicts()->create([
                            'university_id' => $conflict['university_id'],
                            'conflict_text' => $conflict['conflict_text'],
                        ]);
                    }
                }

                // Handle deleted attachments
                if (! empty($validated['deleted_attachments'])) {
                    $attachmentsToDelete = $evaluator->attachments()->whereIn('id', $validated['deleted_attachments'])->get();
                    foreach ($attachmentsToDelete as $attachment) {
                        Storage::delete($attachment->path);
                        $attachment->delete();
                    }
                }

                // Store uploaded new attachments
                if (! empty($validated['attachments'])) {
                    foreach ($validated['attachments'] as $index => $attachment) {
                        if (isset($attachment['file'])) {
                            $file = $request->file("attachments.{$index}.file");
                            $filename = Str::slug($attachment['name']).'_'.time().'_'.$index.'.'.$file->getClientOriginalExtension();
                            $path = $file->storeAs("evaluator_attachments/{$evaluator->id}", $filename);

                            $evaluator->attachments()->create([
                                'name' => $attachment['name'],
                                'path' => $path,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('council_secretariat.evaluators.index')
                ->with('success', 'تم تحديث بيانات المقيم بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث البيانات.')->withInput();
        }
    }

    // Show the read-only profile page for a single evaluator with all relations
    public function show(Evaluator $evaluator)
    {
        $evaluator->load(['user', 'city', 'currentUniversity', 'conflicts.university', 'attachments']);

        $cities = City::orderBy('city_name')->get();
        $universities = University::orderBy('name')->get();

        $mode = 'show';

        return view('council_secretariat.evaluators.insert_evaluator', compact('evaluator', 'cities', 'universities', 'mode'));
    }

    // Stream an evaluator attachment file securely to the browser
    public function viewAttachment(Evaluator $evaluator, EvaluatorAttachment $attachment)
    {
        abort_if($attachment->evaluator_id !== $evaluator->id, 404);

        return Storage::response($attachment->path, $attachment->name);
    }
}
