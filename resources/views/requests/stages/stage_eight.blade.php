{{-- stage_eight: تقارير نتائج التقييم(الختامية) --}}
@php
    $committee = $committee ?? null;
    $userRole = $user->role;
    $isChairEvaluator = $userRole === 'evaluator' && $committee?->chair_evaluator_id === ($user->evaluator?->id ?? null);
    $report = $accreditationRequest->committeeReport;
    
    $statusMap = [
        'draft'                => ['label' => 'مسودة', 'color' => 'gray'],
        'under_review'         => ['label' => 'قيد المراجعة', 'color' => 'amber'],
        'returned_for_edit'    => ['label' => 'معاد للتعديل', 'color' => 'red'],
        'submitted_to_council' => ['label' => 'مرفوع للمجلس', 'color' => 'blue'],
        'council_responded'    => ['label' => 'تم رد المجلس', 'color' => 'purple'],
        'uni_responded'        => ['label' => 'تم رد الجامعة', 'color' => 'indigo'],
        'final_under_review'   => ['label' => 'قيد المراجعة النهائية', 'color' => 'orange'],
        'completed'            => ['label' => 'مكتمل', 'color' => 'green'],
    ];

    $currentStatus = $report ? $report->status : 'uni_responded';
    $st = $statusMap[$currentStatus] ?? ['label' => 'قيد المعالجة النهائية', 'color' => 'orange'];
    
    $isCommitteeMember = $userRole === 'evaluator' && 
                        $committee && 
                        $committee->chair_evaluator_id !== ($user->evaluator?->id ?? null) && 
                        $committee->acceptedMembers->pluck('evaluator_id')->contains($user->evaluator?->id ?? null);

    $committeeApprovals = $committeeApprovals ?? collect();
    $allMembersApproved = $report && $committeeApprovals->count() > 0 && 
                        $committeeApprovals->where('status', 'approved')->count() === $committeeApprovals->count();
                        
    $memberApproval = $isCommitteeMember ? $committeeApprovals->where('member_id', $user->evaluator?->id ?? null)->first() : null;

    $stageOrder = ['stage_one', 'stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex = array_search('stage_eight', $stageOrder);
    $isLocked = $currentStageIndex < $thisStageIndex;
    
    $isCouncilCoordinator = $userRole === 'council_coordinator' && $accreditationRequest->council_coordinator_id === $user->id;
    $isCouncilSecretariat = $userRole === 'council_secretariat';
    $canSeeInternalNotes = $isChairEvaluator || $isCommitteeMember || $isCouncilCoordinator || $isCouncilSecretariat;
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
                لا يمكنك الوصول إلى محتوى "تقارير التقييم الختامية" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>
@else

<div class="w-full text-start space-y-6" x-data="{
    nullScoredIndicators: [],
    showNullModal: false,
    isValidating: false,
    showRequestModal: false,
    showWithdrawModal: false,
    
    showRejectModal: false,
    rejectReasons: [''],
    addReason() { this.rejectReasons.push(''); },
    removeReason(i) { if (this.rejectReasons.length > 1) this.rejectReasons.splice(i, 1); },

    showSignModal: false,
    signStep: 1,
    signature6: null,
    signatureFinal: null,
    pad6: null,
    padFinal: null,
    submitType: 'member',
    isPortrait: false,
    checkOrientation() {
        this.isPortrait = window.matchMedia('(orientation: portrait)').matches && window.innerWidth < 1024;
    },

    async tryRequestApproval() {
        if (this.isValidating) return;
        
        this.isValidating = true;
        try {
            const response = await axios.post('{{ route('requests.stage_eight.validate', $accreditationRequest) }}');
            this.nullScoredIndicators = response.data.nullScoredIndicators;
            
            if (this.nullScoredIndicators.length > 0) {
                this.showNullModal = true;
            } else {
                this.showRequestModal = true;
            }
        } catch (error) {
            console.error('Validation error:', error);
            alert('حدث خطأ أثناء التحقق من البيانات. يرجى المحاولة مرة أخرى.');
        } finally {
            this.isValidating = false;
        }
    },
    
    init() {
        this.checkOrientation();
        window.addEventListener('resize', () => {
            this.checkOrientation();
            if (this.showSignModal) {
                if (this.signStep === 1) this.initPad('canvas6');
                if (this.signStep === 2) this.initPad('canvasFinal');
            }
        });
        const observer = new MutationObserver(() => {
            const color = 'rgb(0, 0, 0)';
            if (this.pad6) this.pad6.penColor = color;
            if (this.padFinal) this.padFinal.penColor = color;
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    },
    
    initPad(refName) {
        this.$nextTick(() => {
            const canvas = this.$refs[refName];
            if (!canvas) return;

            requestAnimationFrame(() => {
                const padKey = 'pad' + refName.replace('canvas', '');
                const color = 'rgb(0, 0, 0)';

                if (!this[padKey]) {
                    this[padKey] = new window.SignaturePad(canvas, {
                        backgroundColor: 'rgba(255, 255, 255, 0)',
                        penColor: color,
                        minWidth: 1.2,
                        maxWidth: 4,
                        velocityFilterWeight: 0.6,
                    });
                } else {
                    this[padKey].penColor = color;
                }

                const rect = canvas.parentElement.getBoundingClientRect();
                if (rect.width === 0 || rect.height === 0) {
                    setTimeout(() => this.initPad(refName), 100);
                    return;
                }

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                this[padKey].clear();
            });
        });
    },
    clearPad(num) {
        if (this['pad' + num]) this['pad' + num].clear();
    },
    nextStep() {
        if (this.signStep === 1) {
            if (this.pad6 && this.pad6.isEmpty()) return alert('الرجاء توقيع نموذج مقاييس التقييم الختامي');
            this.signature6 = this.pad6.toDataURL('image/svg+xml');
            this.signStep = 2;
        } else if (this.signStep === 2) {
            if (this.padFinal && this.padFinal.isEmpty()) return alert('الرجاء التوقيع على القرار النهائي');
            this.signatureFinal = this.padFinal.toDataURL('image/svg+xml');
            this.signStep = 3;
        }
    },
    prevStep() {
        if (this.signStep > 1) this.signStep--;
    },
    resetSignatures() {
        this.signStep = 1;
        this.signature6 = null;
        this.signatureFinal = null;
        if(this.pad6) this.pad6.clear();
        if(this.padFinal) this.padFinal.clear();
    }
}" x-init="$watch('showSignModal', value => { if(value) { checkOrientation(); if(signStep === 1) initPad('canvas6'); } }); $watch('signStep', value => { if(value === 1) initPad('canvas6'); if(value === 2) initPad('canvasFinal'); })">

    {{-- Null Indicators Warning Modal --}}
    <template x-teleport="body">
        <div x-show="showNullModal" style="display:none" class="relative z-[250]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showNullModal = false"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div @click.away="showNullModal = false"
                     class="relative w-full max-w-2xl rounded-2xl bg-(--surface-card) shadow-2xl border border-(--border-primary) flex flex-col max-h-[85vh]">

                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shadow-inner shrink-0">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-(--text-primary)">مؤشرات غير مكتملة التقييم</h3>
                                <p class="text-xs text-(--text-secondary)">يجب تقييم جميع المؤشرات قبل طلب موافقة الأعضاء</p>
                            </div>
                        </div>
                        <button @click="showNullModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="overflow-y-auto flex-1 p-6 space-y-4">
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-sm text-amber-800 dark:text-amber-300 flex items-start gap-2">
                            <i class="fa-solid fa-circle-info shrink-0 mt-0.5"></i>
                            <span>المؤشرات التالية لم يتم تقييمها بعد (التقييم الختامي).</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="(standard, si) in nullScoredIndicators" :key="si">
                                <div class="flex items-center justify-between p-4 rounded-xl border border-(--border-primary) bg-(--surface-card) hover:bg-(--bg-main) transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-(--text-primary)" x-text="standard.standard_name"></p>
                                            <p class="text-[11px] text-(--text-secondary) mt-0.5">المعيار الرئيسي رقم <span x-text="si + 1"></span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-500/20 flex items-center gap-2">
                                            <span class="text-base font-black" x-text="standard.total_missing"></span>
                                            <span class="text-[10px] font-bold">مؤشر غير مقيم</span>
                                        </div>
                                        <i class="fa-solid fa-circle-exclamation text-red-500 animate-pulse"></i>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-between items-center shrink-0">
                        <span class="text-xs text-(--text-secondary)">
                            <i class="fa-solid fa-circle-xmark text-red-500 ml-1"></i>
                            لا يمكن طلب الموافقة حتى اكتمال التقييم
                        </span>
                        <div class="flex gap-3">
                            <button @click="showNullModal = false"
                                    class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold text-(--text-primary) hover:bg-(--bg-main) transition">
                                إغلاق
                            </button>
                            <a href="{{ route('requests.stage_eight.rubrics_edit', $accreditationRequest) }}"
                               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center gap-2">
                                <i class="fa-solid fa-pen"></i> الانتقال للتقييم الختامي
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-bold shadow-sm">
            <i class="fa-solid fa-circle-xmark text-xl shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Top Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner
                    {{ $st['color'] === 'green' ? 'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                    : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                    : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                    : ($st['color'] === 'purple' ? 'bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'
                    : 'bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'))) }}">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-(--text-secondary) mb-1">حالة المرحلة</h3>
                    <p class="font-black text-(--text-primary)">{{ $st['label'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ الرد النهائي</h3>
                    <p class="font-black text-(--text-primary) tracking-wide">
                        {{ $report && $report->stage8_submitted_at ? $report->stage8_submitted_at->format('Y/m/d H:i') : ($report && $report->updated_at ? $report->updated_at->format('Y/m/d H:i') : 'جاري المعالجة...') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-inner shrink-0">
                <i class="fa-solid fa-copy text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-(--text-primary)">النماذج والتقارير الختامية</h3>
                <p class="text-xs text-(--text-secondary)">النماذج المعتمدة في هذه المرحلة (المرحلة الثامنة)</p>
            </div>
        </div>

        <div class="p-0 overflow-x-auto">
            <table class="w-full text-sm text-start whitespace-nowrap">
                <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                    <tr>
                        <th class="px-5 py-4 text-start">اسم النموذج</th>
                        <th class="px-5 py-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">نموذج رد المؤسسة التعليمية على التوصيات</td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('requests.stage_seven.form9.show', $accreditationRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </a>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">مقاييس تقييم البرنامج(التقييم الختامي)</td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                @if($isChairEvaluator && in_array($currentStatus, ['uni_responded', 'returned_for_edit']))
                                    <a href="{{ route('requests.stage_eight.rubrics_edit', $accreditationRequest) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </a>
                                @endif
                                <a href="{{ route('requests.stage_eight.rubrics_show', $accreditationRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </a>
                                <a href="{{ route('requests.stage_eight.comparison', $accreditationRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 dark:bg-orange-500/10 dark:hover:bg-orange-500/20 dark:text-orange-400 dark:border-orange-500/20 text-xs font-bold transition shadow-sm">
                                    <i class="fa-solid fa-code-compare"></i> مقارنة مع النسخة الأولية
                                </a>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">التقرير النهائي للجنة المقيمين</td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('requests.stage_eight.final_report', $accreditationRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </a>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">القرار النهائي وتوصيات لجنة المقيمين بخصوص اعتماد البرنامج</td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('requests.stage_eight.final_decision', $accreditationRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </a>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($committee)
        @php
            $membersList = $committee->activeMembers->where('member_status', 'accepted')->filter(fn($m) => $m->evaluator_id !== $committee->chair_evaluator_id);
        @endphp
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-100 dark:border-purple-500/20 shadow-inner shrink-0">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-(--text-primary)">موافقات أعضاء اللجنة</h3>
                        <p class="text-xs text-(--text-secondary)">متابعة توقيعات وموافقات الأعضاء في المراجعة النهائية @if($report && $report->current_iteration > 0) (الدورة رقم {{ $report->current_iteration }}) @endif</p>
                    </div>
                </div>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-sm text-start whitespace-nowrap">
                    <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">#</th>
                            <th class="px-5 py-4">الاسم</th>
                            <th class="px-5 py-4">الحالة</th>
                            <th class="px-5 py-4">تاريخ الرد</th>
                            @if($canSeeInternalNotes)
                            <th class="px-5 py-4">ملاحظات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @forelse($membersList as $index => $member)
                            @php
                                $showApprovals = !in_array($currentStatus, ['uni_responded', 'returned_for_edit']);
                                $approval = $showApprovals ? $committeeApprovals->where('member_id', $member->evaluator_id)->first() : null;
                            @endphp
                            <tr class="hover:bg-(--bg-main) transition-colors">
                                <td class="px-5 py-4 text-center text-(--text-secondary)">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 font-bold text-(--text-primary)">{{ $member->evaluator->user->name ?? 'عضو لجنة' }}</td>
                                <td class="px-5 py-4">
                                    @if($approval)
                                        @if($approval->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
                                                <i class="fa-solid fa-check"></i> تمت الموافقة
                                            </span>
                                        @elseif($approval->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                                <i class="fa-solid fa-xmark"></i> يوجد ملاحظات
                                            </span>
                                        @elseif($approval->status === 'canceled')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">
                                                <i class="fa-solid fa-ban"></i> ملغي
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                                                <i class="fa-solid fa-hourglass-half"></i> بانتظار الموافقة
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
                                            <i class="fa-solid fa-clock"></i> لم يُطلب بعد
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-(--text-secondary)">{{ $approval && $approval->responded_at ? $approval->responded_at->format('Y/m/d H:i') : '—' }}</td>
                                @if($canSeeInternalNotes)
                                <td class="px-5 py-4">
                                    @if($approval && $approval->status === 'rejected' && $approval->reject_reason)
                                        @php $reasons = json_decode($approval->reject_reason, true) ?? []; @endphp
                                        <div x-data="{ showReason: false }" class="relative">
                                            <button @click="showReason = true" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">عرض الملاحظات ({{ count($reasons) }})</button>
                                            <template x-teleport="body">
                                                <div x-show="showReason" style="display:none" class="relative z-[200]">
                                                    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showReason = false"></div>
                                                    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                                                        <div @click.away="showReason = false" class="relative w-full max-w-md rounded-2xl bg-(--surface-card) shadow-2xl text-start">
                                                            <div class="px-6 py-5 border-b border-(--border-primary) flex justify-between items-center bg-(--bg-main)">
                                                                <h3 class="font-bold text-(--text-primary)">ملاحظات العضو الختامية</h3>
                                                                <button @click="showReason = false"><i class="fa-solid fa-xmark"></i></button>
                                                            </div>
                                                            <div class="p-6 space-y-3">
                                                                @foreach($reasons as $r)
                                                                    <div class="p-4 rounded-xl bg-(--bg-main) border border-(--border-primary) text-sm text-(--text-secondary)">{{ $r }}</div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @else
                                        <span class="text-(--text-secondary)">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-(--text-secondary)">لا يوجد أعضاء في اللجنة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3 mt-6 border-t border-(--border-primary) pt-6">
        @if($isChairEvaluator)
            @if(in_array($currentStatus, ['uni_responded', 'returned_for_edit']))
                    <button @click="tryRequestApproval()" 
                            :disabled="isValidating"
                            class="px-6 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold shadow-md transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-wait">
                        <template x-if="!isValidating">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> طلب موافقة الأعضاء (المراجعة النهائية)
                            </div>
                        </template>
                        <template x-if="isValidating">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...
                            </div>
                        </template>
                    </button>
            @endif

            @if($currentStatus === 'final_under_review')
                <button @click="showWithdrawModal = true" class="px-6 py-3 rounded-xl bg-orange-100 text-orange-700 hover:bg-orange-200 font-bold border border-orange-200 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-arrow-rotate-left"></i> سحب الطلب للتعديل
                </button>
            @endif

            @if($currentStatus === 'final_under_review' && $allMembersApproved)
                <button @click="showSignModal = true; submitType = 'chair'; resetSignatures();" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-file-signature"></i> الاعتماد النهائي للتقرير
                </button>
            @endif
        @endif

        @if($isCommitteeMember && $memberApproval && $memberApproval->status === 'pending')
            <button @click="showRejectModal = true; rejectReasons = [''];" class="px-6 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-bold border border-red-200 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> رفض لوجود ملاحظات ختامية
            </button>
            <button @click="showSignModal = true; submitType = 'member'; resetSignatures();" class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold shadow-md transition-colors flex items-center gap-2">
                <i class="fa-solid fa-signature"></i> توقيع وموافقة نهائية
            </button>
        @endif
    </div>

    @if($isChairEvaluator)
        <template x-teleport="body">
            <div x-show="showRequestModal" style="display:none" class="relative z-[200]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showRequestModal = false"></div>
                <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                    <div @click.away="showRequestModal = false" class="relative w-full max-w-md rounded-2xl bg-(--surface-card) shadow-2xl text-start border border-(--border-primary)">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid fa-paper-plane"></i></div>
                            <h3 class="font-bold text-lg text-(--text-primary)">تأكيد طلب الموافقة الختامية</h3>
                            <p class="text-sm text-(--text-secondary) mt-2">سيتم إرسال إشعار لأعضاء اللجنة للدخول وتوقيع التقارير الختامية. لن تتمكن من التعديل أثناء المراجعة.</p>
                        </div>
                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button @click="showRequestModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold">إلغاء</button>
                            <form method="POST" action="{{ route('requests.stage_eight.request_approval', $accreditationRequest) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">تأكيد الطلب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template x-teleport="body">
            <div x-show="showWithdrawModal" style="display:none" class="relative z-[200]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showWithdrawModal = false"></div>
                <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                    <div @click.away="showWithdrawModal = false" class="relative w-full max-w-md rounded-2xl bg-(--surface-card) shadow-2xl text-start border border-(--border-primary)">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid fa-arrow-rotate-left"></i></div>
                            <h3 class="font-bold text-lg text-(--text-primary)">سحب الطلب للتعديل</h3>
                            <p class="text-sm text-(--text-secondary) mt-2">سيتم إلغاء الموافقات الختامية المعلقة حالياً للتمكن من تعديل التقارير الختامية.</p>
                        </div>
                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button @click="showWithdrawModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold">إلغاء</button>
                            <form method="POST" action="{{ route('requests.stage_eight.withdraw', $accreditationRequest) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-600 text-white font-bold hover:bg-orange-700">تأكيد السحب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    @endif

    @if($isCommitteeMember)
        <template x-teleport="body">
            <div x-show="showRejectModal" style="display:none" class="relative z-[200]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showRejectModal = false"></div>
                <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                    <div @click.away="showRejectModal = false" class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) shadow-2xl text-start border border-(--border-primary)">
                        <div class="px-6 py-5 border-b border-(--border-primary) flex justify-between items-center bg-(--bg-main)">
                            <h3 class="font-bold text-(--text-primary)"><i class="fa-solid fa-xmark text-red-600 ml-2"></i> إرسال ملاحظات ختامية</h3>
                            <button @click="showRejectModal = false"><i class="fa-solid fa-xmark text-(--text-secondary)"></i></button>
                        </div>
                        <form method="POST" action="{{ route('requests.stage_eight.member_reject', $accreditationRequest) }}">
                            @csrf
                            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                                <template x-for="(reason, i) in rejectReasons" :key="i">
                                    <div class="relative">
                                        <textarea x-model="rejectReasons[i]" name="reject_reasons[]" rows="3" required placeholder="اكتب ملاحظتك الختامية هنا..." class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm focus:ring-2 focus:ring-red-400"></textarea>
                                        <button type="button" @click="removeReason(i)" x-show="rejectReasons.length > 1" class="absolute top-3 left-3 text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </template>
                                <button type="button" @click="addReason" class="text-sm font-bold text-blue-600 hover:text-blue-700"><i class="fa-solid fa-plus ml-1"></i> إضافة ملاحظة أخرى</button>
                            </div>
                            <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                                <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold">إلغاء</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">تأكيد الرفض والإرسال</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    @endif

    <template x-teleport="body">
        <div x-show="showSignModal" style="display:none" class="relative z-[300]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-md"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center">

                {{-- Portrait Mode: rotate prompt --}}
                <div x-show="isPortrait" style="display:none"
                     class="relative w-full max-w-sm mx-4 bg-(--surface-card) shadow-2xl rounded-3xl border border-(--border-primary) overflow-hidden p-10 flex flex-col items-center justify-center text-center gap-6">
                    <button @click="showSignModal = false" class="absolute top-4 left-4 w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                    <div class="w-24 h-24 relative">
                        <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-500/20 rounded-full animate-ping opacity-30"></div>
                        <div class="relative w-full h-full bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                            <i class="fa-solid fa-mobile-screen-button text-4xl" style="transform: rotate(90deg);"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-(--text-primary) mb-2">يرجى تدوير الجهاز</h3>
                        <p class="text-(--text-secondary) leading-relaxed font-bold text-sm">
                            لتوقيع بشكل مريح، يرجى تدوير هاتفك إلى الوضع العرضي (Landscape).
                        </p>
                    </div>
                </div>

                {{-- Landscape/Desktop Mode: full-screen signature --}}
                <div x-show="!isPortrait" style="display:none" class="fixed inset-0 bg-(--surface-card) flex flex-col md:relative md:w-full md:max-w-2xl md:h-[85vh] md:max-h-[700px] md:rounded-3xl md:shadow-2xl md:border md:border-(--border-primary)">

                    {{-- Desktop Header --}}
                    <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) shrink-0 hidden md:block">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-black text-lg text-(--text-primary)">
                                <i class="fa-solid fa-signature text-indigo-600 ml-2"></i> اعتماد التقارير الختامية
                            </h3>
                            <button @click="showSignModal = false" class="text-(--text-secondary) hover:text-(--text-primary) transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 1 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 3 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                        </div>
                    </div>

                    {{-- Canvas Area --}}
                    <div class="flex-1 relative overflow-hidden bg-(--surface-card)">

                        {{-- Step 1: Form 6 Final --}}
                        <div class="absolute inset-0 flex flex-col transition-all duration-500 ease-in-out"
                             :class="{ 'translate-x-0 opacity-100': signStep === 1, '-translate-x-full opacity-0 pointer-events-none': signStep > 1, 'translate-x-full opacity-0 pointer-events-none': signStep < 1 }">
                            <div class="hidden md:block px-6 pt-5 pb-3">
                                <h4 class="font-bold text-(--text-primary)">1. توقيع نموذج مقاييس تقييم البرنامج الختامي</h4>
                                <p class="text-xs text-(--text-secondary)">الرجاء رسم توقيعك لاعتماد نتائج التقييم الختامية.</p>
                            </div>
                            <div class="flex-1 relative bg-white border-t-2 md:mx-6 md:mb-4 md:rounded-2xl md:border-2 border-dashed border-indigo-200 dark:border-indigo-500/20 overflow-hidden">
                                <canvas x-ref="canvas6" class="absolute inset-0 w-full h-full cursor-crosshair touch-none"></canvas>
                                <div class="absolute top-3 right-3 md:hidden flex gap-1">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                </div>
                                <button type="button" @click="clearPad(6)"
                                        class="absolute top-3 left-3 px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-sm border border-red-200 text-red-600 text-xs font-bold shadow-sm">
                                    <i class="fa-solid fa-eraser ml-1"></i> مسح
                                </button>
                                <button @click="showSignModal = false"
                                        class="md:hidden absolute top-3 right-10 w-8 h-8 rounded-xl bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-500 text-sm flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Step 2: Final Decision --}}
                        <div class="absolute inset-0 flex flex-col transition-all duration-500 ease-in-out"
                             :class="{ 'translate-x-0 opacity-100': signStep === 2, '-translate-x-full opacity-0 pointer-events-none': signStep > 2, 'translate-x-full opacity-0 pointer-events-none': signStep < 2 }">
                            <div class="hidden md:block px-6 pt-5 pb-3">
                                <h4 class="font-bold text-(--text-primary)">2. التوقيع على القرار النهائي والتوصيات</h4>
                                <p class="text-xs text-(--text-secondary)">الرجاء رسم توقيعك لاعتماد القرار النهائي بخصوص اعتماد البرنامج.</p>
                            </div>
                            <div class="flex-1 relative bg-white border-t-2 md:mx-6 md:mb-4 md:rounded-2xl md:border-2 border-dashed border-indigo-200 dark:border-indigo-500/20 overflow-hidden">
                                <canvas x-ref="canvasFinal" class="absolute inset-0 w-full h-full cursor-crosshair touch-none"></canvas>
                                <div class="absolute top-3 right-3 md:hidden flex gap-1">
                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                </div>
                                <button type="button" @click="clearPad('Final')"
                                        class="absolute top-3 left-3 px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-sm border border-red-200 text-red-600 text-xs font-bold shadow-sm">
                                    <i class="fa-solid fa-eraser ml-1"></i> مسح
                                </button>
                                <button @click="showSignModal = false"
                                        class="md:hidden absolute top-3 right-10 w-8 h-8 rounded-xl bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-500 text-sm flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Step 3: Confirm --}}
                        <div class="absolute inset-0 p-6 flex flex-col transition-all duration-500 ease-in-out"
                             :class="{ 'translate-x-0 opacity-100': signStep === 3, 'translate-x-full opacity-0 pointer-events-none': signStep < 3 }">
                            <div class="flex-1 flex flex-col items-center justify-center text-center">
                                <div class="w-20 h-20 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-4xl mb-6 shadow-inner"><i class="fa-solid fa-check-double"></i></div>
                                <h4 class="font-black text-2xl text-(--text-primary) mb-3">تأكيد الاعتماد النهائي</h4>
                                <p class="text-(--text-secondary) max-w-sm leading-relaxed" x-show="submitType === 'member'">بمجرد الضغط على تأكيد، سيتم حفظ توقيعاتك الختامية وإرسال الموافقة النهائية إلى رئيس اللجنة.</p>
                                <p class="text-(--text-secondary) max-w-sm leading-relaxed" x-show="submitType === 'chair'">بمجرد الضغط على تأكيد، سيتم إنهاء المرحلة الثامنة واعتماد التقارير الختامية بشكل نهائي. سيتم تحويل الطلب للمرحلة التاسعة.</p>
                            </div>
                        </div>

                    </div>

                    {{-- Footer: floating on mobile, static on desktop --}}
                    <div class="shrink-0 md:border-t md:border-(--border-primary) md:bg-(--bg-main) md:px-6 md:py-4
                                flex justify-between
                                absolute bottom-0 left-0 right-0 p-4
                                md:relative md:bottom-auto md:left-auto md:right-auto">
                        <button type="button" @click="prevStep()"
                                :class="signStep === 1 ? 'opacity-0 pointer-events-none' : ''"
                                class="px-5 py-2.5 rounded-xl border border-(--border-primary) bg-(--surface-card)/90 backdrop-blur-sm font-bold flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-arrow-right"></i> السابق
                        </button>

                        <button type="button" @click="nextStep()" x-show="signStep < 3"
                                class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg flex items-center gap-2">
                            التالي <i class="fa-solid fa-arrow-left"></i>
                        </button>

                        <form x-show="signStep === 3" method="POST"
                              :action="submitType === 'chair' ? '{{ route('requests.stage_eight.final_submit', $accreditationRequest) }}' : '{{ route('requests.stage_eight.member_approve', $accreditationRequest) }}'">
                            @csrf
                            <input type="hidden" name="form_6_signature" :value="signature6">
                            <input type="hidden" name="final_decision_signature" :value="signatureFinal">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg shadow-green-500/30 flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> تأكيد وإرسال
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>
@endif
