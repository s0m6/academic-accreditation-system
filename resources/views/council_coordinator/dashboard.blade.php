@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-coordinator/dashboard'
    ];
@endphp

@section('title', 'لوحة التحكم')
@section('title2', 'لوحة تحكم منسق المجلس')
@section('description', 'مرحباً بك في لوحة التحكم الخاصة بك، يمكنك متابعة طلبات الاعتماد المرتبطة بك هنا.')

@section('content')
<div class="space-y-8">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-500/20">
                <i class="fa-solid fa-folder-tree text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-(--text-secondary) font-bold">إجمالي الطلبات المسندة</p>
                <h3 class="text-2xl font-black text-(--text-primary)">{{ $assignedCount }}</h3>
            </div>
        </div>
    </div>

    {{-- Recent Requests Table --}}
    <div class="bg-(--surface-card) rounded-2xl border border-(--border-primary) shadow-sm overflow-hidden text-start">
        <div class="px-6 py-5 border-b border-(--border-primary) flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="font-bold text-(--text-primary)">آخر الطلبات المسندة إليك</h3>
            </div>
            <a href="{{ route('council_coordinator.requests') }}" class="text-brand-600 dark:text-brand-400 text-sm font-bold hover:underline">عرض الكل</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th class="px-6 py-4 font-bold text-start">البرنامج / الجامعة</th>
                        <th class="px-6 py-4 font-bold">المرحلة الحالية</th>
                        <th class="px-6 py-4 font-bold">تاريخ الإسناد</th>
                        <th class="px-6 py-4 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($recentRequests as $req)
                        @php
                            $stageNames = [
                                'stage_one'   => 'طلب الاعتماد الأولي',
                                'stage_two'   => 'البيانات الأساسية',
                                'stage_three' => 'تقرير الدراسة الذاتية',
                                'stage_four'  => 'اختيار لجنة التقييم',
                                'stage_five'  => 'تحديد جدول الزيارة',
                                'stage_six'   => 'تقارير نتائج التقييم(الأولية)',
                                'stage_seven' => 'توصيات اللجنة والرد عليها',
                                'stage_eight' => 'تقارير نتائج التقييم(الختامية)',
                            ];
                        @endphp
                        <tr class="hover:bg-(--border-primary)/30 transition-colors">
                            <td class="px-6 py-4 text-start font-bold text-(--text-primary)">
                                <div>{{ $req->program->program_name }}</div>
                                <div class="text-xs text-(--text-secondary) font-normal">{{ $req->program->department->college->university->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-100">
                                    {{ $stageNames[$req->current_stage] ?? $req->current_stage }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $req->updated_at->format('Y/m/d') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('requests.show', $req) }}" class="inline-flex items-center gap-1.5 text-brand-600 hover:text-brand-700 font-bold transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    لوحة الطلب
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-(--text-secondary)">لا توجد طلبات مسندة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
