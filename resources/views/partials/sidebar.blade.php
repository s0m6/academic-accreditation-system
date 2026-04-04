<aside
    class="sidebar-transition fixed inset-y-0 inset-inline-start-0 z-50 w-(--sidebar-width) border-e border-(--border-primary) shadow-sm flex flex-col"
    style="background-color: var(--bg-sidebar);" id="sidebar">
    {{-- HEADER: Using justify-center allows the logo to center itself perfectly when the text hides --}}
    <div class="h-(--navbar-height) flex items-center justify-center border-b shrink-0 overflow-hidden"
        style="border-color: var(--border-sidebar);">
        <div class="flex items-center w-full px-6 transition-all duration-300">
            {{-- Logo: Fixed size, will center automatically as sister elements hide --}}
            <div class="w-12 h-12 shrink-0 flex items-center justify-center transition-all duration-300">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="max-w-full max-h-full object-contain dark:brightness-110 dark:contrast-125 transition-all">
            </div>
            {{-- Identity: whitespace-nowrap prevents the text from deforming or shrinking during transitions --}}
            <div class="font-bold text-[15px] leading-tight sidebar-text whitespace-nowrap overflow-hidden transition-all duration-300 ms-3"
                style="color: var(--text-primary);">
                <span class="block">مجلس الاعتماد الأكاديمي</span>
                <span class="block text-[11px] opacity-90">وضمان جودة التعليم العالي</span>
            </div>
        </div>
    </div>
    <button aria-label="Close Sidebar"
        class="p-1 rounded-md hover:bg-gray-100 dark:hover:bg-slate-700 md:hidden cursor-pointer"
        onclick="toggleSidebar()">
        <i class="fa-solid fa-xmark text-(--text-secondary) text-xl"></i>
    </button>

    <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto no-scrollbar">
        {{-- !! sidebar council-secretariat --}}
        @if (auth()->user()->role == 'council_secretariat')
            <!-- Category: General -->
            <div class="sidebar-category mt-2 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">الرئيسية</span>
            </div>

            <!-- Dashboard / الرئيسية -->
            <a href="{{ route('council_secretariat.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('council_secretariat.dashboard') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-house"></i>
                </div>
                <span class="sidebar-text font-semibold">الرئيسية</span>
            </a>

            <!-- Universities / الجامعات -->
            <a href="{{ route('council_secretariat.universities') }}"
                class="sidebar-link {{ request()->routeIs('council_secretariat.universities') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-university"></i>
                </div>
                <span class="sidebar-text font-semibold">الجامعات</span>
            </a>

            <!-- Category: Accreditation / الاعتماد -->
            <div class="sidebar-category mt-8 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">الاعتماد الأكاديمي</span>
            </div>

            <!-- Accreditation Requests / طلبات الاعتماد -->
            <div x-data="{ open: {{ request()->routeIs('council_secretariat.requests.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                    class="sidebar-link w-full text-start group cursor-pointer transition-all">
                    <div class="sidebar-icon-wrapper">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div class="flex-1 flex items-center justify-between">
                        <span class="sidebar-text font-semibold">طلبات الاعتماد</span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 sidebar-text" :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="ps-10 space-y-1">
                    <a href="{{ route('council_secretariat.requests.stage_one') }}" 
                        class="flex items-center gap-2 py-2 text-sm font-medium transition-all hover:text-orange-600 dark:hover:text-orange-400 {{ request()->routeIs('council_secretariat.requests.stage_one') ? 'text-orange-600 dark:text-orange-400 font-bold translate-x-1' : 'text-(--text-secondary)' }}">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[12px] font-black dark:bg-orange-500/20 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">1</span>
                        الطلب الأولي
                    </a>

                    <a href="{{ route('council_secretariat.requests.stage_two') }}" 
                        class="flex items-center gap-2 py-2 text-sm font-medium transition-all hover:text-orange-600 dark:hover:text-orange-400 {{ request()->routeIs('council_secretariat.requests.stage_two') ? 'text-orange-600 dark:text-orange-400 font-bold translate-x-1' : 'text-(--text-secondary)' }}">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[12px] font-black dark:bg-orange-500/20 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">2</span>
                        البيانات الأساسية
                    </a>

                    <a href="{{ route('council_secretariat.requests.stage_three') }}" 
                        class="flex items-center gap-2 py-2 text-sm font-medium transition-all hover:text-orange-600 dark:hover:text-orange-400 {{ request()->routeIs('council_secretariat.requests.stage_three') ? 'text-orange-600 dark:text-orange-400 font-bold translate-x-1' : 'text-(--text-secondary)' }}">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-orange-100 text-orange-700 text-[12px] font-black dark:bg-orange-500/20 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">3</span>
                        تقرير الدراسة الذاتيه
                    </a>
                </div>
            </div>
        @endif
        {{-- !! sidebar council-secretariat --}}
        @if (auth()->user()->role == 'accreditation_officer')
            <!-- Category: General -->
            <div class="sidebar-category mt-2 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">الرئيسية</span>
            </div>

            <!-- Dashboard / الرئيسية -->
            <a href="{{ route('accreditation_officer.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('accreditation_officer.dashboard') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-house"></i>
                </div>
                <span class="sidebar-text font-semibold">الرئيسية</span>
            </a>

            <!-- Category: University Management -->
            <div class="sidebar-category mt-8 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">إدارة الجامعة</span>
            </div>

            <!-- Colleges / الكليات -->
            <a href="{{ route('accreditation_officer.colleges') }}"
                class="sidebar-link {{ request()->routeIs('accreditation_officer.colleges') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <span class="sidebar-text font-semibold">الكليات</span>
            </a>

            <!-- Departments / الأقسام -->
            <a href="{{ route('accreditation_officer.departments') }}"
                class="sidebar-link {{ request()->routeIs('accreditation_officer.departments') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <span class="sidebar-text font-semibold">الأقسام</span>
            </a>

            <!-- Programs / البرامج -->
            <a href="{{ route('accreditation_officer.programs') }}"
                class="sidebar-link {{ request()->routeIs('accreditation_officer.programs') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <span class="sidebar-text font-semibold">البرامج</span>
            </a>
        @endif

        @if (auth()->user()->role == 'program_coordinator')
            <!-- Category: General -->
            <div class="sidebar-category mt-2 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">الرئيسية</span>
            </div>

            <!-- Dashboard / الرئيسية -->
            <a href="{{ route('program_coordinator.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('program_coordinator.dashboard') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
                <span class="sidebar-text font-semibold">الرئيسية</span>
            </a>

            <!-- Accreditation Requests / طلبات الاعتماد -->
            <a href="{{ route('program_coordinator.requests') }}"
                class="sidebar-link {{ request()->routeIs('program_coordinator.requests') ? 'sidebar-link-active' : '' }} group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <span class="sidebar-text font-semibold">طلبات الاعتماد</span>
            </a>
        @endif

    </nav>



    <div class="p-4 border-t border-(--border-sidebar) shrink-0 flex flex-col gap-2">
        <!-- Settings -->
        <a href="{{ route('profile.edit') }}" class="sidebar-link group">
            <div class="sidebar-icon-wrapper">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span class="sidebar-text font-semibold">الإعدادات</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link group w-full text-start text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30 cursor-pointer">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
                <span class="sidebar-text font-semibold">تسجيل الخروج</span>

            </button>
        </form>
    </div>
</aside>