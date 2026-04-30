{{-- stage_six: تقارير نتائج التقييم(الأولية) --}}
@php
    $userRole = $user->role;
    $isChairEvaluator = $userRole === 'evaluator' && $committee?->chair_evaluator_id === $user->evaluator->id;
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

    $currentStatus = $report ? $report->status : 'draft';
    $st = $statusMap[$currentStatus] ?? ['label' => 'لم يبدأ', 'color' => 'gray'];
@endphp

<div class="w-full text-start space-y-6">

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Status Card --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg shadow-inner
                    {{ $st['color'] === 'green' ? 'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                    : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                    : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                    : ($st['color'] === 'red' ? 'bg-red-50 text-red-600 border border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'
                    : 'bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'))) }}">
                    <i class="fa-solid fa-chart-pie"></i>
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
                    <h3 class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ رفع التقرير</h3>
                    <p class="font-black text-(--text-primary) tracking-wide">
                        {{ $report && $report->stage6_submitted_at ? $report->stage6_submitted_at->format('Y/m/d H:i') : 'لم يتم الرفع بعد' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shadow-inner shrink-0">
                <i class="fa-solid fa-file-lines text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-(--text-primary)">النماذج والتقارير</h3>
                <p class="text-xs text-(--text-secondary)">النماذج المطلوبة في هذه المرحلة</p>
            </div>
        </div>

        {{-- Content Table --}}
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-sm text-start whitespace-nowrap">
                <thead class="bg-(--bg-main) border-b border-(--border-primary) text-(--text-secondary) font-bold text-xs">
                    <tr>
                        <th class="px-5 py-4 w-12 text-center">#</th>
                        <th class="px-5 py-4">اسم النموذج</th>
                        <th class="px-5 py-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    <tr class="hover:bg-(--bg-main) transition-colors">
                        <td class="px-5 py-4 text-center">
                            <span class="w-7 h-7 rounded-lg bg-(--surface-card) border border-(--border-primary) inline-flex items-center justify-center font-black text-(--text-primary)">
                                1
                            </span>
                        </td>
                        <td class="px-5 py-4 font-bold text-(--text-primary)">
                            نموذج تقرير الزيارة الميدانية
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if($isChairEvaluator)
                                    <a href="{{ route('requests.stage_six.edit', $accreditationRequest) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-pen"></i> تعديل
                                    </a>
                                @endif
                                <a href="{{ route('requests.stage_six.show', $accreditationRequest) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-(--surface-card) border border-(--border-primary) hover:bg-(--bg-main) text-(--text-primary) text-xs font-bold transition cursor-pointer">
                                    <i class="fa-solid fa-eye text-(--text-secondary)"></i> عرض
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>