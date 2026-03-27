<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $university = auth()->user()->university;

        $programs = Program::whereHas('department.college', function ($query) use ($university) {
            $query->where('university_id', $university->id);
        })
            ->with('department.college')
            ->orderByDesc('id')
            ->get();

        $colleges = College::where('university_id', $university->id)
            ->with('departments')
            ->orderBy('name')
            ->get();

        return view('accreditation_officer.programs', compact('programs', 'colleges', 'university'));
    }

    /**
     * Get departments for a given college (AJAX endpoint).
     */
    public function getDepartments(College $college)
    {
        $university = auth()->user()->university;

        if ($college->university_id !== $university->id) {
            abort(403);
        }

        return response()->json(
            $college->departments()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function store(Request $request)
    {
        $university = auth()->user()->university;

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

    public function update(Request $request, Program $program)
    {
        $university = auth()->user()->university;

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

    public function destroy(Program $program)
    {
        $university = auth()->user()->university;

        if ($program->department->college->university_id !== $university->id) {
            abort(403);
        }

        $program->delete();

        return back()->with('success', 'تم حذف البرنامج بنجاح.');
    }
}
