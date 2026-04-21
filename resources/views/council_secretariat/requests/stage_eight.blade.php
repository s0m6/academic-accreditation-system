@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-secretariat/dashboard',
        'طلبات الاعتماد' => '#',
        'تقارير نتائج التقييم(الختامية)' => route('council_secretariat.requests.stage_eight')
    ];
@endphp

@section('title', 'طلبات الاعتماد - تقارير نتائج التقييم(الختامية)')
@section('title2', 'إدارة طلبات المرحلة الثامنة')
@section('description', 'عرض ومراجعة تقارير نتائج التقييم الختامية وصدور قرارات الاعتماد')

@section('content')
<div class="w-full text-start">
    
    <!-- Requests Table -->
    <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">
        
        <div class="border-b border-(--border-primary) bg-(--content-container) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex justify-center items-center shadow-inner border border-emerald-100 dark:border-emerald-500/20">
                    <span class="text-2xl font-black">8</span>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة تقارير نتائج التقييم الختامية</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">مراجعة التقارير الختامية وإصدار القرارات النهائية للاعتماد الأكاديمي</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-right">الجامعة والكلية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">البرنامج الدراسي</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($requests as $req)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            <td class="px-6 py-5 font-bold text-(--text-primary) text-right whitespace-nowrap">
                                <div class="flex items-center gap-3 justify-start">
                                    <div class="w-10 h-10 shrink-0 rounded-lg bg-(--bg-main) flex items-center justify-center text-brand-600 dark:text-brand-400 border border-(--border-primary)">
                                        <i class="fa-solid fa-university"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[14px]">{{ $req->program->department->college->university->name }}</span>
                                        <span class="text-[11px] text-(--text-secondary) font-normal">{{ $req->program->department->college->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap cursor-default">
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-(--bg-main) border border-(--border-primary) rounded-lg shadow-sm">
                                    <i class="fa-solid fa-book-open text-brand-500 text-xs text-brand-600 dark:text-brand-400"></i>
                                    <span class="font-bold text-(--text-primary)">{{ $req->program->program_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
                                        'Active' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                        'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                        'canceled' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'Active' => 'نشط / قيد المراجعة',
                                        'completed' => 'مكتمل',
                                        'canceled' => 'ملغي',
                                    ];
                                    $currentStatus = $req->request_status;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-bold border shadow-sm {{ $statusClasses[$currentStatus] ?? $statusClasses['draft'] }}">
                                    {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition-all shadow-md shadow-brand-500/20 text-xs font-black cursor-pointer group">
                                    <i class="fa-solid fa-gauge-high group-hover:rotate-12 transition-transform"></i>
                                    لوحة الطلب
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-(--text-secondary) border-t border-(--border-primary)">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-award text-2xl text-(--text-secondary) dark:text-gray-500"></i>
                                    </div>
                                    <span class="text-base md:text-lg font-bold">لا توجد طلبات في مرحلة تقارير النتائج الختامية حالياً</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
