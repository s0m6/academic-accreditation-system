@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-secretariat/dashboard',
        'الجامعات' => '/council-secretariat/universities'
    ];
@endphp

@section('title', 'الجامعات')
@section('title2', 'قائمة الجامعات المسجلة')
@section('description', 'عرض الجامعات المسجله وحالة تسجيل مسؤول اعتماد لكل جامعة')
@section('content')
<div class="w-full text-start" x-data="{
    showModal: false,
    selectedId: '',
    selectedName: '',
    showCreateModal: false,
    showEditModal: false,
    showViewModal: false,
    editingUni: { id: '', name: '', type: 'government', president_name: '', president_email: '', president_mobile: '', president_phone: '' },
    viewingUni: { name: '', type: '', president_name: '', president_email: '', president_mobile: '', president_phone: '', officer: null }
}" @open-officer-modal.window="showModal = true; selectedId = $event.detail.id; selectedName = $event.detail.name" @open-create-modal.window="showCreateModal = true">
    
    <!-- Alerts / Messages -->
    @if(session('success'))
        <div class="mb-6 text-green-700 bg-green-50 p-4 rounded-xl flex items-center shadow-sm border border-green-200">
            <i class="fa-solid fa-circle-check text-xl me-3"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-center shadow-sm border border-red-200">
            <i class="fa-solid fa-triangle-exclamation text-xl me-3"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-start shadow-sm border border-red-200">
            <i class="fa-solid fa-circle-xmark text-xl me-3 mt-0.5 shrink-0"></i>
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

    <div>
         
        <!-- Universities Table -->
       <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">
        
        <div class="border-b border-(--border-primary) bg-(--bg-main) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-building-user text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة الجامعات المسجلة</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">إدارة الجامعات ومسؤولي الاعتماد بفاعلية وموثوقية</p>
                </div>
            </div>
            <button @click="$dispatch('open-create-modal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-brand-600 dark:bg-brand-500 hover:bg-brand-700 dark:hover:bg-brand-600 transition-all shadow-sm hover:shadow hover:scale-[1.02] cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i> إضافة جامعة جديدة
            </button>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">اسم الجامعة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">النوع</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($universities as $university)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            <td class="px-6 py-5 font-bold text-(--text-primary) cursor-default whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-(--bg-main) flex items-center justify-center text-brand-600 dark:text-brand-400 shadow-sm border border-(--border-primary)">
                                        <i class="fa-solid fa-graduation-cap text-lg"></i>
                                    </div>
                                    <span class="text-[15px]">{{ $university->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap cursor-default shrink-0">
                                @if($university->type == 'government')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 shadow-sm">
                                        <i class="fa-regular fa-building shrink-0"></i> حكومية
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 shadow-sm">
                                        <i class="fa-solid fa-city shrink-0"></i> أهلية
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap cursor-default shrink-0">
                                @if(!$university->accreditation_officer_id)
                                    <div class="flex items-center text-slate-700 bg-slate-100 border border-slate-200 dark:bg-slate-800/50 dark:text-slate-300 dark:border-slate-700 font-bold text-xs px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                        <div class="h-2 w-2 rounded-full bg-slate-400 dark:bg-slate-500 me-2 shadow-sm shrink-0"></div> غير مرتبط
                                    </div>
                                @elseif(!$university->officer->email_verified_at)
                                    <div class="flex items-center text-orange-800 bg-orange-100 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 font-bold text-xs px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                        <div class="h-2 w-2 rounded-full bg-orange-500 dark:bg-orange-400 me-2 shadow-sm animate-pulse shrink-0"></div> بانتظار التفعيل
                                    </div>
                                @else
                                    <div class="flex items-center text-emerald-800 bg-emerald-100 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 font-bold text-xs px-3 py-1.5 rounded-lg w-max shrink-0 shadow-sm">
                                        <div class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 me-2 shadow-sm shrink-0"></div> مفعّل نشط
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap shrink-0">
                                <div class="flex justify-start gap-3">
                                    <button type="button" 
                                            @click="viewingUni = {{ json_encode([
                                                'name' => $university->name,
                                                'type' => $university->type,
                                                'president_name' => $university->president_name,
                                                'president_email' => $university->president_email,
                                                'president_mobile' => $university->president_mobile,
                                                'president_phone' => $university->president_phone,
                                                'officer' => $university->officer ? [
                                                    'name' => $university->officer->name,
                                                    'email' => $university->officer->email,
                                                    'mobile' => $university->officer->mobile,
                                                    'phone' => $university->officer->phone,
                                                    'email_verified_at' => $university->officer->email_verified_at,
                                                ] : null,
                                            ]) }}; showViewModal = true"
                                            class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow text-sky-700 bg-sky-100 border border-sky-200 hover:bg-sky-200 dark:text-sky-400 dark:bg-sky-500/10 dark:border-sky-500/20 dark:hover:bg-sky-500/20 cursor-pointer" title="عرض التفاصيل">
                                        <i class="fa-solid fa-eye text-[15px]"></i>
                                    </button>

                                    <button type="button"
                                            @click="editingUni = {{ json_encode([
                                                'id' => $university->id,
                                                'name' => $university->name,
                                                'type' => $university->type,
                                                'president_name' => $university->president_name,
                                                'president_email' => $university->president_email,
                                                'president_mobile' => $university->president_mobile,
                                                'president_phone' => $university->president_phone,
                                            ]) }}; showEditModal = true"
                                            class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow text-amber-700 bg-amber-100 border border-amber-200 hover:bg-amber-200 dark:text-amber-400 dark:bg-amber-500/10 dark:border-amber-500/20 dark:hover:bg-amber-500/20 cursor-pointer" title="تعديل البيانات">
                                        <i class="fa-solid fa-pen text-[14px]"></i>
                                    </button>

                                    @if(!$university->accreditation_officer_id)
                                        <button 
                                            @click="$dispatch('open-officer-modal', { id: {{ $university->id }}, name: '{{ addslashes($university->name) }}' })"
                                            class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer group text-brand-700 bg-brand-100 border border-brand-200 hover:bg-brand-200 dark:text-brand-400 dark:bg-brand-500/10 dark:border-brand-500/20 dark:hover:bg-brand-500/20" 
                                            title="إضافة دور مسؤول اعتماد">
                                            <i class="fa-solid fa-user-plus text-[14px] group-hover:scale-110 transition-transform"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-(--text-secondary) border-t border-(--border-primary)">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-list-ul text-2xl text-(--text-secondary) dark:text-gray-500"></i>
                                    </div>
                                    <span class="text-base md:text-lg font-bold">لا توجد أي جامعات مسجلة</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showModal" @click.away="showModal = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-lg">
                         
                        <form :action="'{{ url('/council-secretariat/universities') }}/' + selectedId + '/officer'" method="POST">
                            @csrf
                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary)">
                                        إضافة مسؤول اعتماد لـ <span class="text-brand-600 dark:text-brand-400 mt-2 block sm:inline-block" x-text="selectedName"></span>
                                    </h3>
                                    <button type="button" @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-xmark text-lg mt-0.5"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-5">
                                    <div>
                                        <label for="name" class="block text-sm font-bold text-(--text-primary) mb-2">الاسم الكامل <span class="text-red-500 dark:text-red-400">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-user text-(--text-secondary)"></i></span>
                                            <input type="text" name="name" id="name" required
                                                   class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                   placeholder="أدخل الاسم الثلاثي">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-bold text-(--text-primary) mb-2">البريد الإلكتروني <span class="text-red-500 dark:text-red-400">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-envelope text-(--text-secondary)"></i></span>
                                            <input type="email" name="email" id="email" dir="ltr" required
                                                   class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                   placeholder="user@university.edu">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="phone" class="block text-sm font-bold text-(--text-primary) mb-2">الهاتف الثابت</label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-phone text-(--text-secondary)"></i></span>
                                                <input type="tel" name="phone" id="phone" dir="ltr"
                                                       class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                       placeholder="01XXXXXXX">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="mobile" class="block text-sm font-bold text-(--text-primary) mb-2">رقم الجوال</label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-mobile-screen text-(--text-secondary)"></i></span>
                                                <input type="tel" name="mobile" id="mobile" dir="ltr"
                                                       class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                       placeholder="7XXXXXXXX">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-2 items-start mt-4 p-3 rounded-lg bg-blue-50 border border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20">
                                        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                        <p class="text-xs font-bold leading-relaxed text-blue-800 dark:text-blue-300">
                                            سيقوم النظام بإنشاء كلمة مرور قوية بشكل تلقائي وإرسالها لبريد المسؤول لسهولة الدخول المباشر.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-(--bg-main) px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-0 sm:space-x-3 sm:space-x-reverse rounded-b-2xl">
                                <button type="button" @click="showModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-(--surface-card) px-5 py-2.5 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:filter hover:brightness-95 dark:hover:brightness-110 transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-brand-600 dark:bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-user-plus me-2"></i> تأكيد الإنشاء
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showCreateModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showCreateModal" @click.away="showCreateModal = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-xl">
                         
                        <form action="{{ route('council_secretariat.universities.store') }}" method="POST">
                            @csrf
                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary) flex items-center gap-2.5">
                                        <span class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-500/20">
                                            <i class="fa-solid fa-graduation-cap text-base"></i>
                                        </span>
                                        <span>إضافة جامعة جديدة</span>
                                    </h3>
                                    <button type="button" @click="showCreateModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-xmark text-lg mt-0.5"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="uni_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم الجامعة <span class="text-red-500 dark:text-red-400">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-building-columns text-(--text-secondary)"></i></span>
                                                <input type="text" name="name" id="uni_name" required
                                                       class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                       placeholder="أدخل اسم الجامعة الكامل">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="uni_type" class="block text-sm font-bold text-(--text-primary) mb-2">نوع الجامعة <span class="text-red-500 dark:text-red-400">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-circle-info text-(--text-secondary)"></i></span>
                                                <select name="type" id="uni_type" required
                                                        class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                    <option value="government">حكومية</option>
                                                    <option value="private">أهلية / خاصة</option>
                                                </select>
                                                <span class="absolute left-3 pointer-events-none mt-0.5"><i class="fa-solid fa-chevron-down text-xs text-(--text-secondary)"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-(--border-primary) pt-4 mt-6">
                                        <h4 class="text-sm font-bold text-brand-600 dark:text-brand-400 mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-user-tie"></i> بيانات رئيس الجامعة (اختياري)
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label for="president_name" class="block text-sm font-bold text-(--text-primary) mb-2">الاسم الكامل</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-user text-(--text-secondary)"></i></span>
                                                    <input type="text" name="president_name" id="president_name"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="أدخل اسم رئيس الجامعة">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="president_email" class="block text-sm font-bold text-(--text-primary) mb-2">البريد الإلكتروني</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-envelope text-(--text-secondary)"></i></span>
                                                    <input type="email" name="president_email" id="president_email" dir="ltr"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="president@university.edu">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="president_phone" class="block text-sm font-bold text-(--text-primary) mb-2">الهاتف الثابت</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-phone text-(--text-secondary)"></i></span>
                                                    <input type="tel" name="president_phone" id="president_phone" dir="ltr"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="01XXXXXXX">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="president_mobile" class="block text-sm font-bold text-(--text-primary) mb-2">رقم الجوال</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-mobile-screen text-(--text-secondary)"></i></span>
                                                    <input type="tel" name="president_mobile" id="president_mobile" dir="ltr"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="7XXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-(--bg-main) px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-0 sm:space-x-3 sm:space-x-reverse rounded-b-2xl">
                                <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-(--surface-card) px-5 py-2.5 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:filter hover:brightness-95 dark:hover:brightness-110 transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-brand-600 dark:bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-plus me-2"></i> إضافة الجامعة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showEditModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showEditModal" @click.away="showEditModal = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-xl">
                         
                        <form :action="'{{ url('/council-secretariat/universities') }}/' + editingUni.id" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary) flex items-center gap-2.5">
                                        <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-500/20">
                                            <i class="fa-solid fa-pen text-base"></i>
                                        </span>
                                        <span>تعديل بيانات الجامعة</span>
                                    </h3>
                                    <button type="button" @click="showEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0">
                                        <i class="fa-solid fa-xmark text-lg mt-0.5"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="edit_uni_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم الجامعة <span class="text-red-500 dark:text-red-400">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-building-columns text-(--text-secondary)"></i></span>
                                                <input type="text" name="name" id="edit_uni_name" required x-model="editingUni.name"
                                                       class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="edit_uni_type" class="block text-sm font-bold text-(--text-primary) mb-2">نوع الجامعة <span class="text-red-500 dark:text-red-400">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-circle-info text-(--text-secondary)"></i></span>
                                                <select name="type" id="edit_uni_type" required x-model="editingUni.type"
                                                        class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                    <option value="government">حكومية</option>
                                                    <option value="private">أهلية / خاصة</option>
                                                </select>
                                                <span class="absolute left-3 pointer-events-none mt-0.5"><i class="fa-solid fa-chevron-down text-xs text-(--text-secondary)"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-(--border-primary) pt-4 mt-6">
                                        <h4 class="text-sm font-bold text-brand-600 dark:text-brand-400 mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-user-tie"></i> بيانات رئيس الجامعة (اختياري)
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label for="edit_president_name" class="block text-sm font-bold text-(--text-primary) mb-2">الاسم الكامل</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-user text-(--text-secondary)"></i></span>
                                                    <input type="text" name="president_name" id="edit_president_name" x-model="editingUni.president_name"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="أدخل اسم رئيس الجامعة">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="edit_president_email" class="block text-sm font-bold text-(--text-primary) mb-2">البريد الإلكتروني</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-envelope text-(--text-secondary)"></i></span>
                                                    <input type="email" name="president_email" id="edit_president_email" dir="ltr" x-model="editingUni.president_email"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="president@university.edu">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="edit_president_phone" class="block text-sm font-bold text-(--text-primary) mb-2">الهاتف الثابت</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-phone text-(--text-secondary)"></i></span>
                                                    <input type="tel" name="president_phone" id="edit_president_phone" dir="ltr" x-model="editingUni.president_phone"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="01XXXXXXX">
                                                </div>
                                            </div>

                                            <div>
                                                <label for="edit_president_mobile" class="block text-sm font-bold text-(--text-primary) mb-2">رقم الجوال</label>
                                                <div class="relative flex items-center">
                                                    <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-mobile-screen text-(--text-secondary)"></i></span>
                                                    <input type="tel" name="president_mobile" id="edit_president_mobile" dir="ltr" x-model="editingUni.president_mobile"
                                                           class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                           placeholder="7XXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-(--bg-main) px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-0 sm:space-x-3 sm:space-x-reverse rounded-b-2xl">
                                <button type="button" @click="showEditModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-(--surface-card) px-5 py-2.5 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:filter hover:brightness-95 dark:hover:brightness-110 transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-brand-600 dark:bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-check me-2"></i> تأكيد التعديل
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showViewModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showViewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showViewModal" @click.away="showViewModal = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-xl">
                         
                        <div class="p-6 border-b border-(--border-primary)">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-(--text-primary) flex items-center gap-2.5">
                                    <span class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-500/20">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </span>
                                    <span>تفاصيل الجامعة</span>
                                </h3>
                                <button type="button" @click="showViewModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0">
                                    <i class="fa-solid fa-xmark text-lg mt-0.5"></i>
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                <!-- University Section -->
                                <div>
                                    <h4 class="text-sm font-bold text-brand-600 dark:text-brand-400 mb-3 flex items-center gap-2 border-b border-(--border-primary) pb-2">
                                        <i class="fa-solid fa-graduation-cap"></i> بيانات الجامعة المسجلة
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">اسم الجامعة</span>
                                            <span class="font-bold text-(--text-primary)" x-text="viewingUni.name"></span>
                                        </div>
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">نوع الجامعة</span>
                                            <span class="font-bold text-(--text-primary)">
                                                <span x-show="viewingUni.type === 'government'">حكومية</span>
                                                <span x-show="viewingUni.type === 'private'">أهلية / خاصة</span>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">رئيس الجامعة</span>
                                            <span class="font-bold text-(--text-primary)" x-text="viewingUni.president_name || 'غير مسجل'"></span>
                                        </div>
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">البريد الإلكتروني للرئيس</span>
                                            <span class="font-bold text-(--text-primary) break-all" dir="ltr" x-text="viewingUni.president_email || 'غير مسجل'"></span>
                                        </div>
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">الهاتف الثابت للرئيس</span>
                                            <span class="font-bold text-(--text-primary)" dir="ltr" x-text="viewingUni.president_phone || 'غير مسجل'"></span>
                                        </div>
                                        <div>
                                            <span class="text-(--text-secondary) block text-xs mb-1">جوال الرئيس</span>
                                            <span class="font-bold text-(--text-primary)" dir="ltr" x-text="viewingUni.president_mobile || 'غير مسجل'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accreditation Officer Section -->
                                <div>
                                    <h4 class="text-sm font-bold text-brand-600 dark:text-brand-400 mb-3 flex items-center gap-2 border-b border-(--border-primary) pb-2">
                                        <i class="fa-solid fa-user-shield"></i> معلومات مسؤول الاعتماد
                                    </h4>
                                    
                                    <div x-show="!viewingUni.officer" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80 text-center text-(--text-secondary) text-sm font-bold flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-user-slash text-xl text-slate-400"></i>
                                        <span>لا يوجد مسؤول اعتماد مرتبط بهذه الجامعة حالياً.</span>
                                    </div>

                                    <div x-show="viewingUni.officer" class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-(--text-secondary) block text-xs mb-1">اسم المسؤول</span>
                                                <span class="font-bold text-(--text-primary)" x-text="viewingUni.officer?.name"></span>
                                            </div>
                                            <div>
                                                <span class="text-(--text-secondary) block text-xs mb-1">البريد الإلكتروني</span>
                                                <span class="font-bold text-(--text-primary) break-all" dir="ltr" x-text="viewingUni.officer?.email"></span>
                                            </div>
                                            <div>
                                                <span class="text-(--text-secondary) block text-xs mb-1">الهاتف الثابت</span>
                                                <span class="font-bold text-(--text-primary)" dir="ltr" x-text="viewingUni.officer?.phone || 'غير مسجل'"></span>
                                            </div>
                                            <div>
                                                <span class="text-(--text-secondary) block text-xs mb-1">الجوال</span>
                                                <span class="font-bold text-(--text-primary)" dir="ltr" x-text="viewingUni.officer?.mobile || 'غير مسجل'"></span>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <span class="text-(--text-secondary) block text-xs mb-1">حالة الحساب</span>
                                                <template x-if="viewingUni.officer?.email_verified_at">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-sm">
                                                        <div class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 me-2 shadow-sm shrink-0"></div> مفعّل نشط
                                                    </span>
                                                </template>
                                                <template x-if="!viewingUni.officer?.email_verified_at">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 shadow-sm">
                                                        <div class="h-2 w-2 rounded-full bg-orange-500 dark:bg-orange-400 me-2 shadow-sm animate-pulse shrink-0"></div> بانتظار التفعيل
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-(--bg-main) px-6 py-4 flex justify-end rounded-b-2xl">
                            <button type="button" @click="showViewModal = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg bg-(--surface-card) px-6 py-2.5 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:filter hover:brightness-95 dark:hover:brightness-110 transition-all cursor-pointer">
                                إغلاق
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
