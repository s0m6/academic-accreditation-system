<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Models\AccreditationCertificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of all certificates.
     */
    public function index(Request $request)
    {
        $certificates = AccreditationCertificate::with(['finalDecision.accreditationRequest.program.department.college.university'])
            ->latest()
            ->paginate(15);

        return view('council_secretariat.certificates.index', compact('certificates'));
    }
}
