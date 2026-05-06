<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نموذج 9 - رد المؤسسة على توصيات اللجنة</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f1f5f9;
        }

        .input-field {
            width: 100%;
            background-color: #fff;
            color: #111827;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s;
        }

        .input-field:focus:not([readonly]) {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .readonly-field {
            background-color: #f3f4f6;
            color: #4b5563;
            cursor: not-allowed;
            border-color: #e5e7eb;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="p-6">

    @php
        /**
         * Build the initial criteriaData JSON for Alpine.js from PHP data.
         * Structure matches what the controller returns from buildDetailedStandards().
         * We merge any existing form9_data (saved responses) back in.
         *
         * savedForm9Data shape:
         * [
         *   { sub_id: int, decision: 'approved'|'rejected', rejection_points: [string] },
         *   ...
         * ]
         */
        $savedBySubId = collect($savedForm9Data)->keyBy('sub_id');

        $alpineData = [];
        foreach ($detailedStandards as $std) {
            $subs = [];
            foreach ($std['subs'] as $sub) {
                $saved = $savedBySubId->get($sub['id']);
                $subs[] = [
                    'sub_id' => $sub['id'],
                    'number' => $sub['number'],
                    'std_number' => $std['number'],
                    'name' => $sub['name'],
                    'average' => $sub['average'],
                    'improvements' => $sub['improvements'],
                    'decision' => $saved ? $saved['decision'] : null,
                    'rejection_points' => ($saved && !empty($saved['rejection_points'])) ? $saved['rejection_points'] : [''],
                ];
            }
            $alpineData[] = [
                'main_id' => $std['id'],
                'main_title' => $std['name'],
                'subs' => $subs,
            ];
        }
    @endphp

    <div class="max-w-[1400px] mx-auto space-y-5" x-data="form9App()" x-init="init()">

        {{-- ===== Header ===== --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-inner shrink-0">
                    <i class="fa-solid fa-file-pen text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-800">نموذج 9 — رد المؤسسة على توصيات اللجنة</h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $accreditationRequest->program->program_name ?? '' }}
                        @if($isEditMode)
                            &nbsp;|&nbsp;
                            <span class="inline-flex items-center gap-1 text-blue-600 font-bold">
                                <i class="fa-solid fa-pen-to-square text-[10px]"></i> وضع التعديل
                            </span>
                        @else
                            &nbsp;|&nbsp;
                            <span class="inline-flex items-center gap-1 text-gray-500 font-bold">
                                <i class="fa-solid fa-eye text-[10px]"></i> وضع العرض فقط
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Progress indicator --}}
                <div x-cloak class="text-xs font-bold px-3 py-1.5 rounded-xl border"
                    :class="allAnswered ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                    x-text="allAnswered ? '✓ تم الرد على جميع المعايير' : pendingCount + ' معيار لم يُرد عليه'">
                </div>
                <a href="{{ route('requests.show', $accreditationRequest->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-bold transition border border-blue-100">
                    <i class="fa-solid fa-chart-line"></i> لوحة الطلب
                </a>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition border border-gray-200">
                    <i class="fa-solid fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>

        {{-- ===== Flash Messages ===== --}}
        @if(session('success'))
            <div
                class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 flex items-center gap-3 text-green-800 font-bold text-sm">
                <i class="fa-solid fa-circle-check text-green-500"></i> {{ session('success') }}
            </div>
        @endif

        {{-- ===== Save/Toast Notification ===== --}}
        <div x-cloak x-show="saveStatus !== ''" x-transition
            class="fixed top-5 left-1/2 -translate-x-1/2 z-[300] px-6 py-3 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-3"
            :class="saveStatus === 'success' ? 'bg-green-600 text-white' : (saveStatus === 'error' ? 'bg-red-600 text-white' : 'bg-blue-600 text-white')">
            <i
                :class="saveStatus === 'success' ? 'fa-solid fa-check-circle' : (saveStatus === 'error' ? 'fa-solid fa-circle-xmark' : 'fa-solid fa-spinner fa-spin')"></i>
            <span x-text="saveMessage"></span>
        </div>

        {{-- ===== Main Table ===== --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="p-4 font-bold w-16 text-center border-l border-slate-700">ت.</th>
                        <th class="p-4 font-bold border-l border-slate-700" style="width:25%">المعيار الفرعي</th>
                        <th class="p-4 font-bold w-24 text-center border-l border-slate-700">الدرجة</th>
                        <th class="p-4 font-bold border-l border-slate-700" style="width:27%">فرص التحسين</th>
                        <th class="p-4 font-bold" style="width:30%">رد المؤسسة بالموافقة أو عدم الموافقة مع الأسباب</th>
                    </tr>
                </thead>

                <template x-for="(main, mIndex) in criteriaData" :key="'m'+main.main_id">
                    <tbody class="divide-y divide-gray-200 border-b-4 border-slate-300">

                        {{-- Main Standard Header --}}
                        <tr class="bg-blue-50">
                            <td colspan="5" class="p-4 font-extrabold text-blue-900 text-base">
                                <i class="fa-solid fa-layer-group me-2 text-blue-600"></i>
                                <span x-text="main.main_title"></span>
                            </td>
                        </tr>

                        {{-- Sub-Standards --}}
                        <template x-for="(sub, sIndex) in main.subs" :key="'s'+sub.sub_id">
                            <tr class="hover:bg-gray-50 transition-colors"
                                :class="sub.decision === null ? 'bg-amber-50/40' : ''">

                                {{-- Number --}}
                                <td class="p-4 text-center font-black text-gray-700 border-l border-gray-200 bg-gray-50 text-sm"
                                    x-text="sub.std_number + '.' + sub.number"></td>

                                {{-- Sub-standard Name --}}
                                <td class="p-4 border-l border-gray-200 align-top font-semibold text-gray-800 text-sm leading-relaxed"
                                    x-text="sub.name"></td>

                                {{-- Degree (read-only) --}}
                                <td class="p-4 border-l border-gray-200 align-top text-center bg-gray-50/50">
                                    <div class="relative group">
                                        <div class="input-field readonly-field text-center font-black text-lg py-2 rounded-lg"
                                            :class="sub.average === null ? 'text-gray-400' : 'text-blue-700'"
                                            x-text="sub.average !== null ? sub.average.toFixed(2) : '—'"></div>
                                        <div
                                            class="absolute -top-2 -right-2 text-[9px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            تلقائي</div>
                                    </div>
                                </td>

                                {{-- Improvement Opportunities (read-only) --}}
                                <td class="p-4 border-l border-gray-200 align-top bg-gray-50/50">
                                    <div class="relative group">
                                        <template x-if="sub.improvements && sub.improvements.length > 0">
                                            <ul class="list-none space-y-1.5 text-sm text-gray-700">
                                                <template x-for="(point, pIdx) in sub.improvements" :key="pIdx">
                                                    <li class="flex items-start gap-2">
                                                        <span
                                                            class="mt-1 shrink-0 w-4 h-4 rounded-full bg-orange-100 border border-orange-200 flex items-center justify-center text-orange-600"
                                                            style="font-size:10px" x-text="pIdx+1"></span>
                                                        <span x-text="point" class="leading-relaxed"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </template>
                                        <template x-if="!sub.improvements || sub.improvements.length === 0">
                                            <span class="text-gray-400 text-xs italic">لا توجد فرص تحسين مسجلة</span>
                                        </template>
                                        <div
                                            class="absolute -top-2 -right-2 text-[9px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            تلقائي</div>
                                    </div>
                                </td>

                                {{-- Institution Response --}}
                                <td class="p-4 align-top">

                                    @if($isEditMode)
                                        {{-- ===== EDIT MODE ===== --}}
                                        <div class="flex items-center p-1 bg-slate-100 rounded-xl w-fit border border-slate-200 mb-3">
                                            <button type="button"
                                                @click="sub.decision = (sub.decision === 'approved' ? null : 'approved')"
                                                :class="sub.decision === 'approved' ? 'bg-white text-green-700 shadow-sm ring-1 ring-green-200' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-black transition-all">
                                                <i class="fa-solid fa-circle-check" :class="sub.decision === 'approved' ? 'text-green-500' : 'text-slate-400'"></i>
                                                موافقة
                                            </button>
                                            <button type="button"
                                                @click="sub.decision = (sub.decision === 'rejected' ? null : 'rejected')"
                                                :class="sub.decision === 'rejected' ? 'bg-white text-red-700 shadow-sm ring-1 ring-red-200' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-black transition-all">
                                                <i class="fa-solid fa-circle-xmark" :class="sub.decision === 'rejected' ? 'text-red-500' : 'text-slate-400'"></i>
                                                عدم موافقة
                                            </button>
                                        </div>

                                        {{-- Unanswered indicator --}}
                                        <template x-if="sub.decision === null">
                                            <div class="text-xs text-amber-600 font-bold flex items-center gap-1 mb-2">
                                                <i class="fa-solid fa-triangle-exclamation"></i> يرجى تحديد الرد
                                            </div>
                                        </template>

                                        {{-- Rejection Reasons (shown only if rejected) --}}
                                        <div x-show="sub.decision === 'rejected'" x-collapse
                                            class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl shadow-inner space-y-2">
                                            <p class="text-xs font-black text-red-700 mb-2">
                                                <i class="fa-solid fa-circle-info me-1"></i>أسباب عدم الموافقة: <span
                                                    class="text-red-500">*</span>
                                            </p>
                                            <template x-for="(point, pointIdx) in sub.rejection_points" :key="pointIdx">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-500 text-xs font-black w-5 shrink-0"
                                                        x-text="(pointIdx + 1) + '.'"></span>
                                                    <textarea x-model="sub.rejection_points[pointIdx]"
                                                        class="flex-1 bg-white border border-gray-300 rounded-lg px-4 py-3 text-sm text-slate-900 focus:ring-1 focus:ring-red-400 focus:border-red-400 outline-none transition-all font-cairo resize-none overflow-hidden min-h-[100px]"
                                                        @input="$el.style.height = '100px'; $el.style.height = ($el.scrollHeight > 100 ? $el.scrollHeight : 100) + 'px'"
                                                        x-init="$nextTick(() => { $el.style.height = '100px'; if ($el.scrollHeight > 100) $el.style.height = $el.scrollHeight + 'px' })"
                                                        placeholder="اكتب السبب بوضوح..."></textarea>
                                                    <button @click="removePoint(mIndex, sIndex, pointIdx)"
                                                        class="text-gray-400 hover:text-red-600 transition-colors p-1 bg-white border border-gray-200 rounded-lg hover:border-red-200 shrink-0"
                                                        title="حذف">
                                                        <i class="fa-solid fa-xmark text-xs"></i>
                                                    </button>
                                                </div>
                                            </template>
                                            <button @click="addPoint(mIndex, sIndex)"
                                                class="mt-1 flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-800 transition-colors bg-white hover:bg-red-50 px-3 py-1.5 rounded-lg w-full justify-center border border-red-200 border-dashed cursor-pointer">
                                                <i class="fa-solid fa-plus"></i> إضافة سبب آخر
                                            </button>
                                        </div>
                                    @else
                                        {{-- ===== READ-ONLY MODE ===== --}}
                                        <template x-if="sub.decision === 'approved'">
                                            <div class="flex items-center gap-2 text-green-700 font-black text-sm">
                                                <i class="fa-solid fa-circle-check text-xl text-green-500"></i> موافقة
                                            </div>
                                        </template>
                                        <template x-if="sub.decision === 'rejected'">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2 text-red-700 font-black text-sm">
                                                    <i class="fa-solid fa-circle-xmark text-xl text-red-500"></i> عدم موافقة
                                                </div>
                                                <ul class="list-none space-y-1 ps-1 mt-1">
                                                    <template
                                                        x-for="(point, pIdx) in sub.rejection_points.filter(p => p.trim() !== '')"
                                                        :key="pIdx">
                                                        <li class="flex items-start gap-2 text-sm text-gray-700">
                                                            <span
                                                                class="mt-1 shrink-0 w-4 h-4 rounded-full bg-red-100 border border-red-200 flex items-center justify-center text-red-600"
                                                                style="font-size:9px" x-text="pIdx+1"></span>
                                                            <span x-text="point" class="leading-relaxed"></span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>
                                        <template x-if="sub.decision === null">
                                            <span class="text-gray-400 text-xs italic">لم يُرد بعد</span>
                                        </template>
                                    @endif

                                </td>
                            </tr>
                        </template>

                    </tbody>
                </template>
            </table>
        </div>

        @if($isEditMode)
            {{-- ===== Save Actions Bar ===== --}}
            <div
                class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center justify-between gap-4 sticky bottom-4 z-10">
                <div class="text-xs text-gray-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                    يمكنك الحفظ المؤقت والعودة لاحقاً لإكمال الرد. يجب الرد على جميع المعايير قبل إرسال النموذج.
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button @click="clearAllDecisions()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition border border-gray-200">
                        <i class="fa-solid fa-rotate-left"></i> مسح الردود
                    </button>
                    <button @click="saveData()" :disabled="isSaving"
                        :class="isSaving ? 'opacity-60 cursor-wait' : 'hover:bg-blue-700 shadow-blue-500/20'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-black transition shadow-lg">
                        <i :class="isSaving ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-floppy-disk'"></i>
                        <span x-text="isSaving ? 'جاري الحفظ...' : 'حفظ التغييرات'"></span>
                    </button>
                </div>
            </div>
        @endif

    </div>

    <script>
        function form9App() {
            const serverData = @json($alpineData);
            const isEditMode = @json($isEditMode);
            const saveUrl = "{{ route('requests.stage_seven.form9.save', $accreditationRequest) }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            return {
                criteriaData: JSON.parse(JSON.stringify(serverData)), // deep clone
                isSaving: false,
                saveStatus: '',   // '' | 'saving' | 'success' | 'error'
                saveMessage: '',

                get allAnswered() {
                    return this.criteriaData.every(m => m.subs.every(s => s.decision !== null));
                },

                get pendingCount() {
                    let count = 0;
                    this.criteriaData.forEach(m => m.subs.forEach(s => { if (s.decision === null) count++; }));
                    return count;
                },

                init() {
                    // No localStorage — all data comes from the server.
                },

                addPoint(mIndex, sIndex) {
                    this.criteriaData[mIndex].subs[sIndex].rejection_points.push('');
                },

                removePoint(mIndex, sIndex, pointIndex) {
                    const points = this.criteriaData[mIndex].subs[sIndex].rejection_points;
                    if (points.length > 1) {
                        points.splice(pointIndex, 1);
                    } else {
                        points[0] = '';
                    }
                },

                clearAllDecisions() {
                    if (!confirm('هل أنت متأكد من رغبتك في مسح جميع ردود المؤسسة؟')) return;
                    this.criteriaData.forEach(m => m.subs.forEach(s => {
                        s.decision = null;
                        s.rejection_points = [''];
                    }));
                },

                /**
                 * Build and validate the JSON payload, then POST to saveForm9 endpoint.
                 */
                async saveData() {
                    // Validate: if decision is 'rejected', at least one non-empty reason required
                    let validationErrors = [];
                    this.criteriaData.forEach(m => m.subs.forEach(s => {
                        if (s.decision === 'rejected') {
                            const hasReason = s.rejection_points.some(p => p.trim() !== '');
                            if (!hasReason) {
                                validationErrors.push(m.main_title + ' / ' + s.name);
                            }
                        }
                    }));

                    if (validationErrors.length > 0) {
                        this.showToast('error', 'يجب إدخال سبب واحد على الأقل للمعايير: ' + validationErrors.join('، '));
                        return;
                    }

                    this.isSaving = true;
                    this.showToast('saving', 'جاري حفظ البيانات...');

                    // Build clean payload — one entry per sub-standard
                    const payload = [];
                    this.criteriaData.forEach(m => m.subs.forEach(s => {
                        payload.push({
                            sub_id: s.sub_id,
                            decision: s.decision,
                            rejection_points: s.decision === 'rejected'
                                ? s.rejection_points.filter(p => p.trim() !== '')
                                : [],
                        });
                    }));

                    try {
                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('form9_data', JSON.stringify(payload));

                        const response = await fetch(saveUrl, {
                            method: 'POST',
                            body: formData,
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast('success', result.message || 'تم حفظ البيانات بنجاح.');
                        } else {
                            this.showToast('error', result.message || 'حدث خطأ أثناء الحفظ.');
                        }
                    } catch (e) {
                        this.showToast('error', 'تعذّر الاتصال بالخادم، يرجى المحاولة مجدداً.');
                    } finally {
                        this.isSaving = false;
                    }
                },

                showToast(type, message) {
                    this.saveStatus = type;
                    this.saveMessage = message;
                    if (type === 'success') {
                        setTimeout(() => { this.saveStatus = ''; }, 4000);
                    } else if (type === 'error') {
                        setTimeout(() => { this.saveStatus = ''; }, 6000);
                    }
                },
            };
        }
    </script>
</body>

</html>