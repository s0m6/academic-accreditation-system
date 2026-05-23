@extends('partials.app')

@php
   $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard',
        'طلبات الاعتماد' => '/accreditation-officer/requests',
        'الطلب #' . $request->id => '#'
    ];
@endphp

@section('title', 'تفاصيل طلب الاعتماد')
@section('title2', 'الطلب #' . $request->id)
@section('description', 'عرض تفاصيل ومعلومات طلب الاعتماد')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- معلومات البرنامج الأساسية -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-(--surface-100) rounded-xl border border-gray-100 dark:border-gray-800 p-6 shadow-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-brand-500"></i>
                    بيانات البرنامج
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">اسم البرنامج</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $request->program->program_name ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">الدرجة العلمية</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ match($request->program->degree_level ?? '') {
                                'diploma' => 'دبلوم',
                                'bachelor' => 'بكالوريوس',
                                'master' => 'ماجستير',
                                'phd' => 'دكتوراه',
                                default => $request->program->degree_level ?? 'غير محدد'
                            } }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">القسم</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $request->program->department->name ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">الكلية</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $request->program->department->college->name ?? 'غير محدد' }}</p>
                    </div>
                </div>
            </div>

            <!-- معلومات الطلب وتفاصيل الإرسال -->
            <div class="bg-white dark:bg-(--surface-100) rounded-xl border border-gray-100 dark:border-gray-800 p-6 shadow-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-brand-500"></i>
                    تاريخ الطلب
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-800/50 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $request->created_at ? $request->created_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-50 dark:border-gray-800/50 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">آخر تحديث</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $request->updated_at ? $request->updated_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- الحالة والمسؤولين -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-(--surface-100) rounded-xl border border-gray-100 dark:border-gray-800 p-6 shadow-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-signal text-brand-500"></i>
                    حالة الطلب
                </h3>
                
                <div class="mb-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">المرحلة الحالية</p>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-800 w-full justify-center">
                        {{ str_replace('_', ' ', $request->current_stage ?? 'غير محدد') }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">حالة الطلب</p>
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
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $statusClass }} w-full justify-center">
                        {{ $statusText }}
                    </span>
                </div>
            </div>

            <!-- معلومات المنسقين -->
            <div class="bg-white dark:bg-(--surface-100) rounded-xl border border-gray-100 dark:border-gray-800 p-6 shadow-xs">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-users text-brand-500"></i>
                    المنسقون
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">منسق البرنامج</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $request->programCoordinator->name ?? 'غير محدد' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->programCoordinator->email ?? 'لا يوجد بريد' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2 border-t border-gray-50 dark:border-gray-800/50">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">منسق المجلس</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $request->councilCoordinator->name ?? 'قيد الانتظار' }}</p>
                                @if($request->councilCoordinator)
                                    <p class="text-xs text-gray-500">{{ $request->councilCoordinator->email ?? '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
