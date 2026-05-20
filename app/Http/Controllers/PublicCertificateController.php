<?php

namespace App\Http\Controllers;

use App\Models\AccreditationCertificate;
use App\Models\City;
use App\Models\FinalDecision;
use App\Models\University;
use Illuminate\Http\Request;

class PublicCertificateController extends Controller
{
    /**
     * Show the certificates explorer page.
     */
    public function index()
    {
        $universities = University::orderBy('name')->get(['id', 'name']);
        $cities = City::orderBy('city_name')->get(['id', 'city_name']);

        $decisionTypes = collect(FinalDecision::$decisionMeta)
            ->filter(fn ($meta) => $meta['approved'])
            ->map(fn ($meta, $key) => ['id' => $key, 'label' => $meta['label']]);

        return view('public.explorer', compact('universities', 'cities', 'decisionTypes'));
    }

    /**
     * API for real-time filtering via Axios.
     */
    public function search(Request $request)
    {
        $query = AccreditationCertificate::query()
            ->with(['finalDecision.accreditationRequest.program.department.college.university', 'finalDecision.accreditationRequest.program.department.college.city']);

        // Filter by University
        if ($request->filled('university_id')) {
            $query->whereHas('finalDecision.accreditationRequest.program.department.college', function ($q) use ($request) {
                $q->where('university_id', $request->university_id);
            });
        }

        // Filter by City
        if ($request->filled('city_id')) {
            $query->whereHas('finalDecision.accreditationRequest.program.department.college', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        // Filter by Decision Type
        if ($request->filled('decision_type')) {
            $query->whereHas('finalDecision', function ($q) use ($request) {
                $q->where('decision_type', $request->decision_type);
            });
        } else {
            // Only show approved by default
            $query->whereHas('finalDecision', function ($q) {
                $q->whereIn('decision_type', ['approved_achieved', 'approved_with_mastery', 'approved_with_excellence']);
            });
        }

        $certificates = $query->latest()->get()->map(function ($cert) {
            $data = $cert->certificate_data;

            return [
                'id' => $cert->id,
                'certificate_number' => $cert->certificate_number,
                'program_name' => $data['program_name'] ?? '—',
                'university_name' => $data['university_name'] ?? '—',
                'achievement_level' => $data['achievement_level'] ?? '—',
                'issued_at' => $data['issued_at'] ?? '—',
                'expires_at' => $data['expires_at'] ?? '—',
                'is_valid' => $cert->isValid(),
                'url' => route('certificate.show', $cert->certificate_number),
                'city_name' => $cert->finalDecision->accreditationRequest->program->department->college->city->city_name ?? '—',
            ];
        });

        return response()->json($certificates);
    }

    /**
     * Helper for landing page carousel (top 10 latest).
     */
    public function getLatest()
    {
        return AccreditationCertificate::whereHas('finalDecision', function ($q) {
            $q->whereIn('decision_type', ['approved_achieved', 'approved_with_mastery', 'approved_with_excellence']);
        })
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($cert) => [
            'program' => $cert->certificate_data['program_name'] ?? '—',
            'university' => $cert->certificate_data['university_name'] ?? '—',
            'level' => $cert->certificate_data['achievement_level'] ?? '—',
            'url' => route('certificate.show', $cert->certificate_number),
        ]);
    }
}
