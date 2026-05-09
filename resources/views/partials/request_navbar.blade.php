{{-- Request Dashboard Navbar --}}
@php
    $user = request()->user();
    $dashboardRoute = match($user?->role) {
        'accreditation_officer' => route('accreditation_officer.dashboard'),
        'council_secretariat'   => route('council_secretariat.dashboard'),
        default                 => '/dashboard',
    };
    $program = $accreditationRequest?->program;
@endphp

<header
    class="pro-layout-header fixed top-0 inset-inline-0 h-(--navbar-height) bg-(--surface-card) border-b border-(--border-primary) z-40 flex items-center justify-between px-4 lg:px-8"
    id="top-navbar">

    <div class="flex items-center gap-3">
        {{-- Sidebar Toggle --}}
        <button aria-label="Toggle Sidebar"
            class="icon-btn rounded-lg cursor-pointer transition-transform hover:scale-110 active:scale-95"
            onclick="toggleSidebar()">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>

        {{-- Back to Dashboard --}}
        <a href="{{ $dashboardRoute }}"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) hover:text-(--text-primary) hover:border-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 dark:hover:text-(--text-primary) transition-all shadow-sm">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            <span class="hidden sm:inline">الداشبورد</span>
        </a>

        {{-- Divider --}}
        <div class="h-6 w-px bg-(--border-primary) hidden sm:block"></div>

        {{-- Program & Request Info --}}
        @if($program)
            <div class="hidden sm:flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-500/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-scroll text-orange-600 dark:text-orange-400 text-xs"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-bold text-(--text-primary) truncate max-w-[200px]">{{ $program->program_name }}</p>
                    <p class="text-[10px] text-(--text-secondary)">طلب رقم #{{ $accreditationRequest->id }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2 md:gap-3">
        {{-- Dark Mode Toggle --}}
        <button class="icon-btn cursor-pointer transition-transform" onclick="toggleDarkMode()" style="color: #f59e0b;">
            <i id="theme-toggle-icon" class="fa-solid fa-moon text-lg"></i>
        </button>

        {{-- Notifications Toggle --}}
        <div x-data="notifications">
            <button class="icon-btn relative cursor-pointer group flex items-center justify-center transition-transform active:scale-90" 
                @click="toggleNotifications(); clearUnreadCount()">
                <i class="fa-solid fa-bell text-lg text-(--text-secondary) group-hover:text-orange-500 transition-colors"></i>
                
                <template x-if="unreadCount > 0">
                    <div class="absolute -top-1.5 -right-1.5">
                        <span class="relative flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-(--surface-card) shadow-sm z-10"
                            x-text="unreadCount">
                        </span>
                        <span class="absolute top-0 right-0 h-5 w-5 animate-ping rounded-full bg-red-500 opacity-30"></span>
                    </div>
                </template>
            </button>
        </div>

        <div class="h-8 w-px bg-(--border-primary) mx-2 hidden sm:block"></div>

        {{-- User Profile --}}
        <div class="flex items-center gap-3 ps-2 cursor-pointer group">
            <div class="text-start hidden md:block">
                <p class="text-sm font-bold leading-none">{{ $user?->name }}</p>
                <p class="text-[13px] text-(--text-secondary) mt-1">{{ $user?->role }}</p>
            </div>
            <img alt="User Avatar"
                class="w-9 h-9 rounded-full border border-(--border-primary) group-hover:ring-2 group-hover:ring-orange-400 transition-all"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGh7rFwN55QNCjBAZAGeF01alWo4529CIUd6J9gS5U0RzgPvekX_SHZvWUA2jL0Duwln1PO1RkD8RTVVoZ8HRYsGLI77pdq3jKj1i8GXdis_vQUu4duewWBflrCR-bM-WqKeUXQ3WOf1u-Fw6ZzI2K9TXh7rfxmV5jmQhfkrYfe3istf0V9R6p_S2JsBFhuyFm-jrCkJwAr1abKHTPxtn8nU5Quz-ksXvPKwAzTExTArwUs-BQ_9zUJsI5mFJ7d5zpax2V4Y6cwLEo" />
        </div>
    </div>
</header>
