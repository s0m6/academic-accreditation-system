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

            {{-- User Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 ps-2 cursor-pointer group select-none">
                    <img alt="User Avatar"
                        class="w-10 h-10 rounded-full border border-(--border-primary) group-hover:ring-2 group-hover:ring-brand-500 transition-all object-cover"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGh7rFwN55QNCjBAZAGeF01alWo4529CIUd6J9gS5U0RzgPvekX_SHZvWUA2jL0Duwln1PO1RkD8RTVVoZ8HRYsGLI77pdq3jKj1i8GXdis_vQUu4duewWBflrCR-bM-WqKeUXQ3WOf1u-Fw6ZzI2K9TXh7rfxmV5jmQhfkrYfe3istf0V9R6p_S2JsBFhuyFm-jrCkJwAr1abKHTPxtn8nU5Quz-ksXvPKwAzTExTArwUs-BQ_9zUJsI5mFJ7d5zpax2V4Y6cwLEo" />
                    
                    <div class="flex items-center gap-2">
                        <div class="text-start hidden md:block">
                            <p class="text-sm font-black leading-none text-(--text-primary)">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] font-bold text-(--text-secondary) mt-1 uppercase tracking-wider">{{ Auth::user()->role }}</p>
                        </div>
                        
                        {{-- Font Awesome Chevron Icon --}}
                        <i class="fa-solid fa-angle-down text-(--text-secondary) text-xs transition-transform duration-300 group-hover:text-brand-500" :class="open ? 'rotate-180' : ''"></i>
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