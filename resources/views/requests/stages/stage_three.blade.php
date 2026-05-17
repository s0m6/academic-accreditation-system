{{-- Stage Three: تقرير الدراسة الذاتية --}}
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
    
    // Only Program Coordinator can submit in Stage Three
    $canSubmit = $userRole === 'program_coordinator' && ! $hasActiveSubmission;

    $stageOrder = ['stage_one', 'stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex = array_search('stage_three', $stageOrder);
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
                لا يمكنك الوصول إلى محتوى "تقرير الدراسة الذاتية" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>
@else

<div class="w-full text-start space-y-6 shadow-sm overflow-hidden" x-data="{
    showSubmitModal: false,
    showRejectModal: false,
    showApproveModal: false,
    showViewReasonsModal: false,
    showValidationModal: false,
    showDownloadModal: false,
    isValidating: false,
    validationMissing: [],
    submitActionUrl: '',
    currentSubId: null,
    rejectSubmissionId: null,
    approveSubmissionId: null,
    reasons: [''],
    rejectionReasons: [],
    downloadStep: 0,
    downloadDone: false,
    downloadError: false,
    downloadSteps: [
        { label: 'إنشاء تقرير الدراسة الذاتية', icon: '📄', pct: 12 },
        { label: 'إنشاء تقرير المعيار الأول', icon: '📊', pct: 22 },
        { label: 'إنشاء تقرير المعيار الثاني', icon: '📊', pct: 32 },
        { label: 'إنشاء تقرير المعيار الثالث', icon: '📊', pct: 42 },
        { label: 'إنشاء تقرير المعيار الرابع', icon: '📊', pct: 52 },
        { label: 'إنشاء تقرير المعيار الخامس', icon: '📊', pct: 62 },
        { label: 'إنشاء تقرير المعيار السادس', icon: '📊', pct: 72 },
        { label: 'إنشاء تقرير المعيار السابع', icon: '📊', pct: 82 },
        { label: 'دمج الأدلة وضغط الملفات', icon: '🗜️', pct: 92 },
        { label: 'التقرير جاهز للتحميل!', icon: '✅', pct: 100 }
    ],
    downloadProgressPct: 0,
    _stepTimer: null,
    async startDownload(url) {
        this.showDownloadModal = true;
        this.downloadDone = false;
        this.downloadError = false;
        this.downloadStep = 0;
        this.downloadProgressPct = 0;
        // Animate progress steps while fetch is in progress
        let stepIndex = 0;
        const maxStepBeforeDone = this.downloadSteps.length - 2; // leave last step for completion
        this._stepTimer = setInterval(() => {
            if (stepIndex < maxStepBeforeDone) {
                stepIndex++;
                this.downloadStep = stepIndex;
                this.downloadProgressPct = this.downloadSteps[stepIndex].pct;
            }
        }, 3500);
        try {
            const response = await fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Server error: ' + response.status);
            const blob = await response.blob();
            // Done!
            clearInterval(this._stepTimer);
            this.downloadStep = this.downloadSteps.length - 1;
            this.downloadProgressPct = 100;
            this.downloadDone = true;
            // Trigger download
            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            const cd = response.headers.get('Content-Disposition');
            a.download = cd ? cd.split('filename=')[1]?.replace(/\x22/g,'') || 'report.zip' : 'report.zip';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(blobUrl);
        } catch(e) {
            clearInterval(this._stepTimer);
            this.downloadError = true;
        }
    },
    closeDownloadModal() {
        clearInterval(this._stepTimer);
        this.showDownloadModal = false;
    },
    addReason() { this.reasons.push(''); },
    removeReason(i) { if (this.reasons.length > 1) this.reasons.splice(i, 1); },
    async startValidation(subId, submitUrl) {
        this.isValidating = true;
        this.currentSubId = subId;
        this.validationMissing = [];
        this.submitActionUrl = submitUrl;

        try {
            const resp = await axios.post(`/requests/{{ $accreditationRequest->id }}/stage-three/${subId}/validate`);
            if (resp.data.success) {
                this.showSubmitModal = true;
            } else {
                this.validationMissing = resp.data.missing;
                this.showValidationModal = true;
            }
        } catch (e) {
            console.error(e);
            alert('تعذر الاتصال بالخادم للتحقق من البيانات.');
        } finally {
            this.isValidating = false;
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
                <div class="w-11 h-11 rounded-2xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-100 dark:border-violet-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-file-pen text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">تقرير الدراسة الذاتية</h3>
                    <p class="text-xs text-(--text-secondary)">مرحلة إعداد وتقديم تقرير التقييم الذاتي للبرنامج</p>
                </div>
            </div>
            
            @if($canSubmit)
                <form action="{{ route('requests.stage_three.draft', $accreditationRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        إنشاء مسودة
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
                    <p class="text-lg font-bold text-(--text-primary)">لا توجد مسودات أو تقارير مرسلة في هذه المرحلة</p>
                    @if($userRole === 'program_coordinator')
                        <p class="text-sm text-(--text-secondary) mt-1 max-w-xs mx-auto">ابدأ بإعداد تقرير الدراسة الذاتية بالضغط على "إنشاء مسودة"</p>
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
                                        {{-- Dummy Action Buttons as requested --}}
                                        @if($sub->status === 'draft' && $userRole === 'program_coordinator')
                                            <a href="{{ route('requests.stage_three.edit', [$accreditationRequest->id, $sub->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-edit"></i> تعديل
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('requests.stage_three.show', [$accreditationRequest->id, $sub->id]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-eye"></i> عرض
                                        </a>

                                        <button
                                            @click="startDownload('{{ route('requests.stage_three.print', [$accreditationRequest->id, $sub->id]) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-file-zipper"></i> تحميل التقرير
                                        </button>
                                        
                                        @if($sub->status === 'draft' && $userRole === 'program_coordinator')
                                            <button type="button" 
                                                @click="startValidation({{ $sub->id }}, '{{ route('requests.stage_three.submit', [$accreditationRequest, $sub]) }}')"
                                                :disabled="isValidating"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 text-xs font-bold transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-wait">
                                                <i class="fa-solid" :class="isValidating ? 'fa-circle-notch fa-spin' : 'fa-paper-plane'"></i>
                                                <span x-text="isValidating ? 'جاري التحقق...' : 'رفع للمجلس'"></span>
                                            </button>
                                        @endif

                                        {{-- Secretariat Actions --}}
                                        @if($sub->status === 'pending' && $userRole === 'council_secretariat')
                                            <button @click="approveSubmissionId = {{ $sub->id }}; showApproveModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-circle-check"></i> موافقة
                                            </button>
                                            <button @click="rejectSubmissionId = {{ $sub->id }}; reasons = ['']; showRejectModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-circle-xmark"></i> رفض
                                            </button>
                                        @endif

                                        {{-- Rejection Reasons (View) --}}
                                        @if($sub->status === 'rejected')
                                            <button @click="showViewReasonsModal = true; rejectionReasons = {{ json_encode($sub->decision_reasons) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                                <i class="fa-solid fa-circle-info"></i> الأسباب
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

    {{-- Empty Notification if no submissions yet --}}
    @if($activeStageSubmissions->isEmpty() && $userRole !== 'program_coordinator')
         <div class="p-6 rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-blue-50/30 dark:bg-blue-500/5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">
                هذه المرحلة مخصصة لمنسق البرنامج. حالما يتم البدء بإعداد تقرير الدراسة الذاتية، ستظهر التفاصيل هنا للمراجعة.
            </p>
         </div>
    @endif

    {{-- MODALS --}}
    
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
                                        هل أنت متأكد من رغبتك في رفع تقرير الدراسة الذاتية للمجلس؟
                                    </p>
                                </div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4">
                                <div class="flex gap-3">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                    <p class="text-[15px] text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                                        بمجرد الرفع، سيتم قفل التقرير ولن تتمكن من تعديله إلا في حال طلب المجلس ذلك.
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
                                <h3 class="font-bold text-(--text-primary)">رفض تقرير المرحلة الثالثة</h3>
                                <p class="text-xs text-(--text-secondary)">سيتم إعادة التقرير للمنسق لتصحيح الملاحظات التالية</p>
                            </div>
                        </div>

                        <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-three') }}/${rejectSubmissionId}/reject`">
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
                                    عند الموافقة على تقرير الدراسة الذاتية، سينتقل البرنامج تلقائياً إلى المرحلة الرابعة:
                                    <span class="block font-bold text-blue-600 dark:text-blue-400 mt-1 uppercase tracking-wider text-[11px]">مرحلة التقييم الميداني</span>
                                </p>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showApproveModal = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                إلغاء
                            </button>
                            <form method="POST" :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-three') }}/${approveSubmissionId}/approve`" class="inline">
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

    {{-- MODAL: VALIDATION ERRORS --}}
    <template x-teleport="body">
        <div x-show="showValidationModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showValidationModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showValidationModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showValidationModal = false"
                        class="relative rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-lg text-start overflow-hidden">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-red-50 dark:bg-red-500/10 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-200 dark:border-red-500/30 shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-red-800 dark:text-red-300">بيانات ناقصة في التقرير</h3>
                                    <p class="text-xs text-red-600/70 dark:text-red-400/70">يجب إكمال الحقول التالية قبل التمكن من رفع التقرير</p>
                                </div>
                            </div>
                            <button @click="showValidationModal = false" class="text-red-400 hover:text-red-600 transition-all cursor-pointer p-2">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>

                        <div class="p-6 max-h-[60vh] overflow-y-auto bg-(--bg-main)/50 custom-scrollbar">
                            <div class="space-y-3">
                                <template x-for="(error, i) in validationMissing" :key="i">
                                    <div class="flex items-start gap-3 bg-(--surface-card) border border-(--border-primary) rounded-xl p-4 shadow-sm group hover:border-red-200 dark:hover:border-red-500/30 transition-all">
                                        <div class="w-6 h-6 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-[10px] font-bold text-red-600 dark:text-red-400 border border-red-100 dark:border-red-500/20 shrink-0 mt-0.5" x-text="i + 1"></div>
                                        <p class="text-sm font-medium text-(--text-primary) leading-relaxed" x-text="error"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="px-6 py-5 border-t border-(--border-primary) bg-(--bg-main) flex items-center justify-between gap-4">
                            <p class="text-[11px] text-(--text-secondary) leading-tight italic max-w-[200px]">
                                * يرجى مراجعة كافة الأقسام والتأكد من حفظ التغييرات كمسودة قبل المحاولة مرة أخرى.
                            </p>
                            <div class="flex gap-3 shrink-0">
                                <button @click="showValidationModal = false"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                    إغلاق
                                </button>
                                <a :href="`{{ url('/requests/' . $accreditationRequest->id . '/stage-three') }}/${currentSubId}/edit`" 
                                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-black shadow-lg shadow-orange-500/20 transition-all cursor-pointer">
                                    <i class="fa-solid fa-edit"></i> الانتقال للتعديل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ── Download Progress Modal ── --}}
    <template x-teleport="body">
        <div
            x-show="showDownloadModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
            style="display:none">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

            {{-- Modal Card --}}
            <div
                x-show="showDownloadModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700"
                style="display:none">

                {{-- Header --}}
                <div class="bg-gradient-to-l from-[#1a3c5e] to-[#0f2340] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <i class="fa-solid fa-file-zipper text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base">إنشاء حزمة التقرير</h3>
                            <p class="text-white/60 text-xs">الدراسة الذاتية + المعايير + الأدلة</p>
                        </div>
                    </div>
                    <button
                        @click="closeDownloadModal()"
                        x-show="downloadDone || downloadError"
                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all cursor-pointer">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Progress Bar --}}
                <div class="px-6 pt-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">التقدم</span>
                        <span class="text-xs font-black text-[#1a3c5e] dark:text-blue-400" x-text="downloadProgressPct + '%'"></span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-700 ease-out"
                            :class="downloadDone ? 'bg-emerald-500' : (downloadError ? 'bg-red-500' : 'bg-[#1a3c5e]')"
                            :style="'width:' + downloadProgressPct + '%'"></div>
                    </div>
                </div>

                {{-- Steps List --}}
                <div class="px-6 py-4 space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="(step, i) in downloadSteps" :key="i">
                        <div class="flex items-center gap-3 py-1.5 transition-all duration-300"
                            :class="i <= downloadStep ? 'opacity-100' : 'opacity-25'">
                            {{-- Status indicator --}}
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-xs transition-all"
                                :class="{
                                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400': i < downloadStep || (i === downloadStep && downloadDone),
                                    'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400': i === downloadStep && !downloadDone && !downloadError,
                                    'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-600': i > downloadStep
                                }">
                                <template x-if="i < downloadStep || (i === downloadStep && downloadDone)">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </template>
                                <template x-if="i === downloadStep && !downloadDone && !downloadError">
                                    <i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i>
                                </template>
                                <template x-if="i > downloadStep">
                                    <span class="text-[9px] font-bold" x-text="i + 1"></span>
                                </template>
                            </div>
                            {{-- Step label --}}
                            <span class="text-sm font-medium"
                                :class="{
                                    'text-slate-600 dark:text-slate-300': i <= downloadStep && !downloadDone,
                                    'text-emerald-600 dark:text-emerald-400 font-bold': i === downloadStep && downloadDone,
                                    'text-slate-400 dark:text-slate-600': i > downloadStep
                                }"
                                x-text="step.label"></span>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">

                    {{-- Error state --}}
                    <template x-if="downloadError">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-red-500 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-red-700 dark:text-red-400">حدث خطأ أثناء الإنشاء</p>
                                <p class="text-xs text-slate-500">تحقق من الاتصال وحاول مرة أخرى</p>
                            </div>
                            <button @click="closeDownloadModal()" class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-bold hover:bg-red-600 transition-colors cursor-pointer">
                                إغلاق
                            </button>
                        </div>
                    </template>

                    {{-- Done state --}}
                    <template x-if="downloadDone">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">تم التحميل بنجاح!</p>
                                <p class="text-xs text-slate-500">تحقق من مجلد التنزيلات لديك</p>
                            </div>
                            <button @click="closeDownloadModal()" class="px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm font-bold hover:bg-emerald-600 transition-colors cursor-pointer">
                                إغلاق
                            </button>
                        </div>
                    </template>

                    {{-- Loading state --}}
                    <template x-if="!downloadDone && !downloadError">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-notch fa-spin text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">جاري المعالجة...</p>
                                <p class="text-xs text-slate-400">قد تستغرق العملية بضع دقائق</p>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </template>

</div>
@endif
