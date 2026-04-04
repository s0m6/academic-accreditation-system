<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs for the officer's university.
     */
    public function index()
    {
        $university = request()->user()->university;

        $programs = Program::whereHas('department.college', function ($query) use ($university) {
            $query->where('university_id', $university->id);
        })
            ->with([
                'department.college',
                'accreditationRequests.programCoordinator',
                'latestAccreditationRequest',
            ])
            ->orderByDesc('id')
            ->get();

        $colleges = College::where('university_id', $university->id)
            ->with('departments')
            ->orderBy('name')
            ->get();

        return view('accreditation_officer.programs', compact('programs', 'colleges', 'university'));
    }

    /**
     * Retrieve departments for a specific college via AJAX.
     */
    public function getDepartments(College $college)
    {
        $university = request()->user()->university;

        if ($college->university_id !== $university->id) {
            abort(403);
        }

        return response()->json(
            $college->departments()->orderBy('name')->get(['id', 'name'])
        );
    }

    /**
     * Store a new program record in the database.
     */
    public function store(Request $request)
    {
        $university = request()->user()->university;

        $validated = $request->validate([
            'program_name' => 'required|string|max:255',
            'degree_level' => 'required|in:diploma,bachelor,master,phd',
            'department_id' => 'required|exists:departments,id',
            'language' => 'required|in:arabic,english',
            'credit_hours' => 'required|integer|min:1',
            'establishment_date' => 'required|date',
            'study_duration' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);

        // Verify department belongs to this university
        $department = Department::where('id', $validated['department_id'])
            ->whereHas('college', function ($query) use ($university) {
                $query->where('university_id', $university->id);
            })
            ->firstOrFail();

        Program::create([
            'program_name' => $validated['program_name'],
            'degree_level' => $validated['degree_level'],
            'department_id' => $department->id,
            'program_details' => [
                'language' => $validated['language'],
                'credit_hours' => (int) $validated['credit_hours'],
                'establishment_date' => $validated['establishment_date'],
                'study_duration' => $validated['study_duration'],
                'website_url' => $validated['website_url'] ?? '',
            ],
        ]);

        return back()->with('success', 'تم إنشاء البرنامج بنجاح.');
    }

    /**
     * Update an existing program's data.
     */
    public function update(Request $request, Program $program)
    {
        $university = request()->user()->university;

        if ($program->department->college->university_id !== $university->id) {
            abort(403);
        }

        $validated = $request->validate([
            'program_name' => 'required|string|max:255',
            'degree_level' => 'required|in:diploma,bachelor,master,phd',
            'department_id' => 'required|exists:departments,id',
            'language' => 'required|in:arabic,english',
            'credit_hours' => 'required|integer|min:1',
            'establishment_date' => 'required|date',
            'study_duration' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);

        // Verify new department also belongs to this university
        Department::where('id', $validated['department_id'])
            ->whereHas('college', function ($query) use ($university) {
                $query->where('university_id', $university->id);
            })
            ->firstOrFail();

        $program->update([
            'program_name' => $validated['program_name'],
            'degree_level' => $validated['degree_level'],
            'department_id' => $validated['department_id'],
            'program_details' => [
                'language' => $validated['language'],
                'credit_hours' => (int) $validated['credit_hours'],
                'establishment_date' => $validated['establishment_date'],
                'study_duration' => $validated['study_duration'],
                'website_url' => $validated['website_url'] ?? '',
            ],
        ]);

        return back()->with('success', 'تم تحديث بيانات البرنامج بنجاح.');
    }

    /**
     * Remove a program from the system.
     */
    public function destroy(Program $program)
    {
        $university = request()->user()->university;

        if ($program->department->college->university_id !== $university->id) {
            abort(403);
        }

        $program->delete();

        return back()->with('success', 'تم حذف البرنامج بنجاح.');
    }

    /**
     * Create a new draft accreditation request for a specific program and redirect to its dashboard.
     */
    public function storeRequest(Request $request, Program $program)
{
    $university = request()->user()->university;
    
    if ($program->department->college->university_id !== $university->id) { 
        abort(403);
    }
    
    $accreditationRequest = $program->accreditationRequests()->create([
        'current_stage' => 'stage_one',
        'request_status' => 'draft',
        'program_coord_id' => request()->user()->id,
    ]);
    
    Storage::makeDirectory("req_{$accreditationRequest->id}");
    
    return redirect()->route('requests.show', $accreditationRequest);
}
}