{{-- Request Dashboard Timeline Sidebar --}}
@php
    $stageOrder = array_keys($stages);
    $currentStageIndex = array_search($accreditationRequest?->current_stage, $stageOrder);

    /**
     * Determine visual state of each stage step.
     * done     → green  (completed stage, past the current one)
     * active   → orange (current stage)
     * upcoming → slate  (future stages)
     */
    function stageState(int $index, int $currentIndex): string
    {
        if ($index < $currentIndex) {
            return 'done';
        }
        if ($index === $currentIndex) {
            return 'active';
        }
        return 'upcoming';
    }
@endphp

<aside
    class="sidebar-transition fixed inset-y-0 inset-inline-start-0 z-50 w-(--sidebar-width) border-e border-(--border-primary) shadow-sm flex flex-col"
    style="background-color: var(--bg-sidebar);" id="sidebar">

    {{-- HEADER --}}
    <div class="h-(--navbar-height) flex items-center justify-center border-b shrink-0 overflow-hidden"
        style="border-color: var(--border-sidebar);">
        <div class="flex items-center w-full px-6 transition-all duration-300">
            <div class="w-12 h-12 shrink-0 flex items-center justify-center transition-all duration-300">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="max-w-full max-h-full object-contain dark:brightness-110 dark:contrast-125 transition-all">
            </div>
            <div class="font-bold text-[15px] leading-tight sidebar-text whitespace-nowrap overflow-hidden transition-all duration-300 ms-3"
                style="color: var(--text-primary);">
                <span class="block">مجلس الاعتماد الأكاديمي</span>
                <span class="block text-[11px] opacity-90">لوحة إدارة الطلب</span>
            </div>
        </div>
    </div>
    <button aria-label="Close Sidebar"
        class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-slate-700 md:hidden cursor-pointer"
        onclick="toggleSidebar()">
        <i class="fa-solid fa-xmark text-(--text-secondary) text-xl"></i>
    </button>

    {{-- TIMELINE NAV --}}
    <nav class="flex-1 px-4 py-6 overflow-y-auto no-scrollbar">

        {{-- Section Label --}}
        <div class="sidebar-category mb-4 px-2">
            <span class="sidebar-text text-[12px] font-bold uppercase tracking-widest"
                style="color: var(--text-secondary);">مراحل الاعتماد</span>
        </div>

        {{-- Timeline --}}
        <div class="relative">
            @foreach ($stages as $stageKey => $stageName)
                @php
                    $index = array_search($stageKey, $stageOrder);
                    $state = stageState($index, $currentStageIndex);
                    $isActive = $activeStage === $stageKey;
                    $stageUrl = route('requests.stage', ['accreditationRequest' => $accreditationRequest->id, 'stage' => $stageKey]);
                    $isLast = $index === count($stageOrder) - 1;
                @endphp

                <div class="relative flex gap-4 {{ $isLast ? '' : 'pb-6' }}">

                    {{-- Vertical connector line (not on last item) --}}
                    @if (!$isLast)
                        <div class="absolute start-[19px] top-10 bottom-0 w-0.5 transition-colors duration-500
                            {{ $state === 'done' ? 'bg-green-400 dark:bg-green-500' : 'bg-(--border-primary)' }}">
                        </div>
                    @endif

                    {{-- Step circle --}}
                    <div class="shrink-0 relative z-10">
                        @if ($state === 'done')
                            {{-- Done: filled green circle with checkmark --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-md
                                bg-green-500 border-2 border-green-400 dark:bg-green-500 dark:border-green-400
                                ring-2 ring-green-200 dark:ring-green-500/30">
                                <i class="fa-solid fa-check text-white text-sm"></i>
                            </div>
                        @elseif ($state === 'active')
                            {{-- Active: orange pulsing circle --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg
                                bg-orange-500 border-2 border-orange-400
                                ring-4 ring-orange-200 dark:ring-orange-500/30
                                transition-all duration-300">
                                <span class="text-white text-sm font-black">{{ $index + 1 }}</span>
                            </div>
                        @else
                            {{-- Upcoming: muted grey circle --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                bg-(--bg-main) border-2 border-(--border-primary) shadow-sm">
                                <span class="text-(--text-secondary) text-sm font-bold">{{ $index + 1 }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Stage label + link --}}
                    <a href="{{ $stageUrl }}"
                        class="flex-1 flex items-center min-h-10 rounded-xl px-3 py-2 transition-all duration-200 group cursor-pointer
                            {{ $isActive
                                ? 'bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 shadow-sm'
                                : 'hover:bg-(--border-primary)/30 dark:hover:bg-(--bg-main)/50 border border-transparent' }}">
                        <div class="sidebar-text">
                            <p class="text-sm font-bold leading-tight
                                {{ $state === 'done'
                                    ? 'text-green-700 dark:text-green-400'
                                    : ($state === 'active'
                                        ? 'text-orange-700 dark:text-orange-400'
                                        : 'text-(--text-secondary)') }}">
                                {{ $stageName }}
                            </p>
                            <p class="text-[10px] mt-0.5
                                {{ $state === 'done'
                                    ? 'text-green-500 dark:text-green-500/80'
                                    : ($state === 'active'
                                        ? 'text-orange-500 dark:text-orange-500/80'
                                        : 'text-(--text-secondary) opacity-60') }}">
                                {{ $state === 'done' ? 'مكتملة' : ($state === 'active' ? 'الحالية' : 'قادمة') }}
                            </p>
                        </div>
                    </a>

                </div>
            @endforeach
        </div>

    </nav>

    {{-- FOOTER --}}
    <div class="p-4 border-t border-(--border-sidebar) shrink-0 flex flex-col gap-2">
        {{-- Settings --}}
        <a href="{{ route('profile.edit') }}" class="sidebar-link group">
            <div class="sidebar-icon-wrapper">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span class="sidebar-text font-semibold">الإعدادات</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="sidebar-link group w-full text-start text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30 cursor-pointer">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
                <span class="sidebar-text font-semibold">تسجيل الخروج</span>
            </button>
        </form>
    </div>

</aside>
