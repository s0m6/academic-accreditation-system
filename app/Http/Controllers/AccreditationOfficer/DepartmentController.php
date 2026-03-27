<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $university = auth()->user()->university;

        $departments = Department::whereHas('college', function ($query) use ($university) {
            $query->where('university_id', $university->id);
        })
            ->with('college')
            ->orderByDesc('id')
            ->get();

        $colleges = College::where('university_id', $university->id)
            ->orderBy('name')
            ->get();

        return view('accreditation_officer.departments', compact('departments', 'colleges', 'university'));
    }

    public function store(Request $request)
    {
        $university = auth()->user()->university;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'college_id' => 'required|exists:colleges,id',
            'head_name' => 'required|string|max:255',
            'head_email' => 'required|email|max:255',
            'head_mobile' => 'required|string|max:20',
            'head_phone' => 'required|string|max:20',
        ]);

        // Verify college belongs to this university
        $college = College::where('id', $validated['college_id'])
            ->where('university_id', $university->id)
            ->firstOrFail();

        Department::create($validated);

        return back()->with('success', 'تم إنشاء القسم بنجاح.');
    }

    public function update(Request $request, Department $department)
    {
        $university = auth()->user()->university;

        if ($department->college->university_id !== $university->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'college_id' => 'required|exists:colleges,id',
            'head_name' => 'required|string|max:255',
            'head_email' => 'required|email|max:255',
            'head_mobile' => 'required|string|max:20',
            'head_phone' => 'required|string|max:20',
        ]);

        // Verify new college also belongs to this university
        College::where('id', $validated['college_id'])
            ->where('university_id', $university->id)
            ->firstOrFail();

        $department->update($validated);

        return back()->with('success', 'تم تحديث بيانات القسم بنجاح.');
    }

    public function destroy(Department $department)
    {
        $university = auth()->user()->university;

        if ($department->college->university_id !== $university->id) {
            abort(403);
        }

        $department->delete();

        return back()->with('success', 'تم حذف القسم بنجاح.');
    }
}
