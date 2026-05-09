<aside
        class="fixed inset-y-0 inset-inline-end-0 z-[70] w-96 bg-md-surface-container-lowest/95 dark:bg-slate-900/95 backdrop-blur-xl border-s border-md-outline-variant/30 shadow-2xl transform translate-x-full transition-transform duration-500 ease-in-out flex flex-col"
        id="notifications-drawer"
        x-data="notifications">
    
    {{-- Drawer Header --}}
    <div class="h-(--navbar-height) flex items-center justify-between px-6 border-b border-md-outline-variant/20 shrink-0">
        <div class="flex items-center gap-3">
            <span class="icon-[material-symbols--notifications-active-outline-rounded] text-2xl text-md-primary"></span>
            <h3 class="font-black text-lg text-md-on-surface dark:text-slate-100 font-headline">مركز التنبيهات</h3>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs font-bold text-md-primary hover:underline cursor-pointer">
                تحديد الكل كمقروء
            </button>
            <button class="p-2 rounded-xl hover:bg-md-surface-container-high transition-colors cursor-pointer text-md-on-surface-variant" onclick="toggleNotifications()">
                <span class="icon-[material-symbols--close-rounded] text-2xl"></span>
            </button>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-3 no-scrollbar">
        {{-- Loading State --}}
        <template x-if="loading">
            <div class="flex flex-col items-center justify-center h-40 space-y-3 opacity-50">
                <div class="w-8 h-8 border-4 border-md-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm font-bold">جاري التحميل...</p>
            </div>
        </template>

        {{-- Empty State --}}
        <template x-if="!loading && notifications.length === 0">
            <div class="flex flex-col items-center justify-center h-60 text-center opacity-50">
                <span class="icon-[material-symbols--notifications-off-outline-rounded] text-6xl mb-4"></span>
                <p class="text-sm font-bold">لا توجد تنبيهات حالياً</p>
            </div>
        </template>

        {{-- Notifications Loop --}}
        <template x-for="notification in notifications" :key="notification.id">
            <div :class="{'opacity-60': notification.read_at}"
                class="p-4 rounded-2xl bg-md-surface-container-low dark:bg-slate-800/40 border border-transparent hover:border-brand-500/30 transition-all cursor-pointer group relative overflow-hidden"
                @click="if(!notification.read_at) markAsRead(notification.id); if(notification.data.action_url) window.location.href = notification.data.action_url">
                
                <div class="absolute inset-y-0 right-0 w-1" 
                    :class="{
                        'bg-green-500': notification.data.type === 'success',
                        'bg-brand-500': notification.data.type === 'info',
                        'bg-amber-500': notification.data.type === 'warning',
                        'bg-red-500': notification.data.type === 'error',
                    }"></div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-inner"
                        :class="{
                            'bg-green-500/10 text-green-600': notification.data.type === 'success',
                            'bg-brand-500/10 text-brand-600': notification.data.type === 'info',
                            'bg-amber-500/10 text-amber-600': notification.data.type === 'warning',
                            'bg-red-500/10 text-red-600': notification.data.type === 'error',
                        }">
                        <span class="text-2xl" :class="{
                            'icon-[material-symbols--check-circle-outline-rounded]': notification.data.type === 'success',
                            'icon-[material-symbols--info-outline-rounded]': notification.data.type === 'info',
                            'icon-[material-symbols--warning-outline-rounded]': notification.data.type === 'warning',
                            'icon-[material-symbols--error-outline-rounded]': notification.data.type === 'error',
                        }"></span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm font-black text-md-on-surface dark:text-slate-200" x-text="notification.data.title"></p>
                            <span class="text-[10px] font-bold text-md-outline opacity-70" x-text="notification.created_at"></span>
                        </div>
                        <p class="text-xs text-md-on-surface-variant dark:text-slate-400 leading-relaxed font-body" x-text="notification.data.message"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Drawer Footer --}}
    <div class="p-6 border-t border-md-outline-variant/20 bg-md-surface-container-lowest dark:bg-slate-900">
        <button class="w-full py-3 text-sm font-black text-md-primary hover:bg-md-primary/5 rounded-xl transition-all cursor-pointer border border-md-primary/20 hover:border-md-primary/40 shadow-sm flex items-center justify-center gap-2">
            <span>عرض كافة الإشعارات</span>
            <span class="icon-[material-symbols--arrow-left-alt-rounded] text-lg"></span>
        </button>
    </div>
</aside>