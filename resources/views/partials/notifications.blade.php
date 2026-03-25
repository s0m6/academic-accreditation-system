<aside
        class="fixed inset-y-0 inset-inline-end-0 z-60 w-80 bg-(--surface-card) border-s border-(--border-primary) shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
        id="notifications-drawer">
        <div
            class="h-(--navbar-height) flex items-center justify-between px-6 border-b border-(--border-subtle) shrink-0">
            <h3 class="font-bold text-lg">التنبيهات</h3>
            <button class="icon-btn cursor-pointer" onclick="toggleNotifications()">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 no-scrollbar">
            @for ($i = 1; $i <= 5; $i++)
                <div
                    class="p-3 rounded-xl bg-(--bg-subtle) border border-transparent hover:border-brand-500/30 transition-all cursor-pointer group">
                    <div class="flex gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-600 dark:text-brand-400 shrink-0">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">تنبيه نظام جديد
                            </p>
                            <p class="text-xs text-(--text-secondary) mt-1">لقد تم تحديث حالة الطلب رقم #12345 بنجاح.
                            </p>
                            <span class="text-[10px] text-(--text-secondary) opacity-60 mt-2 block">منذ ٥ دقائق</span>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="p-4 border-t border-(--border-subtle)">
            <button
                class="w-full py-2 text-sm font-bold text-brand-600 dark:text-brand-400 hover:bg-(--surface-hover) rounded-lg transition-colors cursor-pointer">مشاهدة
                جميع التنبيهات</button>
        </div>
    </aside>