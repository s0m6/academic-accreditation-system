@extends('partials.app')

@php
   $breadcrumbs = [
        'الصفحة الرئيسية' => '/program-coordinator/dashboard'
    ];
@endphp

@section('title', 'تحليلات النظام')
@section('title2', 'لوحة تحكم منسق البرنامج')
@section('description', 'مرحباً بك في نظام الاعتماد الأكاديمي. يمكنك إدارة طلبات الاعتماد الخاصة بك هنا.')
@section('content')
    <div class="w-20 h-20 bg-brand-500/10 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 mb-4">
        <i class="fa-solid fa-chart-pie text-4xl"></i>
    </div>

    <h2 class="text-lg font-bold text-(--text-secondary) mb-2">محتوى الصفحة الفارغ</h2>
    <p class="text-sm text-(--text-secondary) max-w-sm">ابدأ بمتابعة طلبات الاعتماد الأكاديمي المرتبطة بك من خلال القائمة الجانبية.</p>
@endsection
