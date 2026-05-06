<aside
        class="fixed inset-y-0 inset-inline-end-0 z-[70] w-96 bg-md-surface-container-lowest/95 dark:bg-slate-900/95 backdrop-blur-xl border-s border-md-outline-variant/30 shadow-2xl transform translate-x-full transition-transform duration-500 ease-in-out flex flex-col"
        id="notifications-drawer">
    
    {{-- Drawer Header --}}
    <div class="h-(--navbar-height) flex items-center justify-between px-6 border-b border-md-outline-variant/20 shrink-0">
        <div class="flex items-center gap-3">
            <span class="icon-[material-symbols--notifications-active-outline-rounded] text-2xl text-md-primary"></span>
            <h3 class="font-black text-lg text-md-on-surface dark:text-slate-100 font-headline">مركز التنبيهات</h3>
        </div>
        <button class="p-2 rounded-xl hover:bg-md-surface-container-high transition-colors cursor-pointer text-md-on-surface-variant" onclick="toggleNotifications()">
            <span class="icon-[material-symbols--close-rounded] text-2xl"></span>
        </button>
    </div>

    {{-- Notifications List --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-3 no-scrollbar">
        {{-- Success Notification --}}
        <div class="p-4 rounded-2xl bg-md-surface-container-low dark:bg-slate-800/40 border border-transparent hover:border-green-500/30 transition-all cursor-pointer group relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-1 bg-green-500"></div>
            <div class="flex gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0 shadow-inner">
                    <span class="icon-[material-symbols--check-circle-outline-rounded] text-2xl"></span>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-sm font-black text-md-on-surface dark:text-slate-200">تم قبول الطلب</p>
                        <span class="text-[10px] font-bold text-md-outline opacity-70">منذ ٢ د</span>
                    </div>
                    <p class="text-xs text-md-on-surface-variant dark:text-slate-400 leading-relaxed font-body">تمت الموافقة على طلب الاعتماد الأولي لبرنامج هندسة الحاسوب بنجاح.</p>
                </div>
            </div>
        </div>

        {{-- Info Notification --}}
        <div class="p-4 rounded-2xl bg-md-surface-container-low dark:bg-slate-800/40 border border-transparent hover:border-brand-500/30 transition-all cursor-pointer group relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-1 bg-brand-500"></div>
            <div class="flex gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-600 dark:text-brand-400 shrink-0 shadow-inner">
                    <span class="icon-[material-symbols--info-outline-rounded] text-2xl"></span>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-sm font-black text-md-on-surface dark:text-slate-200">تحديث في الجدول</p>
                        <span class="text-[10px] font-bold text-md-outline opacity-70">منذ ساعة</span>
                    </div>
                    <p class="text-xs text-md-on-surface-variant dark:text-slate-400 leading-relaxed font-body">قام المنسق بتحديث موعد زيارة لجنة التقييم الميدانية لجامعة صنعاء.</p>
                </div>
            </div>
        </div>

        {{-- Warning Notification --}}
        <div class="p-4 rounded-2xl bg-md-surface-container-low dark:bg-slate-800/40 border border-transparent hover:border-amber-500/30 transition-all cursor-pointer group relative overflow-hidden">
            <div class="absolute inset-y-0 right-0 w-1 bg-amber-500"></div>
            <div class="flex gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 shadow-inner">
                    <span class="icon-[material-symbols--warning-outline-rounded] text-2xl"></span>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-sm font-black text-md-on-surface dark:text-slate-200">تنبيه مهلة زمنية</p>
                        <span class="text-[10px] font-bold text-md-outline opacity-70">منذ ٣ س</span>
                    </div>
                    <p class="text-xs text-md-on-surface-variant dark:text-slate-400 leading-relaxed font-body">تنبيه: يتبقى ٤٨ ساعة فقط لرفع تقرير الدراسة الذاتية قبل انتهاء المهلة.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Drawer Footer --}}
    <div class="p-6 border-t border-md-outline-variant/20 bg-md-surface-container-lowest dark:bg-slate-900">
        <button class="w-full py-3 text-sm font-black text-md-primary hover:bg-md-primary/5 rounded-xl transition-all cursor-pointer border border-md-primary/20 hover:border-md-primary/40 shadow-sm flex items-center justify-center gap-2">
            <span>عرض كافة الإشعارات</span>
            <span class="icon-[material-symbols--arrow-left-alt-rounded] text-lg"></span>
        </button>
    </div>
</aside>