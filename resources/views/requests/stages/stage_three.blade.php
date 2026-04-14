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
@endphp

<div class="w-full text-start space-y-6" x-data="{}">

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
                                        @else
                                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors cursor-pointer opacity-50 cursor-not-allowed" disabled>
                                                <i class="fa-solid fa-edit"></i> تعديل
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('requests.stage_three.show', [$accreditationRequest->id, $sub->id]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-eye"></i> عرض
                                        </a>
                                        
                                        @if($sub->status === 'draft' && $userRole === 'program_coordinator')
                                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 text-xs font-bold transition-colors cursor-pointer" onclick="alert('تحت التطوير')">
                                                <i class="fa-solid fa-paper-plane"></i> رفع للمجلس
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

</div>
