<!DOCTYPE html>

<html class="scroll-smooth" dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>CAAQAHE YEMEN</title>
   {{-- Vite-compiled assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
        body {
            font-family: 'Cairo', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }

        .perspective-1000 {
            perspective: 1000px;
        }

        .rotate-y-12 {
            transform: rotateY(-12deg);
        }

        .institutional-shadow {
            box-shadow: 0 20px 50px rgba(0, 37, 70, 0.15);
        }
    </style>
</head>

<body class="bg-surface dark:bg-surface-dark text-slate-900 dark:text-blue-50 transition-colors duration-300">
    <!-- Top Navigation Shell -->
    <header
        class="bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 shadow-sm">
        <nav class="flex justify-between items-center max-w-7xl mx-auto px-6 h-20">
            <!-- Brand Logo -->
            <div class="flex items-center gap-4">
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
            </div>
            <!-- Nav Links -->
            <ul class="hidden lg:flex items-center gap-8 text-sm font-bold">
                <li><a class="text-primary dark:text-accent border-b-2 border-accent pb-1" href="#">الرئيسية</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="#">عن المجلس</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="#">المعايير</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="#">الخدمات</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="#">الأخبار</a></li>
                <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-white transition-colors"
                        href="#">اتصل بنا</a></li>
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
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-24 pb-32 bg-primary dark:bg-[#010a14]">
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-900/40 via-transparent to-transparent">
        </div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-right">
                <div
                    class="inline-flex items-center gap-3 px-5 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 mb-8">
                    <span class="w-2.5 h-2.5 rounded-full bg-accent animate-pulse shadow-[0_0_10px_#e9c176]"></span>
                    <span class="text-white/90 text-xs font-bold tracking-wide">الارتقاء بجودة التعليم العالي في
                        اليمن</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-white leading-[1.15] mb-8">
                    نصون <span class="text-accent">المعايير</span><br />لنبني المستقبل
                </h1>
                <p class="text-blue-100/70 text-lg md:text-xl leading-relaxed max-w-xl mb-10 font-medium">
                    نعمل في مجلس الاعتماد الأكاديمي وضمان الجودة على تمكين المؤسسات التعليمية من تحقيق التميز العالمي من
                    خلال معايير اعتماد صارمة وعمليات تقييم شفافة.
                </p>
                <div class="flex flex-wrap gap-5">
                    <button
                        class="bg-accent text-primary px-10 py-4 rounded-xl font-black text-lg hover:scale-105 transition-all shadow-xl shadow-accent/20 flex items-center gap-3">
                        <span class="material-symbols-outlined" data-icon="verified">verified</span>
                        دليل المؤسسات المعتمدة
                    </button>
                    <button
                        class="bg-white/5 hover:bg-white/10 text-white px-10 py-4 rounded-xl font-bold text-lg backdrop-blur-md border border-white/20 transition-all">
                        عن المجلس
                    </button>
                </div>
            </div>
            <!-- Visual Element -->
            <div class="relative hidden lg:block perspective-1000">
                <div class="relative w-full aspect-square max-w-lg mx-auto rotate-y-12">
                    <div
                        class="absolute inset-0 rounded-3xl bg-accent/20 border border-white/10 backdrop-blur-sm transform translate-x-10 translate-y-10">
                    </div>
                    <div class="absolute inset-0 rounded-3xl overflow-hidden shadow-2xl border border-white/20">
                        <img class="w-full h-full object-cover grayscale-[10%] brightness-90 hover:grayscale-0 transition-all duration-700"
                            data-alt="grand library interior"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBRRQOiJYS_iPtDZ7MYljCdT20mpAUe1chIIl20ZgfxWIm4cAsijurHMgjsJ46BrcmEjJ2R8oH4HPowjiA_H0-cQQWbr7OFjj17JaRALaLkqw3ArrpKxNCbBWMHnDsJmU9Cg9zwwG38LhDnIBCyM2owYoYr3Mw4Cq6H_qEW-_YvG9xtfOtw2Anqrvr-usWj8y8TtvzbE8et3YGNxMpT7fdbTwCHbFXLiXTOQ542WgIrw92Sgstn0LNCs1Iagb57lVEAToB8EeqX3QEH" />
                        <div class="absolute inset-0 bg-primary/20 mix-blend-multiply"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Quick Services Section -->
    <section class="py-20 -mt-12 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div
                    class="group p-10 bg-white dark:bg-slate-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800 hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-blue-50 dark:bg-primary/50 rounded-2xl flex items-center justify-center mb-6 text-primary dark:text-accent group-hover:bg-primary group-hover:text-accent transition-all">
                        <span class="material-symbols-outlined text-4xl"
                            data-icon="account_balance">account_balance</span>
                    </div>
                    <h3 class="font-extrabold text-xl mb-3 dark:text-white">اعتماد مؤسسي</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">تقييم شامل لأداء
                        الجامعات والكليات وفق أرقى المعايير الدولية.</p>
                </div>
                <div
                    class="group p-10 bg-white dark:bg-slate-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800 hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-blue-50 dark:bg-primary/50 rounded-2xl flex items-center justify-center mb-6 text-primary dark:text-accent group-hover:bg-primary group-hover:text-accent transition-all">
                        <span class="material-symbols-outlined text-4xl" data-icon="school">school</span>
                    </div>
                    <h3 class="font-extrabold text-xl mb-3 dark:text-white">اعتماد برامجي</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">ضمان جودة المناهج
                        الأكاديمية ومواءمتها مع متطلبات سوق العمل.</p>
                </div>
                <div
                    class="group p-10 bg-white dark:bg-slate-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800 hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-blue-50 dark:bg-primary/50 rounded-2xl flex items-center justify-center mb-6 text-primary dark:text-accent group-hover:bg-primary group-hover:text-accent transition-all">
                        <span class="material-symbols-outlined text-4xl" data-icon="laptop_mac">laptop_mac</span>
                    </div>
                    <h3 class="font-extrabold text-xl mb-3 dark:text-white">بوابة المؤسسات</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">منصة إلكترونية
                        متكاملة لإدارة عمليات الاعتماد والتقييم الذاتي.</p>
                </div>
                <div
                    class="group p-10 bg-white dark:bg-slate-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800 hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-blue-50 dark:bg-primary/50 rounded-2xl flex items-center justify-center mb-6 text-primary dark:text-accent group-hover:bg-primary group-hover:text-accent transition-all">
                        <span class="material-symbols-outlined text-4xl" data-icon="description">description</span>
                    </div>
                    <h3 class="font-extrabold text-xl mb-3 dark:text-white">المعايير الوطنية</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">دليل المواصفات
                        الأكاديمية المعتمدة لضمان جودة المخرجات.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Accreditation Certificates -->
    <section class="py-24 bg-slate-50 dark:bg-slate-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-accent/5 blur-[150px] rounded-full"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="mb-20 flex flex-col md:flex-row justify-between items-end gap-6 border-r-4 border-accent pr-8">
                <div>
                    <h2 class="text-4xl font-black mb-4 dark:text-white">شهادات الاعتماد الصادرة</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-lg font-medium">المؤسسات والبرامج الأكاديمية التي
                        حققت التميز الوطني</p>
                </div>
                <div
                    class="text-8xl font-black text-slate-200 dark:text-slate-800/20 select-none hidden lg:block tracking-tighter">
                    CERTIFIED</div>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <!-- Card 1 -->
                <div class="group relative">
                    <div
                        class="absolute inset-0 bg-accent rounded-3xl transform group-hover:rotate-3 transition-transform duration-500">
                    </div>
                    <div
                        class="relative bg-white dark:bg-slate-900 p-10 rounded-3xl shadow-xl flex flex-col items-center text-center border border-slate-100 dark:border-slate-800">
                        <div
                            class="w-24 h-24 mb-8 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center relative overflow-hidden">
                            <span class="material-symbols-outlined text-accent text-6xl relative z-10"
                                data-icon="workspace_premium"
                                style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        </div>
                        <h4 class="font-black text-2xl mb-3 dark:text-white">الاعتماد المؤسسي الكامل</h4>
                        <p class="text-slate-400 dark:text-slate-500 mb-8 font-bold">جامعة عدن</p>
                        <div class="w-full h-px bg-slate-100 dark:bg-slate-800 mb-8"></div>
                        <div class="flex items-center gap-2 text-sm font-black text-accent uppercase tracking-widest">
                            <span class="material-symbols-outlined text-lg"
                                data-icon="calendar_today">calendar_today</span>
                            صالح لغاية 2028
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="group relative">
                    <div
                        class="absolute inset-0 bg-primary rounded-3xl transform group-hover:rotate-3 transition-transform duration-500">
                    </div>
                    <div
                        class="relative bg-white dark:bg-slate-900 p-10 rounded-3xl shadow-xl flex flex-col items-center text-center border border-slate-100 dark:border-slate-800">
                        <div
                            class="w-24 h-24 mb-8 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-accent text-6xl" data-icon="medical_services"
                                style="font-variation-settings: 'FILL' 1;">medical_services</span>
                        </div>
                        <h4 class="font-black text-2xl mb-3 dark:text-white">برنامج الطب البشري</h4>
                        <p class="text-slate-400 dark:text-slate-500 mb-8 font-bold">جامعة عدن</p>
                        <div class="w-full h-px bg-slate-100 dark:bg-slate-800 mb-8"></div>
                        <div class="flex items-center gap-2 text-sm font-black text-accent uppercase tracking-widest">
                            <span class="material-symbols-outlined text-lg"
                                data-icon="calendar_today">calendar_today</span>
                            صالح لغاية 2027
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="group relative">
                    <div
                        class="absolute inset-0 bg-accent rounded-3xl transform group-hover:rotate-3 transition-transform duration-500">
                    </div>
                    <div
                        class="relative bg-white dark:bg-slate-900 p-10 rounded-3xl shadow-xl flex flex-col items-center text-center border border-slate-100 dark:border-slate-800">
                        <div
                            class="w-24 h-24 mb-8 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-accent text-6xl" data-icon="engineering"
                                style="font-variation-settings: 'FILL' 1;">engineering</span>
                        </div>
                        <h4 class="font-black text-2xl mb-3 dark:text-white">برامج الهندسة</h4>
                        <p class="text-slate-400 dark:text-slate-500 mb-8 font-bold">جامعة تعز</p>
                        <div class="w-full h-px bg-slate-100 dark:bg-slate-800 mb-8"></div>
                        <div class="flex items-center gap-2 text-sm font-black text-accent uppercase tracking-widest">
                            <span class="material-symbols-outlined text-lg"
                                data-icon="calendar_today">calendar_today</span>
                            صالح لغاية 2026
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- University Partners -->
    <section class="py-20 bg-white dark:bg-slate-950 border-y border-slate-100 dark:border-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <h5
                class="text-center text-slate-400 dark:text-slate-600 font-black uppercase tracking-[0.2em] text-sm mb-16">
                شركاؤنا في الجودة التعليمية</h5>
            <div class="flex flex-wrap justify-center items-center gap-16 md:gap-24">
                <div
                    class="grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 transform hover:scale-110">
                    <img class="h-20 w-auto" data-alt="University Logo"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDT9_yJnqFPmx85CfhLpu70rbYgDH3du5IkCcX2Qz7GZchjTRNHLj7xkD_9vx-ZQ9hTDt9r9w0yHBvyehUrbdTBH3Te9VNDIFNqKD9j3HMzdubTiZglbuiQ8-dEwDhOqFBlbz2Bi3dFrq6TNKP05VtkxKgGf9q0gX48n9extLvh--8dpt5Kuvs_CAVn7mPZQc5TF7Hqo2A6UeRE5iRV3viPZzZXzAnLeUc7Q7ekWOXuwjTnFYs7smfBeu9iwJbIQG66-W7ohYnbSVW5" />
                </div>
                <div
                    class="grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 transform hover:scale-110">
                    <img class="h-20 w-auto" data-alt="University Logo"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHsTrrXUVompOAx54axaGl3f1VpR9WqoNahiIwW9yF5krXbJbLfO6kp_j2bR4rip-YwcoRP5YVQoinholsvh375YhuigDf3Hj9VXC0jYKAbZupJLaxHJoK4CThDKzl1PrJl-s5OyDrYewVmJjuJRSvH_iqbhCrdY9tuqWbqDu3aV1npOo6335tLqxClnUf5wWEHs4RFnc0bf1PxODilN3BFvQidgnS8C3Oh6n_jGjdNbfmhu_6rMev3yrzZAwS_udufUqSXuUHMASU" />
                </div>
                <div
                    class="grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 transform hover:scale-110">
                    <img class="h-20 w-auto" data-alt="University Logo"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCYfPDz6gJhUxjljOdgCeFj7l-n1F-FAYjD3vqt7kLBf_tJGADAvynbA-UHgElatuq3QlNebllrAR003pCOkditpHx5OhlT8Tc9B-JpJ_Elb2Zfdu6CpaZEjzrroz7sOVTAFLClgbWZMPb0Uh4xaMCTD4HxN5w54PbMfG-eM64SGvjRCWbXtFJSWkVkyJA9Cvat47c6tPI5-ky_PC8PY9R4JTkvT_B4FVdzKtVjzTQHfNJ3Wbof71h7doDopXyjbkbPuJGFdjEXPZNb" />
                </div>
                <div
                    class="grayscale hover:grayscale-0 opacity-40 hover:opacity-100 transition-all duration-500 transform hover:scale-110">
                    <img class="h-20 w-auto" data-alt="University Logo"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCkxHzmYmkCPeIiahfbRLT_4YVTer_AuPejUuNsJ30rQhdiu5FYlymgbe6pg-IKgEx3KzHFWOILdkbnDG8IbG3lt0ekkL05hEx0CZj8DLiIpklj-YIIi_Dp4Efaf658bRaTD__GTQQ1IHtEV9DIFNgeZmh2GDZCxGGkJIAdLcvkxRwD6f5TqDvIOHvwOA5Qf57FI6SqVym6d9a3t-OZpPMy47et2j5KbeyFGFfth7CNcVJcI_lOFHhB7DHBDqEikZjy2Qb1c1CLagOc" />
                </div>
            </div>
        </div>
    </section>
    <!-- Latest News Section -->
    <section class="py-24 bg-slate-50 dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-20 gap-4">
                <div class="text-right border-r-4 border-primary dark:border-accent pr-6">
                    <h2 class="text-4xl font-black dark:text-white">آخر المستجدات</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">متابعة مستمرة لأنشطة المجلس وفعالياته
                    </p>
                </div>
                <button
                    class="group flex items-center gap-3 text-primary dark:text-accent font-black text-lg hover:gap-5 transition-all">
                    عرض كافة الأخبار
                    <span class="material-symbols-outlined rtl:rotate-180"
                        data-icon="arrow_forward">arrow_forward</span>
                </button>
            </div>
            <div class="grid lg:grid-cols-3 gap-10">
                <!-- News Item 1 -->
                <article
                    class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-700">
                    <div class="h-64 overflow-hidden relative">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700"
                            data-alt="conference"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuACgXWutfUfWQ-tzpJvRFdILskeuZNlrLa8fJFj1FDTh1QjOumqseQxvvR2e3jVP9PRW0l4sDWGlfeppUihKHrN4nlJ70tKARBIJixyJEImO242byOE7JL_7PyVCHQHe3h-I7ELQx0CueMKsbnDCvvYAjA_o5fDP94p9QI7qISvXm9FQbKH4shBhy3IF1BtDatDeMyxkMZTCBxe3pc7E1i10qEavYTTkebXsbPnbr5x2dCX84CUtr3VC90bFsXFqkY3DA7OHKiGMLCT" />
                        <div
                            class="absolute top-6 right-6 bg-accent text-primary px-4 py-1.5 rounded-lg text-xs font-black shadow-lg">
                            إعلان</div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 text-slate-400 text-sm mb-4 font-bold">
                            <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                            15 يونيو 2024
                        </div>
                        <h3
                            class="font-black text-xl mb-4 leading-snug group-hover:text-primary dark:group-hover:text-accent transition-colors dark:text-white">
                            انطلاق ورشة عمل "تطوير معايير الاعتماد البرامجي للهندسة" بمشاركة خبراء</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">ناقشت الورشة آليات
                            تحديث مخرجات التعلم لتتواكب مع متطلبات سوق العمل العالمي والمحلي...</p>
                    </div>
                </article>
                <!-- News Item 2 -->
                <article
                    class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-700">
                    <div class="h-64 overflow-hidden relative">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700"
                            data-alt="laboratory"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDrBrCqYrsec-g0lFTH3UeMQQE22ya1m-RrDoIEWeEa7UpnzRItm3U__jXs6eVMZVvl5tFP5BR_mwyTsU7ZTVQNvu44zLrAuqDQgIH692_9ovZAKdOBV_Q-qfJhOKZiR5ZB1DRqgpf5teb5vS6oJWoxxCPQgDxn3vzmIAgu1yaheqik65NFCzxCfA5O6PhUHfWFltjwp0X4f69wWk8H6eFst3r-sef5qLldf4dtoRjLrKrTJQSKdmCW6SXy8rKdhT5-1C4Bid-M-Ipu" />
                        <div
                            class="absolute top-6 right-6 bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-black shadow-lg">
                            زيارة ميدانية</div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 text-slate-400 text-sm mb-4 font-bold">
                            <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                            12 يونيو 2024
                        </div>
                        <h3
                            class="font-black text-xl mb-4 leading-snug group-hover:text-primary dark:group-hover:text-accent transition-colors dark:text-white">
                            زيارة تقييمية لكليات العلوم الطبية في جامعة عدن لضمان الجودة</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">قامت لجنة من المجلس
                            بزيارة تفقدية للمختبرات والمعامل لتقييم مدى جاهزيتها للاعتماد البرامجي...</p>
                    </div>
                </article>
                <!-- News Item 3 -->
                <article
                    class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-700">
                    <div class="h-64 overflow-hidden relative">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700"
                            data-alt="certificate signing"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnqyUFQgpk3ZalLzJvAAnxxtmPDg2cPY928CzhzZK18duS9etUP1nFApCyx6FOI2uoCk1Y-__fLaMbpEjX-xPPT-pD2vU0CraupS5JuFZTvalCP_kKGWsNVKrN6OP6NPBbREKj7-C3o_31tYan4aiW687zrgYQltvN63FnwhE8f3VZmUvOaijWvmczOHyqKtK5JW3wXbwyv2968vMGWNxT31DV4QC-b5s8I-iejpwyh1ABE6_Ytquz0cJZhK-ByNWZpGpBoGbc4DVJ" />
                        <div
                            class="absolute top-6 right-6 bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-black shadow-lg">
                            جديد</div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 text-slate-400 text-sm mb-4 font-bold">
                            <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                            08 يونيو 2024
                        </div>
                        <h3
                            class="font-black text-xl mb-4 leading-snug group-hover:text-primary dark:group-hover:text-accent transition-colors dark:text-white">
                            توقيع اتفاقية تعاون مع المنظمة العربية لضمان الجودة في التعليم</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">تهدف الاتفاقية إلى
                            تبادل الخبرات وتدريب المقيمين الأكاديميين وفق المعايير الدولية...</p>
                    </div>
                </article>
            </div>
        </div>
    </section>
    <!-- Footer Shell -->
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
                <div class="flex gap-4">
                    <a class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-accent hover:bg-accent hover:text-primary transition-all duration-300"
                        href="#">
                        <span class="material-symbols-outlined" data-icon="public">public</span>
                    </a>
                    <a class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-accent hover:bg-accent hover:text-primary transition-all duration-300"
                        href="#">
                        <span class="material-symbols-outlined" data-icon="mail">mail</span>
                    </a>
                </div>
            </div>
            <!-- Links 1 -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">روابط سريعة</h4>
                <ul class="space-y-5 text-sm font-bold text-blue-100/70">
                    <li><a class="hover:text-accent transition-colors" href="#">الرؤية والرسالة</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">الأهداف الاستراتيجية</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">القوانين واللوائح</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">الهيكل التنظيمي</a></li>
                </ul>
            </div>
            <!-- Links 2 -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">البوابة الإلكترونية</h4>
                <ul class="space-y-5 text-sm font-bold text-blue-100/70">
                    <li><a class="hover:text-accent transition-colors" href="#">دخول المؤسسات</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">تقديم طلب اعتماد</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">دليل المقيمين</a></li>
                    <li><a class="hover:text-accent transition-colors" href="#">تحميل النماذج</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div>
                <h4 class="font-black text-lg mb-8 text-accent">تواصل معنا</h4>
                <ul class="space-y-6 text-sm text-blue-100/70 font-medium">
                    <li class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-accent" data-icon="location_on">location_on</span>
                        <span class="leading-relaxed">عدن، مديرية خور مكسر، مبنى وزارة التعليم العالي والبحث
                            العلمي</span>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-accent" data-icon="phone">phone</span>
                        +967 2 123456
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-accent"
                            data-icon="alternate_email">alternate_email</span>
                        info@aaqac-yemen.org
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/5 py-10 bg-black/20">
            <div
                class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-6 text-xs font-bold text-blue-100/40">
                <p>مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي </p>
                <p>جميع الحقوق محفوظة 2026</p>
                <div class="flex gap-8">
                    <a class="hover:text-accent transition-colors" href="#">سياسة الخصوصية</a>
                    <a class="hover:text-accent transition-colors" href="#">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>