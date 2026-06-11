<!-- About Section -->
<section id="about" class="py-20 bg-slate-50 dark:bg-slate-950 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 dark:bg-primary/10 rounded-full blur-[100px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent/5 dark:bg-accent/10 rounded-full blur-[100px] pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Logo Column -->
            <div class="lg:col-span-4 flex justify-center items-center">
                <div class="relative group">
                    <!-- Glow effect behind the logo -->
                    <div
                        class="absolute inset-0 bg-primary/10 dark:bg-primary/20 rounded-full blur-3xl scale-120 group-hover:scale-130 transition-transform duration-500">
                    </div>

                    <!-- Decorative Circular Borders -->
                    <div
                        class="w-44 h-44 md:w-52 md:h-52 rounded-full border-2 border-dashed border-primary/20 dark:border-accent/20 flex items-center justify-center p-4 animate-[spin_60s_linear_infinite]">
                    </div>

                    <div
                        class="absolute inset-2 md:inset-3 rounded-full bg-white dark:bg-slate-900 shadow-xl flex items-center justify-center p-4 border border-slate-100 dark:border-slate-800 transition-transform duration-500 group-hover:scale-[1.03]">
                        <img src="{{ asset('images/logo.png') }}" alt="شعار مجلس الاعتماد الأكاديمي"
                            class="max-w-[85%] max-h-[85%] object-contain">
                    </div>
                </div>
            </div>

            <!-- Content Column -->
            <div class="lg:col-span-8 space-y-6 text-right">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 mb-1 justify-start lg:justify-start">
                        <span class="w-2 h-8 bg-primary dark:bg-accent rounded-full"></span>
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">عن المجلس</h2>
                    </div>
                    <p class="text-sm font-semibold tracking-wider text-primary dark:text-accent uppercase">مجلس
                        الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>

                <p class="text-slate-600 dark:text-slate-300 leading-relaxed font-medium text-base text-justify">
                    تم إنشاء مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي بناءً على القرار الجمهوري رقم 210 لسنة
                    2009 بإنشاء مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي في الجمهورية اليمنية، وهو الجهة
                    المختصة بوضع أسس ومعايير اعتماد مؤسسات التعليم العالي ودراسة طلبات الاعتماد العام والخاص وتقييم
                    المؤسسات التعليمية ويتمتع بالشخصية الاعتبارية والذمة المالية المستقلة ويتألف من تسعة أعضاء بدرجة
                    أستاذ من تخصصات أكاديمية مختلفة وقد تم إعادة تشكيله مؤخراً بقرار رئيس مجلس الوزراء رقم 24 لسنة 2023.
                </p>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <div
                        class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-primary/20 flex items-center justify-center text-primary dark:text-accent shrink-0">
                            <i class="fa-solid fa-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-slate-800 dark:text-white">2009</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-bold">تاريخ التأسيس (رقم 210)
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-accent/20 flex items-center justify-center text-emerald-600 dark:text-accent shrink-0">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-slate-800 dark:text-white">9 أعضاء</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-bold">بدرجة أستاذ (بروفيسور)
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                            <i class="fa-solid fa-gavel text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-slate-800 dark:text-white">قرار 24</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-bold">إعادة التشكيل لسنة 2023
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
