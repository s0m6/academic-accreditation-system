{{-- Request Dashboard Navbar --}}
@php
    $user = request()->user();
    $dashboardRoute = match($user?->role) {
        'accreditation_officer' => route('accreditation_officer.dashboard'),
        'council_secretariat'   => route('council_secretariat.dashboard'),
        default                 => '/dashboard',
    };
    $program = $accreditationRequest?->program;

    // Gather contacts for the modal
    $contactProgramCoord  = $accreditationRequest?->programCoordinator;
    $contactCouncilCoord  = $accreditationRequest?->councilCoordinator;
    $contactCommittee     = $accreditationRequest?->committee;
    $contactChairId       = $contactCommittee?->chair_evaluator_id;
    $contactEvaluators    = $contactCommittee?->activeMembers ?? collect();
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

    <div class="flex items-center gap-1 md:gap-3" x-data="{ showContactsModal: false }">
        {{-- Dark Mode Toggle (Consistent with main navbar) --}}
        <button class="icon-btn cursor-pointer transition-transform flex items-center justify-center" onclick="toggleDarkMode()">
            <span class="icon-[material-symbols--dark-mode-outline] text-2xl dark:hidden text-(--text-secondary) hover:text-brand-500"></span>
            <span class="icon-[material-symbols--light-mode] text-2xl hidden dark:block text-yellow-400"></span>
        </button>

        {{-- Contacts Modal Trigger --}}
        <button
            id="btn-request-contacts"
            class="icon-btn cursor-pointer transition-transform flex items-center justify-center relative group"
            @click="showContactsModal = true"
            title="أعضاء الطلب"
        >
            <i class="fa-solid fa-users text-xl text-(--text-secondary) group-hover:text-brand-500 transition-colors"></i>
        </button>

        {{-- ═══════════════ CONTACTS MODAL ═══════════════ --}}
        <template x-teleport="body">
            <div
                x-show="showContactsModal"
                style="display:none"
                class="relative z-[300]"
                role="dialog"
                aria-modal="true"
                aria-labelledby="contacts-modal-title"
            >
                {{-- Backdrop --}}
                <div
                    x-show="showContactsModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                    @click="showContactsModal = false"
                ></div>

                {{-- Modal Panel --}}
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div
                            x-show="showContactsModal"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click.away="showContactsModal = false"
                            class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl overflow-hidden text-start"
                        >
                            {{-- Header --}}
                            <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-500/10 flex items-center justify-center border border-brand-500/20 shrink-0">
                                        <i class="fa-solid fa-users text-brand-600 dark:text-brand-400"></i>
                                    </div>
                                    <div>
                                        <h3 id="contacts-modal-title" class="font-bold text-(--text-primary)">أعضاء طلب الاعتماد</h3>
                                        <p class="text-xs text-(--text-secondary) mt-0.5">المسؤولون المرتبطون بهذا الطلب</p>
                                    </div>
                                </div>
                                <button
                                    @click="showContactsModal = false"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-(--text-secondary) hover:text-(--text-primary) hover:bg-(--bg-main) transition-colors cursor-pointer"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                                {{-- Program Coordinator --}}
                                <div class="rounded-xl border border-(--border-primary) overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-blue-50 dark:bg-blue-500/10 border-b border-(--border-primary) flex items-center gap-2">
                                        <i class="fa-solid fa-chalkboard-teacher text-blue-600 dark:text-blue-400 text-sm"></i>
                                        <span class="text-xs font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider">منسق البرنامج</span>
                                    </div>
                                    <div class="px-4 py-3 bg-(--surface-card)">
                                        @if($contactProgramCoord)
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-500/20">
                                                    <i class="fa-solid fa-user text-blue-600 dark:text-blue-400 text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-(--text-primary) truncate">{{ $contactProgramCoord->name }}</p>
                                                    <a href="mailto:{{ $contactProgramCoord->email }}" class="text-xs text-brand-500 hover:text-brand-600 transition-colors truncate block">
                                                        {{ $contactProgramCoord->email }}
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-(--text-secondary) italic">لم يُحدَّد بعد</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Council Coordinator --}}
                                <div class="rounded-xl border border-(--border-primary) overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-violet-50 dark:bg-violet-500/10 border-b border-(--border-primary) flex items-center gap-2">
                                        <i class="fa-solid fa-landmark text-violet-600 dark:text-violet-400 text-sm"></i>
                                        <span class="text-xs font-black text-violet-700 dark:text-violet-300 uppercase tracking-wider">منسق المجلس</span>
                                    </div>
                                    <div class="px-4 py-3 bg-(--surface-card)">
                                        @if($contactCouncilCoord)
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-violet-100 dark:bg-violet-500/10 flex items-center justify-center shrink-0 border border-violet-200 dark:border-violet-500/20">
                                                    <i class="fa-solid fa-user text-violet-600 dark:text-violet-400 text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-(--text-primary) truncate">{{ $contactCouncilCoord->name }}</p>
                                                    <a href="mailto:{{ $contactCouncilCoord->email }}" class="text-xs text-brand-500 hover:text-brand-600 transition-colors truncate block">
                                                        {{ $contactCouncilCoord->email }}
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-(--text-secondary) italic">لم يُعيَّن منسق مجلس بعد</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Evaluation Committee --}}
                                <div class="rounded-xl border border-(--border-primary) overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 border-b border-(--border-primary) flex items-center gap-2">
                                        <i class="fa-solid fa-magnifying-glass-chart text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">لجنة التقييم</span>
                                    </div>
                                    <div class="divide-y divide-(--border-primary)">
                                        @if($contactEvaluators->isNotEmpty())
                                            @foreach($contactEvaluators as $member)
                                                @php
                                                    $evalUser     = $member->evaluator?->user;
                                                    $isChair      = $contactChairId && $member->evaluator_id === $contactChairId;
                                                @endphp
                                                @if($evalUser)
                                                    <div class="px-4 py-3 bg-(--surface-card) flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border {{ $isChair ? 'bg-amber-100 dark:bg-amber-500/10 border-amber-300 dark:border-amber-500/30' : 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20' }}">
                                                            <i class="fa-solid {{ $isChair ? 'fa-star text-amber-500' : 'fa-user text-emerald-600 dark:text-emerald-400' }} text-xs"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <p class="text-sm font-bold text-(--text-primary) truncate">{{ $evalUser->name }}</p>
                                                                @if($isChair)
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 shrink-0">
                                                                        <i class="fa-solid fa-crown text-[8px]"></i> رئيس اللجنة
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 shrink-0">
                                                                        عضو
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <a href="mailto:{{ $evalUser->email }}" class="text-xs text-brand-500 hover:text-brand-600 transition-colors truncate block">
                                                                {{ $evalUser->email }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="px-4 py-4 bg-(--surface-card)">
                                                <p class="text-xs text-(--text-secondary) italic">لم يتم تشكيل لجنة التقييم بعد</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>{{-- end body --}}

                            {{-- Footer --}}
                            <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end">
                                <button
                                    @click="showContactsModal = false"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-black shadow-md shadow-orange-500/20 transition-all cursor-pointer"
                                >
                                    إغلاق
                                </button>
                            </div>

                        </div>{{-- end panel --}}
                    </div>
                </div>
            </div>
        </template>{{-- end teleport --}}

        {{-- Notifications Toggle --}}
        <div x-data="notifications">
            <button class="icon-btn relative cursor-pointer group flex items-center justify-center transition-transform active:scale-90" 
                @click="toggleNotifications(); clearUnreadCount()">
                <span class="icon-[material-symbols--notifications-outline-rounded] text-2xl text-(--text-secondary) group-hover:text-brand-500 transition-colors"></span>
                
                <template x-if="unreadCount > 0">
                    <div class="absolute -top-1.5 -right-1.5">
                        <span class="relative flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[13px] font-black text-white ring-2 ring-(--surface-card) shadow-md z-10"
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
