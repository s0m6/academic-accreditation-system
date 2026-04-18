<?php

namespace App\Http\Controllers\stages;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Controller for handling Accreditation Stage Two (Basic Data).
 */
class StageTwoController extends Controller
{
    /**
     * Create a new draft form submission for stage two.
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
            ->where('stage', 'stage_two')
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'يوجد طلب نشط المراجعة أو معتمد مسبقاً.');
        }

        // Check if there is an existing draft, if so don't create multiple.
        $draftExists = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_two')
            ->where('status', 'draft')
            ->exists();

        if ($draftExists) {
            return back()->with('error', 'يوجد مسودة بالفعل يمكنك تعديلها.');
        }

        // Check if there's a previously rejected submission to copy its data
        $lastRejected = $accreditationRequest->formSubmissions()
            ->where('stage', 'stage_two')
            ->where('status', 'rejected')
            ->latest('id')
            ->first();

        $formSubmission = FormSubmission::create([
            'accreditation_request_id' => $accreditationRequest->id,
            'stage' => 'stage_two',
            'status' => 'draft',
            'form_data' => $lastRejected ? $lastRejected->form_data : null,
            'submitted_by' => $user->id,
            'submitted_at' => null,
            'decided_by' => null,
            'decision_at' => null,
            'decision_reasons' => null,
        ]);

        return redirect()->route('requests.stage_two.edit', [$accreditationRequest, $formSubmission])
            ->with('success', 'تم إنشاء مسودة جديدة بنجاح، يمكنك الآن التعديل والحفظ.');
    }

    /**
     * Show the stage two edit form.
     */
    public function edit(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $readonly = false;

        // If not a draft, or not the coordinator, make it readonly
        if ($formSubmission->status !== 'draft' || $request->user()->role !== 'program_coordinator') {
            $readonly = true;
        }

        return view('requests.stageTwoForm', compact('accreditationRequest', 'formSubmission', 'readonly'));
    }

    /**
     * Show the stage two data (view only).
     */
    public function show(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);
        $readonly = true;

