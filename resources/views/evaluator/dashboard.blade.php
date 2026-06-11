@extends('partials.app')

@section('title', 'لوحة تحكم المقيم')
@section('title2', 'لوحة التحكم')
@section('description', 'مرحباً بك في نظام الاعتماد الأكاديمي - لوحة تحكم المقيم')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    {{-- Statistics Card --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-file-signature text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">طلبات التقييم</h3>
                <p class="text-sm text-(--text-secondary)">الطلبات الموكلة إليك</p>
            </div>
        </div>
        <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['active_count'] }}</div>
    </div>

    {{-- Performance Card --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fa-solid fa-check-double text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">التقييمات المكتملة</h3>
                <p class="text-sm text-(--text-secondary)">إجمالي التقييمات التي تمت</p>
            </div>
        </div>
        <div class="text-3xl font-black text-green-600 dark:text-green-400">{{ $stats['completed_count'] }}</div>
    </div>

    {{-- Schedule Card --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fa-solid fa-calendar-day text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">المواعيد القادمة</h3>
                <p class="text-sm text-(--text-secondary)">الزيارات والجلسات المجدولة</p>
            </div>
        </div>
        <div class="text-3xl font-black text-purple-600 dark:text-purple-400">{{ $stats['upcoming_count'] }}</div>
    </div>

    {{-- Invitations Card --}}
    <a href="{{ route('evaluator.invitations') }}" class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md hover:-translate-y-1 transition-all no-underline block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-envelope-open-text text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">دعوات المشاركة</h3>
                <p class="text-sm text-(--text-secondary)">بانتظار ردك وتأكيدك</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <div class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['pending_invitations_count'] }}</div>
            @if($stats['pending_invitations_count'] > 0)
                <span class="px-2 py-0.5 bg-orange-500 text-white text-[10px] font-black rounded-full animate-pulse">جديد</span>
            @endif
        </div>
    </a>
</div>

{{-- Content Area --}}
@if($activeEvaluations->isEmpty())
    <div class="mt-10 bg-(--surface-card) rounded-2xl border border-(--border-primary) shadow-sm p-8 text-center">
        <div class="max-w-md mx-auto py-10">
            <div class="w-20 h-20 bg-orange-100 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center rounded-full mx-auto mb-6">
                <i class="fa-solid fa-hourglass-half text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-(--text-primary)">لا توجد مهام حالية</h2>
            <p class="text-(--text-secondary) mb-8">سيتم عرض طلبات التقييم والمهام الموكلة إليك هنا بمجرد تعيينها لك من قبل أمانة المجلس.</p>
            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition-all no-underline inline-block">تحديث القائمة</a>
        </div>
    </div>
@else
    {{-- Active Sessions Table --}}
    <div class="mt-10 bg-(--surface-card) rounded-2xl border border-(--border-primary) shadow-sm overflow-hidden text-start">
        <div class="px-6 py-5 border-b border-(--border-primary) flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-500/20">
                <i class="fa-solid fa-folder-open text-lg"></i>
            </div>
            <h3 class="font-bold text-(--text-primary)">طلبات التقييم النشطة المنسقة إليك</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th class="px-6 py-4 font-bold text-start">البرنامج / الجامعة</th>
                        <th class="px-6 py-4 font-bold">المرحلة الحالية</th>
                        <th class="px-6 py-4 font-bold">دورك في اللجنة</th>
                        <th class="px-6 py-4 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @foreach($activeEvaluations as $member)
                        @php
                            $req = $member->committee->accreditationRequest;
                            $stageNames = [
                                'stage_one'   => 'طلب الاعتماد الأولي',
                                'stage_two'   => 'البيانات الأساسية',
                                'stage_three' => 'تقرير الدراسة الذاتية',
                                'stage_four'  => 'اختيار لجنة التقييم',
                                'stage_five'  => 'تحديد جدول الزيارة',
                                'stage_six'   => 'تقارير نتائج التقييم (الأولية)',
                                'stage_seven' => 'توصيات اللجنة والرد عليها',
                                'stage_eight' => 'تقارير نتائج التقييم (الختامية)',
                                'stage_nine'  => 'القرار النهائي والشهادة',
                            ];
                            $isChair = $member->committee->chair_evaluator_id === $member->evaluator_id;
                        @endphp
                        <tr class="hover:bg-(--border-primary)/30 transition-colors">
                            <td class="px-6 py-4 text-start font-bold text-(--text-primary)">
                                <div>{{ $req->program->program_name }}</div>
                                <div class="text-xs text-(--text-secondary) font-normal">{{ $req->program->department->college->university->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
                                    {{ $stageNames[$req->current_stage] ?? $req->current_stage }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($isChair)
                                    <span class="px-2.5 py-1 rounded bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 text-xs font-bold">
                                        <i class="fa-solid fa-crown me-1 text-[10px]"></i>رئيس اللجنة
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded bg-slate-50 text-slate-700 border border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20 text-xs font-bold">
                                        عضو
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('requests.show', $req) }}" class="inline-flex items-center gap-1.5 text-brand-600 hover:text-brand-700 font-bold transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    لوحة الطلب
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
