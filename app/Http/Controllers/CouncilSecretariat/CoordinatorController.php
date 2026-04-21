<?php

namespace App\Http\Controllers\CouncilSecretariat;

use App\Http\Controllers\Controller;
use App\Mail\CoordinatorCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CoordinatorController extends Controller
{
    /**
     * Display a listing of the council coordinators.
     */
    public function index(Request $request)
    {
        $coordinators = User::where('role', 'council_coordinator')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('council_secretariat.coordinators.index', compact('coordinators'));
    }

    /**
     * Store a newly created coordinator in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $password = Str::random(10);

        try {
            $user = DB::transaction(function () use ($validated, $password) {
                return User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($password),
                    'role' => 'council_coordinator',
                    'phone' => $validated['phone'] ?? null,
                    'mobile' => $validated['mobile'],
                ]);
            });

            // Send activation email
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            Mail::to($user->email)->send(new CoordinatorCreated($user, $password, $verificationUrl));

            return redirect()->route('council_secretariat.coordinators.index')
                ->with('success', 'تم إنشاء حساب المنسق وإرسال رسالة التفعيل بنجاح.');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إنشاء الحساب.')->withInput();
        }
    }
}
