{{-- stage_seven: توصيات اللجنة والرد عليها --}}
@php
    $stageOrder = ['stage_one', 'stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex = array_search('stage_seven', $stageOrder);
    $isLocked = $currentStageIndex < $thisStageIndex;
@endphp

@if($isLocked)
    <div class="flex flex-col items-center justify-center py-20 text-center gap-6 animate-in fade-in zoom-in duration-500">
        <div class="relative">
            <div
                class="w-24 h-24 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 shadow-inner">
                <i class="fa-solid fa-lock text-4xl"></i>
            </div>
            <div
                class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-slate-900">
                <i class="fa-solid fa-hourglass-half text-sm"></i>
            </div>
        </div>
        <div class="max-w-md">
            <h3 class="text-xl font-bold text-(--text-primary) mb-2">المرحلة غير متاحة حالياً</h3>
            <p class="text-(--text-secondary) leading-relaxed">
                لا يمكنك الوصول إلى محتوى "توصيات اللجنة والرد عليها" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span
                    class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>
@else
    @php
        $userRole = $user->role;
        $isProgramCoord = $userRole === 'program_coordinator' && $accreditationRequest->program_coord_id === $user->id;
        $report = $accreditationRequest->committeeReport;

        $statusMap = [
            'draft' => ['label' => 'مسودة', 'color' => 'gray'],
            'under_review' => ['label' => 'قيد المراجعة', 'color' => 'amber'],
            'returned_for_edit' => ['label' => 'معاد للتعديل', 'color' => 'red'],
            'submitted_to_council' => ['label' => 'مرفوع للمجلس', 'color' => 'blue'],
            'council_responded' => ['label' => 'تم رد المجلس', 'color' => 'purple'],
            'uni_responded' => ['label' => 'تم رد الجامعة', 'color' => 'indigo'],
            'final_under_review' => ['label' => 'قيد المراجعة النهائية', 'color' => 'orange'],
            'completed' => ['label' => 'مكتمل', 'color' => 'green'],
        ];

        $currentStatus = $report ? $report->status : 'council_responded';
        $st = $statusMap[$currentStatus] ?? ['label' => 'قيد المعالجة', 'color' => 'purple'];
    @endphp
    @php
        // Count total sub-standards that need a response
        $totalSubs = 0;
        foreach ($accreditationRequest->program->department->college->university ? [] : [] as $_) {
        }
        // Count responded sub-standards from saved form9_data
        $form9Data = $report ? ($report->form9_data ?? []) : [];
        $respondedSubs = collect($form9Data)->filter(fn($item) => !empty($item['decision']))->count();
    @endphp
    <div x-data="stageSevenApp({{ $respondedSubs }})"
        class="w-full text-start space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">

        {{-- Top Status Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Status Card --}}
            <div
                class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner
                        {{ $st['color'] === 'green' ? 'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
            : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                    : ($st['color'] === 'purple' ? 'bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'
                        : 'bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'))) }}">
                        <i class="fa-solid fa-circle-nodes"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-(--text-secondary) mb-1">حالة المرحلة الحالية</h3>
                        <p class="font-black text-(--text-primary)">{{ $st['label'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Response Deadline or Date Card --}}
            <div
                class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ وصول التوصيات</h3>
                        <p class="font-black text-(--text-primary) tracking-wide">
                            {{ $report && $report->council_responded_at ? $report->council_responded_at->format('Y/m/d H:i') : 'جاري المعالجة...' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Section --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-file-contract text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">توصيات اللجنة والردود</h3>
                    <p class="text-xs text-(--text-secondary)">مراجعة توصيات لجنة المقيمين وتقديم الردود التصحيحية</p>
                </div>
            </div>

            {{-- Content Table --}}
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-sm text-start whitespace-nowrap">
                    <thead
                        class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">#</th>
                            <th class="px-5 py-4">المستند / النموذج</th>
                            <th class="px-5 py-4 text-center">الإجراءات المتاحة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">

                        {{-- Row 1: Recommendations Letter (Form 8) --}}
                        <tr class="hover:bg-(--bg-main)/50 transition-colors">
                            <td class="px-5 py-6 text-center">
                                <span
                                    class="w-8 h-8 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary) shadow-sm">
                                    1
                                </span>
                            </td>
                            <td class="px-5 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-(--text-primary) text-base">خطاب التوصيات للمؤسسة
                                        التعليمية</span>
                                    <span class="text-xs text-(--text-secondary) mt-1">الخطاب الرسمي المرسل من المجلس
                                        متضمناً ملاحظات وتوصيات اللجنة</span>
                                </div>
                            </td>
                            <td class="px-5 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    @if($report && $report->form8_pdf_path)
                                        <a href="{{ route('requests.stage_six.recommendations_letter', $accreditationRequest) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-100 dark:bg-orange-500/10 dark:hover:bg-orange-500/20 dark:text-orange-400 dark:border-orange-500/20 text-xs font-bold transition shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-file-lines"></i> عرض (نسخة الكترونية)
                                        </a>
                                        <a href="{{ route('requests.stage_seven.recommendations.view', $accreditationRequest) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-100 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 dark:text-indigo-400 dark:border-indigo-500/20 text-xs font-bold transition shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-eye"></i> عرض النسخة (الموقّعة)
                                        </a>
                                        <a href="{{ route('requests.stage_seven.recommendations.download', $accreditationRequest) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700 text-xs font-bold transition shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-download"></i> تحميل النسخة(الموقّعة)
                                        </a>
                                    @else
                                        <span class="text-xs font-bold text-(--text-secondary) italic">المستند غير متوفر
                                            حالياً</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2: Response to Recommendations (Form 9) --}}
                        <tr class="hover:bg-(--bg-main)/50 transition-colors">
                            <td class="px-5 py-6 text-center">
                                <span
                                    class="w-8 h-8 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary) shadow-sm">
                                    2
                                </span>
                            </td>
                            <td class="px-5 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-(--text-primary) text-base">نموذج الرد على توصيات
                                        اللجنة</span>
                                    <span class="text-xs text-(--text-secondary) mt-1">الرد التفصيلي والخطة التصحيحية
                                        المقدمة من قبل البرنامج</span>
                                </div>
                            </td>
                            <td class="px-5 py-6">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    {{-- Edit Button (Coordinator only, only if status allows) --}}
                                    @if($isProgramCoord && $report && $report->status === 'council_responded')
                                        <a href="{{ route('requests.stage_seven.form9.edit', $accreditationRequest) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md shadow-blue-500/20">
                                            <i class="fa-solid fa-pen-to-square"></i> تعديل الرد
                                        </a>
                                    @endif

                                    {{-- View Button (all authorized users) --}}
                                    @if($report)
                                        <a href="{{ route('requests.stage_seven.form9.show', $accreditationRequest) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition">
                                            <i class="fa-solid fa-eye text-indigo-500"></i> عرض
                                        </a>
                                        <a href="{{ route('requests.stage_seven.form9.print', $accreditationRequest) }}"
                                            x-data="{ loading: false, finished: false }"
                                            x-on:click="loading = true; finished = false; setTimeout(() => { loading = false; finished = true; setTimeout(() => finished = false, 5000) }, 5000)"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-xs font-bold transition-all hover:-translate-y-0.5 cursor-pointer no-underline"
                                            :class="finished ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 dark:hover:bg-green-500/30' : 'bg-red-50 hover:bg-red-100 text-red-700 border border-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 dark:border-red-500/20'"
                                            :class="{ 'opacity-60 pointer-events-none': loading }">
                                            <i x-show="!loading && !finished" class="fa-solid fa-file-pdf text-red-500"></i>
                                            <i x-show="loading" class="fa-solid fa-circle-notch animate-spin text-red-500"></i>
                                            <i x-show="finished" class="fa-solid fa-check text-green-600 dark:text-green-400"></i>
                                            <span x-text="loading ? 'جاري التحميل...' : (finished ? 'تم التحميل' : 'تحميل الرد (PDF)')"></span>
                                        </a>
                                    @else
                                        <button type="button" disabled
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-bold transition cursor-not-allowed">
                                            <i class="fa-solid fa-eye text-slate-400"></i> عرض
                                        </button>
                                    @endif


                                    {{-- Send Button (Coordinator only) --}}
                                    @if($isProgramCoord)
                                        @if($report && $report->status === 'council_responded')
                                            <button type="button" @click="openSendModal()"
                                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition shadow-md shadow-green-500/20">
                                                <i class="fa-solid fa-paper-plane"></i> ارسال الرد
                                            </button>
                                        @else
                                            <button type="button" disabled
                                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-bold transition cursor-not-allowed">
                                                <i class="fa-solid fa-paper-plane"></i> تم الارسال مسبقاً
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            {{-- Footer Info --}}
            <div
                class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-(--border-primary) flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-(--text-secondary)">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <span>يتم تقديم الردود والخطط التصحيحية خلال الفترة الزمنية المحددة من تاريخ استلام التوصيات.</span>
                </div>
            </div>
        </div>

        {{-- Validation Warning Modal (shown when form9 is incomplete) --}}
        <template x-teleport="body">
            <div x-show="showValidationWarning" style="display:none" class="relative z-[210]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-show="showValidationWarning"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div @click.away="showValidationWarning = false"
                            class="relative w-full max-w-md rounded-3xl bg-(--surface-card) p-8 text-start shadow-2xl"
                            x-show="showValidationWarning" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center gap-4 mb-5">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center border border-amber-200 shadow-inner shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-(--text-primary)">النموذج غير مكتمل</h3>
                                    <p class="text-sm text-(--text-secondary) mt-0.5">يجب الرد على جميع المعايير الفرعية
                                        أولاً</p>
                                </div>
                            </div>
                            <div
                                class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/5 border border-amber-100 text-sm text-amber-800 dark:text-amber-400 leading-relaxed mb-6">
                                <p class="font-bold mb-2"><i class="fa-solid fa-circle-info me-1"></i>المعايير التي لم يُرد
                                    عليها بعد:</p>
                                <ul class="list-disc ps-4 space-y-1">
                                    <template x-for="(item, idx) in missingItems" :key="idx">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </div>
                            <div class="flex gap-3">
                                @if($isProgramCoord && $report && $report->status === 'council_responded')
                                    <a href="{{ route('requests.stage_seven.form9.edit', $accreditationRequest) }}"
                                        class="flex-1 flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-sm transition shadow-lg shadow-blue-500/20">
                                        <i class="fa-solid fa-pen-to-square"></i> تعديل النموذج
                                    </a>
                                @endif
                                <button @click="showValidationWarning = false"
                                    class="flex-1 px-5 py-3 rounded-2xl bg-(--bg-main) border border-(--border-primary) text-(--text-primary) font-black text-sm hover:bg-slate-50 transition">
                                    إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Upload Modal --}}
        <template x-teleport="body">
            <div x-show="showUpload" style="display:none" class="relative z-[200]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" x-show="showUpload"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div @click.away="showUpload = false"
                            class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-(--surface-card) p-8 text-start shadow-2xl transition-all"
                            x-show="showUpload" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">

                            {{-- Close Button --}}
                            <button @click="showUpload = false"
                                class="absolute left-6 top-6 w-10 h-10 rounded-full hover:bg-(--bg-main) flex items-center justify-center text-(--text-secondary) transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>

                            {{-- Header --}}
                            <div class="flex items-center gap-4 mb-8">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 flex items-center justify-center border border-green-100 dark:border-green-500/20 shadow-inner">
                                    <i class="fa-solid fa-file-arrow-up text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-(--text-primary)">إرسال الرد الرسمي</h3>
                                    <p class="text-sm text-(--text-secondary)">يرجى إرفاق ملف الرد على التوصيات بصيغة PDF
                                    </p>
                                </div>
                            </div>

                            {{-- Form --}}
                            <form action="{{ route('requests.stage_seven.recommendations.submit', $accreditationRequest) }}"
                                method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="block text-sm font-bold text-(--text-primary) mb-2">ملف الرد على
                                            التوصيات</span>
                                        <div class="relative group">
                                            <input type="file" name="response_pdf" accept=".pdf" required
                                                class="block w-full text-sm text-(--text-secondary)
                                                file:mr-4 file:py-3 file:px-6
                                                file:rounded-xl file:border-0
                                                file:text-xs file:font-black
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100
                                                file:transition-colors file:cursor-pointer
                                                border border-(--border-primary) rounded-2xl bg-(--bg-main) p-2 group-hover:border-indigo-300 transition-all">
                                        </div>
                                        <p class="mt-2 text-[10px] text-(--text-secondary) flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-info text-indigo-400"></i>
                                            يسمح فقط بملفات PDF بحجم أقصى 10 ميجابايت.
                                        </p>
                                    </label>
                                </div>

                                {{-- Confirmation Checkbox --}}
                                <div
                                    class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/20">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="confirmed" required
                                            class="mt-1 w-4 h-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500 transition-all">
                                        <span class="text-xs font-bold text-amber-800 dark:text-amber-400 leading-relaxed">
                                            أقر بأن الملف المرفق هو الرد الرسمي المعتمد من قبل البرنامج، وأدرك أنه لا يمكن
                                            التعديل عليه بعد الإرسال.
                                        </span>
                                    </label>
                                </div>

                                {{-- Buttons --}}
                                <div class="flex items-center gap-3 pt-2">
                                    <button type="submit" :disabled="!confirmed"
                                        :class="confirmed ? 'bg-green-600 hover:bg-green-700 shadow-green-500/20' : 'bg-slate-300 cursor-not-allowed opacity-60'"
                                        class="flex-1 px-6 py-4 rounded-2xl text-white font-black shadow-lg transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check-circle"></i>
                                        تأكيد وإرسال الرد
                                    </button>
                                    <button type="button" @click="showUpload = false"
                                        class="px-6 py-4 rounded-2xl bg-(--bg-main) border border-(--border-primary) text-(--text-primary) font-black hover:bg-slate-50 transition-all">
                                        إلغاء
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <script>
        function stageSevenApp(respondedCount) {
            @php
                // Build the missing-items list: sub-standards with no saved response in form9_data
                $missingItems = [];
                if ($accreditationRequest->committeeReport) {
                    $form9Responses = collect($report?->form9_data ?? [])->keyBy('sub_id');
                    $standards = \App\Models\Standard::with('subStandards')->orderBy('id')->get();
                    foreach ($standards as $std) {
                        foreach ($std->subStandards as $sub) {
                            $saved = $form9Responses->get($sub->id);
                            if (!$saved || empty($saved['decision'])) {
                                $missingItems[] = $std->name . ' / ' . $sub->name;
                            }
                        }
                    }
                }
            @endphp
            return {
                showUpload: false,
                confirmed: false,
                showValidationWarning: false,
            missingItems: @json($missingItems),

                openSendModal() {
                    // Block sending if any sub-standard has no response recorded
                    if (this.missingItems.length > 0) {
                        this.showValidationWarning = true;
                        return;
                    }
                    this.showUpload = true;
                },
            };
    }
    </script>

@endif