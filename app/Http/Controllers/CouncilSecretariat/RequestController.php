<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Models\AccreditationRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display a listing of requests in stage_one (الطلب الأولي).
     */
    public function stageOne(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_one')
            ->whereNot('request_status', 'draft')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_one', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_two (البيانات الأساسية).
     */
    public function stageTwo(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_two')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_two', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_three (تقرير الدراسة الذاتية).
     */
    public function stageThree(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_three')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_three', compact('requests'));
    }
}
