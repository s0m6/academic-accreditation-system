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
    $hasActiveSubmission = $latestSubmission && in_array($latestSubmission->status, ['pending', 'approved']);
    
    // Only Program Coordinator can submit in Stage Two
    $canSubmit = $userRole === 'program_coordinator' && ! $hasActiveSubmission;
@endphp

<div class="w-full text-start space-y-6" x-data="{
    showFormModal: false,
    showViewModal: false,
    viewData: null,
}">

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
                <button @click="showFormModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    إنشاء نموذج جديد
                </button>
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
                            <th class="px-6 py-4 font-bold tracking-wider">#</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-start">المرسل</th>
                            <th class="px-6 py-4 font-bold tracking-wider">تاريخ الإرسال</th>
                            <th class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                            <th class="px-6 py-4 font-bold tracking-wider uppercase tracking-widest">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @foreach ($activeStageSubmissions as $i => $sub)
                            @php $sStatus = $submissionStatusMap[$sub->status] ?? $submissionStatusMap['draft']; @endphp
                            <tr class="hover:bg-(--border-primary)/30 transition-colors">
                                <td class="px-6 py-5 font-bold text-(--text-primary)">{{ $i + 1 }}</td>
                                <td class="px-6 py-5 text-start font-bold text-(--text-primary)">
                                    {{ $sub->submitter?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    {{ $sub->submitted_at?->format('Y/m/d H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $sStatus['class'] }}">
                                        {{ $sStatus['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <button @click="showViewModal = true; viewData = {{ json_encode($sub->form_data) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors cursor-pointer">
                                        <i class="fa-solid fa-eye"></i> عرض التفاصيل
                                    </button>
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

</div>
