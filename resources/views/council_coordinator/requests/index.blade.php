@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-coordinator/dashboard',
        'طلبات الاعتماد' => '/council-coordinator/requests',
    ];
@endphp

@section('title', 'طلبات الاعتماد')
@section('title2', 'إدارة طلبات الاعتماد')
@section('description', 'عرض ومتابعة كافة طلبات الاعتماد الأكاديمي المسندة إليك لإدارتها.')

@section('content')
<div class="w-full text-start">

    {{-- Toolbar: Search --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <form action="{{ route('council_coordinator.requests') }}" method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 end-3 flex items-center pointer-events-none text-(--text-secondary)">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ابحث باسم البرنامج..."
                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full pe-10 ps-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                >
            </div>
            <button type="submit" class="hidden"></button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">

        {{-- Card Header --}}
        <div class="border-b border-(--border-primary) bg-(--bg-main) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">طلبات الاعتماد المسندة</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">
                        {{ $requests->total() }} طلب إجمالي
                    </p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-start">البرنامج / الجامعة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">المرحلة الحالية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة العامة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">تاريخ آخر تحديث</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">

                    @forelse($requests as $req)
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
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            {{-- Program / University --}}
                            <td class="px-6 py-4 text-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm border border-brand-100 dark:border-brand-500/20">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-(--text-primary) text-[14px]">{{ $req->program->program_name }}</div>
                                        <div class="text-[11px] text-(--text-secondary) font-normal">{{ $req->program->department->college->university->name }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- Stage --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 shadow-sm">
                                    <i class="fa-solid fa-layer-group text-[10px]"></i>
                                    {{ $stageNames[$req->current_stage] ?? $req->current_stage }}
                                </span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @php
                                    $statusIcons = [
                                        'draft' => 'fa-pen-ruler',
                                        'Active' => 'fa-circle-play',
                                        'completed' => 'fa-circle-check',
                                        'canceled' => 'fa-circle-xmark'
                                    ];
                                @endphp
                                <span @class([
                                    'font-bold inline-flex items-center gap-1',
                                    'text-blue-600' => $req->request_status === 'Active',
                                    'text-slate-500' => $req->request_status === 'draft',
                                    'text-emerald-600' => $req->request_status === 'completed',
                                    'text-red-500' => $req->request_status === 'canceled',
                                ])>
                                    <i class="fa-solid {{ $statusIcons[$req->request_status] ?? 'fa-circle' }}"></i>
                                    {{ $req->request_status }}
                                </span>
                            </td>
                            {{-- Last Update --}}
                            <td class="px-6 py-4 text-xs">{{ $req->updated_at->format('Y-m-d H:i') }}</td>
                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('requests.show', $req) }}"
                                   class="inline-flex items-center gap-2 bg-brand-50 hover:bg-brand-100 dark:bg-brand-500/10 dark:hover:bg-brand-500/20 text-brand-700 dark:text-brand-400 font-bold text-xs px-4 py-2 rounded-xl transition-all border border-brand-100 dark:border-brand-500/20">
                                    <i class="fa-solid fa-display"></i>
                                    عرض لوحة الطلب
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-(--text-secondary)">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-file-circle-exclamation text-2xl text-(--text-secondary)"></i>
                                    </div>
                                    <span class="font-bold text-base">لا توجد طلبات مرتبطة بك</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
            <div class="border-t border-(--border-primary) px-6 py-4 bg-(--bg-main)">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
