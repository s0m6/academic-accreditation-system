<!DOCTYPE html>
<html class="scroll-smooth" dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'CAAQAHE YEMEN')</title>
   {{-- Vite-compiled assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#002546",
                        "primary-dark": "#001a33",
                        "accent": "#e9c176",
                        "accent-dark": "#c39f58",
                        "surface": "#f7fafc",
                        "surface-dark": "#020617"
                    },
                    fontFamily: {
                        "sans": ["Cairo", "sans-serif"],
                        "headline": ["Cairo", "sans-serif"],
                        "body": ["Cairo", "sans-serif"]
                    },
                    borderRadius: {
                        "xl": "0.75rem",
                        "2xl": "1.5rem"
                    }
                }
            }
        }
    </script>
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
        body {
            font-family: 'Cairo', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-surface dark:bg-surface-dark text-slate-900 dark:text-blue-50 transition-colors duration-300">
    <!-- Top Navigation -->
    <header
        class="bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 shadow-sm">
        <nav class="flex justify-between items-center max-w-7xl mx-auto px-6 h-20">
            <!-- Brand Logo -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-accent">
                    <span class="material-symbols-outlined text-3xl" data-icon="account_balance">account_balance</span>
                </div>
                <div class="flex flex-col">
                    <span
                        class="text-xl font-black tracking-tight text-primary dark:text-white uppercase leading-none">CAAQAHE
                        Yemen</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1">مجلس الاعتماد الأكاديمي
                        وضمان الجودة</span>
                </div>
            </a>
            <!-- Nav Links -->
            <ul class="hidden lg:flex items-center gap-8 text-sm font-bold">
                <li><a class="{{ request()->routeIs('welcome') ? 'text-primary dark:text-accent border-b-2 border-accent pb-1' : 'text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors' }}" href="{{ route('welcome') }}">الرئيسية</a></li>
                <li><a class="{{ request()->routeIs('certificates.explorer') ? 'text-primary dark:text-accent border-b-2 border-accent pb-1' : 'text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors' }}" href="{{ route('certificates.explorer') }}">الشهادات المعتمدة</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors" href="#">عن المجلس</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors" href="#">المعايير</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors" href="#">اتصل بنا</a></li>
            </ul>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                @auth
                <a href="{{ route('dashboard') }}"
                    class="bg-primary text-accent px-6 py-2.5 rounded-xl font-extrabold text-sm hover:bg-primary-dark hover:scale-105 transition-all shadow-lg shadow-primary/20">
                    لوحة التحكم
                </a>
                @else
                <a href="{{ route('login') }}"
                    class="hidden sm:flex items-center gap-2 px-5 py-2.5 text-primary dark:text-white font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-lg" data-icon="login">login</span>
                    تسجيل الدخول
                </a>
                @endauth
                <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-800 mx-2"></div>
                <button
                    class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-accent hover:ring-2 ring-accent/30 transition-all"
                    onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-symbols-outlined dark:hidden" data-icon="dark_mode">dark_mode</span>
                    <span class="material-symbols-outlined hidden dark:block" data-icon="light_mode">light_mode</span>
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
                        <span class="material-symbols-outlined text-2xl"
                            data-icon="account_balance">account_balance</span>
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
                        <span class="material-symbols-outlined text-accent" data-icon="location_on">location_on</span>
                        <span class="leading-relaxed">عدن، مديرية خور مكسر، مبنى وزارة التعليم العالي والبحث العلمي</span>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-accent" data-icon="phone">phone</span>
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
