@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-secretariat/dashboard',
        'المقيمون' => '/council-secretariat/evaluators',
    ];
@endphp

@section('title', 'المقيمون')
@section('title2', 'إدارة المقيمين الأكاديميين')
@section('description', 'عرض وإدارة المقيمين المسجلين في النظام وحالة تفعيل حساباتهم')

@section('content')
<div class="w-full text-start" x-data="evaluatorsIndex()" x-init="fetchEvaluators()">

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

    {{-- Toolbar: Search + City Filter + Add Button --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            {{-- Search --}}
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 end-3 flex items-center pointer-events-none text-(--text-secondary)">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.350ms="fetchEvaluators()"
                    placeholder="ابحث باسم المقيم..."
                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full pe-10 ps-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                >
            </div>

            {{-- City Filter --}}
            <div class="relative">
                <select
                    x-model="cityId"
                    @change="fetchEvaluators()"
                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none pe-8 ps-4 cursor-pointer"
                >
                    <option value="">كل المدن</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 end-2.5 flex items-center pointer-events-none text-(--text-secondary)">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </span>
            </div>
        </div>

        {{-- Add Evaluator Button — opens in a new tab --}}
        <a href="{{ route('council_secretariat.evaluators.create') }}"
            
            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus"></i>
            إضافة مقيم جديد
        </a>
    </div>

    {{-- Table Card --}}
    <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">

        {{-- Card Header --}}
        <div class="border-b border-(--border-primary) bg-(--bg-main) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-user-tie text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة المقيمين الأكاديميين</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">
                        <span x-text="total"></span> مقيم مسجل
                    </p>
                </div>
            </div>

            {{-- Loading spinner --}}
            <div x-show="loading" class="flex items-center gap-2 text-(--text-secondary) text-sm">
                <i class="fa-solid fa-circle-notch animate-spin"></i>
                <span>جارٍ التحديث...</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-start">الاسم</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">البريد الإلكتروني</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">المدينة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الدرجة العلمية</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">

                    {{-- No results row (Alpine) --}}
                    <template x-if="!loading && evaluators.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center text-(--text-secondary)">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-user-slash text-2xl text-(--text-secondary)"></i>
                                    </div>
                                    <span class="font-bold text-base">لا يوجد مقيمون مطابقون</span>
                                    <span class="text-sm">جرّب تغيير معايير البحث أو أضف مقيماً جديداً</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Evaluator rows (Alpine) --}}
                    <template x-for="ev in evaluators" :key="ev.id">
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            {{-- Name --}}
                            <td class="px-6 py-4 text-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 shrink-0 rounded-full bg-brand-100 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm border border-brand-200 dark:border-brand-500/20">
                                        <span x-text="ev.name.charAt(0)"></span>
                                    </div>
                                    <span class="font-bold text-(--text-primary) text-[14px]" x-text="ev.name"></span>
                                </div>
                            </td>
                            {{-- Email --}}
                            <td class="px-6 py-4 font-mono text-xs" x-text="ev.email"></td>
                            {{-- City --}}
                            <td class="px-6 py-4 whitespace-nowrap" x-text="ev.city"></td>
                            {{-- Academic Rank --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold leading-none bg-violet-100 text-violet-800 border border-violet-200 dark:bg-violet-500/10 dark:text-violet-400 dark:border-violet-500/20 shadow-sm"
                                      x-text="ev.academic_rank"></span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <template x-if="ev.verified">
                                    <div class="inline-flex items-center text-emerald-800 bg-emerald-100 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm gap-1.5">
                                        <div class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 shrink-0"></div>
                                        مفعّل نشط
                                    </div>
                                </template>
                                <template x-if="!ev.verified">
                                    <div class="inline-flex items-center text-orange-800 bg-orange-100 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm gap-1.5">
                                        <div class="h-2 w-2 rounded-full bg-orange-500 dark:bg-orange-400 animate-pulse shrink-0"></div>
                                        بانتظار التفعيل
                                    </div>
                                </template>
                            </td>
                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a :href="'/council-secretariat/evaluators/' + ev.id"
                                        target="_blank"
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow text-sky-700 bg-sky-100 border border-sky-200 hover:bg-sky-200 dark:text-sky-400 dark:bg-sky-500/10 dark:border-sky-500/20 dark:hover:bg-sky-500/20"
                                        title="عرض ملف المقيم">
                                        <i class="fa-solid fa-eye text-[14px]"></i>
                                    </a>
                                    <a :href="'/council-secretariat/evaluators/' + ev.id + '/edit'"
                                        
                                        class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center transition-all shadow-sm hover:shadow text-emerald-700 bg-emerald-100 border border-emerald-200 hover:bg-emerald-200 dark:text-emerald-400 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:hover:bg-emerald-500/20"
                                        title="تعديل بيانات المقيم">
                                        <i class="fa-solid fa-pen-to-square text-[14px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-(--border-primary) px-6 py-4 flex items-center justify-between bg-(--bg-main)" x-show="lastPage > 1">
            <span class="text-sm text-(--text-secondary)">
                صفحة <span x-text="currentPage"></span> من <span x-text="lastPage"></span>
            </span>
            <div class="flex gap-2">
                <button
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage <= 1"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border border-(--border-primary) text-(--text-secondary) hover:bg-(--border-primary)/40 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                    السابق
                </button>
                <button
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage >= lastPage"
                    class="px-4 py-2 rounded-lg text-sm font-semibold border border-(--border-primary) text-(--text-secondary) hover:bg-(--border-primary)/40 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                    التالي
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function evaluatorsIndex() {
    return {
        evaluators: [],
        total: 0,
        currentPage: 1,
        lastPage: 1,
        search: '',
        cityId: '',
        loading: false,

        async fetchEvaluators(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.search,
                    city_id: this.cityId,
                    page: page,
                });
                const response = await axios.get('{{ route('council_secretariat.evaluators.search') }}?' + params.toString());
                const data = response.data;
                this.evaluators   = data.evaluators;
                this.total        = data.total;
                this.currentPage  = data.current_page;
                this.lastPage     = data.last_page;
            } catch (error) {
                console.error('Error fetching evaluators:', error);
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.fetchEvaluators(page);
            }
        },
    };
}
</script>
@endpush
