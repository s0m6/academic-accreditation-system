{{-- Stage One: طلب الاعتماد الأولي --}}
@php
    $userRole    = $user->role;
    $degreeLabels = [
        'diploma'  => 'دبلوم',
        'bachelor' => 'بكالوريوس',
        'master'   => 'ماجستير',
        'phd'      => 'دكتوراه',
    ];
    $langLabels = ['arabic' => 'العربية', 'english' => 'الإنجليزية'];

    $submissionStatusMap = [
        'pending'  => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'],
        'approved' => ['label' => 'موافق عليه',   'class' => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'],
        'rejected' => ['label' => 'مرفوض',         'class' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'],
        'draft'    => ['label' => 'مسودة',          'class' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20'],
    ];

    // Latest pending or approved submission
    $latestSubmission = $activeStageSubmissions->first();
    $hasActiveSubmission = $latestSubmission && in_array($latestSubmission->status, ['pending', 'approved']);
    $canSubmit = $userRole === 'accreditation_officer' && ! $hasActiveSubmission;
@endphp

<div class="w-full text-start space-y-6" x-data="{
    showFormModal: false,
    showViewModal: false,
    showRejectModal: false,
    showApproveModal: false,
    viewData: null,
    rejectSubmissionId: null,
    approveSubmissionId: null,
    reasons: [''],
    addReason() { this.reasons.push(''); },
    removeReason(i) { if (this.reasons.length > 1) this.reasons.splice(i, 1); },
}">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 text-green-700 bg-green-50 p-4 rounded-xl flex items-center shadow-sm border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
            <i class="fa-solid fa-circle-check text-xl me-3 shrink-0"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-center shadow-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
            <i class="fa-solid fa-triangle-exclamation text-xl me-3 shrink-0"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-start shadow-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
            <i class="fa-solid fa-circle-xmark text-xl me-3 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-bold block mb-1">يرجى تصحيح الأخطاء التالية:</span>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ACCREDITATION OFFICER VIEW                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($userRole === 'accreditation_officer')

        {{-- Header --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
            <div class="bg-(--bg-main) border-b border-(--border-primary) px-6 py-4 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shadow-inner shrink-0">
                        <i class="fa-solid fa-file-circle-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-(--text-primary)">نماذج الطلب الأولي المرسلة</h3>
                        <p class="text-xs text-(--text-secondary)">سجل جميع نماذج المرحلة الأولى لهذا الطلب</p>
                    </div>
                </div>
                @if($canSubmit)
                    <button @click="showFormModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        إنشاء طلب أولي
                    </button>
                @endif
            </div>

            {{-- Table --}}
            @if($activeStageSubmissions->isEmpty())
                <div class="p-10 flex flex-col items-center justify-center text-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-file-circle-xmark text-2xl text-(--text-secondary)"></i>
                    </div>
                    <div>
                        <p class="text-base font-bold text-(--text-primary)">لا توجد نماذج مرسلة بعد</p>
                        <p class="text-sm text-(--text-secondary) mt-1">اضغط "إنشاء طلب أولي" لبدء إجراءات الاعتماد</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-center text-(--text-secondary)">
                        <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                            <tr>
                                <th class="px-4 py-3 font-bold tracking-wider">#</th>
                                <th class="px-4 py-3 font-bold tracking-wider">تاريخ الإرسال</th>
                                <th class="px-4 py-3 font-bold tracking-wider text-center">الحالة</th>
                                <th class="px-4 py-3 font-bold tracking-wider text-center">العمليات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-(--border-primary)">
                            @foreach($activeStageSubmissions as $i => $sub)
                                @php $sStatus = $submissionStatusMap[$sub->status] ?? $submissionStatusMap['draft']; @endphp
                                <tr class="hover:bg-(--border-primary)/30 transition-colors">
                                    <td class="px-4 py-4 font-bold text-(--text-primary)">{{ $i + 1 }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $sub->submitted_at?->format('Y/m/d H:i') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $sStatus['class'] }}">
                                            {{ $sStatus['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="showViewModal = true; viewData = {{ json_encode($sub->form_data) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-eye"></i> عرض
                                            </button>
                                            @if($sub->status === 'rejected')
                                                <button @click="showViewModal = true; viewData = {{ json_encode(['_reasons' => $sub->decision_reasons]) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-circle-xmark"></i> أسباب الرفض
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($canSubmit === false && $latestSubmission?->status === 'rejected')
                    <div class="px-6 py-3 border-t border-(--border-primary) text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i>
                        تم رفض آخر طلب. يمكنك إنشاء طلب أولي جديد من زر أعلاه.
                    </div>
                @endif
            @endif
        </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- COUNCIL SECRETARIAT VIEW                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @elseif($userRole === 'council_secretariat')

        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
            <div class="bg-(--bg-main) border-b border-(--border-primary) px-6 py-4 flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 flex items-center justify-center border border-slate-100 dark:border-slate-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-inbox text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">الطلبات الواردة — المرحلة الأولى</h3>
                    <p class="text-xs text-(--text-secondary)">مراجعة وقبول أو رفض الطلبات الأولية</p>
                </div>
            </div>

            @if($activeStageSubmissions->isEmpty())
                <div class="p-10 flex flex-col items-center justify-center text-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-inbox text-2xl text-(--text-secondary)"></i>
                    </div>
                    <p class="text-base font-bold text-(--text-primary)">لم يرسل مسؤول الاعتماد الطلب الأولي بعد</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-center text-(--text-secondary)">
                        <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                            <tr>
                                <th class="px-4 py-3 font-bold tracking-wider">#</th>
                                <th class="px-4 py-3 font-bold tracking-wider">تاريخ الإرسال</th>
                                <th class="px-4 py-3 font-bold tracking-wider text-center">الحالة</th>
                                <th class="px-4 py-3 font-bold tracking-wider text-center">العمليات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-(--border-primary)">
                            @foreach($activeStageSubmissions as $i => $sub)
                                @php $sStatus = $submissionStatusMap[$sub->status] ?? $submissionStatusMap['draft']; @endphp
                                <tr class="hover:bg-(--border-primary)/30 transition-colors">
                                    <td class="px-4 py-4 font-bold text-(--text-primary)">{{ $i + 1 }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $sub->submitted_at?->format('Y/m/d H:i') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $sStatus['class'] }}">
                                            {{ $sStatus['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center gap-2 flex-wrap">
                                            <button @click="showViewModal = true; viewData = {{ json_encode($sub->form_data) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-eye"></i> عرض
                                            </button>
                                            @if($sub->status === 'pending')
                                                <button @click="approveSubmissionId = {{ $sub->id }}; showApproveModal = true"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-circle-check"></i> موافقة
                                                </button>
                                                <button @click="rejectSubmissionId = {{ $sub->id }}; reasons = ['']; showRejectModal = true"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-circle-xmark"></i> رفض
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    @else
        {{-- Other roles: read-only info --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-6">
            <p class="text-sm text-(--text-secondary)">
                <i class="fa-solid fa-eye text-slate-400 me-2"></i>
                هذه المرحلة تعرض الطلب الأولي المقدم من مسؤول الاعتماد. دورك يبدأ في المرحلة التالية.
            </p>
        </div>
    @endif

    {{-- ═══════ MODAL: FORM SUBMISSION (Officer) ════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showFormModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showFormModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showFormModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                        @click.away="showFormModal = false"
                        class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-5xl">

                        <form method="POST" action="{{ route('requests.stage_one.store', $accreditationRequest) }}">
                            @csrf

                            {{-- Modal Header --}}
                            <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shrink-0">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-(--text-primary)">نموذج الطلب الأولي للاعتماد</h3>
                                        <p class="text-xs text-(--text-secondary)">البيانات معبأة تلقائياً من سجلات النظام</p>
                                    </div>
                                </div>
                                <button type="button" @click="showFormModal = false"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) hover:text-(--text-primary) transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <div class="p-6 space-y-8 max-h-[75vh] overflow-y-auto">

                                {{-- ── SECTION ONE: Basic Program Data ─────────────────────── --}}
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="w-7 h-7 rounded-lg bg-orange-500 text-white text-xs font-black flex items-center justify-center shrink-0">1</span>
                                        <h4 class="font-bold text-(--text-primary)">البيانات الأساسية للبرنامج</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                                        @php
                                            $readonlyClass = 'bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) text-sm rounded-lg block w-full p-2.5 cursor-not-allowed opacity-80';
                                        @endphp

                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">المرحلة الدراسية</label>
                                            <input readonly value="{{ $degreeLabels[$prefill['degree_level']] ?? $prefill['degree_level'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">اسم البرنامج</label>
                                            <input readonly value="{{ $prefill['program_name'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">القسم العلمي</label>
                                            <input readonly value="{{ $prefill['department_name'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">الجامعة</label>
                                            <input readonly value="{{ $prefill['university_name'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">الكلية</label>
                                            <input readonly value="{{ $prefill['college_name'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">لغة البرنامج</label>
                                            <input readonly value="{{ $langLabels[$prefill['language']] ?? $prefill['language'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">الساعات المعتمدة</label>
                                            <input readonly value="{{ $prefill['credit_hours'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">تاريخ تأسيس البرنامج</label>
                                            <input readonly value="{{ $prefill['establishment_date'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">مدة الدراسة</label>
                                            <input readonly value="{{ $prefill['study_duration'] }}" class="{{ $readonlyClass }}">
                                        </div>
                                        <div class="sm:col-span-2 lg:col-span-3">
                                            <label class="block text-xs font-bold text-(--text-secondary) mb-1.5">عنوان الموقع الإلكتروني</label>
                                            <input readonly value="{{ $prefill['website_url'] }}" dir="ltr" class="{{ $readonlyClass }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- ── SECTION TWO: Contact Info Table ─────────────────────── --}}
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="w-7 h-7 rounded-lg bg-orange-500 text-white text-xs font-black flex items-center justify-center shrink-0">2</span>
                                        <h4 class="font-bold text-(--text-primary)">عناوين التواصل</h4>
                                    </div>

                                    <div class="overflow-x-auto rounded-xl border border-(--border-primary) shadow-sm">
                                        <table class="w-full text-sm">
                                            <thead class="bg-(--bg-main) text-xs uppercase">
                                                <tr class="border-b border-(--border-primary)">
                                                    <th class="px-4 py-3 text-start font-bold text-(--text-secondary) w-36">الصفة</th>
                                                    <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الاسم</th>
                                                    <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الهاتف</th>
                                                    <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الجوال</th>
                                                    <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">البريد الإلكتروني</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-(--border-primary)">

                                                @php
                                                    $contacts = [
                                                        ['title' => 'رئيس الجامعة',    'name' => $prefill['president_name'],  'phone' => $prefill['president_phone'],  'mobile' => $prefill['president_mobile'],  'email' => $prefill['president_email']],
                                                        ['title' => 'مسؤول الاعتماد',   'name' => $prefill['officer_name'],    'phone' => $prefill['officer_phone'],    'mobile' => $prefill['officer_mobile'],    'email' => $prefill['officer_email']],
                                                        ['title' => 'عميد الكلية',      'name' => $prefill['dean_name'],       'phone' => $prefill['dean_phone'],       'mobile' => $prefill['dean_mobile'],       'email' => $prefill['dean_email']],
                                                        ['title' => 'رئيس القسم',       'name' => $prefill['head_name'],       'phone' => $prefill['head_phone'],       'mobile' => $prefill['head_mobile'],       'email' => $prefill['head_email']],
                                                    ];
                                                @endphp

                                                {{-- Pre-filled rows (read-only) --}}
                                                @foreach($contacts as $contact)
                                                    <tr class="hover:bg-(--border-primary)/20 transition-colors">
                                                        <td class="px-4 py-3 font-bold text-(--text-primary) text-xs whitespace-nowrap">{{ $contact['title'] }}</td>
                                                        <td class="px-4 py-3"><input readonly value="{{ $contact['name'] }}" class="{{ $readonlyClass }} min-w-32"></td>
                                                        <td class="px-4 py-3"><input readonly value="{{ $contact['phone'] }}" dir="ltr" class="{{ $readonlyClass }} min-w-28"></td>
                                                        <td class="px-4 py-3"><input readonly value="{{ $contact['mobile'] }}" dir="ltr" class="{{ $readonlyClass }} min-w-28"></td>
                                                        <td class="px-4 py-3"><input readonly value="{{ $contact['email'] }}" dir="ltr" class="{{ $readonlyClass }} min-w-44"></td>
                                                    </tr>
                                                @endforeach

                                                {{-- Program Coordinator row (editable) --}}
                                                <tr class="bg-orange-50/40 dark:bg-orange-500/5">
                                                    <td class="px-4 py-3 font-bold text-orange-700 dark:text-orange-400 text-xs whitespace-nowrap">
                                                        منسق البرنامج
                                                        <span class="block text-[10px] font-normal text-orange-500/80">(مطلوب)</span>
                                                    </td>
                                                    @php $inputClass = 'bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400 transition-all min-w-32'; @endphp
                                                    <td class="px-4 py-3"><input type="text" name="coord_name" required placeholder="الاسم الكامل" class="{{ $inputClass }}"></td>
                                                    <td class="px-4 py-3"><input type="text" name="coord_phone" placeholder="الهاتف" dir="ltr" class="{{ $inputClass }} min-w-28"></td>
                                                    <td class="px-4 py-3"><input type="text" name="coord_mobile" placeholder="الجوال" dir="ltr" class="{{ $inputClass }} min-w-28"></td>
                                                    <td class="px-4 py-3">
                                                        <input type="email" name="coord_email" required placeholder="البريد الإلكتروني" dir="ltr" class="{{ $inputClass }} min-w-44">
                                                        <p class="text-[10px] text-orange-600 dark:text-orange-400 mt-1 font-bold">
                                                            <i class="fa-solid fa-circle-exclamation me-1"></i>سيُستخدم للتواصل في جميع المراحل
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-5 border-t border-(--border-primary) bg-(--bg-main) flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                                <button type="button" @click="showFormModal = false"
                                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i> إلغاء
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-8 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-black shadow-lg shadow-orange-500/20 transition-all cursor-pointer">
                                    <i class="fa-solid fa-paper-plane"></i> إرسال الطلب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════ MODAL: VIEW FORM DATA ════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showViewModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showViewModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showViewModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showViewModal = false"
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-4xl">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between">
                            <h3 class="font-bold text-(--text-primary)">عرض بيانات النموذج</h3>
                            <button @click="showViewModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) hover:text-(--text-primary) cursor-pointer">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">

                            {{-- Rejection reasons view --}}
                            <template x-if="viewData && viewData._reasons">
                                <div class="space-y-3">
                                    <p class="font-bold text-red-600 dark:text-red-400 text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-circle-xmark"></i> أسباب الرفض:
                                    </p>
                                    <ul class="space-y-2">
                                        <template x-for="(reason, i) in viewData._reasons" :key="i">
                                            <li class="flex items-start gap-2 text-sm text-(--text-secondary) bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 rounded-xl px-4 py-3">
                                                <i class="fa-solid fa-circle-dot text-red-400 mt-0.5 shrink-0"></i>
                                                <span x-text="reason"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            {{-- Full form data view --}}
                            <template x-if="viewData && viewData.section_one">
                                <div class="space-y-6">
                                    {{-- Section 1 --}}
                                    <div>
                                        <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-3">القسم الأول: البيانات الأساسية</p>
                                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                            <template x-for="[key, val] in Object.entries(viewData.section_one || {})" :key="key">
                                                <div class="bg-(--bg-main) rounded-xl p-3 border border-(--border-primary)">
                                                    <p class="text-[10px] text-(--text-secondary) mb-1 uppercase" x-text="key.replace(/_/g,' ')"></p>
                                                    <p class="text-sm font-bold text-(--text-primary) break-all" x-text="val || '—'"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    {{-- Section 2 --}}
                                    <div>
                                        <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-3">القسم الثاني: عناوين التواصل</p>
                                        <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
                                            <table class="w-full text-sm">
                                                <thead class="bg-(--bg-main) text-xs">
                                                    <tr class="border-b border-(--border-primary)">
                                                        <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الصفة</th>
                                                        <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الاسم</th>
                                                        <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الهاتف</th>
                                                        <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">الجوال</th>
                                                        <th class="px-4 py-3 text-start font-bold text-(--text-secondary)">البريد</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-(--border-primary)">
                                                    <template x-for="[key, contact] in Object.entries(viewData.section_two || {})" :key="key">
                                                        <tr class="hover:bg-(--border-primary)/20 transition-colors">
                                                            <td class="px-4 py-3 font-bold text-(--text-primary) whitespace-nowrap text-xs" x-text="contact.title"></td>
                                                            <td class="px-4 py-3 text-(--text-primary)" x-text="contact.name || '—'"></td>
                                                            <td class="px-4 py-3 text-(--text-secondary)" dir="ltr" x-text="contact.phone || '—'"></td>
                                                            <td class="px-4 py-3 text-(--text-secondary)" dir="ltr" x-text="contact.mobile || '—'"></td>
                                                            <td class="px-4 py-3 text-(--text-secondary)" dir="ltr" x-text="contact.email || '—'"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════ MODAL: REJECT (Secretary) ════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showRejectModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showRejectModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showRejectModal = false"
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-lg">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shrink-0">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-(--text-primary)">رفض الطلب الأولي</h3>
                                <p class="text-xs text-(--text-secondary)">أدخل أسباب الرفض بوضوح</p>
                            </div>
                        </div>

                        <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-one') }}/${rejectSubmissionId}/reject`">
                            @csrf
                            @method('PATCH')

                            <div class="p-6 space-y-3">
                                <template x-for="(reason, i) in reasons" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-bold text-(--text-secondary) shrink-0" x-text="i + 1"></span>
                                        <input type="text" :name="`reasons[${i}]`" x-model="reasons[i]" required
                                            placeholder="سبب الرفض..."
                                            class="flex-1 bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                                        <button type="button" @click="removeReason(i)" x-show="reasons.length > 1"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addReason()"
                                    class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer mt-2">
                                    <i class="fa-solid fa-plus"></i> إضافة سبب آخر
                                </button>
                            </div>

                            <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                                <button type="button" @click="showRejectModal = false"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-black transition-all cursor-pointer">
                                    <i class="fa-solid fa-circle-xmark"></i> تأكيد الرفض
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════ MODAL: APPROVE CONFIRM (Secretary) ═══════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showApproveModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showApproveModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showApproveModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showApproveModal = false"
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-md">

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-(--text-primary)">تأكيد الموافقة</h3>
                                    <p class="text-sm text-(--text-secondary) mt-1">سيتم إنشاء حساب منسق البرنامج وإرسال بريد التفعيل، والانتقال للمرحلة الثانية.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                إلغاء
                            </button>
                            <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-one') }}/${approveSubmissionId}/approve`" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black transition-all cursor-pointer">
                                    <i class="fa-solid fa-circle-check"></i> تأكيد الموافقة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
