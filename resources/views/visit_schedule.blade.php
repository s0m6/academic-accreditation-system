<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data="visitSchedule()" :class="darkMode ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول الزيارة الميدانية | نظام الاعتماد الأكاديمي</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.2s;
        }
        .btn-gradient:hover {
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
            transform: translateY(-1px);
        }
        .tab-active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }
        .fade-in { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .row-anim { animation: rowIn 0.25s ease; }
        @keyframes rowIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.5; cursor: pointer;
        }
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.7);
        }
    </style>
</head>

<body class="min-h-screen transition-colors duration-300"
      style="background-color: var(--bg-main); color: var(--text-primary); font-family: 'Cairo', sans-serif;">

    {{-- ═══ HEADER ═══ --}}
    <header class="sticky top-0 z-50 border-b" style="background-color:var(--surface-card); border-color:var(--border-primary);">
        <div class="max-w-4xl mx-auto px-6 h-16 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl btn-gradient flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-days text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold" style="color:var(--text-primary);">جدول الزيارة الميدانية</h1>
                    <p class="text-xs" style="color:var(--text-secondary);">نظام الاعتماد الأكاديمي</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Dark mode --}}
                <button type="button" @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-xl border flex items-center justify-center transition-colors cursor-pointer"
                    style="border-color:var(--border-primary); background:var(--bg-main); color:var(--text-secondary);">
                    <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="text-sm"></i>
                </button>

                {{-- Save --}}
                <button type="button" @click="saveSchedule()"
                    class="btn-gradient text-white px-5 py-2 rounded-xl font-bold text-sm flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    حفظ الجدول
                </button>
            </div>
        </div>
    </header>

    {{-- ═══ MAIN ═══ --}}
    <main class="max-w-4xl mx-auto px-6 py-8 space-y-6">

        {{-- Card --}}
        <div class="rounded-2xl border overflow-hidden shadow-sm" style="background:var(--surface-card); border-color:var(--border-primary);">

            {{-- Tab Bar --}}
            <div class="px-5 pt-5 pb-0">
                <div class="flex gap-2 p-1.5 rounded-xl" style="background:var(--bg-main);">
                    <template x-for="(day, i) in days" :key="i">
                        <button type="button" @click="activeDay = i"
                            :class="activeDay === i ? 'tab-active' : ''"
                            class="flex-1 py-2.5 px-3 rounded-lg font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
                            :style="activeDay !== i ? 'color:var(--text-secondary);' : ''">
                            <span x-text="day.label"></span>
                            <span x-show="day.rows.length > 0"
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-black"
                                :class="activeDay === i ? 'bg-white/25 text-white' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'"
                                x-text="day.rows.length"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Tab Content --}}
            <template x-for="(day, dayIdx) in days" :key="dayIdx">
                <div x-show="activeDay === dayIdx" x-transition class="fade-in p-5 space-y-5">

                    {{-- Date Row --}}
                    <div class="flex items-center gap-4 p-4 rounded-xl border" style="background:var(--bg-main); border-color:var(--border-primary);">
                        <label class="text-sm font-bold shrink-0" style="color:var(--text-primary);">
                            تاريخ <span x-text="day.label"></span>
                        </label>
                        <input type="date" x-model="day.date"
                            class="px-4 py-2 rounded-xl border text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all ms-auto"
                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                    </div>

                    {{-- Table --}}
                    <div class="rounded-xl border overflow-hidden" style="border-color:var(--border-primary);">

                        {{-- Table Head --}}
                        <div class="grid grid-cols-12 text-xs font-bold uppercase tracking-wide border-b"
                            style="background:var(--bg-main); border-color:var(--border-primary); color:var(--text-secondary);">
                            <div class="col-span-1 px-3 py-3 text-center">#</div>
                            <div class="col-span-3 px-4 py-3 border-s" style="border-color:var(--border-primary);">التوقيت</div>
                            <div class="col-span-5 px-4 py-3 border-s" style="border-color:var(--border-primary);">المهمة / النشاط</div>
                            <div class="col-span-2 px-4 py-3 border-s" style="border-color:var(--border-primary);">التوضيح</div>
                            <div class="col-span-1 px-2 py-3 border-s" style="border-color:var(--border-primary);"></div>
                        </div>

                        {{-- Empty State --}}
                        <div x-show="day.rows.length === 0"
                            class="py-14 text-center" style="color:var(--text-secondary);">
                            <p class="text-sm font-medium">لا توجد أنشطة مضافة لهذا اليوم</p>
                            <p class="text-xs mt-1 opacity-70">اضغط على "إضافة صف" للبدء</p>
                        </div>

                        {{-- Rows --}}
                        <div class="divide-y" style="--tw-divide-opacity:1;">
                            <template x-for="(row, rowIdx) in day.rows" :key="rowIdx">
                                <div class="grid grid-cols-12 row-anim group transition-colors"
                                    style="--hover-bg:var(--bg-main);"
                                    @mouseenter="$el.style.backgroundColor='var(--bg-main)'"
                                    @mouseleave="$el.style.backgroundColor=''">

                                    {{-- # --}}
                                    <div class="col-span-1 px-3 py-3 flex items-center justify-center">
                                        <span class="w-6 h-6 rounded-lg text-[11px] font-black flex items-center justify-center border"
                                            style="background:var(--bg-main); border-color:var(--border-primary); color:var(--text-secondary);"
                                            x-text="rowIdx + 1"></span>
                                    </div>

                                    {{-- Time --}}
                                    <div class="col-span-3 px-3 py-3 border-s" style="border-color:var(--border-primary);">
                                        <input type="text" x-model="row.time"
                                            placeholder="08:00 – 09:30"
                                            dir="ltr"
                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                                    </div>

                                    {{-- Task --}}
                                    <div class="col-span-5 px-3 py-3 border-s" style="border-color:var(--border-primary);">
                                        <input type="text" x-model="row.task"
                                            placeholder="المهمة أو النشاط المقرر"
                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                                    </div>

                                    {{-- Notes --}}
                                    <div class="col-span-2 px-3 py-3 border-s" style="border-color:var(--border-primary);">
                                        <input type="text" x-model="row.notes"
                                            placeholder="ملاحظات"
                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                                    </div>

                                    {{-- Delete --}}
                                    <div class="col-span-1 px-2 py-3 flex items-center justify-center border-s" style="border-color:var(--border-primary);">
                                        <button type="button" @click="day.rows.splice(rowIdx, 1)"
                                            class="w-7 h-7 rounded-lg text-red-400 hover:text-white hover:bg-red-500 flex items-center justify-center transition-all cursor-pointer opacity-0 group-hover:opacity-100 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Add Row --}}
                    <button type="button" @click="day.rows.push({ time: '', task: '', notes: '' })"
                        class="w-full py-2.5 rounded-xl border-2 border-dashed font-bold text-sm flex items-center justify-center gap-2 transition-all cursor-pointer"
                        style="border-color:var(--border-primary); color:var(--text-secondary);"
                        @mouseenter="$el.style.borderColor='#2563eb'; $el.style.color='#2563eb'; $el.style.backgroundColor='var(--bg-main)'"
                        @mouseleave="$el.style.borderColor=''; $el.style.color=''; $el.style.backgroundColor=''">
                        <i class="fa-solid fa-plus text-xs"></i>
                        إضافة صف
                    </button>

                </div>
            </template>

        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-between pb-8">
            <button type="button" @click="resetAll()"
                class="px-5 py-2.5 rounded-xl border font-bold text-sm transition-colors cursor-pointer flex items-center gap-2"
                style="border-color:var(--border-primary); color:var(--text-secondary); background:var(--surface-card);">
                <i class="fa-solid fa-rotate-left text-xs"></i>
                إعادة تعيين
            </button>

            <button type="button" @click="saveSchedule()"
                class="btn-gradient text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                حفظ جدول الزيارة
            </button>
        </div>

    </main>

    {{-- Toast --}}
    <div x-show="toast.show" style="display:none"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0 translate-y-3"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[999] px-5 py-3 rounded-xl shadow-2xl font-bold text-sm flex items-center gap-3"
        :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
        <i :class="toast.type === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-triangle-exclamation'"></i>
        <span x-text="toast.message"></span>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <script>
        function visitSchedule() {
            return {
                darkMode: localStorage.getItem('vs-dark') === 'true'
                    || (!localStorage.getItem('vs-dark') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                activeDay: 0,
                toast: { show: false, message: '', type: 'success' },
                days: [
                    { label: 'اليوم الأول',  date: '', rows: [] },
                    { label: 'اليوم الثاني', date: '', rows: [] },
                    { label: 'اليوم الثالث', date: '', rows: [] },
                ],
                init() {
                    this.$watch('darkMode', val => localStorage.setItem('vs-dark', val));
                    const saved = localStorage.getItem('vs-draft');
                    if (saved) {
                        try { const d = JSON.parse(saved); if (d.days) this.days = d.days; } catch(e) {}
                    }
                },
                saveSchedule() {
                    const hasAny = this.days.some(d => d.rows.length > 0);
                    if (!hasAny) {
                        this.showToast('أضف نشاطاً واحداً على الأقل', 'error');
                        return;
                    }
                    localStorage.setItem('vs-draft', JSON.stringify({ days: this.days }));
                    this.showToast('تم حفظ الجدول بنجاح', 'success');
                },
                resetAll() {
                    this.days = [
                        { label: 'اليوم الأول',  date: '', rows: [] },
                        { label: 'اليوم الثاني', date: '', rows: [] },
                        { label: 'اليوم الثالث', date: '', rows: [] },
                    ];
                    localStorage.removeItem('vs-draft');
                    this.showToast('تم إعادة تعيين الجدول', 'success');
                },
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },
            };
        }
    </script>
</body>
</html>
