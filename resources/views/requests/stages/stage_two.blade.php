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
@endphp

<div class="w-full text-start space-y-6" x-data="{
    showFormModal: false,
    showViewModal: false,
    showSubmitModal: false,
    submitActionUrl: '',
    viewData: null,
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
                            <th class="px-6 py-4 font-bold tracking-wider">م</th>
                            <th class="px-6 py-4 font-bold tracking-wider">تاريخ الإرسال</th>
                            <th class="px-6 py-4 font-bold tracking-wider">تاريخ آخر تحديث</th>
                            <th class="px-6 py-4 font-bold tracking-wider">تاريخ رد المجلس</th>
                            <th class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                            <th class="px-6 py-4 font-bold tracking-wider uppercase tracking-widest leading-loose">العمليات</th>
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
                                <td class="px-6 py-5 flex items-center justify-center gap-2">
                                    {{-- Print Action --}}
                                    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 text-xs font-bold transition-colors cursor-pointer" onclick="alert('تحت التطوير')">
                                        <i class="fa-solid fa-print"></i> طباعة
                                    </button>
                                    
                                    {{-- Edit Action (Only for Draft and Program Coordinator) --}}
                                    @if($sub->status === 'draft' && $userRole === 'program_coordinator')
                                        <a href="{{ route('requests.stage_two.edit', [$accreditationRequest, $sub]) }}" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-edit"></i> تعديل
                                        </a>
                                        
                                        {{-- Submit Action --}}
                                        <button type="button" @click="submitActionUrl = '{{ route('requests.stage_two.submit', [$accreditationRequest, $sub]) }}'; showSubmitModal = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-paper-plane"></i> رفع للمجلس
                                        </button>
                                    @else
                                        {{-- View Action --}}
                                        <a href="{{ route('requests.stage_two.show', [$accreditationRequest, $sub]) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-eye"></i> عرض
                                        </a>
                                    @endif
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

    {{-- ═══════ MODAL: SUBMIT CONFIRM ═══════════════════════ --}}
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

</div>
