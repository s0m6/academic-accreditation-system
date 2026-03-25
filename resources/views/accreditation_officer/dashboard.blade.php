@extends('partials.app')

@php
   $breadcrumbs = [
        'الصفحة الرئيسية' => '/accreditation-officer/dashboard'
    ];
@endphp

@section('title', 'تحليلات النظام')
@section('title2', 'عنوان رئيسي')
@section('description', 'وصف رئيسي')
@section('user_name', auth()->user()->name)
@section('user_role', auth()->user()->role)
@section('content')
    <div class="w-20 h-20 bg-brand-500/10 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
        <i class="fa-solid fa-chart-pie text-4xl"></i>
    </div>

    <h2 class="text-lg font-bold text-(--text-secondary) mb-2">محتوى الصفحة الفارغ</h2>
    <p class="text-sm text-(--text-secondary) max-w-sm">ابدأ بإضافة الأقسام والبيانات هنا للبدء في عرض
        التحليلات.</p>
@endsection
