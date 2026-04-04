{{-- Stage Three: تقرير الدراسة الذاتية --}}
@php
    $userRole = $user->role;
@endphp

<div class="w-full text-start space-y-6">

    {{-- ─── Stage Info Card ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="bg-(--bg-main) border-b border-(--border-primary) px-6 py-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-100 dark:border-violet-500/20 shadow-inner shrink-0">
                <i class="fa-solid fa-file-pen text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-(--text-primary)">تقرير الدراسة الذاتية</h3>
                <p class="text-xs text-(--text-secondary)">مرحلة إعداد وتقديم تقرير التقييم الذاتي للبرنامج</p>
            </div>
        </div>

        <div class="p-6">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-violet-50/50 dark:bg-violet-500/5 border border-violet-100 dark:border-violet-500/20">
                <i class="fa-solid fa-circle-exclamation text-violet-500 text-xl shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold text-(--text-primary) mb-1">ما هو تقرير الدراسة الذاتية؟</p>
                    <p class="text-sm text-(--text-secondary) leading-relaxed">
                        تقرير الدراسة الذاتية هو وثيقة شاملة تُعدّها مؤسسة التعليم العالي أو البرنامج الأكاديمي لتقييم وضعه الراهن، ويتضمن تحليلاً معمقاً لنقاط القوة والضعف والفرص والتحديات وفقاً لمعايير الاعتماد المعتمدة.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Upload Section (placeholder) ───────────────────────────── --}}
    <div class="rounded-2xl border-2 border-dashed border-(--border-primary) bg-(--surface-card)/50 shadow-sm p-10 flex flex-col items-center justify-center text-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
            <i class="fa-solid fa-cloud-arrow-up text-2xl text-(--text-secondary)"></i>
        </div>
        <div>
            <p class="text-base font-bold text-(--text-primary)">لم يتم رفع تقرير بعد</p>
            <p class="text-sm text-(--text-secondary) mt-1">سيتمكن منسق البرنامج من رفع وثيقة التقرير في هذه المرحلة</p>
        </div>
    </div>

    {{-- ─── Role-based Actions ───────────────────────────────────────── --}}
    @if($userRole === 'program_coordinator')
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-100 dark:border-violet-500/20 shrink-0">
                    <i class="fa-solid fa-upload text-sm"></i>
                </div>
                <div>
                    <h4 class="font-bold text-(--text-primary)">رفع تقرير الدراسة الذاتية</h4>
                    <p class="text-xs text-(--text-secondary)">يرجى رفع الوثيقة بصيغة PDF</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    رفع التقرير
                </button>
            </div>
        </div>

    @elseif($userRole === 'council_coordinator')
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-100 dark:border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <div>
                    <h4 class="font-bold text-(--text-primary)">مراجعة التقرير</h4>
                    <p class="text-xs text-(--text-secondary)">قم بمراجعة التقرير المرفوع واتخاذ الإجراء المناسب</p>
                </div>
            </div>
            <p class="text-sm text-(--text-secondary) bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                <i class="fa-solid fa-clock text-amber-500 me-2"></i>
                في انتظار رفع التقرير من منسق البرنامج.
            </p>
        </div>

    @else
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5">
            <p class="text-sm text-(--text-secondary)">
                <i class="fa-solid fa-eye text-slate-400 me-2"></i>
                هذه المرحلة في انتظار رفع تقرير الدراسة الذاتية من منسق البرنامج.
            </p>
        </div>
    @endif

</div>
