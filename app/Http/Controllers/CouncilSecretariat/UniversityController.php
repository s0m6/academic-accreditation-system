<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Mail\AccreditationOfficerCreated;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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

        try {
            $user = DB::transaction(function () use ($request, $university, $password) {
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

                return $user;
            });
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حفظ البيانات في قاعدة البيانات، لم يتم إنشاء الحساب.');
        }

        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            Mail::to($user->email)->send(new AccreditationOfficerCreated($user, $password, $verificationUrl));

            return back()->with('success', 'تم إنشاء حساب مسؤول الاعتماد وإرسال رسالة التفعيل بنجاح.');
        } catch (\Exception $e) {
            return back()->with('success', 'تم إنشاء الحساب في قاعدة البيانات بنجاح، ولكن تعذر إرسال بريد التفعيل .');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'type' => 'required|in:government,private',
            'president_name' => 'nullable|string|max:255',
            'president_email' => 'nullable|email|max:255',
            'president_mobile' => 'nullable|string|max:20',
            'president_phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'اسم الجامعة مطلوب.',
            'name.unique' => 'اسم الجامعة هذا مسجل بالفعل.',
            'type.required' => 'نوع الجامعة مطلوب.',
            'type.in' => 'نوع الجامعة غير صالح.',
            'president_email.email' => 'البريد الإلكتروني لرئيس الجامعة غير صحيح.',
        ]);

        try {
            University::create([
                'name' => $request->name,
                'type' => $request->type,
                'president_name' => $request->president_name,
                'president_email' => $request->president_email,
                'president_mobile' => $request->president_mobile,
                'president_phone' => $request->president_phone,
            ]);

            return back()->with('success', 'تم إضافة الجامعة بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إضافة الجامعة. يرجى المحاولة مرة أخرى.');
        }
    }
}
