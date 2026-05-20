<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Models\FinalDecision;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    /**
     * Display a listing of all decisions.
     */
    public function index(Request $request)
    {
        $decisions = FinalDecision::with(['accreditationRequest.program.department.college.university', 'certificate', 'issuedBy'])
            ->latest()
            ->paginate(15);

        return view('council_secretariat.decisions.index', compact('decisions'));
    }
}
