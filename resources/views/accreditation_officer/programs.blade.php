@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard',
        'البرامج الدراسية' => '/accreditation-officer/programs',
    ];
@endphp

@section('title', 'البرامج الدراسية')
@section('title2', 'إدارة البرامج الأكاديمية')
@section('description', 'إضافة وتعديل وحذف البرامج الدراسية في أقسام الجامعة')
@section('content')
<div class="w-full text-start" x-data="programsPage()">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 text-green-700 bg-green-50 p-4 rounded-xl flex items-center shadow-sm border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
            <i class="fa-solid fa-circle-check text-xl me-3"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-center shadow-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
            <i class="fa-solid fa-triangle-exclamation text-xl me-3"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-start shadow-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
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

    {{-- Table Card --}}
    <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">

        {{-- Header --}}
        <div class="border-b border-(--border-primary) bg-(--bg-main) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex justify-center items-center shadow-inner border border-orange-100 dark:border-orange-500/20">
                    <i class="fa-solid fa-book-open text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة البرامج الدراسية</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">إدارة البرامج الأكاديمية لجامعة {{ $university->name }}</p>
                </div>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 dark:bg-brand-500 text-white text-sm font-bold hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors shadow-sm cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">إضافة برنامج</span>
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">اسم البرنامج</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">القسم / الكلية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">المستوى الدراسي</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">اللغة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">عدد الساعات</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($programs as $program)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            <td class="px-6 py-5 font-bold text-(--text-primary) whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-(--bg-main) flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm border border-(--border-primary)">
                                        <i class="fa-solid fa-scroll text-sm"></i>
                                    </div>
                                    <span>{{ $program->program_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-end">
                                    <span class="block font-bold text-(--text-primary)">{{ $program->department->name }}</span>
                                    <span class="block text-xs text-(--text-secondary)">{{ $program->department->college->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                @php
                                    $levels = [
                                        'diploma' => ['label' => 'دبلوم', 'class' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20'],
                                        'bachelor' => ['label' => 'بكالوريوس', 'class' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'],
                                        'master' => ['label' => 'ماجستير', 'class' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'],
                                        'phd' => ['label' => 'دكتوراه', 'class' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'],
                                    ];
                                    $level = $levels[$program->degree_level] ?? ['label' => $program->degree_level, 'class' => 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold border shadow-sm {{ $level['class'] }}">
                                    {{ $level['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center whitespace-nowrap cursor-default">
                                <span class="text-xs font-bold">{{ $program->program_details['language'] == 'arabic' ? 'العربية' : 'الإنجليزية' }}</span>
                            </td>
                            <td class="px-6 py-5 text-center font-bold text-(--text-primary) whitespace-nowrap">
                                {{ $program->program_details['credit_hours'] }} ساعة
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex justify-center gap-2">
                                    <button @click="openRequests({{ $program->id }}, {{ json_encode($program->accreditationRequests) }})"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-blue-700 bg-blue-100 border border-blue-200 hover:bg-blue-200 dark:text-blue-400 dark:bg-blue-500/10 dark:border-blue-500/20 dark:hover:bg-blue-500/20" title="طلبات الاعتماد">
                                        <i class="fa-solid fa-list-check text-[14px]"></i>
                                    </button>
                                    <button @click="openEdit({{ $program->id }}, {{ json_encode($program) }})"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-amber-700 bg-amber-100 border border-amber-200 hover:bg-amber-200 dark:text-amber-400 dark:bg-amber-500/10 dark:border-amber-500/20 dark:hover:bg-amber-500/20" title="تعديل">
                                        <i class="fa-solid fa-pen text-[14px]"></i>
                                    </button>
                                    <button @click="openDelete({{ $program->id }}, '{{ addslashes($program->program_name) }}')"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-red-700 bg-red-100 border border-red-200 hover:bg-red-200 dark:text-red-400 dark:bg-red-500/10 dark:border-red-500/20 dark:hover:bg-red-500/20" title="حذف">
                                        <i class="fa-solid fa-trash-can text-[14px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-(--text-secondary)">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-book-open text-2xl text-(--text-secondary) dark:text-gray-500"></i>
                                    </div>
                                    <span class="text-base md:text-lg font-bold">لا توجد برامج مسجلة بعد</span>
                                    <p class="text-sm text-(--text-secondary)">ابدأ بإضافة أول برنامج دراسي باستخدام زر "إضافة برنامج"</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Modal (Unified Form) --}}
    <template x-teleport="body">
        <div x-show="showFormModal" style="display: none;" class="relative z-[100]" role="dialog" aria-modal="true">
            <div x-show="showFormModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                    <div x-show="showFormModal" @click.away="showFormModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-2xl px-0">

                        <form :action="formAction" method="POST">
                            @csrf
                            <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary)">
                                        <span x-text="isEdit ? 'تعديل بيانات البرنامج' : 'إضافة برنامج دراسي جديد'"></span>
                                    </h3>
                                    <button type="button" @click="showFormModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0 cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Left Column: Core Info --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">اسم البرنامج <span class="text-red-500">*</span></label>
                                            <input type="text" name="program_name" required x-model="form.program_name"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all"
                                                placeholder="مثال: هندسة البرمجيات">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">المستوى الدراسي <span class="text-red-500">*</span></label>
                                            <select name="degree_level" required x-model="form.degree_level"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                <option value="">اختر المستوى</option>
                                                <option value="diploma">دبلوم</option>
                                                <option value="bachelor">بكالوريوس</option>
                                                <option value="master">ماجستير</option>
                                                <option value="phd">دكتوراه</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">الكلية <span class="text-red-500">*</span></label>
                                            <select name="college_id" required x-model="selectedCollegeId" @change="fetchDepartments()"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                <option value="">اختر الكلية</option>
                                                @foreach($colleges as $college)
                                                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">القسم <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <select name="department_id" required x-model="form.department_id" :disabled="!selectedCollegeId || isLoadingDepts"
                                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <option value="">اختر القسم</option>
                                                    <template x-for="dept in currentDepartments" :key="dept.id">
                                                        <option :value="dept.id" x-text="dept.name"></option>
                                                    </template>
                                                </select>
                                                <div x-show="isLoadingDepts" class="absolute left-3 top-3">
                                                    <i class="fa-solid fa-circle-notch fa-spin text-brand-600"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right Column: Program Details (JSON) --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">لغة التدريس <span class="text-red-500">*</span></label>
                                            <select name="language" required x-model="form.language"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                <option value="arabic">العربية</option>
                                                <option value="english">الإنجليزية</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">عدد الساعات المعتمدة <span class="text-red-500">*</span></label>
                                            <input type="number" name="credit_hours" required x-model="form.credit_hours"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all"
                                                placeholder="مثال: 132">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">تاريخ تأسيس البرنامج <span class="text-red-500">*</span></label>
                                            <input type="date" name="establishment_date" required x-model="form.establishment_date"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-(--text-primary) mb-2">مدة الدراسة <span class="text-red-500">*</span></label>
                                            <input type="text" name="study_duration" required x-model="form.study_duration"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all"
                                                placeholder="مثال: 4 سنوات">
                                        </div>
                                    </div>

                                    {{-- Full Width: Website URL --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-(--text-primary) mb-2">الموقع الإلكتروني للبرنامج</label>
                                        <input type="url" name="website_url" x-model="form.website_url" dir="ltr"
                                            class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all"
                                            placeholder="https://univ.edu/program-page">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-(--bg-main) px-6 py-5 flex flex-col-reverse sm:flex-row sm:justify-end items-center gap-3 rounded-b-2xl">
                                <button type="button" @click="showFormModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-(--surface-card) px-6 py-3 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:bg-(--bg-main) hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-brand-600 dark:bg-brand-500 px-8 py-3 text-sm font-black text-white shadow-lg shadow-brand-500/20 hover:bg-brand-700 dark:hover:bg-brand-600 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-check"></i>
                                    <span x-text="isEdit ? 'حفظ التعديلات' : 'إنشاء البرنامج'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete Modal --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" style="display: none;" class="relative z-[100]" role="dialog" aria-modal="true">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                    <div x-show="showDeleteModal" @click.away="showDeleteModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-md">
                        <form :action="deleteAction" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-6">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-red-600 dark:text-red-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-(--text-primary)">تأكيد الحذف</h3>
                                        <p class="text-sm text-(--text-secondary) mt-1">هل أنت متأكد من حذف برنامج <span class="font-bold text-red-600 dark:text-red-400" x-text="deleteName"></span>؟</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-(--bg-main) px-6 py-5 flex flex-col-reverse sm:flex-row sm:justify-end items-center gap-3 rounded-b-2xl">
                                <button type="button" @click="showDeleteModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-(--surface-card) px-6 py-3 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:bg-(--bg-main) hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-red-600 px-8 py-3 text-sm font-black text-white shadow-lg shadow-red-500/20 hover:bg-red-700 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can"></i>
                                    تأكيد الحذف النهائي
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Requests Modal --}}
    <template x-teleport="body">
        <div x-show="showRequestsModal" style="display: none;" class="relative z-[100]" role="dialog" aria-modal="true">
            <div x-show="showRequestsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                    <div x-show="showRequestsModal" @click.away="showRequestsModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-4xl">
                        
                        <div class="p-6 border-b border-(--border-primary) flex justify-between items-center bg-(--bg-main)">
                            <h3 class="text-xl font-bold text-(--text-primary)">
                                طلبات الاعتماد الأكاديمي
                            </h3>
                            <button type="button" @click="showRequestsModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0 cursor-pointer">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <template x-if="currentRequests.length === 0">
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto rounded-full bg-(--bg-main) flex items-center justify-center mb-4 border border-(--border-primary) shadow-inner">
                                        <i class="fa-solid fa-file-circle-xmark text-2xl text-(--text-secondary)"></i>
                                    </div>
                                    <p class="text-lg font-bold text-(--text-primary) mb-2">لا توجد طلبات اعتماد لهذا البرنامج</p>
                                    <p class="text-sm text-(--text-secondary) mb-6">يمكنك إنشاء مسودة طلب اعتماد جديد للبدء في الإجراءات.</p>
                                    <form :action="'{{ url('/accreditation-officer/programs') }}/' + selectedProgramId + '/requests'" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 dark:bg-brand-500 text-white text-sm font-bold hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-plus"></i>
                                            إنشاء مسودة طلب اعتماد
                                        </button>
                                    </form>
                                </div>
                            </template>

                            <template x-if="currentRequests.length > 0">
                                <div class="overflow-x-auto w-full border border-(--border-primary) rounded-xl shadow-sm">
                                    <table class="w-full text-sm text-center text-(--text-secondary)">
                                        <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                                            <tr>
                                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">رقم الطلب</th>
                                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">المرحلة الحالية</th>
                                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">الحالة</th>
                                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-(--border-primary)">
                                            <template x-for="req in currentRequests" :key="req.id">
                                                <tr class="hover:bg-(--border-primary)/30 transition-colors">
                                                    <td class="px-6 py-4 font-bold text-(--text-primary) whitespace-nowrap" x-text="'REQ-' + req.id"></td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-bold border shadow-sm bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20" x-text="formatStage(req.current_stage)"></span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-bold border shadow-sm"
                                                            :class="{
                                                                'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20': req.request_status === 'draft',
                                                                'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20': req.request_status === 'Active',
                                                                'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20': req.request_status === 'completed',
                                                                'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20': req.request_status === 'canceled'
                                                            }" x-text="formatStatus(req.request_status)"></span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <a :href="'{{ url('/accreditation-officer/requests') }}/' + req.id" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-orange-100 text-orange-700 border border-orange-200 hover:bg-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 dark:hover:bg-orange-500/20 text-xs font-bold transition-colors shadow-sm cursor-pointer">
                                                            <i class="fa-solid fa-layer-group"></i>
                                                            لوحة الطلب
                                                        </a>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    function programsPage() {
        return {
            showFormModal: false,
            showDeleteModal: false,
            showRequestsModal: false,
            currentRequests: [],
            selectedProgramId: null,
            isEdit: false,
            isLoadingDepts: false,
            formAction: '{{ route("accreditation_officer.programs.store") }}',
            deleteAction: '',
            deleteName: '',

            openRequests(programId, requests) {
                this.selectedProgramId = programId;
                this.currentRequests = requests || [];
                this.showRequestsModal = true;
            },

            formatStage(stage) {
                const stages = {
                    'stage_one': 'المرحلة الأولى',
                    'stage_two': 'المرحلة الثانية',
                    'stage_three': 'المرحلة الثالثة',
                    'stage_four': 'المرحلة الرابعة',
                    'stage_five': 'المرحلة الخامسة',
                    'stage_six': 'المرحلة السادسة',
                    'stage_seven': 'المرحلة السابعة',
                    'stage_eight': 'المرحلة الثامنة',
                    'completed': 'مكتمل'
                };
                return stages[stage] || stage;
            },

            formatStatus(status) {
                const statuses = {
                    'draft': 'مسودة',
                    'Active': 'نشط',
                    'completed': 'مكتمل',
                    'canceled': 'ملغي'
                };
                return statuses[status] || status;
            },

            selectedCollegeId: '',
            currentDepartments: [],

            form: {
                program_name: '',
                degree_level: '',
                department_id: '',
                language: 'arabic',
                credit_hours: '',
                establishment_date: '',
                study_duration: '',
                website_url: ''
            },

            openCreate() {
                this.isEdit = false;
                this.formAction = '{{ route("accreditation_officer.programs.store") }}';
                this.selectedCollegeId = '';
                this.currentDepartments = [];
                this.form = {
                    program_name: '',
                    degree_level: '',
                    department_id: '',
                    language: 'arabic',
                    credit_hours: '',
                    establishment_date: '',
                    study_duration: '',
                    website_url: ''
                };
                this.showFormModal = true;
            },

            async openEdit(id, program) {
                this.isEdit = true;
                this.formAction = '{{ url("/accreditation-officer/programs") }}/' + id;

                // Set primary college to trigger departments fetch
                this.selectedCollegeId = String(program.department.college_id);

                // Fetch departments synchronously before opening modal to set selected dept correctly
                await this.fetchDepartments();

                this.form = {
                    program_name: program.program_name,
                    degree_level: program.degree_level,
                    department_id: String(program.department_id),
                    language: program.program_details.language,
                    credit_hours: program.program_details.credit_hours,
                    establishment_date: program.program_details.establishment_date,
                    study_duration: program.program_details.study_duration,
                    website_url: program.program_details.website_url
                };

                this.showFormModal = true;
            },

            async fetchDepartments() {
                if (!this.selectedCollegeId) {
                    this.currentDepartments = [];
                    this.form.department_id = '';
                    return;
                }

                this.isLoadingDepts = true;
                try {
                    const response = await fetch(`{{ url('/accreditation-officer/programs/departments') }}/${this.selectedCollegeId}`);
                    this.currentDepartments = await response.json();
                } catch (error) {
                    console.error('Error fetching departments:', error);
                    this.currentDepartments = [];
                } finally {
                    this.isLoadingDepts = false;
                }
            },

            openDelete(id, name) {
                this.deleteAction = '{{ url("/accreditation-officer/programs") }}/' + id;
                this.deleteName = name;
                this.showDeleteModal = true;
            }
        }
    }
</script>
@endpush
@endsection
