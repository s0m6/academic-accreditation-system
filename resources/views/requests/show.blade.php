@extends('partials.request_layout')

@php
    $stageNames = [
        'stage_one'   => 'طلب الاعتماد الأولي',
        'stage_two'   => 'البيانات الأساسية',
        'stage_three' => 'تقرير الدراسة الذاتية',
        'stage_four'  => 'اختيار لجنة التقييم',
        'stage_five'  => 'تحديد جدول الزيارة',
        'stage_six'   => 'تقارير نتائج التقييم(الأولية)',
        'stage_seven' => 'توصيات اللجنة والرد عليها',
        'stage_eight' => 'تقارير نتائج التقييم(الختامية)',
    ];
    $currentStageName = $stageNames[$activeStage] ?? $activeStage;
    $program = $accreditationRequest->program;
@endphp

@section('title', 'لوحة الطلب #' . $accreditationRequest->id)
@section('title2', $currentStageName)
@section('description', 'برنامج: ' . $program->program_name . ' — ' . $program->department->college->name)

@section('content')
    @include('requests.stages.' . $activeStage, [
        'accreditationRequest' => $accreditationRequest,
        'program' => $program,
        'user' => request()->user(),
    ])
@endsection
