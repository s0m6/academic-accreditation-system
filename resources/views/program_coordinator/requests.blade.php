@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/program-coordinator/dashboard',
        'طلبات الاعتماد' => '/program-coordinator/requests',
    ];
@endphp

@section('title', 'طلبات الاعتماد')
@section('title2', 'إدارة طلبات الاعتماد')
@section('description', 'قائمة بطلبات الاعتماد المرتبطة بك والتابعة لبرامجك.')

@section('content')
<div class="w-full text-start px-4 sm:px-6">

    {{-- Request Table --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden mt-6 mb-12">
        <div class="bg-(--bg-main) border-b border-(--border-primary) px-6 py-4 flex items-center justify-between flex-wrap gap-4">
            <h3 class="font-bold text-(--text-primary)">طلبات الاعتماد الخاصة بك</h3>
            <div class="text-xs text-(--text-secondary) font-bold bg-(--bg-main) px-3 py-1.5 rounded-lg border border-(--border-primary)">
                إجمالي الطلبات: {{ $requests->count() }}
            </div>
        </div>

        @if($requests->isEmpty())
            <div class="p-16 flex flex-col items-center justify-center text-center gap-4">
                <div class="w-20 h-20 rounded-3xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-folder-open text-3xl text-(--text-secondary)"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-(--text-primary)">لا توجد طلبات اعتماد مرتبطة بك بعد</p>
                    <p class="text-sm text-(--text-secondary) mt-1 max-w-sm mx-auto">عندما يتم تعيينك كمنسق لطلب اعتماد، سيظهر هنا.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center text-(--text-secondary)">
                    <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary) border-b border-(--border-primary)">
                        <tr>
                            <th class="px-6 py-4 font-bold tracking-wider">#</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-start">البرنامج / الكلية</th>
                            <th class="px-6 py-4 font-bold tracking-wider">المرحلة الحالية</th>
                            <th class="px-6 py-4 font-bold tracking-wider">حالة الطلب</th>
                            <th class="px-6 py-4 font-bold tracking-wider uppercase tracking-widest">تحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @foreach ($requests as $i => $req)
                            <tr class="hover:bg-(--border-primary)/30 transition-colors group">
                                <td class="px-6 py-5 font-bold text-(--text-primary)">{{ $i + 1 }}</td>
                                <td class="px-6 py-5 text-start">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center group-hover:border-(--brand-primary) group-hover:text-(--brand-primary) transition-all">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-(--text-primary)">{{ $req->program->program_name }}</p>
                                            <p class="text-xs text-(--text-secondary)">
                                                {{ $req->program->department->college->name }} - {{ $req->program->department->name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-500/20 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 me-2 animate-pulse"></span>
                                        {{ str_replace('_', ' ', strtoupper($req->current_stage)) }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span @class([
                                        'px-3 py-1.5 rounded-lg text-xs font-bold border',
                                        'bg-green-50 text-green-700 border-green-200' => $req->request_status === 'Active',
                                        'bg-gray-50 text-gray-700 border-gray-200' => $req->request_status === 'Draft',
                                    ])>
                                        {{ $req->request_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <a href="{{ route('requests.show', $req->id) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-xs font-bold hover:bg-(--bg-main) hover:border-(--brand-primary) hover:text-(--brand-primary) transition-all cursor-pointer">
                                        <i class="fa-solid fa-gauge-high"></i>
                                        لوحة التحكم
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
