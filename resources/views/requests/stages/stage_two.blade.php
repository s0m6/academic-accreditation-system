{{-- Stage Two: البيانات الأساسية للبرنامج --}}
@php
    $userRole = $user->role;
    $submissionStatusMap = [
        'pending'  => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'],
        'approved' => ['label' => 'موافق عليه',   'class' => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'],
        'rejected' => ['label' => 'مرفوض',         'class' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'],
        'draft'    => ['label' => 'مسودة',          'class' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20'],
    ];

    $latestSubmission = $activeStageSubmissions->first();
    // Consider draft as active too, so we don't show "Create" if a draft already exists.
    $hasActiveSubmission = $latestSubmission && in_array($latestSubmission->status, ['pending', 'approved', 'draft']);
    
    // Only Program Coordinator can submit in Stage Two
    $canSubmit = $userRole === 'program_coordinator' && ! $hasActiveSubmission;

    $stageOrder = ['stage_one', 'stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex = array_search('stage_two', $stageOrder);
    $isLocked = $currentStageIndex < $thisStageIndex;
@endphp

@if($isLocked)
    <div class="flex flex-col items-center justify-center py-20 text-center gap-6 animate-in fade-in zoom-in duration-500">
        <div class="relative">
            <div class="w-24 h-24 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 shadow-inner">
                <i class="fa-solid fa-lock text-4xl"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-slate-900">
                <i class="fa-solid fa-hourglass-half text-sm"></i>
            </div>
        </div>
        <div class="max-w-md">
            <h3 class="text-xl font-bold text-(--text-primary) mb-2">المرحلة غير متاحة حالياً</h3>
            <p class="text-(--text-secondary) leading-relaxed">
                لا يمكنك الوصول إلى محتوى "البيانات الأساسية" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>
@else

<div class="w-full text-start space-y-6" x-data="{
    showSubmitModal: false,
    showRejectModal: false,
    showApproveModal: false,
    showViewReasonsModal: false,
    showValidationModal: false,
    missingFields: [],
    submitActionUrl: '',
    viewData: null,
    rejectSubmissionId: null,
    approveSubmissionId: null,
    reasons: [''],
    rejectionReasons: [],
    addReason() { this.reasons.push(''); },
    removeReason(i) { if (this.reasons.length > 1) this.reasons.splice(i, 1); },

    validateSubmission(sub) {
        const data = sub.form_data || {};
        const missing = [];

        // 1. Decisions
        const decisionsList = {
            1: 'قرار إنشاء البرنامج',
            2: 'قرار الطاقة الاستيعابية',
            3: 'قرار قبول أول دفعة',
            4: 'قرار قبول دفعة العام الماضي',
            5: 'قرار قبول دفعة العام قبل الماضي',
            6: 'قرار اعتماد أحدث خطة دراسية',
            7: 'محضر قرار تخرج دفعة العام الحالي',
            8: 'قرار تقديم طلب الاعتماد الأكاديمي',
        };
        const decisionsData = data.decisions || [];
        const decisionFiles = data.decision_files || {};
        for (let i = 1; i <= 8; i++) {
            const d = decisionsData.find(item => item.id == i);
            const label = decisionsList[i];
            if (!d || !d.number || d.number.toString().trim() === '') missing.push({ cat: 'القرارات', field: `رقم القرار (${label})` });
            if (!d || !d.authority || d.authority.toString().trim() === '') missing.push({ cat: 'القرارات', field: `الجهة المصدرة (${label})` });
            if (!d || !d.date || d.date.toString().trim() === '') missing.push({ cat: 'القرارات', field: `تاريخ القرار (${label})` });
            if (!decisionFiles[i]) missing.push({ cat: 'القرارات', field: `المرفق PDF (${label})` });
        }

        // 2. Student Statistics
        const studentSections = {
            'planned': 'عدد الطلبة المخطط التحاقهم',
            'total': 'العدد الكلي للطلاب الملتحقين',
            'average': 'متوسط عدد الطلبة في الشعبة',
            'graduates_higher_ed': 'عدد الخريجين (دراسات عليا)',
            'graduates_employed': 'عدد الخريجين (توظيف)'
        };
        const rowLabels = {
            'general': 'قبول عام', 'special': 'قبول خاص', 'international': 'قبول دولي',
            'male': 'ذكور', 'female': 'إناث'
        };
        const periodLabels = { 'past': 'العام الماضي', 'current': 'العام الحالي', 'next': 'المتوقع' };
        if (!data.student_stats) {
            missing.push({ cat: 'إحصائيات الطلاب', field: 'بيانات إحصائيات الطلاب بالكامل' });
        } else {
            Object.entries(studentSections).forEach(([sKey, sLabel]) => {
                const section = data.student_stats[sKey] || {};
                let rows = ['male', 'female'];
                if (['planned', 'total'].includes(sKey)) rows = ['general', 'special', 'international'];
                rows.forEach(rKey => {
                    const row = section[rKey] || {};
                    ['past', 'current', 'next'].forEach(pKey => {
                        const val = row[pKey];
                        // In stats, if it's undefined, null, empty string, or 0 (often means not filled), we flag it
                        if (val === undefined || val === null || val === '' || val === 0) {
                             missing.push({ cat: 'إحصائيات الطلاب', field: `${sLabel} - ${rowLabels[rKey]} (${periodLabels[pKey]})` });
                        }
                    });
                });
            });
        }

        // 3. Faculty Statistics Table - NECESSARY
        const facultyRanks = {
            'professor': 'أستاذ', 'associate': 'أستاذ مشارك', 'assistant': 'أستاذ مساعد',
            'lecturer': 'مدرس', 'teaching_assistant': 'معيد'
        };
        const facultyCols = { 'male': 'ذكور', 'female': 'إناث', 'load': 'العبء التدريسي', 'parttime': 'غير المتفرغين' };
        if (!data.faculty_stats) {
            missing.push({ cat: 'إحصائيات الهيئة التدريسية', field: 'بيانات إحصائيات الهيئة بالكامل' });
        } else {
            Object.entries(facultyRanks).forEach(([rKey, rLabel]) => {
                const rankData = data.faculty_stats[rKey] || {};
                Object.entries(facultyCols).forEach(([cKey, cLabel]) => {
                    const val = rankData[cKey];
                    if (val === undefined || val === null || val === '' || val === 0) {
                        missing.push({ cat: 'إحصائيات الهيئة التدريسية', field: `${rLabel} - ${cLabel}` });
                    }
                });
            });
        }

        // 4. (Skipped: Faculty Members List - per user request)

        return missing;
    },

    handleSubmission(sub, url) {
        const errors = this.validateSubmission(sub);
        this.viewData = sub;
        if (errors.length > 0) {
            this.missingFields = errors;
            this.showValidationModal = true;
        } else {
            this.submitActionUrl = url;
            this.showSubmitModal = true;
        }
    }
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

    {{-- Header Card --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="bg-(--bg-main) border-b border-(--border-primary) px-6 py-4 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">نماذج البيانات الأساسية (المرحلة الثانية)</h3>
                    <p class="text-xs text-(--text-secondary)">إدارة وتعبئة البيانات التفصيلية للبرنامج</p>
                </div>
            </div>
            
            @if($canSubmit)
                <form action="{{ route('requests.stage_two.draft', $accreditationRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        إنشاء نموذج جديد
                    </button>
                </form>
            @endif
        </div>

        {{-- Submissions Table --}}
        @if($activeStageSubmissions->isEmpty())
            <div class="p-16 flex flex-col items-center justify-center text-center gap-4">
                <div class="w-20 h-20 rounded-3xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-clipboard-list text-3xl text-(--text-secondary)"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-(--text-primary)">لا توجد نماذج مرسلة في هذه المرحلة</p>
                    @if($userRole === 'program_coordinator')
                        <p class="text-sm text-(--text-secondary) mt-1 max-w-xs mx-auto">ابدأ بتعبئة البيانات الأساسية بالضغط على "إنشاء نموذج جديد"</p>
                    @else
                        <p class="text-sm text-(--text-secondary) mt-1 max-w-xs mx-auto">في انتظار قيام منسق البرنامج ببدء هذه المرحلة.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center text-(--text-secondary)">
                    <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary) border-b border-(--border-primary)">
                        <tr>
                            <th class="px-6 py-4 font-bold tracking-wider w-16 text-center">م</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">تاريخ الإرسال</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">تاريخ آخر تحديث</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center text-nowrap">تاريخ رد المجلس</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">الحالة</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @foreach ($activeStageSubmissions as $i => $sub)
                            @php $sStatus = $submissionStatusMap[$sub->status] ?? $submissionStatusMap['draft']; @endphp
                            <tr class="hover:bg-(--border-primary)/30 transition-colors">
                                <td class="px-6 py-5 font-bold text-(--text-primary)">{{ $i + 1 }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    {{ $sub->submitted_at?->format('Y/m/d H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    {{ $sub->updated_at?->format('Y/m/d H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    {{ $sub->decision_at?->format('Y/m/d H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $sStatus['class'] }}">
                                        {{ $sStatus['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        {{-- Edit Action (Only for Draft and Program Coordinator) --}}
                                        @if($sub->status === 'draft' && $userRole === 'program_coordinator')
                                            <a href="{{ route('requests.stage_two.edit', [$accreditationRequest, $sub]) }}" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:-translate-y-0.5 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 dark:hover:bg-blue-500/30 text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-edit"></i> تعديل
                                            </a>
                                            
                                            {{-- View Action (For Coordinator in Draft) --}}
                                            <a href="{{ route('requests.stage_two.show', [$accreditationRequest, $sub]) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 hover:-translate-y-0.5 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20 dark:hover:bg-slate-500/30 text-xs font-bold transition-all cursor-pointer no-underline">
                                                <i class="fa-solid fa-eye"></i> عرض النموذج
                                            </a>
                                            
                                            {{-- Submit Action --}}
                                            <button type="button" @click="handleSubmission({{ Js::from($sub) }}, '{{ route('requests.stage_two.submit', [$accreditationRequest, $sub]) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:-translate-y-0.5 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 dark:hover:bg-green-500/30 text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-paper-plane"></i> رفع للمجلس
                                            </button>
                                        @else
                                            {{-- View Action (For everyone else or other statuses) --}}
                                            <a href="{{ route('requests.stage_two.show', [$accreditationRequest, $sub]) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 hover:-translate-y-0.5 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20 dark:hover:bg-slate-500/30 text-xs font-bold transition-all cursor-pointer no-underline">
                                                <i class="fa-solid fa-eye"></i> عرض النموذج
                                            </a>
                                        @endif

                                        {{-- Secretariat Actions --}}
                                        @if($sub->status === 'pending' && $userRole === 'council_secretariat')
                                            <button @click="approveSubmissionId = {{ $sub->id }}; showApproveModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:-translate-y-0.5 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 dark:hover:bg-green-500/30 text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-circle-check"></i> موافقة
                                            </button>
                                            <button @click="rejectSubmissionId = {{ $sub->id }}; reasons = ['']; showRejectModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:-translate-y-0.5 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 dark:hover:bg-red-500/30 text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-circle-xmark"></i> رفض
                                            </button>
                                        @endif

                                        {{-- Rejection Reasons (View) --}}
                                        @if($sub->status === 'rejected')
                                            <button @click="showViewReasonsModal = true; rejectionReasons = {{ json_encode($sub->decision_reasons) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:-translate-y-0.5 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 dark:hover:bg-red-500/30 text-xs font-bold transition-all cursor-pointer">
                                                <i class="fa-solid fa-circle-info"></i> الأسباب
                                            </button>
                                        @endif

                                        {{-- Print Action (Always available if not draft) --}}
                                        @if($sub->status !== 'draft')
                                            <a href="{{ route('requests.stage_two.print', [$accreditationRequest, $sub]) }}" 
                                                x-data="{ loading: false, finished: false }"
                                                x-on:click="loading = true; finished = false; setTimeout(() => { loading = false; finished = true; setTimeout(() => finished = false, 5000) }, 8000)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-bold transition-all hover:-translate-y-0.5 cursor-pointer no-underline"
                                                :class="finished ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 dark:hover:bg-green-500/30' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 dark:hover:bg-indigo-500/30'"
                                                :class="{ 'opacity-60 pointer-events-none': loading }"
                                                title="طباعة وتحميل القرارات (ZIP)">
                                                <i x-show="!loading && !finished" class="fa-solid fa-download"></i>
                                                <i x-show="loading" class="fa-solid fa-circle-notch animate-spin"></i>
                                                <i x-show="finished" class="fa-solid fa-check"></i>
                                                <span x-text="loading ? 'جاري التجهيز...' : (finished ? 'تم التحميل' : 'تحميل النموذج')"></span>
                                            </a>
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

    {{-- Empty Notification if no submissions yet --}}
    @if($activeStageSubmissions->isEmpty() && $userRole !== 'program_coordinator')
         <div class="p-6 rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-blue-50/30 dark:bg-blue-500/5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">
                هذه المرحلة مخصصة لمنسق البرنامج. حالما يتم تعبئة البيانات، ستظهر التفاصيل هنا للمراجعة.
            </p>
         </div>
    @endif

    {{-- ═══════ MODALS ═══════════════════════════════════════════════════ --}}
    
    {{-- MODAL: SUBMIT CONFIRM (Coordinator) --}}
    <template x-teleport="body">
        <div x-show="showSubmitModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showSubmitModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="showSubmitModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showSubmitModal = false"
                        class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-md text-start">

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-paper-plane text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-(--text-primary)">تأكيد الرفع للمجلس</h3>
                                    <p class="text-xs text-(--text-secondary) mt-1">
                                        هل أنت متأكد من رغبتك في رفع نموذج البيانات الأساسية للمجلس؟
                                    </p>
                                </div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4">
                                <div class="flex gap-3">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                                        بمجرد الرفع، سيتم قفل النموذج ولن تتمكن من تعديله إلا في حال طلب المجلس ذلك.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showSubmitModal = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                إلغاء
                            </button>
                            <form method="POST" :action="submitActionUrl" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black shadow-lg shadow-green-500/20 transition-all cursor-pointer">
                                    <i class="fa-solid fa-circle-check"></i> تأكيد وإرسال
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: REJECT (Secretary) --}}
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
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-lg text-start leading-relaxed">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shrink-0">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-(--text-primary)">رفض نموذج المرحلة الثانية</h3>
                                <p class="text-xs text-(--text-secondary)">سيتم إعادة النموذج للمنسق لتصحيح الملاحظات التالية</p>
                            </div>
                        </div>

                        <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-two') }}/${rejectSubmissionId}/reject`">
                            @csrf
                            @method('PATCH')

                            <div class="p-6 space-y-3">
                                <template x-for="(reason, i) in reasons" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-bold text-(--text-secondary) shrink-0" x-text="i + 1"></span>
                                        <input type="text" :name="`reasons[${i}]`" x-model="reasons[i]" required
                                            placeholder="أدخل ملاحظة الرفض..."
                                            class="flex-1 bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                                        <button type="button" @click="removeReason(i)" x-show="reasons.length > 1"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addReason()"
                                    class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer mt-2 px-1">
                                    <i class="fa-solid fa-plus-circle"></i> إضافة سبب آخر
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

    {{-- MODAL: APPROVE CONFIRM (Secretary) --}}
    <template x-teleport="body">
        <div x-show="showApproveModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showApproveModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showApproveModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showApproveModal = false"
                        class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-md text-start">

                        <div class="px-6 py-8">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-check-double text-green-600 dark:text-green-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-(--text-primary)">تأكيد الموافقة النهائية</h3>
                                <p class="text-sm text-(--text-secondary) mt-2 max-w-xs leading-relaxed">
                                    عند الموافقة على البيانات الأساسية، سينتقل البرنامج تلقائياً إلى المرحلة الثالثة:
                                    <span class="block font-bold text-blue-600 dark:text-blue-400 mt-1 uppercase tracking-wider text-[11px]">تقرير الدراسة الذاتية</span>
                                </p>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                إلغاء
                            </button>
                            <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-two') }}/${approveSubmissionId}/approve`" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black transition-all cursor-pointer">
                                    <i class="fa-solid fa-circle-check"></i> تأكيد وإغلاق المرحلة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: VIEW REJECTION REASONS --}}
    <template x-teleport="body">
        <div x-show="showViewReasonsModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showViewReasonsModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showViewReasonsModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showViewReasonsModal = false"
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-lg text-start">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shrink-0">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                                <h3 class="font-bold text-(--text-primary)">أسباب الرفض</h3>
                            </div>
                            <button @click="showViewReasonsModal = false" class="text-(--text-secondary) hover:text-(--text-primary) transition-all cursor-pointer p-2">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-3">
                                <template x-for="(reason, i) in rejectionReasons" :key="i">
                                    <li class="flex items-start gap-3 bg-(--bg-main) border border-(--border-primary) rounded-xl p-4 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0"></div>
                                        <p class="text-sm font-medium text-(--text-primary) leading-relaxed" x-text="reason"></p>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end">
                            <button @click="showViewReasonsModal = false"
                                class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-orange-500 text-white text-xs font-black shadow-lg shadow-orange-500/20 transition-all cursor-pointer">
                                فهمت ذلك
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    {{-- MODAL: VALIDATION ERRORS (Coordinator) --}}
    <template x-teleport="body">
        <div x-show="showValidationModal" style="display:none" class="relative z-[250]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showValidationModal = false"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div @click.away="showValidationModal = false"
                     class="relative w-full max-w-2xl rounded-2xl bg-(--surface-card) shadow-2xl border border-(--border-primary) flex flex-col max-h-[85vh]">

                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3 shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shadow-inner shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">بيانات غير مكتملة</h3>
                            <p class="text-xs text-(--text-secondary)">يجب إكمال كافة الحقول المطلوبة قبل الرفع للمجلس</p>
                        </div>
                        <button @click="showValidationModal = false" class="mr-auto text-(--text-secondary) hover:text-(--text-primary) transition p-2">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="overflow-y-auto flex-1 p-6 space-y-4">
                        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-sm text-amber-800 dark:text-amber-300 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info shrink-0 mt-0.5 text-lg"></i>
                            <span class="leading-relaxed">لقد اكتشف النظام وجود حقول لم يتم تعبئتها في نموذج البيانات الأساسية. يرجى مراجعة القائمة أدناه وتعبئتها في صفحة التعديل.</span>
                        </div>

                        {{-- Grouped Errors --}}
                        <div class="space-y-4">
                            @php
                                $categories = [
                                    'القرارات' => ['icon' => 'fa-file-signature', 'color' => 'blue'],
                                    'إحصائيات الطلاب' => ['icon' => 'fa-chart-bar', 'color' => 'emerald'],
                                    'إحصائيات الهيئة التدريسية' => ['icon' => 'fa-chalkboard-user', 'color' => 'violet'],
                                ];
                            @endphp

                            @foreach($categories as $cat => $style)
                                <div x-show="missingFields.some(f => f.cat === '{{ $cat }}')" class="rounded-xl border border-(--border-primary) overflow-hidden shadow-sm">
                                    <div class="px-4 py-3 bg-{{ $style['color'] }}-50 dark:bg-{{ $style['color'] }}-500/10 border-b border-(--border-primary) flex items-center gap-2">
                                        <i class="fa-solid {{ $style['icon'] }} text-{{ $style['color'] }}-600 dark:text-{{ $style['color'] }}-400 text-sm"></i>
                                        <span class="font-bold text-{{ $style['color'] }}-700 dark:text-{{ $style['color'] }}-300 text-sm">{{ $cat }}</span>
                                    </div>
                                    <div class="p-4 bg-(--surface-card)">
                                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                                            <template x-for="(f, i) in missingFields.filter(f => f.cat === '{{ $cat }}')" :key="i">
                                                <li class="flex items-start gap-2 text-xs text-(--text-primary)">
                                                    <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>
                                                    <span x-text="f.field" class="leading-relaxed"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end items-center shrink-0">
                        <div class="flex gap-3">
                            <button @click="showValidationModal = false"
                                    class="px-6 py-2.5 rounded-xl border border-(--border-primary) font-bold text-(--text-primary) hover:bg-(--surface-card) transition-all cursor-pointer text-sm">
                                إغلاق
                            </button>
                             <a :href="'{{ url('/requests/' . $accreditationRequest->id . '/stage-two') }}/' + (viewData?.id || '') + '/edit'"
                               class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold transition-all shadow-lg shadow-orange-500/20 flex items-center gap-2 text-sm no-underline">
                                <i class="fa-solid fa-pen-to-square"></i> الذهاب للتعديل الآن
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endif
