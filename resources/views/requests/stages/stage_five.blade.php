{{-- Stage Five: تحديد جدول الزيارة --}}
@php
    $userRole = $user->role;
    $isSecretariat = $userRole === 'council_secretariat';
    $isProgramCoord = $userRole === 'program_coordinator';
    $isCouncilCoord = $userRole === 'council_coordinator';
    $isChairEvaluator = $userRole === 'evaluator' && $committee?->chair_evaluator_id === $user->evaluator->id;
    $isCommitteeMember = $userRole === 'evaluator' && $committee?->acceptedMembers->pluck('evaluator_id')->contains($user->evaluator?->id);

    $visitSchedules = collect($visitSchedules ?? []);
    
    // Filter out drafts and pending council forwarding for the program coordinator
    if ($isProgramCoord) {
        $visitSchedules = $visitSchedules->reject(function($schedule) {
            return in_array($schedule->status, ['draft', 'submitted_to_council']);
        })->values();
    }

    $latestSchedule = $visitSchedules->first();
    
    // Can the Chair Evaluator create a new schedule?
    $canCreateSchedule = $isChairEvaluator && (!$latestSchedule || $latestSchedule->status === 'rejected_uni');

    $statusMap = [
        'draft'                => ['label' => 'مسودة', 'color' => 'gray'],
        'submitted_to_council' => ['label' => 'في إنتظار المجلس لتحويل الجدول للجامعة', 'color' => 'amber'],
        'pending_uni'          => ['label' => 'في إنتظار رد الجامعة', 'color' => 'blue'],
        'rejected_uni'         => ['label' => 'مرفوض', 'color' => 'red'],
        'approved_uni'         => ['label' => 'مقبول', 'color' => 'green'],
    ];

    $stageOrder = ['stage_one', 'stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex = array_search('stage_five', $stageOrder);
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
                لا يمكنك الوصول إلى محتوى "تحديد جدول الزيارة" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>
@else

<div class="w-full text-start space-y-6" x-data="{
    showUploadModal: false,
    uploadActionUrl: '',
    
    showSubmitModal: false,
    submitActionUrl: '',
    isValidating: false,
    validationErrors: [],
    validationCounts: [],
    validationSuccess: false,

    showAcceptModal: false,
    acceptActionUrl: '',
    
    showRejectModal: false,
    rejectActionUrl: '',
    rejectReasons: [''],
    
    showReasonsModal: false,
    viewReasons: [],

    addReason() { this.rejectReasons.push(''); },
    removeReason(i) { if (this.rejectReasons.length > 1) this.rejectReasons.splice(i, 1); },

    async validateAndShowModal(validateUrl, submitUrl) {
        this.submitActionUrl = submitUrl;
        this.showSubmitModal = true;
        this.isValidating = true;
        this.validationErrors = [];
        this.validationCounts = [];
        this.validationSuccess = false;

        try {
            const response = await axios.post(validateUrl);
            this.validationSuccess = response.data.success;
            this.validationErrors = response.data.errors;
            this.validationCounts = response.data.counts;
        } catch (error) {
            this.validationErrors = ['حدث خطأ أثناء عملية التحقق. يرجى المحاولة مرة أخرى.'];
        } finally {
            this.isValidating = false;
        }
    }
}">

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-bold shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-xl shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-calendar-days text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">جدول الزيارة الميدانية</h3>
                    <p class="text-xs text-(--text-secondary)">إعداد واعتماد جدول الزيارة الميدانية من قبل الجامعة والمجلس</p>
                </div>
            </div>
            
            @if($canCreateSchedule)
                <form method="POST" action="{{ route('requests.stage_five.draft', $accreditationRequest) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        إنشاء جدول زيارة
                    </button>
                </form>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-0 overflow-x-auto">
            @if($visitSchedules->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border-2 border-dashed border-(--border-primary) flex items-center justify-center">
                        <i class="fa-solid fa-calendar-xmark text-2xl text-(--text-secondary) opacity-50"></i>
                    </div>
                    <div>
                        <p class="font-bold text-(--text-primary)">لا يوجد جدول زيارة مسجل</p>
                        @if($isChairEvaluator)
                            <p class="text-sm text-(--text-secondary) mt-1">اضغط على "إنشاء جدول زيارة" للبدء في الإعداد</p>
                        @else
                            <p class="text-sm text-(--text-secondary) mt-1">في انتظار قيام رئيس اللجنة بإنشاء الجدول</p>
                        @endif
                    </div>
                </div>
            @else
                <table class="w-full text-sm text-start whitespace-nowrap">
                    <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                        <tr>
                            <th class="px-5 py-4 text-center">رقم النسخة</th>
                            <th class="px-5 py-4">تاريخ رفع الجدول للمجلس</th>
                            <th class="px-5 py-4">تاريخ رفع الجدول للجامعة</th>
                            <th class="px-5 py-4">تاريخ رد الجامعة</th>
                            <th class="px-5 py-4">الحالة</th>
                            <th class="px-5 py-4">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @foreach($visitSchedules as $schedule)
                            @php 
                                $st = $statusMap[$schedule->status] ?? ['label' => $schedule->status, 'color' => 'gray']; 
                                // Reverse the index to act as a version number (oldest is 1)
                                $versionNumber = $visitSchedules->count() - $loop->index;
                            @endphp
                            <tr class="hover:bg-(--bg-main) transition-colors">
                                <td class="px-5 py-4 text-center">
                                    <span class="w-7 h-7 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary)">
                                        {{ $versionNumber }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-medium text-(--text-secondary)" dir="ltr">
                                    {{ $schedule->submitted_at ? $schedule->submitted_at->format('Y/m/d H:i') : '—' }}
                                </td>
                                <td class="px-5 py-4 font-medium text-(--text-secondary)" dir="ltr">
                                    {{ $schedule->council_processed_at ? $schedule->council_processed_at->format('Y/m/d H:i') : '—' }}
                                </td>
                                <td class="px-5 py-4 font-medium text-(--text-secondary)" dir="ltr">
                                    {{ $schedule->university_responded_at ? $schedule->university_responded_at->format('Y/m/d H:i') : '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $st['color'] === 'green' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                                        : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                                        : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                                        : ($st['color'] === 'red' ? 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'
                                        : 'bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'))) }}">
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        {{-- Everyone can view PDF data --}}
                                        <a href="{{ route('requests.stage_five.show', [$accreditationRequest, $schedule]) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition cursor-pointer">
                                            <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                        </a>

                                        {{-- PDF Download (Visible to all if exists) --}}
                                        @if($schedule->council_pdf_path)
                                            <a href="{{ route('requests.stage_five.view_pdf', [$accreditationRequest, $schedule]) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 dark:text-indigo-400 dark:border-indigo-500/20 text-xs font-bold transition cursor-pointer">
                                                <i class="fa-solid fa-file-pdf"></i> تحميل النسخة الموقعة
                                            </a>
                                        @endif

                                        {{-- Chair Actions --}}
                                        @if($isChairEvaluator && $schedule->status === 'draft')
                                            <a href="{{ route('requests.stage_five.edit', [$accreditationRequest, $schedule]) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition cursor-pointer">
                                                <i class="fa-solid fa-pen"></i> تعديل
                                            </a>
                                            
                                            <button type="button" @click="validateAndShowModal('{{ route('requests.stage_five.validate', [$accreditationRequest, $schedule]) }}', '{{ route('requests.stage_five.submit', [$accreditationRequest, $schedule]) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer shadow-sm">
                                                <i class="fa-solid fa-paper-plane"></i> رفع للمجلس
                                            </button>
                                        @endif
                                        @if(($isCommitteeMember || $isProgramCoord || $isSecretariat || $isCouncilCoord) && $schedule->status === 'rejected_uni')
                                            <button type="button" @click="showReasonsModal = true; viewReasons = {{ json_encode([$schedule->rejection_reason['reason'] ?? '']) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition cursor-pointer">
                                                <i class="fa-solid fa-circle-info"></i> أسباب الرفض
                                            </button>
                                        @endif

                                        {{-- Council Coordinator Actions --}}
                                        @if($isCouncilCoord && $schedule->status === 'submitted_to_council')
                                            <button type="button" @click="showUploadModal = true; uploadActionUrl = '{{ route('requests.stage_five.council_forward', [$accreditationRequest, $schedule]) }}'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer shadow-sm">
                                                <i class="fa-solid fa-file-arrow-up"></i> إرفاق وتحويل للجامعة
                                            </button>
                                        @endif

                                        {{-- Program Coordinator Actions --}}
                                        @if($isProgramCoord && $schedule->status === 'pending_uni')
                                            
                                            <button type="button" @click="showRejectModal = true; rejectActionUrl = '{{ route('requests.stage_five.university_reject', [$accreditationRequest, $schedule]) }}'; rejectReasons = [''];"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition cursor-pointer">
                                                <i class="fa-solid fa-xmark"></i> رفض
                                            </button>
                                            
                                            <button type="button" @click="showAcceptModal = true; acceptActionUrl = '{{ route('requests.stage_five.university_accept', [$accreditationRequest, $schedule]) }}'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition cursor-pointer shadow-sm">
                                                <i class="fa-solid fa-check"></i> قبول
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- MODAL: Submit to Council --}}
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
                        <div class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-2xl text-start">
                            {{-- Close button --}}
                            <div class="absolute top-4 left-4 z-10">
                                <button @click="showSubmitModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-paper-plane text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-(--text-primary)">رفع الجدول للمجلس</h3>
                                    <p class="text-xs text-(--text-secondary) mt-1">
                                        سيتم تحويل الجدول للمجلس تمهيداً لرفعه للجامعة لاعتماده.
                                    </p>
                                </div>
                            </div>

                            {{-- Validation State --}}
                            <div class="space-y-4">
                                {{-- Loading --}}
                                <div x-show="isValidating" class="flex flex-col items-center justify-center py-6 gap-3">
                                    <div class="w-8 h-8 border-4 border-indigo-500/20 border-t-indigo-600 rounded-full animate-spin"></div>
                                    <p class="text-xs text-(--text-secondary) font-bold">جاري التحقق من بيانات الجدول...</p>
                                </div>

                                {{-- Errors --}}
                                <div x-show="!isValidating && validationErrors.length > 0" class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl p-4">
                                    <div class="flex gap-3 mb-2">
                                        <i class="fa-solid fa-circle-xmark text-red-600 dark:text-red-400 mt-0.5"></i>
                                        <p class="text-xs font-bold text-red-800 dark:text-red-300">لا يمكن الرفع للأسباب التالية:</p>
                                    </div>
                                    <ul class="list-disc list-inside space-y-1">
                                        <template x-for="error in validationErrors" :key="error">
                                            <li class="text-[11px] text-red-700 dark:text-red-400/80 leading-relaxed" x-text="error"></li>
                                        </template>
                                    </ul>
                                </div>

                                {{-- Success Summary --}}
                                <div x-show="!isValidating && validationErrors.length === 0" class="space-y-3">
                                    <div class="bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl p-4">
                                        <div class="flex gap-3 mb-3">
                                            <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                            <p class="text-xs font-bold text-green-800 dark:text-green-300">تم التحقق من اكتمال البيانات بنجاح:</p>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <template x-for="day in validationCounts" :key="day.label">
                                                <div class="bg-white dark:bg-white/5 rounded-lg p-2 border border-green-100 dark:border-green-500/10 text-center">
                                                    <p class="text-[10px] text-(--text-secondary) mb-1 font-bold" x-text="day.label"></p>
                                                    <p class="text-base font-black text-green-600 dark:text-green-400">
                                                        <span x-text="day.count"></span> نشاط
                                                    </p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4">
                                        <div class="flex gap-3">
                                            <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                            <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                                                بمجرد الإرسال، سيتم قفل الجدول ولن تتمكن من تعديله إلا في حال رفضه من قبل الجامعة أو المجلس.
                                            </p>
                                        </div>
                                    </div>
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
                                <button type="submit" :disabled="isValidating || !validationSuccess"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black shadow-lg shadow-indigo-500/20 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-circle-check"></i> تأكيد الرفع للمجلس
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: University Accept --}}
    <template x-teleport="body">
        <div x-show="showAcceptModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showAcceptModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="showAcceptModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        <div class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-2xl text-start">
                            {{-- Close button --}}
                            <div class="absolute top-4 left-4 z-10">
                                <button @click="showAcceptModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                        <div class="p-6">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-check-double text-green-600 dark:text-green-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-(--text-primary)">تأكيد الموافقة على الجدول</h3>
                                <p class="text-sm text-(--text-secondary) mt-2 max-w-xs leading-relaxed">
                                    بمجرد الموافقة على جدول الزيارة الميدانية، سيتم اعتماد المواعيد المحددة وسينتقل الطلب تلقائياً إلى المرحلة السادسة.
                                </p>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showAcceptModal = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                إلغاء
                            </button>
                            <form method="POST" :action="acceptActionUrl" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black shadow-lg shadow-green-500/20 transition-all cursor-pointer">
                                    <i class="fa-solid fa-circle-check"></i> تأكيد الموافقة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Attach & Forward to Uni --}}
    <template x-teleport="body">
        <div x-show="showUploadModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-show="showUploadModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="showUploadModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="showUploadModal = false"
                        class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl w-full max-w-lg text-start">

                        <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-(--text-primary)">إرفاق وتحويل للجامعة</h3>
                                    <p class="text-[10px] text-(--text-secondary)">إرسال النسخة المعتمدة والموقعة من المجلس</p>
                                </div>
                            </div>
                        <button @click="showUploadModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        </div>

                        <form method="POST" :action="uploadActionUrl" enctype="multipart/form-data">
                            @csrf
                            <div class="p-6 space-y-5">
                                {{-- Info Box --}}
                                <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-xl p-4">
                                    <div class="flex gap-3">
                                        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                        <p class="text-[11px] text-blue-800 dark:text-blue-300 leading-relaxed font-medium">
                                            يرجى إرفاق ملف PDF للجدول المعتمد. سيتم إخطار منسق الجامعة فور التحويل لمراجعة الجدول والموافقة عليه.
                                        </p>
                                    </div>
                                </div>

                                {{-- Simplified Modern File 
                                 --}}
                                <div class="space-y-2" x-data="{ fileName: '' }">
                                    <label class="block text-sm font-bold text-(--text-primary)">الملف المعتمد (PDF)</label>
                                    
                                    <div class="relative flex items-center bg-(--bg-main) border border-(--border-primary) rounded-2xl p-1.5 group transition-all hover:border-indigo-500/50 focus-within:ring-2 focus-within:ring-indigo-500/20">
                                        <input type="file" name="council_pdf" id="council_pdf" accept=".pdf" required 
                                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        
                                        <div class="flex-1 px-3 flex items-center gap-2 overflow-hidden">
                                            <i class="fa-solid fa-file-pdf text-indigo-500 opacity-60"></i>
                                            <span class="text-xs font-medium truncate" 
                                                  :class="fileName ? 'text-(--text-primary)' : 'text-(--text-secondary)'"
                                                  x-text="fileName || 'يرجى اختيار ملف الجدول المعتمد...'"></span>
                                        </div>

                                        <div class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-black group-hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                                            <i class="fa-solid fa-folder-open"></i>
                                            <span>تصفح الملفات</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between px-1 opacity-70">
                                        <p class="text-[10px] text-(--text-secondary) flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-info"></i> الحجم الأقصى 10MB
                                        </p>
                                        <p class="text-[10px] text-(--text-secondary) font-bold uppercase tracking-wider">
                                            PDF only
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                                <button type="button" @click="showUploadModal = false; fileName = ''" 
                                    class="px-5 py-2.5 rounded-xl border border-(--border-primary) bg-(--surface-card) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit" 
                                    class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black shadow-lg shadow-indigo-500/20 transition-all cursor-pointer flex items-center gap-2">
                                    <i class="fa-solid fa-check text-xs"></i> 
                                    تأكيد وتحويل
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Reject Reasons --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showRejectModal" @click.away="showRejectModal = false" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative w-full max-w-xl rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20"><i class="fa-solid fa-ban"></i></div>
                            <div>
                                <h3 class="font-bold text-(--text-primary)">رفض جدول الزيارة</h3>
                                <p class="text-xs text-(--text-secondary)">أدخل أسباب الرفض أو المقترحات</p>
                            </div>
                        </div>
                        <button @click="showRejectModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <form method="POST" :action="rejectActionUrl">
                        @csrf @method('PATCH')
                        <div class="p-5 max-h-[60vh] overflow-y-auto space-y-4">
                            <template x-for="(reason, i) in rejectReasons" :key="i">
                                <div class="relative">
                                    <textarea x-model="rejectReasons[i]" name="reject_reasons" rows="3" required placeholder="اكتب السبب أو الاقتراح هنا..." class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"></textarea>
                                </div>
                            </template>
                        </div>
                        <div class="px-5 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-sm font-bold">إلغاء</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-black"><i class="fa-solid fa-check me-1"></i> تأكيد الرفض</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: View Reject Reasons --}}
    <template x-teleport="body">
        <div x-show="showReasonsModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showReasonsModal" @click.away="showReasonsModal = false" class="relative w-full max-w-xl rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center"><i class="fa-solid fa-info-circle"></i></div>
                            <h3 class="font-bold text-(--text-primary)">أسباب الرفض</h3>
                        </div>
                        <button @click="showReasonsModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
                        <template x-for="(reason, index) in viewReasons" :key="index">
                            <div class="p-4 rounded-xl border border-(--border-primary) bg-(--bg-main) text-sm text-(--text-primary) whitespace-pre-wrap leading-relaxed" x-text="reason"></div>
                        </template>
                    </div>
                    <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end">
                        <button @click="showReasonsModal = false" class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-sm font-bold">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>@endif
