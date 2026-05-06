<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - التحقق من البريد الإلكتروني</title>

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

        <!-- Verify Email Card -->
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
                    <h1 class="text-2xl font-bold text-md-primary dark:text-slate-100 font-headline leading-tight">التحقق من البريد</h1>
                    <p class="text-md-on-surface-variant dark:text-slate-400 text-sm mt-3 font-body">يرجى تأكيد عنوان بريدك الإلكتروني</p>
                </div>

                <div class="mb-6 text-sm text-center leading-relaxed text-md-on-surface-variant dark:text-slate-400 bg-md-surface-container-low/50 dark:bg-slate-800/30 p-5 rounded-xl border border-md-outline-variant/10">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 font-medium text-sm text-green-600 dark:text-green-400 text-center bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-200 dark:border-green-800">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto py-3 px-6 bg-md-primary hover:bg-md-primary-container text-md-on-primary font-bold rounded-xl shadow-lg shadow-md-primary/30 border border-md-primary-container/20 transform active:scale-[0.99] transition-all duration-300 flex items-center justify-center gap-2 group">
                            <span class="text-sm">إعادة إرسال البريد</span>
                            <span class="icon-[material-symbols--forward-to-inbox-outline] text-[20px] group-hover:translate-x-[-2px] transition-transform"></span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto py-3 px-6 text-sm font-bold text-md-on-surface-variant dark:text-slate-400 hover:text-md-primary dark:hover:text-md-tertiary-fixed-dim transition-colors flex items-center justify-center gap-2">
                            <span>تسجيل الخروج</span>
                            <span class="icon-[material-symbols--logout-rounded] text-[20px]"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
