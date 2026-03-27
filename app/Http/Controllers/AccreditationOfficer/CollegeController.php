<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function index()
    {
        $university = auth()->user()->university;

        $colleges = College::where('university_id', $university->id)
            ->with('city')
            ->orderByDesc('id')
            ->get();

        $cities = City::orderBy('city_name')->get();

        return view('accreditation_officer.colleges', compact('colleges', 'cities', 'university'));
    }

    public function store(Request $request)
    {
        $university = auth()->user()->university;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'dean_name' => 'required|string|max:255',
            'dean_email' => 'required|email|max:255',
            'dean_mobile' => 'required|string|max:20',
            'dean_phone' => 'required|string|max:20',
        ]);

        $validated['university_id'] = $university->id;

        College::create($validated);

        return back()->with('success', 'تم إنشاء الكلية بنجاح.');
    }

    public function update(Request $request, College $college)
    {
        $university = auth()->user()->university;

        if ($college->university_id !== $university->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'dean_name' => 'required|string|max:255',
            'dean_email' => 'required|email|max:255',
            'dean_mobile' => 'required|string|max:20',
            'dean_phone' => 'required|string|max:20',
        ]);

        $college->update($validated);

        return back()->with('success', 'تم تحديث بيانات الكلية بنجاح.');
    }

    public function destroy(College $college)
    {
        $university = auth()->user()->university;

        if ($college->university_id !== $university->id) {
            abort(403);
        }

        $college->delete();

        return back()->with('success', 'تم حذف الكلية بنجاح.');
    }
}
