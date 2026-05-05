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
    showRequestModal: false,
    showWithdrawModal: false,
    showSignModal: false,
    signStep: 1,
    signature1: null,
    signature2: null,
    signature3: null,
    signature4: null,
    pad1: null, pad2: null, pad3: null, pad4: null,
    submitType: 'member',
    
    initPad(refName) {
        this.$nextTick(() => {
            setTimeout(() => {
                const canvas = this.$refs[refName];
                if (canvas) {
                    const padKey = 'pad' + refName.replace('canvas', '');
                    if (!this[padKey]) {
                        this[padKey] = new window.SignaturePad(canvas, {
                            backgroundColor: 'rgba(255, 255, 255, 0)',
                            penColor: 'rgb(0, 0, 0)',
                        });
                    }
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    this[padKey].clear();
                }
            }, 200);
        });
    },
    clearPad(num) {
        if (this['pad' + num]) this['pad' + num].clear();
    },
    resetSignatures() {
        this.signStep = 1;
        [1,2,3,4].forEach(i => { if(this['pad'+i]) this['pad'+i].clear(); });
    }
}">

    {{-- Top Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Status Card --}}
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

        {{-- Date Card --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ الرد النهائي</h3>
                    <p class="font-black text-(--text-primary) tracking-wide">
                        {{ $report && $report->updated_at ? $report->updated_at->format('Y/m/d H:i') : 'جاري المعالجة...' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden mt-6">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-inner shrink-0">
                <i class="fa-solid fa-copy text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-(--text-primary)">النماذج والتقارير الختامية</h3>
                <p class="text-xs text-(--text-secondary)">النماذج المعتمدة في هذه المرحلة (المرحلة الثامنة)</p>
            </div>
        </div>

        {{-- Content Table --}}
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-sm text-start whitespace-nowrap">
                <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                    <tr>
                        <th class="px-5 py-4 text-start">اسم النموذج</th>
                        <th class="px-5 py-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    {{-- Row 1: Institution Response --}}
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">
                            نموذج رد المؤسسة التعليمية على التوصيات
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </button>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Row 2: Final Assessment Metrics --}}
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">
                            مقاييس تقييم البرنامج(التقييم الختامي)
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                @if($isChairEvaluator)
                                    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </button>
                                @endif
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </button>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Row 3: Final Committee Report --}}
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">
                            التقرير النهائي للجنة المقيمين
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </button>
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-download text-(--text-secondary)"></i> تحميل
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Row 4: Final Decision and Recommendations --}}
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-6 font-bold text-(--text-primary)">
                            القرار النهائي وتوصيات لجنة المقيمين بخصوص اعتماد البرنامج
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </button>
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

    {{-- Committee Approvals Table (Exactly as Phase 6) --}}
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
                        <p class="text-xs text-(--text-secondary)">متابعة توقيعات وموافقات الأعضاء في المرحلة الختامية</p>
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
                            @if($isChairEvaluator)
                            <th class="px-5 py-4">ملاحظات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @forelse($membersList as $index => $member)
                            @php
                                $showApprovals = !in_array($currentStatus, ['draft', 'returned_for_edit']);
                                $approval = $showApprovals ? $committeeApprovals->where('member_id', $member->evaluator_id)->first() : null;
                            @endphp
                            <tr class="hover:bg-(--bg-main) transition-colors">
                                <td class="px-5 py-4 text-center text-(--text-secondary)">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 font-bold text-(--text-primary)">
                                    {{ $member->evaluator->user->name ?? 'عضو لجنة' }}
                                </td>
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
                                <td class="px-5 py-4 text-(--text-secondary)">
                                    {{ $approval && $approval->responded_at ? $approval->responded_at->format('Y/m/d H:i') : '—' }}
                                </td>
                                @if($isChairEvaluator)
                                <td class="px-5 py-4">
                                    @if($approval && $approval->status === 'rejected' && $approval->reject_reason)
                                        <span class="text-xs font-bold text-blue-600 cursor-pointer hover:underline">عرض الملاحظات</span>
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

    {{-- Call to Actions (UI Only) --}}
    <div class="flex flex-wrap items-center gap-3 mt-6 border-t border-(--border-primary) pt-6">
        @if($isChairEvaluator)
            <button @click="showRequestModal = true" type="button" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition-colors flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> طلب موافقة الأعضاء (المرحلة الختامية)
            </button>
        @endif

        @if($isCommitteeMember && (!$memberApproval || $memberApproval->status === 'pending'))
            <button @click="showRejectModal = true" type="button" class="px-6 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-bold border border-red-200 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> رفض لوجود ملاحظات
            </button>
            
            <button @click="showSignModal = true; signStep = 1;" type="button" class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold shadow-md transition-colors flex items-center gap-2">
                <i class="fa-solid fa-signature"></i> توقيع وموافقة نهائية
            </button>
        @endif
    </div>

    {{-- Chair Modals (UI Only) --}}
    <template x-teleport="body">
        <div x-show="showRequestModal" style="display:none" class="relative z-[200]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showRequestModal = false"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div @click.away="showRequestModal = false" class="relative w-full max-w-md rounded-2xl bg-(--surface-card) shadow-2xl text-start border border-(--border-primary)">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid fa-paper-plane"></i></div>
                        <h3 class="font-bold text-lg text-(--text-primary)">تأكيد طلب الموافقة الختامية</h3>
                        <p class="text-sm text-(--text-secondary) mt-2">سيتم إرسال إشعار لأعضاء اللجنة للدخول وتوقيع التقارير الختامية.</p>
                    </div>
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                        <button @click="showRequestModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold">إلغاء</button>
                        <button @click="showRequestModal = false" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">تأكيد الطلب</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Member Reject Modal (UI Only) --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display:none" class="relative z-[200]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showRejectModal = false"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div @click.away="showRejectModal = false" class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) shadow-2xl text-start border border-(--border-primary)">
                    <div class="px-6 py-5 border-b border-(--border-primary) flex justify-between items-center bg-(--bg-main)">
                        <h3 class="font-bold text-(--text-primary)"><i class="fa-solid fa-xmark text-red-600 ml-2"></i> إرسال ملاحظات ختامية</h3>
                        <button @click="showRejectModal = false"><i class="fa-solid fa-xmark text-(--text-secondary)"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <textarea rows="3" placeholder="اكتب ملاحظتك هنا..." class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm"></textarea>
                    </div>
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                        <button @click="showRejectModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold">إلغاء</button>
                        <button @click="showRejectModal = false" class="px-6 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">تأكيد الإرسال</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Signature Modal (UI Only) --}}
    <template x-teleport="body">
        <div x-show="showSignModal" style="display:none" class="relative z-[300]">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-md" @click="showSignModal = false"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div @click.away="showSignModal = false" class="relative w-full max-w-2xl bg-(--surface-card) shadow-2xl rounded-3xl border border-(--border-primary) overflow-hidden flex flex-col h-[80vh] max-h-[700px]">
                    
                    {{-- Header progress --}}
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) shrink-0">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-black text-lg text-(--text-primary)">
                                <i class="fa-solid fa-signature text-indigo-600 ml-2"></i> توقيع التقارير الختامية
                            </h3>
                            <button @click="showSignModal = false" class="text-(--text-secondary) hover:text-(--text-primary) transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 1 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                            <div class="flex-1 h-2 rounded-full transition-colors duration-300" :class="signStep >= 3 ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 p-12 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-4xl mb-6 shadow-inner"><i class="fa-solid fa-file-signature"></i></div>
                        <h4 class="font-black text-2xl text-(--text-primary) mb-3">شاشة التوقيع الختامي</h4>
                        <p class="text-(--text-secondary) max-w-sm leading-relaxed">هذه الشاشة مصممة لمحاكاة عملية التوقيع والاعتماد النهائي للتقارير في المرحلة الثامنة.</p>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end shrink-0">
                        <button type="button" @click="showSignModal = false" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-md">إغلاق المعاينة</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
@endif