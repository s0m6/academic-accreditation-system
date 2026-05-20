<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="light">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - الملف الشخصي</title>

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
            box-shadow: 0 10px 30px -5px rgba(0, 37, 70, 0.05), 0 0 1px 0 rgba(0, 37, 70, 0.1);
        }
        .dark .premium-shadow {
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), 0 0 1px 0 rgba(233, 193, 118, 0.05);
        }
    </style>
</head>
<body class="bg-md-surface dark:bg-slate-950 text-md-on-surface transition-colors duration-300 min-h-screen flex flex-col font-sans">
    <!-- Global Preloader -->
    @include('public.partials.preloader')

    <!-- Theme Toggle & Back Button -->
    <div class="fixed top-6 left-6 z-50 flex gap-3">
        <a href="{{ route('dashboard') }}" class="p-2.5 rounded-xl border border-md-outline-variant/30 bg-md-surface-container-lowest/50 dark:bg-slate-900/50 backdrop-blur-md hover:bg-md-surface-container-high dark:hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2 group">
            <span class="icon-[material-symbols--arrow-back-rounded] text-2xl group-hover:translate-x-[2px] transition-transform"></span>
            <span class="hidden md:block text-sm font-bold">العودة للوحة التحكم</span>
        </a>
        <button class="p-2.5 rounded-xl border border-md-outline-variant/30 bg-md-surface-container-lowest/50 dark:bg-slate-900/50 backdrop-blur-md hover:bg-md-surface-container-high dark:hover:bg-slate-800 transition-colors shadow-sm" onclick="document.documentElement.classList.toggle('dark')">
            <span class="icon-[material-symbols--dark-mode-outline] text-2xl dark:hidden"></span>
            <span class="icon-[material-symbols--light-mode] text-2xl hidden dark:block text-md-tertiary-fixed-dim"></span>
        </button>
    </div>

    <main class="flex-grow pt-24 pb-12 px-4 institutional-pattern relative overflow-hidden">
        <!-- Aesthetic Decorative Elements -->
        <div class="absolute -top-48 -right-48 w-[600px] h-[600px] bg-md-primary/5 dark:bg-md-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-48 -left-48 w-[600px] h-[600px] bg-md-tertiary-fixed-dim/5 dark:bg-md-tertiary/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto space-y-8 relative z-10">
            <!-- Header Section -->
            <div class="text-right mb-10">
                <h1 class="text-3xl font-black text-md-primary dark:text-slate-100 font-headline mb-2">إعدادات الحساب</h1>
                <p class="text-md-on-surface-variant dark:text-slate-400 font-body">إدارة معلوماتك الشخصية وأمان حسابك</p>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-md-surface-container-lowest dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl premium-shadow border border-md-outline-variant/20 dark:border-white/5 overflow-hidden">
                <div class="h-1.5 w-full bg-md-primary"></div>
                <div class="p-8 md:p-10">
                    <section>
                        <header class="mb-8">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="icon-[material-symbols--person-edit-outline] text-2xl text-md-primary"></span>
                                <h2 class="text-xl font-bold text-md-primary dark:text-slate-100 font-headline">المعلومات الشخصية</h2>
                            </div>
                            <p class="text-sm text-md-on-surface-variant dark:text-slate-400">قم بتحديث معلومات ملفك الشخصي وعنوان بريدك الإلكتروني.</p>
                        </header>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-md-primary dark:text-slate-300 mr-1" for="name">الاسم الكامل</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-primary transition-colors">
                                            <span class="icon-[material-symbols--person-outline] text-[22px]"></span>
                                        </div>
                                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                            class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-primary/20 transition-all font-body text-sm" />
                                    </div>
                                    @if ($errors->get('name'))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>

                                <!-- Email -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-md-primary dark:text-slate-300 mr-1" for="email">البريد الإلكتروني</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-primary transition-colors">
                                            <span class="icon-[material-symbols--alternate-email] text-[22px]"></span>
                                        </div>
                                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                            class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-primary/20 transition-all font-body text-sm" />
                                    </div>
                                    @if ($errors->get('email'))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">{{ $errors->first('email') }}</p>
                                    @endif

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                        <div class="mt-2 bg-amber-50 dark:bg-amber-900/10 p-3 rounded-lg border border-amber-100 dark:border-amber-800/30">
                                            <p class="text-xs text-amber-800 dark:text-amber-400 flex items-center gap-2">
                                                <span class="icon-[material-symbols--warning-outline-rounded]"></span>
                                                عنوان بريدك الإلكتروني غير محقق.
                                                <button form="send-verification" class="font-bold underline hover:text-amber-900 dark:hover:text-amber-300">اضغط هنا لإعادة الإرسال.</button>
                                            </p>
                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-xs font-bold text-green-600 dark:text-green-400">تم إرسال رابط جديد لبريدك.</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t border-md-outline-variant/10">
                                <button type="submit" class="py-3 px-8 bg-md-primary hover:bg-md-primary-container text-md-on-primary font-bold rounded-xl shadow-lg shadow-md-primary/20 transform active:scale-[0.98] transition-all flex items-center gap-2">
                                    <span>حفظ التغييرات</span>
                                    <span class="icon-[material-symbols--check-circle-outline-rounded]"></span>
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-green-600 dark:text-green-400 flex items-center gap-1">
                                        <span class="icon-[material-symbols--done-all-rounded]"></span>
                                        تم الحفظ بنجاح
                                    </p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="bg-md-surface-container-lowest dark:bg-slate-900/80 backdrop-blur-xl rounded-2xl premium-shadow border border-md-outline-variant/20 dark:border-white/5 overflow-hidden">
                <div class="h-1.5 w-full bg-md-tertiary"></div>
                <div class="p-8 md:p-10">
                    <section>
                        <header class="mb-8">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="icon-[material-symbols--lock-reset-rounded] text-2xl text-md-tertiary"></span>
                                <h2 class="text-xl font-bold text-md-tertiary dark:text-slate-100 font-headline">تغيير كلمة المرور</h2>
                            </div>
                            <p class="text-sm text-md-on-surface-variant dark:text-slate-400">تأكد من استخدام كلمة مرور طويلة وعشوائية للحفاظ على أمان حسابك.</p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Current Password -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-md-tertiary dark:text-slate-300 mr-1" for="update_password_current_password">كلمة المرور الحالية</label>
                                    <div class="relative group" x-data="{ show: false }">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--key-outline] text-[22px]"></span>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" id="update_password_current_password" name="current_password" autocomplete="current-password"
                                            class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-tertiary/20 transition-all font-body text-sm" />
                                        <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 pl-4 text-md-outline hover:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--visibility-off-outline]" x-show="!show"></span>
                                            <span class="icon-[material-symbols--visibility-outline]" x-show="show"></span>
                                        </button>
                                    </div>
                                    @if ($errors->updatePassword->get('current_password'))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">{{ $errors->updatePassword->first('current_password') }}</p>
                                    @endif
                                </div>

                                <!-- New Password -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-md-tertiary dark:text-slate-300 mr-1" for="update_password_password">كلمة المرور الجديدة</label>
                                    <div class="relative group" x-data="{ show: false }">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--shield-person-outline] text-[22px]"></span>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" id="update_password_password" name="password" autocomplete="new-password"
                                            class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-tertiary/20 transition-all font-body text-sm" />
                                        <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 pl-4 text-md-outline hover:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--visibility-off-outline]" x-show="!show"></span>
                                            <span class="icon-[material-symbols--visibility-outline]" x-show="show"></span>
                                        </button>
                                    </div>
                                    @if ($errors->updatePassword->get('password'))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">{{ $errors->updatePassword->first('password') }}</p>
                                    @endif
                                </div>

                                <!-- Confirm New Password -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-md-tertiary dark:text-slate-300 mr-1" for="update_password_password_confirmation">تأكيد كلمة المرور</label>
                                    <div class="relative group" x-data="{ show: false }">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-md-outline group-focus-within:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--shield-lock-outline] text-[22px]"></span>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                                            class="block w-full pr-12 py-3.5 bg-md-surface-container-low dark:bg-slate-800/50 border-2 border-transparent rounded-xl text-md-on-surface dark:text-slate-100 placeholder:text-md-outline/60 focus:ring-0 focus:border-md-tertiary/20 transition-all font-body text-sm" />
                                        <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 pl-4 text-md-outline hover:text-md-tertiary transition-colors">
                                            <span class="icon-[material-symbols--visibility-off-outline]" x-show="!show"></span>
                                            <span class="icon-[material-symbols--visibility-outline]" x-show="show"></span>
                                        </button>
                                    </div>
                                    @if ($errors->updatePassword->get('password_confirmation'))
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 mr-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4 border-t border-md-outline-variant/10">
                                <button type="submit" class="py-3 px-8 bg-md-tertiary hover:bg-md-tertiary-container text-md-on-tertiary font-bold rounded-xl shadow-lg shadow-md-tertiary/20 transform active:scale-[0.98] transition-all flex items-center gap-2">
                                    <span>تحديث كلمة المرور</span>
                                    <span class="icon-[material-symbols--security-update-good-outline-rounded]"></span>
                                </button>

                                @if (session('status') === 'password-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-green-600 dark:text-green-400 flex items-center gap-1">
                                        <span class="icon-[material-symbols--done-all-rounded]"></span>
                                        تم التحديث بنجاح
                                    </p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

        </div>
    </main>

    <div class="pb-8 text-center px-4">
        <p class="font-body text-[11px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            © {{ date('Y') }} المجلس الوطني للاعتماد الأكاديمي وضمان الجودة - الجمهورية اليمنية
        </p>
    </div>
</body>
</html>
