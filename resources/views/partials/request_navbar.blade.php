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
            <span class="hidden sm:inline">لوحة التحكم</span>
        </a>

        {{-- Divider --}}
        <div class="h-6 w-px bg-(--border-primary) hidden sm:block"></div>

        {{-- Program & Request Info --}}
        @if($program)
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-brand-500/10 flex items-center justify-center shrink-0 hidden xs:flex">
                    <i class="fa-solid fa-graduation-cap text-brand-600 dark:text-brand-400 text-xs"></i>
                </div>
                <div class="leading-tight hidden sm:block">
                    <p class="text-xs font-black text-(--text-primary)">
                        {{ $program->program_name }}
                        @php
                            $universityName = $program->department?->college?->university?->name;
                        @endphp
                        <span class="hidden md:inline text-(--text-secondary) font-bold"> - {{ $universityName ?? 'الجامعة' }}</span>
                    </p>
                    <p class="text-[10px] font-bold text-(--text-secondary) mt-0.5">طلب اعتماد #{{ $accreditationRequest->id }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-1 md:gap-3">
        {{-- Dark Mode Toggle (Consistent with main navbar) --}}
        <button class="icon-btn cursor-pointer transition-transform flex items-center justify-center" onclick="toggleDarkMode()">
            <span class="icon-[material-symbols--dark-mode-outline] text-2xl dark:hidden text-(--text-secondary) hover:text-brand-500"></span>
            <span class="icon-[material-symbols--light-mode] text-2xl hidden dark:block text-yellow-400"></span>
        </button>

        {{-- Notifications Toggle --}}
        <div x-data="notifications">
            <button class="icon-btn relative cursor-pointer group flex items-center justify-center transition-transform active:scale-90" 
                @click="toggleNotifications(); clearUnreadCount()">
                <span class="icon-[material-symbols--notifications-outline-rounded] text-2xl text-(--text-secondary) group-hover:text-brand-500 transition-colors"></span>
                
                <template x-if="unreadCount > 0">
                    <div class="absolute -top-1.5 -right-1.5">
                        <span class="relative flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white ring-2 ring-(--surface-card) shadow-md z-10"
                            x-text="unreadCount">
                        </span>
                        <span class="absolute top-0 right-0 h-5 w-5 animate-ping rounded-full bg-red-500 opacity-30"></span>
                    </div>
                </template>
            </button>
        </div>

        <div class="h-8 w-px bg-(--border-primary) mx-1 md:mx-2 hidden sm:block"></div>

        {{-- User Profile Dropdown (Full logic from main navbar) --}}
        <div class="relative" x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center gap-2 md:gap-3 ps-1 md:ps-2 cursor-pointer group select-none">
                <img alt="User Avatar"
                    class="w-10 h-10 rounded-full border border-(--border-primary) group-hover:ring-2 group-hover:ring-brand-500 transition-all object-cover hidden md:block"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGh7rFwN55QNCjBAZAGeF01alWo4529CIUd6J9gS5U0RzgPvekX_SHZvWUA2jL0Duwln1PO1RkD8RTVVoZ8HRYsGLI77pdq3jKj1i8GXdis_vQUu4duewWBflrCR-bM-WqKeUXQ3WOf1u-Fw6ZzI2K9TXh7rfxmV5jmQhfkrYfe3istf0V9R6p_S2JsBFhuyFm-jrCkJwAr1abKHTPxtn8nU5Quz-ksXvPKwAzTExTArwUs-BQ_9zUJsI5mFJ7d5zpax2V4Y6cwLEo" />
                
                <div class="flex items-center gap-1.5">
                    <div class="text-start hidden md:block">
                        <p class="text-sm font-black leading-none text-(--text-primary)">{{ $user?->name }}</p>
                        <p class="text-[11px] font-bold text-(--text-secondary) mt-1 uppercase tracking-wider">{{ $user?->role }}</p>
                    </div>
                    <i class="fa-solid fa-angle-down text-(--text-secondary) text-[10px] md:text-xs transition-transform duration-300 group-hover:text-brand-500" :class="open ? 'rotate-180' : ''"></i>
                </div>
            </div>

            {{-- Dropdown Menu --}}
            <div x-show="open" @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute left-0 mt-2 w-48 bg-(--surface-card) border border-(--border-primary) rounded-xl shadow-xl z-50 overflow-hidden py-1"
                style="display: none;">
                
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-(--text-primary) hover:bg-(--bg-main) transition-colors">
                    <i class="fa-solid fa-gear text-(--text-secondary)"></i>
                    الإعدادات
                </a>

                <div class="h-px bg-(--border-primary) my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-start cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
