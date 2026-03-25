<header
        class="pro-layout-header fixed top-0 inset-inline-0 h-(--navbar-height) bg-(--surface-card) border-b border-(--border-primary) z-40 flex items-center justify-between px-4 lg:px-8"
        id="top-navbar">

        <div class="flex items-center gap-4">
            {{-- Sidebar Toggle --}}
            <button aria-label="Toggle Sidebar"
                class="icon-btn rounded-lg cursor-pointer transition-transform hover:scale-110 active:scale-95"
                onclick="toggleSidebar()">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        <div class="flex items-center gap-2 md:gap-3">
            {{-- Dark Mode Toggle --}}
            <button class="icon-btn cursor-pointer transition-transform" onclick="toggleDarkMode()" style="color: #f59e0b;">
                <i id="theme-toggle-icon" class="fa-solid fa-moon text-lg"></i>
            </button>

            {{-- Notifications Toggle --}}
            <button class="icon-btn relative cursor-pointer" onclick="toggleNotifications()">
                <span
                    class="absolute top-2 inset-inline-end-2 w-2 h-2 bg-red-500 rounded-full border-2 border-(--surface-card)"></span>
                <i class="fa-solid fa-bell text-lg"></i>
            </button>

            <div class="h-8 w-px bg-(--border-primary) mx-2 hidden sm:block"></div>

            {{-- User Profile --}}
            <div class="flex items-center gap-3 ps-2 cursor-pointer group">
                <div class="text-start hidden md:block">
                    <p class="text-sm font-bold leading-none">@yield('user_name', 'اسم المستخدم')</p>
                    <p class="text-[13px] text-(--text-secondary) mt-1">@yield('user_role', 'الوظيفة')</p>
                </div>
                <img alt="User Avatar"
                    class="w-9 h-9 rounded-full border border-(--border-primary) group-hover:ring-2 group-hover:ring-brand-500 transition-all"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGh7rFwN55QNCjBAZAGeF01alWo4529CIUd6J9gS5U0RzgPvekX_SHZvWUA2jL0Duwln1PO1RkD8RTVVoZ8HRYsGLI77pdq3jKj1i8GXdis_vQUu4duewWBflrCR-bM-WqKeUXQ3WOf1u-Fw6ZzI2K9TXh7rfxmV5jmQhfkrYfe3istf0V9R6p_S2JsBFhuyFm-jrCkJwAr1abKHTPxtn8nU5Quz-ksXvPKwAzTExTArwUs-BQ_9zUJsI5mFJ7d5zpax2V4Y6cwLEo" />
            </div>
        </div>
    </header>