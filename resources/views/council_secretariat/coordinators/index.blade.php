@extends('partials.app')

@php
    $breadcrumbs = [
        'الرئيسية' => '/council-secretariat/dashboard',
        'منسقي المجلس' => '/council-secretariat/coordinators',
    ];
@endphp

@section('title', 'منسقي المجلس')
@section('title2', 'إدارة منسقي المجلس')
@section('description', 'عرض وإدارة منسقي المجلس المسجلين في النظام وحالة تفعيل حساباتهم')

@section('content')
<div class="w-full text-start" x-data="coordinatorModal()">

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

    {{-- Toolbar: Search + Add Button --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <form action="{{ route('council_secretariat.coordinators.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            {{-- Search --}}
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 end-3 flex items-center pointer-events-none text-(--text-secondary)">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ابحث باسم المنسق أو بريده..."
                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full pe-10 ps-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                >
            </div>
            <button type="submit" class="hidden"></button>
        </form>

        {{-- Add Coordinator Button --}}
        <button x-on:click="showAddModal = true"
            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus"></i>
            إضافة منسق جديد
        </button>
    </div>

    {{-- Table Card --}}
    <div class="shadow-md rounded-2xl overflow-hidden border border-(--border-primary) bg-(--surface-card)">

        {{-- Card Header --}}
        <div class="border-b border-(--border-primary) bg-(--bg-main) px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex justify-center items-center shadow-inner border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-user-gear text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary) text-lg">قائمة منسقي المجلس</h3>
                    <p class="text-xs md:text-sm text-(--text-secondary) mt-0.5">
                        {{ $coordinators->total() }} منسق مسجل
                    </p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-center text-(--text-secondary)">
                <thead class="text-xs uppercase bg-(--bg-main) text-(--text-secondary)">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider text-start">الاسم</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">البريد الإلكتروني</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الجوال</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الهاتف</th>
                        <th scope="col" class="px-6 py-4 font-bold tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-(--border-primary)">

                    @forelse($coordinators as $coordinator)
                        <tr class="hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 transition-colors duration-200">
                            {{-- Name --}}
                            <td class="px-6 py-4 text-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 shrink-0 rounded-full bg-brand-100 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm border border-brand-200 dark:border-brand-500/20">
                                        <span>{{ mb_substr($coordinator->name, 0, 1) }}</span>
                                    </div>
                                    <span class="font-bold text-(--text-primary) text-[14px]">{{ $coordinator->name }}</span>
                                </div>
                            </td>
                            {{-- Email --}}
                            <td class="px-6 py-4 font-mono text-xs">{{ $coordinator->email }}</td>
                            {{-- Mobile --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ $coordinator->mobile }}</td>
                            {{-- Phone --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ $coordinator->phone ?? '-' }}</td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($coordinator->email_verified_at)
                                    <div class="inline-flex items-center text-emerald-800 bg-emerald-100 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm gap-1.5">
                                        <div class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 shrink-0"></div>
                                        مفعّل نشط
                                    </div>
                                @else
                                    <div class="inline-flex items-center text-orange-800 bg-orange-100 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm gap-1.5">
                                        <div class="h-2 w-2 rounded-full bg-orange-500 dark:bg-orange-400 animate-pulse shrink-0"></div>
                                        بانتظار التفعيل
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-(--text-secondary)">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-(--bg-main) flex items-center justify-center">
                                        <i class="fa-solid fa-users-slash text-2xl text-(--text-secondary)"></i>
                                    </div>
                                    <span class="font-bold text-base">لا يوجد منسقون حالياً</span>
                                    <span class="text-sm">أضف منسقاً جديداً للبدء</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($coordinators->hasPages())
            <div class="border-t border-(--border-primary) px-6 py-4 bg-(--bg-main)">
                {{ $coordinators->links() }}
            </div>
        @endif
    </div>

    <div x-cloak x-show="showAddModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Overlay --}}
        <div x-show="showAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 transition-opacity" 
             x-on:click="showAddModal = false"></div>

        {{-- Modal Content --}}
        <div x-show="showAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="relative bg-(--surface-card) rounded-2xl text-start overflow-hidden shadow-2xl transform transition-all w-full max-w-md border border-(--border-primary) z-10"
             x-on:click.outside="showAddModal = false">
                
                <form action="{{ route('council_secretariat.coordinators.store') }}" method="POST">
                    @csrf
                    <div class="bg-(--surface-card) px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-(--text-primary)" id="modal-title">إضافة منسق مجلس جديد</h3>
                            <button type="button" x-on:click="showAddModal = false" class="text-(--text-secondary) hover:text-(--text-primary) transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-bold text-(--text-primary) mb-1.5">الاسم الكامل</label>
                                <input type="text" name="name" id="name" required
                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-bold text-(--text-primary) mb-1.5">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" required
                                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Mobile --}}
                                <div>
                                    <label for="mobile" class="block text-sm font-bold text-(--text-primary) mb-1.5">رقم الجوال</label>
                                    <input type="text" name="mobile" id="mobile" required
                                        class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label for="phone" class="block text-sm font-bold text-(--text-primary) mb-1.5">رقم الهاتف (اختياري)</label>
                                    <input type="text" name="phone" id="phone"
                                        class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl block w-full px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-(--bg-main) px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit"
                            class="inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-brand-600 text-base font-bold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:text-sm transition-all">
                            إنشاء الحساب
                        </button>
                        <button type="button" x-on:click="showAddModal = false"
                            class="inline-flex justify-center rounded-xl border border-(--border-primary) shadow-sm px-6 py-2.5 bg-(--surface-card) text-base font-bold text-(--text-secondary) hover:bg-(--border-primary)/40 focus:outline-none focus:ring-2 focus:ring-brand-500 sm:text-sm transition-all">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function coordinatorModal() {
    return {
        showAddModal: {{ $errors->any() ? 'true' : 'false' }},
    };
}
</script>
@endpush
