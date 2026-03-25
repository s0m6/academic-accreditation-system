@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-secretariat/dashboard',
        'الجامعات' => '/council-secretariat/universities'
    ];
@endphp

@section('title', 'الجامعات')
@section('title2', 'الجامعات')
@section('description', 'عرض الجامعات المسجله بالمجلس')
@section('user_name', auth()->user()->name)
@section('user_role', auth()->user()->role)
@section('content')
    <!-- Alerts / Messages -->
    @if(session('success'))
        <div class="mb-5 text-green-800 bg-green-100 dark:bg-green-900/30 dark:text-green-400 p-4 rounded-xl flex items-center shadow-sm border border-green-200 dark:border-green-800">
            <i class="fa-solid fa-circle-check text-xl me-3"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 p-4 rounded-xl flex items-center shadow-sm border border-red-200 dark:border-red-800">
            <i class="fa-solid fa-triangle-exclamation text-xl me-3"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 text-red-800 bg-red-100 dark:bg-red-900/30 dark:text-red-400 p-4 rounded-xl flex items-start shadow-sm border border-red-200 dark:border-red-800">
            <i class="fa-solid fa-circle-xmark text-xl me-3 mt-0.5"></i>
            <div>
                <span class="font-bold block mb-1">يرجى تصحيح الأخطاء التالية:</span>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div x-data="{ showModal: false, selectedId: '', selectedName: '' }"
         @open-officer-modal.window="showModal = true; selectedId = $event.detail.id; selectedName = $event.detail.name">
         
        <!-- Universities Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/80 px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                        <i class="fa-solid fa-building-user text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">قائمة الجامعات</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">إدارة الجامعات ومسؤولي الاعتماد التابعين لها بفاعلية</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-600 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">اسم الجامعة</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">النوع</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($universities as $university)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50/70 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-5 font-bold text-gray-900 dark:text-white cursor-default">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 shrink-0 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </div>
                                        <span>{{ $university->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap cursor-default w-32">
                                    @if($university->type == 'government')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 shrink-0 shadow-sm">
                                            <i class="fa-solid fa-building-columns shrink-0"></i> حكومية
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 shrink-0 shadow-sm">
                                            <i class="fa-solid fa-building shrink-0"></i> أهلية
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap cursor-default w-40">
                                    @if(!$university->accreditation_officer_id)
                                        <div class="flex items-center text-gray-500 dark:text-gray-400 font-bold text-xs bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                            <div class="h-2 w-2 rounded-full bg-gray-400 me-2 shadow-sm shrink-0"></div> غير مرتبط
                                        </div>
                                    @elseif(!$university->officer->email_verified_at)
                                        <div class="flex items-center text-orange-700 dark:text-orange-400 font-bold text-xs bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-500/30 px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                            <div class="h-2 w-2 rounded-full bg-orange-500 me-2 shadow-sm animate-pulse shrink-0"></div> غير مفعل
                                        </div>
                                    @else
                                        <div class="flex items-center text-emerald-700 dark:text-emerald-400 font-bold text-xs bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-500/30 px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                            <div class="h-2 w-2 rounded-full bg-emerald-500 me-2 shadow-sm shrink-0"></div> مفعل
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap w-48">
                                    <div class="flex items-center space-x-2 space-x-reverse justify-end">
                                        <!-- Details Button -->
                                        <a href="#" class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center text-sky-600 bg-sky-50 hover:bg-sky-100 dark:text-sky-400 dark:bg-sky-500/10 dark:hover:bg-sky-500/20 transition-all border border-sky-200 dark:border-sky-500/20 shadow-sm hover:shadow" title="عرض التفاصيل">
                                            <i class="fa-solid fa-eye text-[15px]"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="#" class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 transition-all border border-amber-200 dark:border-amber-500/20 shadow-sm hover:shadow" title="تعديل البيانات">
                                            <i class="fa-solid fa-pen text-[14px]"></i>
                                        </a>

                                        @if(!$university->accreditation_officer_id)
                                            <!-- Add Officer Button -->
                                            <button 
                                                @click="$dispatch('open-officer-modal', { id: {{ $university->id }}, name: '{{ addslashes($university->name) }}' })"
                                                class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center text-brand-600 bg-brand-50 hover:bg-brand-100 dark:text-brand-400 dark:bg-brand-500/10 dark:hover:bg-brand-500/20 transition-all border border-brand-200 dark:border-brand-500/20 shadow-sm hover:shadow group" 
                                                title="إضافة وتعيين مسؤول اعتماد">
                                                <i class="fa-solid fa-user-plus text-[14px] group-hover:scale-110 transition-transform"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <i class="fa-solid fa-building-circle-xmark text-2xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <span class="text-lg font-bold">لا توجد جامعات مسجلة حتى الآن</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Officer Modal (AlpineJS) -->
        <template x-teleport="body">
            <div x-show="showModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div x-show="showModal" @click.away="showModal = false"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-right shadow-2xl transition-all sm:my-8 w-full max-w-lg border border-gray-100 dark:border-gray-700">
                             
                            <!-- Form -->
                            <form :action="'{{ url('/council-secretariat/universities') }}/' + selectedId + '/officer'" method="POST">
                                @csrf
                                <div class="bg-white dark:bg-gray-800 p-6 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex justify-between items-center mb-5">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">
                                            إضافة مسؤول حساب لـ <br class="sm:hidden" /><span class="text-brand-600 dark:text-brand-400 mt-1 inline-block" x-text="selectedName"></span>
                                        </h3>
                                        <button type="button" @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-gray-700 transition-colors shrink-0">
                                            <i class="fa-solid fa-xmark text-lg mt-0.5 mt-0.5"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <!-- Name -->
                                        <div>
                                            <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5">
                                                     <i class="fa-solid fa-user text-gray-400"></i>
                                                </span>
                                                <input type="text" name="name" id="name" required
                                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pr-10 p-2.5 focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 transition-colors"
                                                       placeholder="الاسم الثلاثي أو الرباعي">
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني الأساسي للمنصة <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5">
                                                     <i class="fa-solid fa-envelope text-gray-400"></i>
                                                </span>
                                                <input type="email" name="email" id="email" dir="ltr" required
                                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block text-left w-full pr-10 p-2.5 focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 transition-colors"
                                                       placeholder="user@university.edu">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <!-- Phone -->
                                            <div>
                                                <label for="phone" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الهاتف الثابت</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5">
                                                         <i class="fa-solid fa-phone text-gray-400"></i>
                                                    </span>
                                                    <input type="tel" name="phone" id="phone" dir="ltr"
                                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block text-left w-full pr-10 p-2.5 focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 transition-colors"
                                                           placeholder="01XXXXXXX">
                                                </div>
                                            </div>

                                            <!-- Mobile -->
                                            <div>
                                                <label for="mobile" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الموبايل / الجوال</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5">
                                                         <i class="fa-solid fa-mobile-screen text-gray-400"></i>
                                                    </span>
                                                    <input type="tel" name="mobile" id="mobile" dir="ltr"
                                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block text-left w-full pr-10 p-2.5 focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 transition-colors"
                                                           placeholder="7XXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-lg border border-gray-200 dark:border-gray-700"><i class="fa-solid fa-circle-info me-1"></i> سيتم إنشاء كلمة مرور عشوائية وإرسالها للبريد المدخل ليقوم المسؤول بتفعيل الحساب بنفسه.</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 sm:space-x-reverse">
                                    <button type="button" @click="showModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        إلغاء التغييرات
                                    </button>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-brand-500 transition-colors">
                                        <i class="fa-solid fa-user-check me-2"></i> إنشاء الحساب
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
