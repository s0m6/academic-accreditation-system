@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard',
        'الكليات' => '/accreditation-officer/colleges',
    ];
@endphp

@section('title', 'الكليات')
@section('title2', 'إدارة الكليات')
@section('description', 'إضافة وتعديل وحذف كليات الجامعة')
@section('user_name', auth()->user()->name)
@section('user_role', auth()->user()->role)
@section('content')
<div class="w-full text-start" x-data="collegesPage()">

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
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-building-columns text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة الكليات</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">إدارة كليات {{ $university->name }}</p>
                </div>
            </div>
            <button @click="openCreate()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 dark:bg-brand-500 text-white text-sm font-bold hover:bg-brand-700 dark:hover:bg-brand-600 transition-colors shadow-sm cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">إضافة كلية</span>
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">#</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">اسم الكلية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">المدينة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">عميد الكلية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">البريد الإلكتروني</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الجوال</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">
                    @forelse($colleges as $index => $college)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            <td class="px-6 py-5 font-bold text-(--text-secondary)">{{ $index + 1 }}</td>
                            <td class="px-6 py-5 font-bold text-(--text-primary) whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-(--bg-main) flex items-center justify-center text-brand-600 dark:text-brand-400 shadow-sm border border-(--border-primary)">
                                        <i class="fa-solid fa-building-columns text-sm"></i>
                                    </div>
                                    <span>{{ $college->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 shadow-sm">
                                    <i class="fa-solid fa-location-dot shrink-0"></i> {{ $college->city->city_name }}
                                </span>
                            </td>
                            <td class="px-6 py-5 font-bold whitespace-nowrap">{{ $college->dean_name }}</td>
                            <td class="px-6 py-5 whitespace-nowrap" dir="ltr">{{ $college->dean_email }}</td>
                            <td class="px-6 py-5 whitespace-nowrap" dir="ltr">{{ $college->dean_mobile }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex justify-center gap-2">
                                    <button @click="openEdit({{ $college->id }}, {{ json_encode(['name' => $college->name, 'city_id' => $college->city_id, 'dean_name' => $college->dean_name, 'dean_email' => $college->dean_email, 'dean_mobile' => $college->dean_mobile, 'dean_phone' => $college->dean_phone]) }})"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow cursor-pointer text-amber-700 bg-amber-100 border border-amber-200 hover:bg-amber-200 dark:text-amber-400 dark:bg-amber-500/10 dark:border-amber-500/20 dark:hover:bg-amber-500/20" title="تعديل">
                                        <i class="fa-solid fa-pen text-[14px]"></i>
                                    </button>
                                    <button @click="openDelete({{ $college->id }}, '{{ addslashes($college->name) }}')"
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
                                        <i class="fa-solid fa-building-columns text-2xl text-(--text-secondary) dark:text-gray-500"></i>
                                    </div>
                                    <span class="text-base md:text-lg font-bold">لا توجد كليات مسجلة بعد</span>
                                    <p class="text-sm text-(--text-secondary)">ابدأ بإضافة أول كلية باستخدام زر "إضافة كلية"</p>
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
                                        <span x-text="isEdit ? 'تعديل بيانات الكلية' : 'إضافة كلية جديدة'"></span>
                                    </h3>
                                    <button type="button" @click="showFormModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0 cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <div class="space-y-5">
                                    {{-- College Name --}}
                                    <div>
                                        <label for="college_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم الكلية <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-building-columns text-(--text-secondary)"></i></span>
                                            <input type="text" name="name" id="college_name" required x-model="form.name"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="مثال: كلية الهندسة">
                                        </div>
                                    </div>

                                    {{-- City --}}
                                    <div>
                                        <label for="city_id" class="block text-sm font-bold text-(--text-primary) mb-2">المدينة <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-location-dot text-(--text-secondary)"></i></span>
                                            <select name="city_id" id="city_id" required x-model="form.city_id"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all appearance-none cursor-pointer">
                                                <option value="">اختر المدينة</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Dean Name --}}
                                    <div>
                                        <label for="dean_name" class="block text-sm font-bold text-(--text-primary) mb-2">اسم عميد الكلية <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-user-tie text-(--text-secondary)"></i></span>
                                            <input type="text" name="dean_name" id="dean_name" required x-model="form.dean_name"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="الاسم الثلاثي للعميد">
                                        </div>
                                    </div>

                                    {{-- Dean Email --}}
                                    <div>
                                        <label for="dean_email" class="block text-sm font-bold text-(--text-primary) mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                                        <div class="relative flex items-center">
                                            <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-envelope text-(--text-secondary)"></i></span>
                                            <input type="email" name="dean_email" id="dean_email" dir="ltr" required x-model="form.dean_email"
                                                class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                placeholder="dean@university.edu">
                                        </div>
                                    </div>

                                    {{-- Phone & Mobile --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="dean_phone" class="block text-sm font-bold text-(--text-primary) mb-2">الهاتف الثابت <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-phone text-(--text-secondary)"></i></span>
                                                <input type="tel" name="dean_phone" id="dean_phone" dir="ltr" required x-model="form.dean_phone"
                                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block text-left w-full pl-3 pr-10 p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                                    placeholder="01XXXXXXX">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="dean_mobile" class="block text-sm font-bold text-(--text-primary) mb-2">رقم الجوال <span class="text-red-500">*</span></label>
                                            <div class="relative flex items-center">
                                                <span class="absolute right-3.5 mt-0.5"><i class="fa-solid fa-mobile-screen text-(--text-secondary)"></i></span>
                                                <input type="tel" name="dean_mobile" id="dean_mobile" dir="ltr" required x-model="form.dean_mobile"
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
                                    <span x-text="isEdit ? 'حفظ التعديلات' : 'إنشاء الكلية'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete Confirmation Modal --}}
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
                                        <p class="text-sm text-(--text-secondary) mt-1">هل أنت متأكد من حذف كلية <span class="font-bold text-red-600 dark:text-red-400" x-text="deleteName"></span>؟</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 items-start p-3 rounded-lg bg-red-50 border border-red-200 dark:bg-red-500/10 dark:border-red-500/20">
                                    <i class="fa-solid fa-circle-info text-red-600 dark:text-red-400 mt-0.5"></i>
                                    <p class="text-xs font-bold leading-relaxed text-red-800 dark:text-red-300">سيتم حذف جميع الأقسام والبرامج المرتبطة بهذه الكلية بشكل نهائي.</p>
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
</div>

@push('scripts')
<script>
    function collegesPage() {
        return {
            showFormModal: false,
            showDeleteModal: false,
            isEdit: false,
            formAction: '{{ route("accreditation_officer.colleges.store") }}',
            deleteAction: '',
            deleteName: '',
            form: { name: '', city_id: '', dean_name: '', dean_email: '', dean_mobile: '', dean_phone: '' },

            openCreate() {
                this.isEdit = false;
                this.formAction = '{{ route("accreditation_officer.colleges.store") }}';
                this.form = { name: '', city_id: '', dean_name: '', dean_email: '', dean_mobile: '', dean_phone: '' };
                this.showFormModal = true;
            },

            openEdit(id, data) {
                this.isEdit = true;
                this.formAction = '{{ url("/accreditation-officer/colleges") }}/' + id;
                this.form = { ...data, city_id: String(data.city_id) };
                this.showFormModal = true;
            },

            openDelete(id, name) {
                this.deleteAction = '{{ url("/accreditation-officer/colleges") }}/' + id;
                this.deleteName = name;
                this.showDeleteModal = true;
            }
        }
    }
</script>
@endpush
@endsection
