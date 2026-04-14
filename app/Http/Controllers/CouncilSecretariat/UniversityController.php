<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Mail\AccreditationOfficerCreated;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::with('officer')->get();

        return view('council_secretariat.universities', compact('universities'));
    }

    public function storeOfficer(Request $request, University $university)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
        ]);

        if ($university->accreditation_officer_id) {
            return back()->with('error', 'الجامعة مرتبطة بالفعل بمسؤول اعتماد.');
        }

        $password = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'accreditation_officer',
            'phone' => $request->phone,
            'mobile' => $request->mobile,
        ]);

        $university->update([
            'accreditation_officer_id' => $user->id,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        Mail::to($user->email)->send(new AccreditationOfficerCreated($user, $password, $verificationUrl));

        return back()->with('success', 'تم إنشاء حساب مسؤول الاعتماد وإرسال رسالة التفعيل والتفاصيل بنجاح.');
    }
}
