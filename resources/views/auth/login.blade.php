<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - تسجيل الدخول</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .institutional-pattern {
            background-image: radial-gradient(circle at 2px 2px, rgba(0, 37, 70, 0.03) 1.5px, transparent 0);
            background-size: 40px 40px;
        }
        .dark .institutional-pattern {
            background-image: radial-gradient(circle at 2px 2px, rgba(233, 193, 118, 0.02) 1.5px, transparent 0);
        }
        .premium-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 37, 70, 0.08), 0 0 1px 0 rgba(0, 37, 70, 0.1);
        }
        .dark .premium-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 1px 0 rgba(233, 193, 118, 0.1);
        }
    </style>
</head>
<body class="bg-md-surface dark:bg-slate-950 text-md-on-surface transition-colors duration-300 min-h-screen flex flex-col font-sans">
    <!-- Global Preloader -->
    @include('public.partials.preloader')

    <div class="fixed top-6 left-6 z-50">
        <button class="p-2.5 rounded-xl border border-md-outline-variant/30 bg-md-surface-container-lowest/50 dark:bg-slate-900/50 backdrop-blur-md hover:bg-md-surface-container-high dark:hover:bg-slate-800 transition-colors shadow-sm" onclick="document.documentElement.classList.toggle('dark')">
            <span class="icon-[material-symbols--dark-mode-outline] text-2xl dark:hidden"></span>
            <span class="icon-[material-symbols--light-mode] text-2xl hidden dark:block text-md-tertiary-fixed-dim"></span>
        </button>
    </div>

    <main class="flex-grow flex items-center justify-center lg:p-6 institutional-pattern relative overflow-hidden">
        <!-- Aesthetic Decorative Elements -->
        <div class="absolute -top-48 -right-48 w-[600px] h-[600px] bg-md-primary/5 dark:bg-md-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-48 -left-48 w-[600px] h-[600px] bg-md-tertiary-fixed-dim/5 dark:bg-md-tertiary/10 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Login Card -->
        <div class="w-full max-w-[560px] bg-md-surface-container-lowest dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl premium-shadow border border-md-outline-variant/20 dark:border-white/5 relative z-10 overflow-hidden mx-4">
            <!-- Top Institutional Accent Bar -->
            <div class="h-1.5 w-full bg-gradient-to-l from-md-primary via-md-tertiary to-md-primary"></div>
            
            <div class="p-8 md:p-12">
                <!-- Card Header -->
                <div class="text-center mb-8">
                    <div class="relative inline-block mb-6">
                        <div class="absolute inset-0 bg-md-primary/5 rounded-full blur-2xl scale-150"></div>
                        <img alt="AAQAC Logo" class="w-28 h-28 relative z-10 mx-auto object-contain transition-transform hover:scale-105 duration-500" src="{{ asset('images/logo.png') }}"/>
                    </div>
                    <h1 class="text-2xl font-bold text-md-primary dark:text-slate-100 font-headline leading-tight">البوابة الإلكترونية</h1>
                    <p class="text-md-on-surface-variant dark:text-slate-400 text-sm mt-3 font-body">لمجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-md-primary dark:text-slate-300 mr-1" for="email">البريد الإلكتروني </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-primary dark:group-focus-within:text-md-tertiary-fixed-dim transition-colors">
                                <span class="icon-[material-symbols--alternate-email] text-[22px]"></span>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-primary/20 dark:focus:border-md-tertiary/30 focus:bg-white dark:focus:bg-slate-800 transition-all font-body text-sm" 
                                placeholder="name@institution.edu.ye"/>
                        </div>
                        @if ($errors->get('email'))
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-bold text-md-primary dark:text-slate-300 mr-1" for="password">كلمة المرور</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-bold text-md-tertiary-container dark:text-md-tertiary-fixed-dim hover:text-md-primary transition-colors" href="{{ route('password.request') }}">
                                    نسيت كلمة المرور؟
                                </a>
                            @endif
                        </div>
                        <div class="relative group" x-data="{ show: false }">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-primary dark:group-focus-within:text-md-tertiary-fixed-dim transition-colors">
                                <span class="icon-[material-symbols--shield-person] text-[22px]"></span>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password" 
                                class="block w-full pr-12 pl-16 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-primary/20 dark:focus:border-md-tertiary/30 focus:bg-white dark:focus:bg-slate-800 transition-all font-body text-sm" 
                                placeholder="••••••••"/>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 gap-2">
                                <button class="w-6 h-6 rounded-full bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center transition-colors border border-amber-500/20" type="button" onclick="document.getElementById('password').value = '123456789'" title="تعبئة كلمة المرور الافتراضية">
                                    <span class="icon-[material-symbols--key] text-xs"></span>
                                </button>
                                <button class="text-md-outline hover:text-md-primary dark:hover:text-md-tertiary-fixed-dim transition-colors" type="button" @click="show = !show">
                                    <span class="icon-[material-symbols--visibility-off-outline]" x-show="!show"></span>
                                    <span class="icon-[material-symbols--visibility-outline]" x-show="show"></span>
                                </button>
                            </div>
                        </div>
                        @if ($errors->get('password'))
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                            class="h-4 w-4 rounded border-md-outline-variant/50 text-md-primary focus:ring-md-primary/20 bg-md-surface-container-low dark:bg-slate-800 cursor-pointer"/>
                        <label class="mr-3 block text-xs font-medium text-md-on-surface-variant dark:text-slate-400 cursor-pointer select-none" for="remember_me">
                            الإبقاء على تسجيل دخولي في هذا الجهاز
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button class="w-full py-4 bg-md-primary hover:bg-md-primary-container text-md-on-primary font-bold rounded-xl shadow-lg shadow-md-primary/30 border border-md-primary-container/20 transform active:scale-[0.99] transition-all duration-300 flex items-center justify-center gap-3 group" type="submit">
                        <span class="text-base">دخول النظام</span>
                        <span class="icon-[material-symbols--arrow-circle-left-outline] text-[20px] group-hover:translate-x-[-4px] transition-transform"></span>
                    </button>
                </form>

        
            </div>
        </div>
    </main>

    <!-- Minimal Legal Branding -->
    <div class="pb-8 text-center px-4">
        <p class="font-body text-[11px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            © {{ date('Y') }} المجلس الوطني للاعتماد الأكاديمي وضمان الجودة - الجمهورية اليمنية
        </p>
    </div>
</body>
</html>
