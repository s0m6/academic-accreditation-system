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

    /**
     * Display a listing of requests in stage_four (اختيار لجنة التقييم).
     */
    public function stageFour(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_four')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_four', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_five (تحديد جدول الزيارة).
     */
    public function stageFive(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_five')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_five', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_six (تقارير نتائج التقييم(الأولية)).
     */
    public function stageSix(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_six')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_six', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_seven (توصيات اللجنة والرد عليها).
     */
    public function stageSeven(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_seven')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_seven', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_eight (تقارير نتائج التقييم(الختامية)).
     */
    public function stageEight(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_eight')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_eight', compact('requests'));
    }

    /**
     * Display a listing of requests in stage_nine (قرار الاعتماد).
     */
    public function stageNine(Request $request)
    {
        $requests = AccreditationRequest::where('current_stage', 'stage_nine')
            ->with(['program.department.college.university'])
            ->orderByDesc('created_at')
            ->get();

        return view('council_secretariat.requests.stage_nine', compact('requests'));
    }
}
