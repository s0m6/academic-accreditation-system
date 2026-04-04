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

        $formSubmission = FormSubmission::create([
            'accreditation_request_id' => $accreditationRequest->id,
            'stage' => 'stage_two',
            'status' => 'draft',
            'form_data' => null,
            'submitted_by' => $user->id,
            'submitted_at' => null,
            'decided_by' => null,
            'decision_at' => null,
            'decision_reasons' => null,
        ]);

        return redirect()->route('requests.stage_two.edit', [$accreditationRequest, $formSubmission])
            ->with('success', 'تم إنشاء مسودة جديدة بنجاح.');
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
        
        $existingPaths = $jsonData['decision_files_paths'] ?? [];

        // Handle files handling up to 8 decision files.
        for ($i = 1; $i <= 8; $i++) {
            $fileKey = "decision_file_{$i}";
            $oldPath = $formSubmission->form_data['decision_files'][$i] ?? null;

            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $path = $file->storeAs(
                    "req_{$accreditationRequest->id}/stage_two_decisions", 
                    $fileKey . '_' . time() . '.' . $file->getClientOriginalExtension(),
                    'local'
                );
                
                // If there was an old file, delete it from storage
                if ($oldPath && Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }

                // Modify the JSON data to point to the new file
                if (!isset($jsonData['decision_files'])) {
                    $jsonData['decision_files'] = [];
                }
                $jsonData['decision_files'][$i] = $path;
            } else if (!empty($existingPaths[$i])) {
                // keep the old file mapping if it was not deleted
                if (!isset($jsonData['decision_files'])) {
                    $jsonData['decision_files'] = [];
                }
                $jsonData['decision_files'][$i] = $existingPaths[$i];
            } else {
                // User may have deleted the file, so unset it and delete it physically if exists
                if ($oldPath && Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
                if (isset($jsonData['decision_files'][$i])) {
                    unset($jsonData['decision_files'][$i]);
                }
            }
        }
        
        unset($jsonData['decision_files_paths']); // Remove temp payload key
        
        $formSubmission->update([
            'form_data' => $jsonData
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
     * Download securely a protected file.
     */
    public function downloadFile(Request $request, AccreditationRequest $accreditationRequest, FormSubmission $formSubmission, $decisionIndex)
    {
        $this->ensureAuthorized($request, $accreditationRequest, $formSubmission);

        $path = $formSubmission->form_data['decision_files'][$decisionIndex] ?? null;

        if (!$path) {
            abort(404, 'لم يتم العثور على رابط للملف.');
        }

        // Backward compatibility: check if it exists in local disk, else check public disk
        $disk = 'local';
        if (!Storage::exists($path)) {
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
            'Content-Disposition' => 'inline; filename="attachment.pdf"'
        ]);
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
