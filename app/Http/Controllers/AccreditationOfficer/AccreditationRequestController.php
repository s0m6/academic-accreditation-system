<?php

namespace App\Http\Controllers\AccreditationOfficer;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccreditationRequestController extends Controller
{
    /**
     * Display a listing of the accreditation requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $university = $user->university;

        $requests = AccreditationRequest::with(['program.department.college'])
            ->whereHas('program.department.college', function ($query) use ($university) {
                if ($university) {
                    $query->where('university_id', $university->id);
                } else {
                    $query->whereNull('id'); // return none if no university is linked
                }
            })
            ->latest()
            ->paginate(15);

        return view('accreditation_officer.requests.index', compact('requests'));
    }

   
}
