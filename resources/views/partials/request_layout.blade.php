<!DOCTYPE html>
<html class="scroll-smooth rtl" dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'لوحة طلب الاعتماد')</title>



    {{-- Font Awesome 6.4.0 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    {{-- Vite-compiled assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-(--bg-main) text-(--text-primary) overflow-x-hidden min-h-screen">

    {{-- REQUEST NAVBAR --}}
    @include('partials.request_navbar', [
        'accreditationRequest' => $accreditationRequest ?? null,
    ])

    {{-- REQUEST SIDEBAR (Timeline) --}}
    @include('partials.request_sidebar', [
        'accreditationRequest' => $accreditationRequest ?? null,
        'stages' => $stages ?? [],
        'activeStage' => $activeStage ?? null,
    ])

    {{-- NOTIFICATIONS --}}
    @include('partials.notifications')

    {{-- MAIN CONTENT --}}
    <main class="pro-layout-content flex-1 p-4 lg:p-10 transition-all duration-300" id="main-content">
        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-bold">@yield('title2', 'لوحة الطلب')</h1>
            <p class="text-sm text-(--text-secondary)">@yield('description', '')</p>
        </div>

        <div class=" min-h-100  text-center">
            @yield('content')
        </div>
    </main>

    {{-- OVERLAYS --}}
    <div class="fixed inset-0 bg-black/50 z-40 hidden md:hidden cursor-pointer" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <div class="fixed inset-0 bg-black/40 z-50 hidden transition-opacity cursor-pointer" id="notifications-overlay"
        onclick="toggleNotifications()"></div>

    @stack('scripts')
</body>

</html>
