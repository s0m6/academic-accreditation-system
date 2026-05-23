@extends('partials.app')

@php
   $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard',
        'طلبات الاعتماد' => '/accreditation-officer/requests'
    ];
@endphp

@section('title', 'طلبات الاعتماد')
@section('title2', 'إدارة طلبات الاعتماد')
@section('description', 'عرض ومتابعة جميع طلبات الاعتماد لبرامج الجامعة')

@section('content')
    <div class="bg-white dark:bg-(--surface-100) rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-xs">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-list text-brand-500"></i>
                جميع طلبات الاعتماد
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/20 text-gray-500 dark:text-gray-400 text-sm">
                        <th class="py-4 px-6 font-medium">رقم الطلب</th>
                        <th class="py-4 px-6 font-medium">البرنامج</th>
                        <th class="py-4 px-6 font-medium">القسم / الكلية</th>
                        <th class="py-4 px-6 font-medium">المرحلة الحالية</th>
                        <th class="py-4 px-6 font-medium">الحالة</th>
                        <th class="py-4 px-6 font-medium text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">
                                #{{ $request->id }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ $request->program->program_name ?? 'غير محدد' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ match($request->program->degree_level ?? '') {
                                        'diploma' => 'دبلوم',
                                        'bachelor' => 'بكالوريوس',
                                        'master' => 'ماجستير',
                                        'phd' => 'دكتوراه',
                                        default => $request->program->degree_level ?? 'غير محدد'
                                    } }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-700 dark:text-gray-300">{{ $request->program->department->name ?? 'غير محدد' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $request->program->department->college->name ?? 'غير محدد' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                    {{ str_replace('_', ' ', $request->current_stage ?? 'غير محدد') }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusClass = match($request->request_status) {
                                        'Active' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-800',
                                        'draft' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-700',
                                        'completed' => 'bg-brand-50 text-brand-700 border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-800',
                                        'canceled' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-800',
                                        default => 'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-800'
                                    };
                                    $statusText = match($request->request_status) {
                                        'Active' => 'نشط',
                                        'draft' => 'مسودة',
                                        'completed' => 'مكتمل',
                                        'canceled' => 'ملغى',
                                        default => $request->request_status
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('requests.show', $request->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-50 text-brand-600 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition-colors" title="التفاصيل">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-folder-open text-2xl"></i>
                                    </div>
                                    <p>لا توجد طلبات اعتماد حتى الآن</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-800">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
