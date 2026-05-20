<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data="visitSchedule({{ $readonly ?? 'false' }})" :class="darkMode ? 'dark' : ''">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل جدول الزيارة الميدانية | نظام الاعتماد الأكاديمي</title>
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
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.2s;
        }
        html:not(.dark) input[type="date"]::-webkit-calendar-picker-indicator {
            filter: brightness(0);
        }
        html.dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(2);
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
    </style>
</head>

<body class="min-h-screen transition-colors duration-300"
      style="background-color: var(--bg-main); color: var(--text-primary); font-family: 'Cairo', sans-serif;">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    {{-- ═══ HEADER ═══ --}}
    <header class="sticky top-0 z-50 border-b" style="background-color:var(--surface-card); border-color:var(--border-primary);">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <a href="{{ route('requests.show', $accreditationRequest->id) }}?stage=stage_five" class="w-9 h-9 rounded-xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shrink-0 hover:bg-(--surface-card) transition-colors cursor-pointer group" title="العودة للوحة الطلب">
                    <i class="fa-solid fa-arrow-right text-(--text-secondary) group-hover:text-blue-500"></i>
                </a>
                <div class="w-9 h-9 rounded-xl btn-gradient flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-days text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold" style="color:var(--text-primary);">
                        <span x-text="readonly ? 'عرض جدول الزيارة الميدانية' : 'تعديل جدول الزيارة الميدانية'"></span>
                    </h1>
                    <p class="text-xs" style="color:var(--text-secondary);">طلب الاعتماد الأكاديمي #{{ $accreditationRequest->id }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2" x-show="!readonly">
                {{-- Dark mode (moved inside if needed, or keep outside) --}}
                <button type="button" @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-xl border flex items-center justify-center transition-colors cursor-pointer"
                    style="border-color:var(--border-primary); background:var(--bg-main); color:var(--text-secondary);">
                    <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="text-sm"></i>
                </button>

                {{-- Save --}}
                <button type="button" @click="saveSchedule()" :disabled="isSaving"
                    class="btn-gradient text-white px-5 py-2 rounded-xl font-bold text-sm flex items-center gap-2 cursor-pointer disabled:opacity-70">
                    <i x-show="!isSaving" class="fa-solid fa-floppy-disk"></i>
                    <i x-show="isSaving" class="fa-solid fa-spinner fa-spin"></i>
                    <span x-text="isSaving ? 'جاري الحفظ...' : 'حفظ مسودة'"></span>
                </button>

                <a href="{{ route('requests.show', $accreditationRequest->id) }}?stage=stage_five"
                    class="bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) hover:text-(--text-primary) px-5 py-2 rounded-xl font-bold text-sm flex items-center gap-2 cursor-pointer transition shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    العودة للوحة الطلب
                </a>
            </div>
            <div x-show="readonly" class="flex items-center gap-2">
                 <button type="button" @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-xl border flex items-center justify-center transition-colors cursor-pointer"
                    style="border-color:var(--border-primary); background:var(--bg-main); color:var(--text-secondary);">
                    <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="text-sm"></i>
                </button>
                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i> وضع العرض فقط
                </div>
            </div>
        </div>
    </header>

    {{-- ═══ MAIN ═══ --}}
    <main class="max-w-6xl mx-auto px-6 py-8 space-y-6">

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
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-black"
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
                        <input type="date" x-model="day.date" :disabled="readonly"
                            class="px-4 py-2 rounded-xl border text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all ms-auto disabled:opacity-70"
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
                            <div class="col-span-1 px-2 py-3 border-s" style="border-color:var(--border-primary);" x-show="!readonly"></div>
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
                                        <div class="flex items-center gap-2" dir="rtl">
                                            <div class="flex-1 relative">
                                                <input type="time" x-model="row.start_time" :disabled="readonly"
                                                    @input="row.time = (row.start_time || '') + ' - ' + (row.end_time || '')"
                                                    class="w-full px-2 py-2 rounded-lg border text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-70"
                                                    style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                                                <div class="text-[9px] absolute -top-2 right-2 px-1 bg-(--surface-card) text-(--text-secondary)">من</div>
                                            </div>
                                            <span class="text-(--text-secondary) opacity-50">/</span>
                                            <div class="flex-1 relative">
                                                <input type="time" x-model="row.end_time" :disabled="readonly"
                                                    @input="row.time = (row.start_time || '') + ' - ' + (row.end_time || '')"
                                                    class="w-full px-2 py-2 rounded-lg border text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-70"
                                                    style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);">
                                                <div class="text-[9px] absolute -top-2 right-2 px-1 bg-(--surface-card) text-(--text-secondary)">إلى</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Task --}}
                                    <div class="col-span-5 px-3 py-3 border-s" style="border-color:var(--border-primary);">
                                        <textarea x-model="row.task" :disabled="readonly"
                                            placeholder="المهمة أو النشاط المقرر"
                                            rows="1"
                                            x-init="$nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' })"
                                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-70 resize-none overflow-hidden"
                                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);"></textarea>
                                    </div>

                                    {{-- Notes --}}
                                    <div :class="readonly ? 'col-span-3' : 'col-span-2'" class="px-3 py-3 border-s" style="border-color:var(--border-primary);">
                                        <textarea x-model="row.notes" :disabled="readonly"
                                            placeholder="ملاحظات"
                                            rows="1"
                                            x-init="$nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' })"
                                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all disabled:opacity-70 resize-none overflow-hidden"
                                            style="border-color:var(--border-primary); background:var(--surface-card); color:var(--text-primary);"></textarea>
                                    </div>

                                    {{-- Delete --}}
                                    <div class="col-span-1 px-2 py-3 flex items-center justify-center border-s" style="border-color:var(--border-primary);" x-show="!readonly">
                                        <button type="button" @click="day.rows.splice(rowIdx, 1)"
                                            class="w-8 h-8 rounded-lg text-red-500 hover:text-white hover:bg-red-600 flex items-center justify-center transition-all cursor-pointer opacity-50 group-hover:opacity-100 text-sm bg-red-50 dark:bg-red-500/10">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Add Row --}}
                    <button type="button" @click="day.rows.push({ time: '', start_time: '', end_time: '', task: '', notes: '' })" x-show="!readonly"
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


    <script>
        function visitSchedule(readonly = false) {
            return {
                readonly: readonly,
                darkMode: localStorage.getItem('vs-dark') === 'true'
                    || (!localStorage.getItem('vs-dark') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                activeDay: 0,
                toast: { show: false, message: '', type: 'success' },
                isSaving: false,
                showSubmitModal: false,
                days: {!! json_encode(is_array($visitSchedule->schedule_data) && isset($visitSchedule->schedule_data['days']) ? $visitSchedule->schedule_data['days'] : [
                    ['label' => 'اليوم الأول',  'date' => '', 'rows' => []],
                    ['label' => 'اليوم الثاني', 'date' => '', 'rows' => []],
                    ['label' => 'اليوم الثالث', 'date' => '', 'rows' => []],
                ]) !!},
                init() {
                    this.$watch('darkMode', val => localStorage.setItem('vs-dark', val));
                    
                    // Initialize start_time and end_time from existing time string
                    this.days.forEach(day => {
                        day.rows.forEach(row => {
                            if (row.time && row.time.includes('-')) {
                                const parts = row.time.split('-').map(s => s.trim());
                                row.start_time = parts[0] || '';
                                row.end_time = parts[1] || '';
                            } else if (row.time && row.time.includes('–')) {
                                const parts = row.time.split('–').map(s => s.trim());
                                row.start_time = parts[0] || '';
                                row.end_time = parts[1] || '';
                            } else {
                                row.start_time = row.time || '';
                                row.end_time = '';
                            }
                        });
                    });
                },
                async saveSchedule() {
                    const hasAny = this.days.some(d => d.rows.length > 0);
                    if (!hasAny) {
                        this.showToast('أضف نشاطاً واحداً على الأقل قبل الحفظ', 'error');
                        return;
                    }
                    
                    this.isSaving = true;
                    try {
                        const response = await fetch('{{ route("requests.stage_five.save", [$accreditationRequest, $visitSchedule]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ days: this.days })
                        });
                        
                        if (response.ok) {
                            this.showToast('تم حفظ مسودة الجدول بنجاح', 'success');
                        } else {
                            const data = await response.json();
                            this.showToast(data.message || 'حدث خطأ أثناء الحفظ', 'error');
                        }
                    } catch (error) {
                        this.showToast('حدث خطأ بالاتصال أثناء الحفظ', 'error');
                    }
                    this.isSaving = false;
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
