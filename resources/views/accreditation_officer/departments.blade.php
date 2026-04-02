@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard',
        'الأقسام' => '/accreditation-officer/departments',
    ];
@endphp

@section('title', 'الأقسام')
@section('title2', 'إدارة الأقسام')
@section('description', 'إضافة وتعديل وحذف أقسام الكليات')
@section('content')
<div class="w-full text-start" x-data="departmentsPage()">

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
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex justify-center items-center shadow-inner border border-indigo-100 dark:border-indigo-500/20">
                    <i class="fa-solid fa-sitemap text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة الأقسام</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">إدارة أقسام كليات {{ $university->name }}</p>
                </div>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 dark:bg-brand-500 text-white text-sm font-bold hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors shadow-sm cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">إضافة قسم</span>
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">#</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">اسم القسم</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الكلية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">رئيس القسم</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">البريد الإلكتروني</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الجوال</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($departments as $index => $department)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            <td class="px-6 py-5 font-bold text-(--text-secondary)">{{ $index + 1 }}</td>
                            <td class="px-6 py-5 font-bold text-(--text-primary) whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-(--bg-main) flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm border border-(--border-primary)">
                                        <i class="fa-solid fa-sitemap text-sm"></i>
                                    </div>
                                    <span>{{ $department->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-100 text-brand-800 border border-brand-200 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20 shadow-sm">
                                    <i class="fa-solid fa-building-columns shrink-0"></i> {{ $department->college->name }}
                                </span>
                            </td>
                            <td class="px-6 py-5 font-bold whitespace-nowrap">{{ $department->head_name }}</td>
                            <td class="px-6 py-5 whitespace-nowrap" dir="ltr">{{ $department->head_email }}</td>
                            <td class="px-6 py-5 whitespace-nowrap" dir="ltr">{{ $department->head_mobile }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex justify-center gap-2">
                                    <button @click="openEdit({{ $department->id }}, {{ json_encode(['name' => $department->name, 'college_id' => $department->college_id, 'head_name' => $department->head_name, 'head_email' => $department->head_email, 'head_mobile' => $department->head_mobile, 'head_phone' => $department->head_phone]) }})"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-amber-700 bg-amber-100 border border-amber-200 hover:bg-amber-200 dark:text-amber-400 dark:bg-amber-500/10 dark:border-amber-500/20 dark:hover:bg-amber-500/20" title="تعديل">
                                        <i class="fa-solid fa-pen text-[14px]"></i>
                                    </button>
                                    <button @click="openDelete({{ $department->id }}, '{{ addslashes($department->name) }}')"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-red-700 bg-red-100 border border-red-200 hover:bg-red-200 dark:text-red-400 dark:bg-red-500/10 dark:border-red-500/20 dark:hover:bg-red-500/20" title="حذف">
                                        <i class="fa-solid fa-trash-can text-[14px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-(--text-secondary)">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-sitemap text-2xl text-(--text-secondary) dark:text-gray-500"></i>
                                    </div>
                                    <span class="text-base md:text-lg font-bold">لا توجد أقسام مسجلة بعد</span>
                                    <p class="text-sm text-(--text-secondary)">ابدأ بإضافة أول قسم باستخدام زر "إضافة قسم"</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <template x-teleport="body">
        <div x-show="showFormModal" style="display: none;" class="relative z-[100]" role="dialog" aria-modal="true">
            <div x-show="showFormModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                    <div x-show="showFormModal" @click.away="showFormModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-lg">

                        <form :action="formAction" method="POST">
                            @csrf
                            <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary)">
                                        <span x-text="isEdit ? 'تعديل بيانات القسم' : 'إضافة قسم جديد'"></span>
                                    </h3>
                                    <button type="button" @click="showFormModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0 cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <div class="space-y-5">
                                    {{-- College Select --}}
                                    <div>
                                        <label for="college_id" class="block text-sm font-bold text-(--text-primary) mb-2">الكلية <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-building-columns text-(--text-secondary)"></i></span>
                                            <select name="college_id" id="college_id" required x-model="form.college_id"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                <option value="">اختر الكلية</option>
                                                @foreach($colleges as $college)
                                                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Department Name --}}
                                    <div>
                                        <label for="dept_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم القسم <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-sitemap text-(--text-secondary)"></i></span>
                                            <input type="text" name="name" id="dept_name" required x-model="form.name"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="مثال: قسم علوم الحاسب">
                                        </div>
                                    </div>

                                    {{-- Head Name --}}
                                    <div>
                                        <label for="head_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم رئيس القسم <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-user-tie text-(--text-secondary)"></i></span>
                                            <input type="text" name="head_name" id="head_name" required x-model="form.head_name"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="الاسم الثلاثي لرئيس القسم">
                                        </div>
                                    </div>

                                    {{-- Head Email --}}
                                    <div>
                                        <label for="head_email" class="block text-sm font-bold text-(--text-primary) mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-envelope text-(--text-secondary)"></i></span>
                                            <input type="email" name="head_email" id="head_email" dir="ltr" required x-model="form.head_email"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="head@university.edu">
                                        </div>
                                    </div>

                                    {{-- Phone & Mobile --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="head_phone" class="block text-sm font-bold text-(--text-primary) mb-2">الهاتف الثابت <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-phone text-(--text-secondary)"></i></span>
                                                <input type="tel" name="head_phone" id="head_phone" dir="ltr" required x-model="form.head_phone"
                                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                    placeholder="01XXXXXXX">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="head_mobile" class="block text-sm font-bold text-(--text-primary) mb-2">رقم الجوال <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-mobile-screen text-(--text-secondary)"></i></span>
                                                <input type="tel" name="head_mobile" id="head_mobile" dir="ltr" required x-model="form.head_mobile"
                                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                    placeholder="7XXXXXXXX">
                                            </div>
                                        </div>
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
                                    <span x-text="isEdit ? 'حفظ التعديلات' : 'إنشاء القسم'"></span>
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
                                        <p class="text-sm text-(--text-secondary) mt-1">هل أنت متأكد من حذف قسم <span class="font-bold text-red-600 dark:text-red-400" x-text="deleteName"></span>؟</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 items-start p-3 rounded-lg bg-red-50 border border-red-200 dark:bg-red-500/10 dark:border-red-500/20">
                                    <i class="fa-solid fa-circle-info text-red-600 dark:text-red-400 mt-0.5"></i>
                                    <p class="text-xs font-bold leading-relaxed text-red-800 dark:text-red-300">سيتم حذف جميع البرامج المرتبطة بهذا القسم بشكل نهائي.</p>
                                </div>
                            </div>
                            <div class="bg-(--bg-main) px-6 py-5 flex flex-col-reverse sm:flex-row sm:justify-end items-center gap-3 rounded-b-2xl">
                                <button type="button" @click="showDeleteModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-(--surface-card) px-6 py-3 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:bg-(--bg-main) hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-red-600 px-8 py-3 text-sm font-black text-white shadow-lg shadow-red-500/20 hover:bg-red-700 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can"></i>
                                    حذف القسم نهائياً
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    function departmentsPage() {
        return {
            showFormModal: false,
            showDeleteModal: false,
            isEdit: false,
            formAction: '{{ route("accreditation_officer.departments.store") }}',
            deleteAction: '',
            deleteName: '',
            form: { name: '', college_id: '', head_name: '', head_email: '', head_mobile: '', head_phone: '' },

            openCreate() {
                this.isEdit = false;
                this.formAction = '{{ route("accreditation_officer.departments.store") }}';
                this.form = { name: '', college_id: '', head_name: '', head_email: '', head_mobile: '', head_phone: '' };
                this.showFormModal = true;
            },

            openEdit(id, data) {
                this.isEdit = true;
                this.formAction = '{{ url("/accreditation-officer/departments") }}/' + id;
                this.form = { ...data, college_id: String(data.college_id) };
                this.showFormModal = true;
            },

            openDelete(id, name) {
                this.deleteAction = '{{ url("/accreditation-officer/departments") }}/' + id;
                this.deleteName = name;
                this.showDeleteModal = true;
            }
        }
    }
</script>
@endpush
@endsection
