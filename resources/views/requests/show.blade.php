@extends('partials.request_layout')

@php
    $stageNames = [
        'stage_one'   => 'طلب الاعتماد الأولي',
        'stage_two'   => 'البيانات الأساسية',
        'stage_three' => 'تقرير الدراسة الذاتية',
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
