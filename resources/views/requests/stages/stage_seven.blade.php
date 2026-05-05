{{-- stage_seven: توصيات اللجنة والرد عليها --}}
@php
    $userRole = $user->role;
    $isProgramCoord = $userRole === 'program_coordinator' && $accreditationRequest->program_coord_id === $user->id;
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

    $currentStatus = $report ? $report->status : 'council_responded';
    $st = $statusMap[$currentStatus] ?? ['label' => 'قيد المعالجة', 'color' => 'purple'];
@endphp

<div class="w-full text-start space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
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
                    <i class="fa-solid fa-circle-nodes"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-(--text-secondary) mb-1">حالة المرحلة الحالية</h3>
                    <p class="font-black text-(--text-primary)">{{ $st['label'] }}</p>
                </div>
            </div>
        </div>

        {{-- Response Deadline or Date Card --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner bg-indigo-50 text-indigo-600 border border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
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
            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shadow-inner shrink-0">
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
                <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
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
                            <span class="w-8 h-8 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary) shadow-sm">
                                1
                            </span>
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-(--text-primary) text-base">خطاب التوصيات للمؤسسة التعليمية</span>
                                <span class="text-xs text-(--text-secondary) mt-1">الخطاب الرسمي المرسل من المجلس متضمناً ملاحظات وتوصيات اللجنة</span>
                            </div>
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-3">
                                @if($report && $report->form8_pdf_path)
                                    <a href="{{ route('requests.stage_seven.recommendations.view', $accreditationRequest) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-100 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 dark:text-indigo-400 dark:border-indigo-500/20 text-xs font-bold transition shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-eye"></i> عرض الخطاب
                                    </a>
                                    <a href="{{ route('requests.stage_seven.recommendations.download', $accreditationRequest) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:border-slate-700 text-xs font-bold transition shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-download"></i> تحميل النسخة
                                    </a>
                                @else
                                    <span class="text-xs font-bold text-(--text-secondary) italic">المستند غير متوفر حالياً</span>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Row 2: Response to Recommendations (Form 9) --}}
                    <tr class="hover:bg-(--bg-main)/50 transition-colors">
                        <td class="px-5 py-6 text-center">
                            <span class="w-8 h-8 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary) shadow-sm">
                                2
                            </span>
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-(--text-primary) text-base">نموذج الرد على توصيات اللجنة</span>
                                <span class="text-xs text-(--text-secondary) mt-1">الرد التفصيلي والخطة التصحيحية المقدمة من قبل البرنامج</span>
                            </div>
                        </td>
                        <td class="px-5 py-6">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                {{-- Edit Button (Coordinator only) --}}
                                @if($isProgramCoord)
                                    <button type="button" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md shadow-blue-500/20 cursor-default">
                                        <i class="fa-solid fa-pen-to-square"></i> تعديل الرد
                                    </button>
                                @endif

                                <button type="button" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition cursor-default">
                                    <i class="fa-solid fa-eye text-indigo-500"></i> عرض
                                </button>

                                <button type="button" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition cursor-default">
                                    <i class="fa-solid fa-file-pdf text-red-500"></i> تنزيل
                                </button>

                                {{-- Send Button (Coordinator only) --}}
                                @if($isProgramCoord)
                                    <button type="button" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition shadow-md shadow-green-500/20 cursor-default">
                                        <i class="fa-solid fa-paper-plane"></i> ارسال الرد
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        {{-- Footer Info --}}
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-(--border-primary) flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-(--text-secondary)">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                <span>يتم تقديم الردود والخطط التصحيحية خلال الفترة الزمنية المحددة من تاريخ استلام التوصيات.</span>
            </div>
        </div>
    </div>

</div>