@extends('partials.app')

@section('title', 'لوحة تحكم المقيم')
@section('title2', 'لوحة التحكم')
@section('description', 'مرحباً بك في نظام الاعتماد الأكاديمي - لوحة تحكم المقيم')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- Statistics Card --}}
    <div class="bg-(--bg-component) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-file-signature text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">طلبات التقييم</h3>
                <p class="text-sm text-(--text-secondary)">الطلبات الموكلة إليك</p>
            </div>
        </div>
        <div class="text-3xl font-black text-blue-600 dark:text-blue-400">0</div>
    </div>

    {{-- Performance Card --}}
    <div class="bg-(--bg-component) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fa-solid fa-check-double text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">التقييمات المكتملة</h3>
                <p class="text-sm text-(--text-secondary)">إجمالي التقييمات التي تمت</p>
            </div>
        </div>
        <div class="text-3xl font-black text-green-600 dark:text-green-400">0</div>
    </div>

    {{-- Schedule Card --}}
    <div class="bg-(--bg-component) p-6 rounded-2xl border border-(--border-primary) shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fa-solid fa-calendar-day text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">المواعيد القادمة</h3>
                <p class="text-sm text-(--text-secondary)">الزيارات والجلسات المجدولة</p>
            </div>
        </div>
        <div class="text-3xl font-black text-purple-600 dark:text-purple-400">0</div>
    </div>
</div>

{{-- Content Area --}}
<div class="mt-10 bg-(--bg-component) rounded-2xl border border-(--border-primary) shadow-sm p-8 text-center">
    <div class="max-w-md mx-auto py-10">
        <div class="w-20 h-20 bg-orange-100 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center rounded-full mx-auto mb-6">
            <i class="fa-solid fa-hourglass-half text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">لا توجد مهام حالية</h2>
        <p class="text-(--text-secondary) mb-8">سيتم عرض طلبات التقييم والمهام الموكلة إليك هنا بمجرد تعيينها لك من قبل أمانة المجلس.</p>
        <button class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-bold transition-all">تحديث القائمة</button>
    </div>
</div>
@endsection
