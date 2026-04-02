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

            <!-- Category: Management -->
            <div class="sidebar-category mt-8 mb-1 px-3">
                <span class="sidebar-text text-[14px] font-bold uppercase"
                    style="color: var(--text-primary);">الإدارة</span>
            </div>

            <!-- Users / المستخدمين -->
            <a href="#" class="sidebar-link group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span class="sidebar-text font-semibold">المستخدمين</span>
            </a>

            <!-- Orders / الطلبات -->
            <a href="#" class="sidebar-link group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <span class="sidebar-text font-semibold">الطلبات</span>
            </a>

            <!-- Invoices / الفواتير -->
            <a href="#" class="sidebar-link group">
                <div class="sidebar-icon-wrapper">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <span class="sidebar-text font-semibold">الفواتير</span>
            </a>
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