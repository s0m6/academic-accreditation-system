<!DOCTYPE html>
<html class="scroll-smooth" dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'CAAQAHE YEMEN')</title>
    {{-- Vite-compiled assets (Tailwind v4 + Alpine.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Local Fonts (Cairo & Material Symbols Outlined) --}}
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}" />
    <style>
        :root {
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-primary: #e2e8f0;
            --surface-card: #ffffff;
            --bg-main: #f8fafc;
        }
        .dark {
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-primary: #1e293b;
            --surface-card: #0f172a;
            --bg-main: #020617;
        }

        html:not(.dark) .dark-only {
            display: none !important;
        }
        html.dark .light-only {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[var(--bg-main)] text-[var(--text-primary)] transition-colors duration-300">
    <!-- Top Navigation -->
    <header
        class="bg-white/90 dark:bg-[#020617]/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <nav class="flex justify-between items-center max-w-7xl mx-auto px-6 h-20">
            <!-- Brand Logo -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary dark:bg-white/10 rounded-lg flex items-center justify-center text-accent">
                    <i class="fa-solid fa-building-columns text-3xl"></i>
                </div>
                <div class="flex flex-col">
                    <span
                        class="text-xl font-black tracking-tight text-primary dark:text-white uppercase leading-none">CAAQAHE
                        Yemen</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-300 mt-1">مجلس الاعتماد الأكاديمي
                        وضمان الجودة</span>
                </div>
            </a>
            <!-- Nav Links -->
            <ul class="hidden lg:flex items-center gap-8 text-sm font-bold">
                <li><a class="{{ request()->routeIs('welcome') ? 'text-primary dark:text-accent border-b-2 border-primary dark:border-accent pb-1' : 'text-slate-600 dark:text-slate-200 hover:text-primary dark:hover:text-accent transition-colors' }}" href="{{ route('welcome') }}">الرئيسية</a></li>
                <li><a class="{{ request()->routeIs('certificates.explorer') ? 'text-primary dark:text-accent border-b-2 border-primary dark:border-accent pb-1' : 'text-slate-600 dark:text-slate-200 hover:text-primary dark:hover:text-accent transition-colors' }}" href="{{ route('certificates.explorer') }}">الشهادات المعتمدة</a></li>
                <li><a class="text-slate-600 dark:text-slate-200 hover:text-primary dark:hover:text-accent transition-colors" href="#">عن المجلس</a></li>
                <li><a class="text-slate-600 dark:text-slate-200 hover:text-primary dark:hover:text-accent transition-colors" href="#">المعايير</a></li>
                <li><a class="text-slate-600 dark:text-slate-200 hover:text-primary dark:hover:text-accent transition-colors" href="#">اتصل بنا</a></li>
            </ul>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                @auth
                <a href="{{ route('dashboard') }}"
                    class="bg-primary dark:bg-white/10 text-accent dark:text-white px-6 py-2.5 rounded-xl font-extrabold text-sm hover:bg-primary-dark dark:hover:bg-white/20 hover:scale-105 transition-all shadow-md">
                    لوحة التحكم
                </a>
                @else
                <a href="{{ route('login') }}"
                    class="hidden sm:flex items-center gap-2 px-5 py-2.5 text-primary dark:text-slate-100 font-bold text-sm hover:bg-slate-100 dark:hover:bg-white/10 rounded-xl transition-all">
                    <i class="fa-solid fa-right-to-bracket text-lg"></i>
                    تسجيل الدخول
                </a>
                @endauth
                <div class="h-8 w-[1px] bg-slate-200 dark:bg-white/10 mx-2"></div>
                <button
                    class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-accent hover:ring-2 ring-accent/30 transition-all cursor-pointer"
                    onclick="const isDark = document.documentElement.classList.toggle('dark'); document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light'); localStorage.setItem('theme', isDark ? 'dark' : 'light');">
                    <i class="fa-solid fa-moon light-only"></i>
                    <i class="fa-solid fa-sun dark-only"></i>
                </button>
            </div>
        </nav>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-primary dark:bg-slate-950 text-white relative border-t-[6px] border-accent">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 max-w-7xl mx-auto px-8 py-20">
            <!-- Brand & Info -->
            <div class="col-span-1">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-accent rounded flex items-center justify-center text-primary">
                        <i class="fa-solid fa-building-columns text-2xl"></i>
                    </div>
                    <span class="text-xl font-black uppercase tracking-tighter">CAAQAHE YEMEN</span>
                </div>
                <p class="text-blue-100/60 text-sm leading-loose mb-8 font-medium">
                    الجهة الوطنية المسؤولة عن منح الاعتماد الأكاديمي وضمان جودة مؤسسات التعليم العالي في الجمهورية
                    اليمنية.
                </p>
            </div>
            <!-- Links 1 -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">روابط سريعة</h4>
                <ul class="space-y-5 text-sm font-bold text-blue-100/70">
                    <li><a class="hover:text-accent transition-colors" href="#">الرؤية والرسالة</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">الأهداف الاستراتيجية</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">القوانين واللوائح</a></li>
                </ul>
            </div>
            <!-- Links 2 -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">البوابة الإلكترونية</h4>
                <ul class="space-y-5 text-sm font-bold text-blue-100/70">
                    <li><a class="hover:text-accent transition-colors" href="{{ route('login') }}">دخول المؤسسات</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">تقديم طلب اعتماد</a></li>
                    <li><a class="hover:text-accent transition-colors" href="{{ route('certificates.explorer') }}">الشهادات المعتمدة</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">تواصل معنا</h4>
                <ul class="space-y-6 text-sm text-blue-100/70 font-medium">
                    <li class="flex items-start gap-4">
                        <i class="fa-solid fa-location-dot text-accent mt-1"></i>
                        <span class="leading-relaxed">عدن، مديرية خور مكسر، مبنى وزارة التعليم العالي والبحث العلمي</span>
                    </li>
                    <li class="flex items-start gap-4">
                        <i class="fa-solid fa-phone text-accent mt-1"></i>
                        +967 2 123456
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/5 py-10 bg-black/20">
            <div
                class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-6 text-xs font-bold text-blue-100/40">
                <p>جميع الحقوق محفوظة {{ date('Y') }}</p>
                <div class="flex gap-8">
                    <a class="hover:text-accent transition-colors" href="#">سياسة الخصوصية</a>
                    <a class="hover:text-accent transition-colors" href="#">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
