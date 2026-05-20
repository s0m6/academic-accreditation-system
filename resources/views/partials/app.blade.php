<!DOCTYPE html>
<html class="scroll-smooth rtl" dir="rtl" lang="ar">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'blank page')</title>



    {{-- Vite-compiled assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Local Fonts --}}
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}" />

    @stack('styles')

    <script>
        window.userId = @json(auth()->id());
    </script>
</head>

<body class="bg-(--bg-main) text-(--text-primary) overflow-x-hidden min-h-screen">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    {{-- NAVBAR --}}
    @include('partials.navbar')

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- NOTIFICATIONS --}}
    @include('partials.notifications')

    {{-- MAIN CONTENT --}}
    <main class="pro-layout-content flex-1 p-4 lg:p-10 transition-all duration-300" id="main-content">
        {{-- 4. BREADCRUMBS (Expert Data-Driven Logic) --}}
        @include('partials.breadcrumbs', ['items' => $breadcrumbs ?? []])

        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-bold">@yield('title2', 'عنوان رئيسي')</h1>
            <p class="text-sm text-(--text-secondary)">@yield('description', 'وصف رئيسي')</p>
        </div>

        <div
            class=" min-h-100 text-center">
            @yield('content')
        </div>
    </main>

    {{-- OVERLAYS --}}
    <div class="fixed inset-0 bg-black/50 z-40 hidden md:hidden cursor-pointer" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <div class="fixed inset-0 bg-black/40 dark:bg-black/60 z-50 hidden transition-opacity cursor-pointer" id="notifications-overlay"
        onclick="toggleNotifications()"></div>

    @stack('scripts')
</body>

</html>