        return view('requests.stageTwoForm', compact('accreditationRequest', 'formSubmission', 'readonly'));
    }

    /**
     * Save the stage two draft data and handle file uploads.
     */
    public function saveDraft(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator' || $formSubmission->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Unauthorized or non-draft state'], 403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        // Security validation for uploaded files (allow only PDF max 10MB)
        $rules = [];
        for ($i = 1; $i <= 8; $i++) {
            $rules["decision_file_{$i}"] = 'nullable|file|mimes:pdf|max:10240';
        }
        $request->validate($rules);

        // Extract JSON data
        $jsonData = json_decode($request->input('json_data', '{}'), true);

        // Helper to check if a file path is safe to delete
        $isSafeToDelete = function ($path) use ($formSubmission) {
            if (! $path || ! Storage::exists($path)) {
                return false;
            }

            // Count how many FORM submissions (EXCLUDING current one) use this exact file path
            return ! FormSubmission::where('id', '!=', $formSubmission->id)
                ->where('form_data', 'LIKE', '%'.$path.'%')
                ->exists();
        };

        $existingPaths = $jsonData['decision_files_paths'] ?? [];

        // Handle files handling up to 8 decision files.
        for ($i = 1; $i <= 8; $i++) {
            $fileKey = "decision_file_{$i}";
            $oldPath = $formSubmission->form_data['decision_files'][$i] ?? null;

            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $path = $file->storeAs(
                    "req_{$accreditationRequest->id}/stage_two_decisions",
                    $fileKey.'_'.time().'.'.$file->getClientOriginalExtension(),
                    'local'
                );

                // If there was an old file, delete it from storage ONLY if no one else uses it
                if ($oldPath && $isSafeToDelete($oldPath)) {
                    Storage::delete($oldPath);
                }

                // Modify the JSON data to point to the new file
                if (! isset($jsonData['decision_files'])) {
                    $jsonData['decision_files'] = [];
                }
                $jsonData['decision_files'][$i] = $path;
            } elseif (! empty($existingPaths[$i])) {
                // keep the old file mapping if it was not deleted
                $currentPath = $existingPaths[$i];
                if (str_starts_with($currentPath, 'temp_files/')) {
                    $filename = basename($currentPath);
                    $newPath = "req_{$accreditationRequest->id}/stage_two_decisions/".$filename;
                    if (Storage::exists($currentPath)) {
                        Storage::move($currentPath, $newPath);
                        $currentPath = $newPath;
                    }
                }

                // If there was an old file that is DIFFERENT from this one, try to delete it
                if ($oldPath && $oldPath !== $currentPath && $isSafeToDelete($oldPath)) {
                    Storage::delete($oldPath);
                }

                if (! isset($jsonData['decision_files'])) {
                    $jsonData['decision_files'] = [];
                }
                $jsonData['decision_files'][$i] = $currentPath;
            } else {
                // User may have deleted the file, try to delete physically if safe
                if ($oldPath && $isSafeToDelete($oldPath)) {
                    Storage::delete($oldPath);
                }
                if (isset($jsonData['decision_files'][$i])) {
                    unset($jsonData['decision_files'][$i]);
                }
            }
        }

        unset($jsonData['decision_files_paths']); // Remove temp payload key

        $formSubmission->update([
            'form_data' => $jsonData,
        ]);

        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }

    /**
     * Submit the stage two draft to the council.
     */
    public function submit(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        if ($formSubmission->status !== 'draft') {
            return back()->with('error', 'لا يمكن رفع طلب ليس كمسودة.');
        }

        $formSubmission->update([
            'status' => 'pending',
            'submitted_by' => $user->id,
            'submitted_at' => Carbon::now(),
        ]);

        return back()->with('success', 'تم رفع البيانات للأمانة بنجاح.');
    }

    /**
     * View securely a protected file.
     */
    public function viewFile(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission, $decisionIndex)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $path = $formSubmission->form_data['decision_files'][$decisionIndex] ?? null;

        if (! $path) {
            abort(404, 'لم يتم العثور على رابط للملف.');
        }

        // Backward compatibility: check if it exists in local disk, else check public disk
        $disk = 'local';
        if (! Storage::exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = 'public';
            } else {
                abort(404, 'عذراً، الملف المطلوب غير موجود في خوادم النظام.');
            }
        }

        // Return the file inline so it displays in the browser instead of downloading directly
        $fullPath = Storage::disk($disk)->path($path);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="attachment.pdf"',
        ]);
    }

    /**
     * Upload an individual decision file asynchronously to a temporary location.
     */
    public function uploadFile(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission, $decisionIndex)
    {
        $user = $request->user();
        if ($user->role !== 'program_coordinator' || $formSubmission->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Unauthorized or non-draft state'], 403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        // Store in temporary folder immediately so if user leaves without saving, the actual DB isn't updated
        $path = $file->storeAs(
            'temp_files',
            'temp_'.uniqid().'_'.time().'.'.$file->getClientOriginalExtension(),
            'local'
        );

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    /**
     * Reject the stage two submission with reasons.
     */
    public function reject(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'council_secretariat') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

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

        return back()->with('success', 'تم رفض نموذج البيانات الأساسية وإعادته للمنسق للتعديل.');
    }

    /**
     * Approve the stage two submission and advance to stage three.
     */
    public function approve(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission)
    {
        $user = $request->user();
        if ($user->role !== 'council_secretariat') {
            abort(403);
        }
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $formSubmission->update([
            'status' => 'approved',
            'decided_by' => $user->id,
            'decision_at' => Carbon::now(),
        ]);

        // Advance the request to stage three
        $accreditationRequest->update([
            'current_stage' => 'stage_three',
        ]);

        return back()->with('success', 'تمت الموافقة على البيانات الأساسية، والطلب الآن في مرحلة تقرير الدراسة الذاتية.');
    }

    /**
     * Ensure the submission belongs to the correct request and the user is authorized.
     */
    private function ensureAuthorized(Request $request, AccreditationRequest $accreditationRequest, ?FormSubmission $formSubmission = null): void
    {
        if ($formSubmission) {
            if ($formSubmission->accreditation_request_id !== $accreditationRequest->id) {
                abort(404);
            }
            if ($formSubmission->stage !== 'stage_two') {
                abort(404);
            }
        }

        // Coordinator of the program or council secretariat/accreditation officer can see it generally.
        // If the user is a program coordinator, they MUST own this specific request.
        $user = $request->user();
        if ($user->role === 'program_coordinator' && $accreditationRequest->program_coord_id !== $user->id) {
            abort(403, 'غير مصرح لك بإدارة هذه المسودة أو الملفات.');
        }
    }
}
