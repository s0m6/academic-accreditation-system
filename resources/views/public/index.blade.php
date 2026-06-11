@extends('public.layout')

@section('title', 'CAAQAHE YEMEN - المجلس الوطني للاعتماد الأكاديمي')

@push('styles')
<style>
    .perspective-1000 {
        perspective: 1000px;
    }

    .rotate-y-12 {
        transform: rotateY(-12deg);
    }

    .institutional-shadow {
        box-shadow: 0 20px 50px rgba(0, 37, 70, 0.15);
    }
</style>
@endpush

@section('content')
    @include('public.partials.hero')
    @include('public.partials.about')
    @include('public.partials.services')
    @include('public.partials.certificates')
    @include('public.partials.partners')
    @include('public.partials.news')
@endsection
