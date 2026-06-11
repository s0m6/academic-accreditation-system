@extends('partials.app')

@php
   $breadcrumbs = [
        'الصفحة الرئيسية' => '/program-coordinator/dashboard'
    ];
@endphp

@section('title', 'تحليلات النظام')
@section('title2', 'لوحة تحكم منسق البرنامج')
@section('description', 'مرحباً بك في نظام الاعتماد الأكاديمي. يمكنك إدارة ومتابعة طلبات الاعتماد الخاصة ببرامجك هنا.')
@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Statistics Card - Total Requests --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-folder text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">إجمالي الطلبات</h3>
                <p class="text-sm text-(--text-secondary)">كل طلبات الاعتماد المسجلة</p>
            </div>
        </div>
        <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['total_count'] }}</div>
    </div>

    {{-- Statistics Card - Active Requests --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-spinner text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">الطلبات النشطة</h3>
                <p class="text-sm text-(--text-secondary)">طلبات قيد المراجعة والدراسة</p>
            </div>
        </div>
        <div class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['active_count'] }}</div>
    </div>

    {{-- Statistics Card - Completed Requests --}}
    <div class="bg-(--surface-card) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-(--text-primary)">الطلبات المكتملة</h3>
                <p class="text-sm text-(--text-secondary)">تم منح الاعتماد الأكاديمي</p>
            </div>
        </div>
        <div class="text-3xl font-black text-green-600 dark:text-green-400">{{ $stats['completed_count'] }}</div>
    </div>
</div>

{{-- Content Area --}}
@if($requests->isEmpty())
    <div class="bg-(--surface-card) rounded-2xl border border-(--border-primary) shadow-sm p-8 text-center">
        <div class="max-w-md mx-auto py-10">
            <div class="w-20 h-20 bg-brand-500/10 text-brand-600 flex items-center justify-center rounded-full mx-auto mb-6">
                <i class="fa-solid fa-folder-plus text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-(--text-primary)">لا توجد طلبات اعتماد مسجلة</h2>
            <p class="text-(--text-secondary) mb-8">لم يتم العثور على أي طلبات اعتماد مرتبطة بحسابك حالياً.</p>
        </div>
    </div>
@else
    {{-- Active Sessions Table --}}
    <div class="bg-(--surface-card) rounded-2xl border border-(--border-primary) shadow-sm overflow-hidden text-start">
        <div class="px-6 py-5 border-b border-(--border-primary) flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
                <h3 class="font-bold text-(--text-primary)">طلبات الاعتماد الخاصة بك</h3>
            </div>
            <a href="{{ route('program_coordinator.requests') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors no-underline">عرض الكل</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th class="px-6 py-4 font-bold text-start">البرنامج / الكلية</th>
                        <th class="px-6 py-4 font-bold text-start">الجامعة</th>
                        <th class="px-6 py-4 font-bold">المرحلة الحالية</th>
                        <th class="px-6 py-4 font-bold">تاريخ التحديث</th>
                        <th class="px-6 py-4 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @foreach($latestRequests as $req)
                        @php
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
                        @endphp
                        <tr class="hover:bg-(--border-primary)/30 transition-colors">
                            <td class="px-6 py-4 text-start font-bold text-(--text-primary)">
                                <div>{{ $req->program->program_name }}</div>
                                <div class="text-xs text-(--text-secondary) font-normal">{{ $req->program->department->college->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-start">
                                <span class="text-sm text-(--text-primary)">{{ $req->program->department->college->university->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
                                    {{ $stageNames[$req->current_stage] ?? $req->current_stage }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium">{{ $req->updated_at->format('Y-m-d') }}</span>
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
