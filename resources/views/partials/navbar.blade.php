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
            {{-- Dark Mode Toggle (MD3 Style) --}}
            <button class="icon-btn cursor-pointer transition-transform flex items-center justify-center" onclick="toggleDarkMode()">
                <span class="icon-[material-symbols--dark-mode-outline] text-2xl dark:hidden text-(--text-secondary) hover:text-brand-500"></span>
                <span class="icon-[material-symbols--light-mode] text-2xl hidden dark:block text-yellow-400"></span>
            </button>

            {{-- Notifications Toggle --}}
            <div x-data="notifications">
                <button class="icon-btn relative cursor-pointer group flex items-center justify-center transition-transform active:scale-90" 
                    @click="toggleNotifications(); clearUnreadCount()">
                    <span class="icon-[material-symbols--notifications-outline-rounded] text-2xl text-(--text-secondary) group-hover:text-brand-500 transition-colors"></span>
                    
                    {{-- Dynamic Red Notification Badge --}}
                    <template x-if="unreadCount > 0">
                        <div class="absolute -top-1.5 -right-1.5">
                            <span class="relative flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-[13px] font-black text-white ring-2 ring-(--surface-card) shadow-md shadow-red-500/20 z-10"
                                x-text="unreadCount">
                            </span>
                            <span class="absolute top-0 right-0 h-6 w-6 animate-ping rounded-full bg-red-500 opacity-30"></span>
                        </div>
                    </template>
                </button>
            </div>

            <div class="h-8 w-px bg-(--border-primary) mx-2 hidden sm:block"></div>

            {{-- User Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 ps-2 cursor-pointer group select-none">
                    <img alt="User Avatar"
                        class="w-10 h-10 rounded-full border border-(--border-primary) group-hover:ring-2 group-hover:ring-brand-500 transition-all object-cover hidden md:block"
                        src="{{ asset('images/avatar.svg') }}" />
                    
                    <div class="flex items-center gap-2">
                        <div class="text-start hidden md:block">
                            <p class="text-sm font-black leading-none text-(--text-primary)">{{ Auth::user()->name }}</p>
                            @php
                                $roles = [
                                    'council_secretariat' => 'أمانة المجلس',
                                    'accreditation_officer' => 'ضابط الاعتماد',
                                    'program_coordinator' => 'منسق البرنامج',
                                    'council_coordinator' => 'منسق المجلس',
                                    'evaluator' => 'مقيم',
                                ];
                            @endphp
                            <p class="text-[11px] font-bold text-(--text-secondary) mt-1 uppercase tracking-wider">{{ $roles[Auth::user()->role] ?? Auth::user()->role }}</p>
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