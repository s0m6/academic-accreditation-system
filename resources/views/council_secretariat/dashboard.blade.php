@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/council-secretariat/dashboard'
    ];
@endphp

@section('title', 'الصفحة الرئيسية')
@section('title2', 'الصفحة الرئيسية')
@section('description', 'الصفحة الرئيسية')
@section('content')
    <div class="w-20 h-20 bg-brand-500/10 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
        <i class="fa-solid fa-chart-pie text-4xl"></i>
    </div>

    <h2 class="text-lg font-bold text-(--text-secondary) mb-2">محتوى الصفحة الفارغ</h2>
    <p class="text-sm text-(--text-secondary) max-w-sm">ابدأ بإضافة الأقسام والبيانات هنا للبدء في عرض
        التحليلات.</p>
@endsection
