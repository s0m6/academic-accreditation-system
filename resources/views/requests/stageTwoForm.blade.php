<!doctype html>
<html lang="ar" dir="rtl" x-data="stageTwoApp()" x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نظام إدارة بيانات البرنامج الأكاديمي</title>




    {{-- Vite-compiled assets (Tailwind v4 + FlyonUI) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-(--bg-main) text-(--text-primary) min-h-screen font-sans">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


{{-- Dark mode toggle (floating) --}}
<button onclick="document.documentElement.classList.toggle('dark')" type="button" data-keep
    class="fixed bottom-6 left-6 z-50 w-11 h-11 rounded-full bg-(--surface-card) border border-(--border-primary) shadow-lg flex items-center justify-center hover:scale-110 transition-all cursor-pointer">
    <i class="fa-solid fa-circle-half-stroke text-(--text-secondary)"></i>
</button>

<div class="max-w-6xl mx-auto p-4 md:p-8 space-y-6">

    {{-- ═══════ HEADER ═══════ --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="px-6 py-5 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-file-lines text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-(--text-primary)">نظام إدارة بيانات البرنامج الأكاديمي</h1>
                    <p class="text-xs text-(--text-secondary) mt-0.5">تعبئة البيانات الأساسية للمرحلة الثانية من طلب الاعتماد</p>
                </div>
            </div>
            <button type="button" @click="handleReturnToDashboard()" data-keep
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all no-underline cursor-pointer">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للوحة الطلب
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="px-6 py-3 bg-(--bg-main) border-t border-(--border-primary)">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-(--text-secondary)">نسبة الإكمال</span>
                <span class="text-xs font-bold text-brand-600 dark:text-brand-400" x-text="progress + '% مكتمل'"></span>
            </div>
            <div class="w-full h-2 bg-(--border-primary) rounded-full overflow-hidden">
                <div class="h-full bg-brand-600 rounded-full transition-all duration-500 ease-out" :style="'width:' + progress + '%'"></div>
            </div>
        </div>
    </div>

    {{-- Error Banner --}}
    <div x-show="errors.length > 0" x-transition
        class="p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-lg mt-0.5 shrink-0"></i>
            <div>
                <p class="font-bold mb-1">توجد أخطاء في النموذج:</p>
                <ul class="text-sm space-y-1 list-disc list-inside">
                    <template x-for="err in errors" :key="err">
                        <li x-text="err"></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    {{-- ═══════ TAB NAVIGATION ═══════ --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="flex border-b border-(--border-primary) bg-(--bg-main) overflow-x-auto no-scrollbar">
            <template x-for="(tab, idx) in tabs" :key="idx">
                <button @click="currentTab = idx" type="button" data-keep="true"
                    :class="currentTab === idx
                        ? tab.activeClasses + ' bg-(--surface-card)'
                        : 'border-transparent text-(--text-secondary) hover:text-(--text-primary) hover:bg-(--surface-card)/50'"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap cursor-pointer">
                    <i :class="tab.icon"></i>
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </div>

        {{-- ═══════ TAB 1: القرارات ═══════ --}}
        <div x-show="currentTab === 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-500/20 shrink-0">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h2 class="text-lg font-bold text-(--text-primary)">القرارات المتعلقة بالبرنامج</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-(--bg-main) text-(--text-secondary)">
                            <th class="px-4 py-3 text-start font-bold">القرار</th>
                            <th class="px-4 py-3 text-start font-bold">رقم القرار</th>
                            <th class="px-4 py-3 text-start font-bold">الجهة</th>
                            <th class="px-4 py-3 text-start font-bold">تاريخ القرار</th>
                            <th class="px-4 py-3 text-start font-bold">المرفق (PDF)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @php
                            $decisionsList = [
                                1 => 'قرار إنشاء البرنامج',
                                2 => 'قرار الطاقة الاستيعابية',
                                3 => 'قرار قبول أول دفعة',
                                4 => 'قرار قبول دفعة العام الماضي',
                                5 => 'قرار قبول دفعة العام قبل الماضي',
                                6 => 'قرار اعتماد أحدث خطة دراسية',
                                7 => 'محضر قرار تخرج دفعة العام الحالي',
                                8 => 'قرار تقديم طلب الاعتماد الأكاديمي',
                            ];
                            $files = $formSubmission->form_data['decision_files'] ?? [];
                        @endphp

                        @foreach($decisionsList as $i => $label)
                        <tr class="hover:bg-(--bg-main)/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-(--text-primary) whitespace-nowrap">{{ $label }}</td>
                            <td class="px-4 py-3">
                                <input type="text" data-decision="{{ $i }}" data-field="number"
                                    class="w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all placeholder:text-(--text-secondary)/50"
                                    placeholder="رقم القرار" @input="markChanged()">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" data-decision="{{ $i }}" data-field="authority"
                                    class="w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all placeholder:text-(--text-secondary)/50"
                                    placeholder="الجهة المصدرة" @input="markChanged()">
                            </td>
                            <td class="px-4 py-3">
                                <input type="date" data-decision="{{ $i }}" data-field="date"
                                    class="w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all"
                                    @input="markChanged()">
                            </td>
                            <td class="px-4 py-3">
                                <div id="file_container_{{ $i }}">
                                    {{-- Preview Buttons Container --}}
                                    <div id="file_preview_{{ $i }}" class="{{ !empty($files[$i]) ? 'flex' : 'hidden' }} items-center gap-2 flex-wrap">
                                        <a id="file_view_btn_{{ $i }}" href="{{ !empty($files[$i]) ? route('requests.stage_two.view_file', ['accreditationRequest' => $accreditationRequest->id, 'formSubmission' => $formSubmission->id, 'decisionIndex' => $i]) : '#' }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-xs font-bold transition-colors no-underline">
                                            <i class="fa-solid fa-eye"></i> عرض
                                        </a>
                                        <button type="button" @click="confirmRemoveFile({{ $i }})" data-action-area
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i> حذف
                                        </button>
                                        @if(!empty($files[$i]))
                                            <input type="hidden" id="existing_file_path_{{ $i }}" value="{{ $files[$i] }}">
                                        @endif
                                    </div>
                                    
                                    {{-- File Upload Input --}}
                                    <input type="file" id="decision_file_{{ $i }}" accept="application/pdf" data-action-area
                                        class="{{ !empty($files[$i]) ? 'hidden' : 'block' }} w-full text-sm text-(--text-secondary) file:me-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-(--border-primary) file:text-sm file:font-bold file:bg-(--bg-main) file:text-(--text-primary) hover:file:bg-(--surface-card) file:transition-all file:cursor-pointer"
                                        @change="handleFileSelect(event, {{ $i }}); markChanged()">
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Notes Section for Decisions --}}
            <div class="rounded-xl border border-(--border-primary) bg-(--surface-card) p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-(--text-secondary)"></i>
                    <h3 class="font-bold text-(--text-primary)">ملاحظات حول القرارات</h3>
                </div>
                <div class="space-y-2" id="decisionsNotesContainer">
                </div>
                <button type="button" onclick="addNoteRow('decisionsNotesContainer', 'decisions')" data-action-area
                    class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer px-1">
                    <i class="fa-solid fa-plus-circle"></i> إضافة ملاحظة
                </button>
            </div>
        </div>

        {{-- TAB 2: Student Stats --}}
        <div x-show="currentTab === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 shrink-0">
                    <i class="fa-solid fa-chart-bar"></i>
                </div>
                <h2 class="text-lg font-bold text-(--text-primary)">البيانات الإحصائية للطلاب</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-(--bg-main) text-(--text-secondary)">
                            <th colspan="2" class="px-4 py-3 text-start font-bold">الفئة</th>
                            <th class="px-4 py-3 text-center font-bold">العام الماضي</th>
                            <th class="px-4 py-3 text-center font-bold">العام الحالي</th>
                            <th class="px-4 py-3 text-center font-bold">المتوقع للعام القادم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @php
                            $studentSections = [
                                'planned' => ['label' => 'عدد الطلبة المخطط التحاقهم بالبرنامج', 'rows' => [
                                    'general' => 'قبول عام', 'special' => 'قبول خاص', 'international' => 'قبول دولي'
                                ]],
                                'total' => ['label' => 'العدد الكلي للطلاب الملتحقين بالبرنامج', 'rows' => [
                                    'general' => 'قبول عام', 'special' => 'قبول خاص', 'international' => 'قبول دولي'
                                ]],
                                'average' => ['label' => 'متوسط عدد الطلبة في الشعبة الدراسية', 'rows' => [
                                    'male' => 'ذكور', 'female' => 'إناث'
                                ], 'hasTotal' => true],
                                'graduates_higher_ed' => ['label' => 'عدد الخريجين الذين يواصلون تعليمهم في الدراسات العليا', 'rows' => [
                                    'male' => 'ذكور', 'female' => 'إناث'
                                ], 'hasTotal' => true],
                                'graduates_employed' => ['label' => 'عدد الخريجين الذين التحقوا بوظائف', 'rows' => [
                                    'male' => 'ذكور', 'female' => 'إناث'
                                ], 'hasTotal' => true],
                            ];
                        @endphp

                        @foreach($studentSections as $sectionKey => $section)
                            @php $rowCount = count($section['rows']) + (isset($section['hasTotal']) ? 1 : 0); @endphp
                            @foreach($section['rows'] as $rowKey => $rowLabel)
                                <tr class="hover:bg-(--bg-main)/50 transition-colors">
                                    @if($loop->first)
                                        <td rowspan="{{ $rowCount }}" class="px-4 py-3 font-bold text-(--text-primary) border-e border-(--border-primary) bg-(--bg-main)/30 align-top">{{ $section['label'] }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-(--text-secondary)">{{ $rowLabel }}</td>
                                    @foreach(['past', 'current', 'next'] as $period)
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" data-student="{{ $sectionKey }}" data-row="{{ $rowKey }}" data-period="{{ $period }}"
                                                class="w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all"
                                                @input="calculateStudentTotal('{{ $sectionKey }}'); markChanged()">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @if(isset($section['hasTotal']))
                                <tr class="bg-(--bg-main)/50">
                                    <td class="px-4 py-3 font-bold text-(--text-primary)">الإجمالي</td>
                                    @foreach(['past', 'current', 'next'] as $period)
                                        <td class="px-4 py-3">
                                            <input type="text" readonly data-student-total="{{ $sectionKey }}" data-period="{{ $period }}" value="0"
                                                class="w-full bg-(--border-primary)/30 border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 text-center font-bold">
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Student Notes --}}
            <div class="rounded-xl border border-(--border-primary) bg-(--surface-card) p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-(--text-secondary)"></i>
                    <h3 class="font-bold text-(--text-primary)">ملاحظات حول الطلاب</h3>
                </div>
                <div class="space-y-2" id="studentsNotesContainer">
                </div>
                <button type="button" onclick="addNoteRow('studentsNotesContainer', 'students')" data-action-area class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer px-1"><i class="fa-solid fa-plus-circle"></i> إضافة ملاحظة</button>
            </div>
        </div>

        {{-- TAB 3: Faculty Stats --}}
        <div x-show="currentTab === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-100 dark:border-violet-500/20 shrink-0">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h2 class="text-lg font-bold text-(--text-primary)">البيانات الإحصائية لأعضاء هيئة التدريس</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-(--bg-main) text-(--text-secondary)">
                            <th class="px-4 py-3 text-start font-bold">الدرجة العلمية</th>
                            <th class="px-4 py-3 text-center font-bold">ذكور</th>
                            <th class="px-4 py-3 text-center font-bold">إناث</th>
                            <th class="px-4 py-3 text-center font-bold">متوسط العبء التدريسي</th>
                            <th class="px-4 py-3 text-center font-bold">عدد غير المتفرغين</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)">
                        @php
                            $facultyRanks = [
                                'professor' => 'أستاذ', 'associate' => 'أستاذ مشارك', 'assistant' => 'أستاذ مساعد',
                                'lecturer' => 'مدرس', 'teaching_assistant' => 'معيد',
                            ];
                        @endphp
                        @foreach($facultyRanks as $rankKey => $rankLabel)
                        <tr class="hover:bg-(--bg-main)/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-(--text-primary)">{{ $rankLabel }}</td>
                            @foreach(['male', 'female', 'load', 'parttime'] as $col)
                                <td class="px-4 py-3">
                                    <input type="number" min="0" {{ $col === 'load' ? 'step=0.1' : '' }} data-faculty="{{ $rankKey }}" data-col="{{ $col }}"
                                        class="w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all"
                                        @input="calculateFacultyTotals(); markChanged()">
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-(--bg-main)/50 font-bold">
                            <td class="px-4 py-3 text-(--text-primary)">الإجمالي</td>
                            @foreach(['male', 'female', 'load', 'parttime'] as $col)
                                <td class="px-4 py-3">
                                    <input type="text" readonly data-faculty-total="{{ $col }}" value="0"
                                        class="w-full bg-(--border-primary)/30 border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 text-center font-bold">
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Faculty Notes --}}
            <div class="rounded-xl border border-(--border-primary) bg-(--surface-card) p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-note-sticky text-(--text-secondary)"></i>
                    <h3 class="font-bold text-(--text-primary)">ملاحظات حول الهيئة التدريسية</h3>
                </div>
                <div class="space-y-2" id="facultyNotesContainer">
                </div>
                <button type="button" onclick="addNoteRow('facultyNotesContainer', 'faculty')" data-action-area class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer px-1"><i class="fa-solid fa-plus-circle"></i> إضافة ملاحظة</button>
            </div>
        </div>

        {{-- TAB 4: Faculty Members Details --}}
        <div x-show="currentTab === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-500/20 shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h2 class="text-lg font-bold text-(--text-primary)">بيانات أعضاء هيئة التدريس التفصيلية</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
                <table class="w-full text-sm" id="staffTable">
                    <thead>
                        <tr class="bg-(--bg-main) text-(--text-secondary)">
                            <th class="px-4 py-3 text-start font-bold">الاسم</th>
                            <th class="px-4 py-3 text-start font-bold">الدرجة العلمية</th>
                            <th class="px-4 py-3 text-start font-bold">التخصص العام</th>
                            <th class="px-4 py-3 text-start font-bold">التخصص الدقيق</th>
                            <th class="px-4 py-3 text-start font-bold">بلد التخرج</th>
                            <th class="px-4 py-3 text-start font-bold">عام التعيين</th>
                            <th class="px-4 py-3 text-center font-bold w-20" data-action-area>إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--border-primary)" id="staffTableBody">
                        <tr data-staff-id="1" class="hover:bg-(--bg-main)/50 transition-colors">
                            <td class="px-4 py-3"><input type="text" placeholder="أدخل الاسم" class="staff-name w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50" @input="markChanged()"></td>
                            <td class="px-4 py-3">
                                <select class="staff-degree w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all" @input="markChanged()">
                                    <option value="">اختر الدرجة...</option>
                                    <option value="prof">أستاذ</option>
                                    <option value="assoc">أستاذ مشارك</option>
                                    <option value="assist">أستاذ مساعد</option>
                                    <option value="lecturer">مدرس</option>
                                    <option value="ta">معيد</option>
                                </select>
                            </td>
                            <td class="px-4 py-3"><input type="text" placeholder="التخصص العام" class="staff-major w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50" @input="markChanged()"></td>
                            <td class="px-4 py-3"><input type="text" placeholder="التخصص الدقيق" class="staff-minor w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50" @input="markChanged()"></td>
                            <td class="px-4 py-3"><input type="text" placeholder="بلد التخرج" class="staff-country w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50" @input="markChanged()"></td>
                            <td class="px-4 py-3"><input type="number" placeholder="سنة" min="1900" max="2100" class="staff-year w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all" @input="markChanged()"></td>
                            <td class="px-4 py-3 text-center" data-action-area>
                                <button type="button" onclick="deleteStaffRow(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer" data-action-area><i class="fa-solid fa-trash-can"></i> حذف</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addStaffRow()" data-action-area class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition-colors cursor-pointer px-1"><i class="fa-solid fa-plus-circle"></i> إضافة عضو جديد</button>
        </div>

        {{-- Submit Section --}}
        <div class="px-6 py-5 bg-(--bg-main) border-t border-(--border-primary) flex flex-col items-center gap-4" data-action-area>
            <div class="flex items-center gap-3 flex-wrap justify-center">
                <button type="button" @click="submitFormData()"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold shadow-brand transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات
                </button>
                <button type="button" @click="resetForm()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition-all cursor-pointer">
                    <i class="fa-solid fa-rotate-left"></i> تفريغ النموذج
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Exit Confirmation Modal --}}
<div x-show="showExitModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showExitModal = false"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div x-show="showExitModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-(--surface-card) p-6 text-right align-middle shadow-xl transition-all border border-(--border-primary)">
            
            <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center mx-auto mb-4 border border-amber-100 dark:border-amber-500/20">
                <i class="fa-solid fa-triangle-exclamation text-xl text-amber-600 dark:text-amber-400"></i>
            </div>
            
            <h3 class="text-lg font-bold text-center text-(--text-primary) mb-2">لديك تغييرات غير محفوظة</h3>
            <p class="text-sm text-center text-(--text-secondary) mb-6">هل ترغب في حفظ التغييرات قبل المغادرة؟ إذا غادرت دون حفظ ستفقد التعديلات الأخيرة.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button type="button" @click="showExitModal = false; submitFormData('{{ route('requests.show', $accreditationRequest) }}')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> حفظ ومغادرة
                </button>
                <button type="button" @click="discardAndLeave()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 px-5 py-2.5 text-sm font-bold transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> مغادرة دون حفظ
                </button>
                <button type="button" @click="showExitModal = false"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-(--surface-card) border border-(--border-primary) px-5 py-2.5 text-sm font-bold text-(--text-primary) hover:bg-(--bg-main) transition-all cursor-pointer">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-(--surface-card) p-6 text-right align-middle shadow-xl transition-all border border-(--border-primary)">
            
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-4 border border-red-100 dark:border-red-500/20">
                <i class="fa-solid fa-trash-can text-xl text-red-600"></i>
            </div>
            
            <h3 class="text-lg font-bold text-center text-(--text-primary) mb-2">تأكيد حذف المرفق</h3>
            <p class="text-sm text-center text-(--text-secondary) mb-6">هل أنت متأكد من حذف هذا المرفق؟ سيتم حذف الملف نهائياً ولا يمكن التراجع عن هذا الإجراء بعد حفظ التغييرات.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button type="button" @click="finalizeRemoveFile()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i> تأكيد الحذف
                </button>
                <button type="button" @click="showDeleteModal = false"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-(--surface-card) border border-(--border-primary) px-6 py-2.5 text-sm font-bold text-(--text-primary) hover:bg-(--bg-main) transition-all cursor-pointer">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Notification Container --}}
<div id="notificationArea" class="fixed top-4 left-4 z-50 space-y-2"></div>

<script>
// ==================== Input field CSS class constants ====================
const INPUT_CLASS = 'w-full bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50';
const INPUT_CLASS_CENTER = INPUT_CLASS + ' text-center';
const SELECT_CLASS = INPUT_CLASS;

// ==================== Alpine.js Application ====================
function stageTwoApp() {
    return {
        currentTab: 0,
        hasChanges: false,
        showExitModal: false,
        progress: 0,
        errors: [],
        tabs: [
            { label: 'القرارات', icon: 'fa-solid fa-file-signature', activeClasses: 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' },
            { label: 'إحصائيات الطلاب', icon: 'fa-solid fa-chart-bar', activeClasses: 'border-emerald-600 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400' },
            { label: 'إحصائيات الهيئة', icon: 'fa-solid fa-chalkboard-user', activeClasses: 'border-violet-600 text-violet-600 dark:text-violet-400 dark:border-violet-400' },
            { label: 'بيانات الهيئة', icon: 'fa-solid fa-users', activeClasses: 'border-amber-600 text-amber-600 dark:text-amber-400 dark:border-amber-400' },
        ],
        showDeleteModal: false,
        deleteId: null,

        // Start File Deletion Flow
        confirmRemoveFile(id) {
            this.deleteId = id;
            this.showDeleteModal = true;
        },

        // Finalize File Deletion
        finalizeRemoveFile() {
            if (this.deleteId !== null) {
                removeFileLogic(this.deleteId);
                this.showDeleteModal = false;
                this.deleteId = null;
            }
        },

        // Mark form as changed
        markChanged() {
            this.hasChanges = true;
            this.updateProgress();
        },

        // Calculate progress percentage
        updateProgress() {
            const fields = document.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([readonly]), select');
            let filled = 0;
            fields.forEach(f => { if (f.value && f.value.trim()) filled++; });
            this.progress = Math.round((filled / (fields.length || 1)) * 100);
        },

        // Calculate student section totals
        calculateStudentTotal(section) {
            ['past', 'current', 'next'].forEach(period => {
                const inputs = document.querySelectorAll(`[data-student="${section}"][data-period="${period}"]`);
                let sum = 0;
                inputs.forEach(inp => { sum += Math.max(0, Number(inp.value) || 0); });
                const totalField = document.querySelector(`[data-student-total="${section}"][data-period="${period}"]`);
                if (totalField) totalField.value = sum;
            });
        },

        // Calculate faculty totals
        calculateFacultyTotals() {
            ['male', 'female', 'load', 'parttime'].forEach(col => {
                const inputs = document.querySelectorAll(`[data-faculty][data-col="${col}"]`);
                let sum = 0;
                inputs.forEach(inp => { sum += Math.max(0, Number(inp.value) || 0); });
                const totalField = document.querySelector(`[data-faculty-total="${col}"]`);
                if (totalField) totalField.value = col === 'load' ? sum.toFixed(2) : sum;
            });
        },

        // Validate form data
        validateForm() {
            const errors = [];
            document.querySelectorAll('input[type="date"]').forEach(f => {
                if (f.value && isNaN(new Date(f.value).getTime())) errors.push('تاريخ غير صحيح');
            });
            document.querySelectorAll('input[type="number"]').forEach(f => {
                if (f.value && Number(f.value) < 0) errors.push('لا يمكن إدخال أرقام سالبة');
            });
            return errors;
        },

        // Collect all form data into clean JSON structure
        collectFormData() {
            // Decisions
            const decisions = [];
            for (let i = 1; i <= 8; i++) {
                const num = document.querySelector(`[data-decision="${i}"][data-field="number"]`);
                const auth = document.querySelector(`[data-decision="${i}"][data-field="authority"]`);
                const date = document.querySelector(`[data-decision="${i}"][data-field="date"]`);
                decisions.push({
                    id: i,
                    number: num ? num.value : '',
                    authority: auth ? auth.value : '',
                    date: date ? date.value : ''
                });
            }

            // Student stats
            const studentStats = {};
            const studentSections = ['planned', 'total', 'average', 'graduates_higher_ed', 'graduates_employed'];
            studentSections.forEach(section => {
                studentStats[section] = {};
                document.querySelectorAll(`[data-student="${section}"]`).forEach(inp => {
                    const row = inp.dataset.row;
                    const period = inp.dataset.period;
                    if (!studentStats[section][row]) studentStats[section][row] = {};
                    studentStats[section][row][period] = Number(inp.value) || 0;
                });
            });

            // Faculty stats
            const facultyStats = {};
            const ranks = ['professor', 'associate', 'assistant', 'lecturer', 'teaching_assistant'];
            ranks.forEach(rank => {
                facultyStats[rank] = {};
                ['male', 'female', 'load', 'parttime'].forEach(col => {
                    const inp = document.querySelector(`[data-faculty="${rank}"][data-col="${col}"]`);
                    facultyStats[rank][col] = inp ? Number(inp.value) || 0 : 0;
                });
            });

            // Faculty members
            const facultyMembers = [];
            document.querySelectorAll('#staffTableBody tr[data-staff-id]').forEach(row => {
                facultyMembers.push({
                    name: row.querySelector('.staff-name')?.value || '',
                    degree: row.querySelector('.staff-degree')?.value || '',
                    major: row.querySelector('.staff-major')?.value || '',
                    minor: row.querySelector('.staff-minor')?.value || '',
                    country: row.querySelector('.staff-country')?.value || '',
                    year: row.querySelector('.staff-year')?.value || ''
                });
            });

            // Notes
            const notes = {};
            ['decisions', 'students', 'faculty'].forEach(group => {
                const container = document.getElementById(group + 'NotesContainer');
                if (!container) return;
                notes[group] = [];
                container.querySelectorAll('[data-note-group="' + group + '"]').forEach(inp => {
                    if (inp.value.trim()) notes[group].push(inp.value.trim());
                });
            });

            return { decisions, student_stats: studentStats, faculty_stats: facultyStats, faculty_members: facultyMembers, notes };
        },

        // Return to Dashboard logic
        handleReturnToDashboard() {
            if (this.hasChanges) {
                this.showExitModal = true;
            } else {
                window.location.href = "{{ route('requests.show', $accreditationRequest) }}";
            }
        },

        // Discard temp files and leave
        async discardAndLeave() {
            // Find any temp files currently on the page that were newly uploaded
            const tempPaths = [];
            for (let i = 1; i <= 8; i++) {
                const existingPathInput = document.getElementById('existing_file_path_' + i);
                if (existingPathInput && existingPathInput.value.startsWith('temp_files/')) {
                    tempPaths.push(existingPathInput.value);
                }
            }

            if (tempPaths.length > 0) {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                tempPaths.forEach(path => formData.append('paths[]', path));
                
                try {
                    // Fire and forget cleanup
                    await fetch("{{ route('temp_files.cleanup') }}", { method: 'POST', body: formData });
                } catch(e) {}
            }
            
            // Bypass native beforeunload listener
            this.hasChanges = false;
            
            window.location.href = "{{ route('requests.show', $accreditationRequest) }}";
        },

        // Submit form data to server
        async submitFormData(redirectUrl = null) {
            const validationErrors = this.validateForm();
            if (validationErrors.length > 0) {
                this.errors = validationErrors;
                window.scrollTo(0, 0);
                return;
            }
            this.errors = [];

            const jsonData = this.collectFormData();
            const decisionFilesPaths = {};
            const payload = new FormData();
            payload.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Handle file paths and uploads
            for (let i = 1; i <= 8; i++) {
                const existingPath = document.getElementById('existing_file_path_' + i);
                if (existingPath) decisionFilesPaths[i] = existingPath.value;
                const fileInput = document.getElementById('decision_file_' + i);
                if (fileInput && fileInput.files.length > 0) payload.append('decision_file_' + i, fileInput.files[0]);
            }
            jsonData.decision_files_paths = decisionFilesPaths;
            payload.append('json_data', JSON.stringify(jsonData));

            showNotification('جاري حفظ التغييرات...', 'info');

            try {
                const response = await fetch("{{ route('requests.stage_two.save', [$accreditationRequest, $formSubmission]) }}", {
                    method: 'POST', body: payload, headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (result.success) {
                    this.hasChanges = false;
                    showNotification('تم حفظ البيانات بنجاح!', 'success');
                    const targetUrl = redirectUrl || "{{ route('requests.stage', [$accreditationRequest, 'stage_two']) }}";
                    setTimeout(() => { window.location.href = targetUrl; }, 800);
                } else {
                    showNotification('حدث خطأ: ' + (result.message || 'غير معروف'), 'error');
                }
            } catch (error) {
                showNotification('فشل الاتصال بالسيرفر.', 'error');
            }
        },

        // Reset form
        resetForm() {
            if (!confirm('هل أنت متأكد من رغبتك في تفريغ جميع البيانات؟')) return;
            document.querySelectorAll('input:not([readonly]):not([type="hidden"]):not([type="file"]), select, textarea').forEach(f => f.value = '');
            ['average', 'graduates_higher_ed', 'graduates_employed'].forEach(s => this.calculateStudentTotal(s));
            this.calculateFacultyTotals();
            this.updateProgress();
            showNotification('تم تفريغ النموذج', 'success');
        },

        // Load data from server
        loadFromServer() {
            const saved = {!! isset($formSubmission->form_data) && $formSubmission->form_data ? json_encode($formSubmission->form_data, JSON_UNESCAPED_UNICODE) : 'null' !!};
            if (!saved) return;

            try {
                // Load decisions
                if (saved.decisions) {
                    saved.decisions.forEach(d => {
                        const num = document.querySelector(`[data-decision="${d.id}"][data-field="number"]`);
                        const auth = document.querySelector(`[data-decision="${d.id}"][data-field="authority"]`);
                        const date = document.querySelector(`[data-decision="${d.id}"][data-field="date"]`);
                        if (num) num.value = d.number || '';
                        if (auth) auth.value = d.authority || '';
                        if (date) date.value = d.date || '';
                    });
                }

                // Load student stats
                if (saved.student_stats) {
                    Object.entries(saved.student_stats).forEach(([section, rows]) => {
                        Object.entries(rows).forEach(([row, periods]) => {
                            Object.entries(periods).forEach(([period, value]) => {
                                const inp = document.querySelector(`[data-student="${section}"][data-row="${row}"][data-period="${period}"]`);
                                if (inp) inp.value = value || '';
                            });
                        });
                        this.calculateStudentTotal(section);
                    });
                }

                // Load faculty stats
                if (saved.faculty_stats) {
                    Object.entries(saved.faculty_stats).forEach(([rank, cols]) => {
                        Object.entries(cols).forEach(([col, value]) => {
                            const inp = document.querySelector(`[data-faculty="${rank}"][data-col="${col}"]`);
                            if (inp) inp.value = value || '';
                        });
                    });
                    this.calculateFacultyTotals();
                }

                // Load faculty members
                if (saved.faculty_members && saved.faculty_members.length > 0) {
                    const tbody = document.getElementById('staffTableBody');
                    tbody.innerHTML = '';
                    saved.faculty_members.forEach((m, idx) => {
                        const id = idx + 1;
                        const tr = document.createElement('tr');
                        tr.dataset.staffId = id;
                        tr.className = 'hover:bg-(--bg-main)/50 transition-colors';
                        tr.innerHTML = buildStaffRowHTML(id, m);
                        tbody.appendChild(tr);
                    });
                }

                // Load notes
                const isReadonly = {{ isset($readonly) && $readonly ? 'true' : 'false' }};
                
                ['decisions', 'students', 'faculty'].forEach(group => {
                    const arr = saved.notes && saved.notes[group] ? saved.notes[group] : [];
                    const container = document.getElementById(group + 'NotesContainer');
                    if (!container) return;
                    
                    container.innerHTML = '';
                    
                    // Filter out truly empty notes
                    const validNotes = arr.filter(note => note && note.trim() !== '');
                    
                    if (validNotes.length > 0) {
                        validNotes.forEach((note, idx) => {
                            container.appendChild(buildNoteRowElement(group, idx + 1, note));
                        });
                    }
                });
                
                ['decisionsNotesContainer', 'studentsNotesContainer', 'facultyNotesContainer'].forEach(c => updateNoteNumbers(c));

                this.hasChanges = false;
                this.updateProgress();
                showNotification('تم تحميل البيانات بنجاح', 'success');
            } catch (error) {
                console.error('Load error:', error);
            }
        },

        // Initialize
        init() {
            this.loadFromServer();
            
            // Watch for changes to setup history trap
            this.$watch('hasChanges', (isDirty) => {
                if (isDirty) {
                    history.pushState({ trap: true }, null, location.href);
                }
            });

            // Intercept browser back button
            window.addEventListener('popstate', (e) => {
                if (this.hasChanges && !this.showExitModal) {
                    // Push state again to keep user on same page
                    history.pushState({ trap: true }, null, location.href);
                    this.showExitModal = true;
                }
            });

            // Native browser trap for Refresh / Close Tab
            window.addEventListener('beforeunload', (e) => {
                if (this.hasChanges) { e.returnValue = 'لديك تغييرات غير محفوظة'; return e.returnValue; }
            });

            @if(isset($readonly) && $readonly)
                this.$nextTick(() => {
                    document.querySelectorAll('input, select, textarea, button:not([data-keep])').forEach(el => el.disabled = true);
                    document.querySelectorAll('[data-action-area]').forEach(el => el.style.display = 'none');
                });
            @endif
        }
    };
}

// ==================== Global Helper Functions ====================

// Build staff row HTML
function buildStaffRowHTML(id, data = {}) {
    return `
        <td class="px-4 py-3"><input type="text" value="${data.name || ''}" placeholder="أدخل الاسم" class="staff-name ${INPUT_CLASS}" @input="markChanged()"></td>
        <td class="px-4 py-3">
            <select class="staff-degree ${SELECT_CLASS}" @input="markChanged()">
                <option value="">اختر الدرجة...</option>
                <option value="prof" ${data.degree === 'prof' ? 'selected' : ''}>أستاذ</option>
                <option value="assoc" ${data.degree === 'assoc' ? 'selected' : ''}>أستاذ مشارك</option>
                <option value="assist" ${data.degree === 'assist' ? 'selected' : ''}>أستاذ مساعد</option>
                <option value="lecturer" ${data.degree === 'lecturer' ? 'selected' : ''}>مدرس</option>
                <option value="ta" ${data.degree === 'ta' ? 'selected' : ''}>معيد</option>
            </select>
        </td>
        <td class="px-4 py-3"><input type="text" value="${data.major || ''}" placeholder="التخصص العام" class="staff-major ${INPUT_CLASS}" @input="markChanged()"></td>
        <td class="px-4 py-3"><input type="text" value="${data.minor || ''}" placeholder="التخصص الدقيق" class="staff-minor ${INPUT_CLASS}" @input="markChanged()"></td>
        <td class="px-4 py-3"><input type="text" value="${data.country || ''}" placeholder="بلد التخرج" class="staff-country ${INPUT_CLASS}" @input="markChanged()"></td>
        <td class="px-4 py-3"><input type="number" value="${data.year || ''}" min="1900" max="2100" placeholder="سنة" class="staff-year ${INPUT_CLASS}" @input="markChanged()"></td>
        <td class="px-4 py-3 text-center" data-action-area>
            <button type="button" onclick="deleteStaffRow(this)" data-action-area class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer"><i class="fa-solid fa-trash-can"></i> حذف</button>
        </td>`;
}

// Add staff row
function addStaffRow() {
    const tbody = document.getElementById('staffTableBody');
    const newId = Math.max(...Array.from(tbody.querySelectorAll('tr[data-staff-id]')).map(tr => parseInt(tr.dataset.staffId) || 0), 0) + 1;
    const tr = document.createElement('tr');
    tr.dataset.staffId = newId;
    tr.className = 'hover:bg-(--bg-main)/50 transition-colors';
    tr.innerHTML = buildStaffRowHTML(newId);
    tbody.appendChild(tr);
    showNotification('تم إضافة صف جديد', 'success');
}

// Delete staff row
function deleteStaffRow(btn) {
    const tbody = document.getElementById('staffTableBody');
    if (tbody.querySelectorAll('tr').length === 1) { showNotification('يجب الاحتفاظ بصف واحد على الأقل', 'error'); return; }
    btn.closest('tr').remove();
    showNotification('تم حذف الصف', 'success');
}

// Build note row element
function buildNoteRowElement(group, num, value = '') {
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 note-row';
    div.innerHTML = `
        <span class="w-7 h-7 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-bold text-(--text-secondary) shrink-0 row-number">${num}</span>
        <input type="text" data-note-group="${group}" value="${value}" placeholder="أدخل الملاحظة" class="flex-1 bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500/40 transition-all placeholder:text-(--text-secondary)/50" @input="markChanged()">
        <button type="button" onclick="deleteNoteRow(this, '${group}NotesContainer')" data-action-area class="w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors cursor-pointer shrink-0"><i class="fa-solid fa-trash-can text-xs"></i></button>`;
    return div;
}

// Add note row
function addNoteRow(containerId, group) {
    const container = document.getElementById(containerId);
    const placeholder = container.querySelector('.no-notes-placeholder');
    if (placeholder) placeholder.remove();
    
    const num = container.querySelectorAll('.note-row').length + 1;
    container.appendChild(buildNoteRowElement(group, num));
    updateNoteNumbers(containerId);
}

// Delete note row
function deleteNoteRow(btn, containerId) {
    const container = document.getElementById(containerId);
    btn.closest('.note-row').remove();
    updateNoteNumbers(containerId);
    
    // If no notes left, you might want to show a placeholder or just leave it empty.
    // The user can always add one back via the "Add Note" button.
}

// Update note row numbers
function updateNoteNumbers(containerId) {
    const container = document.getElementById(containerId);
    const rows = container.querySelectorAll('.note-row');
    
    if (rows.length === 0) {
        container.innerHTML = `<div class="no-notes-placeholder text-sm text-(--text-secondary) italic px-4 py-3 bg-(--bg-main) rounded-lg border border-dashed border-(--border-primary) text-center"><i class="fa-solid fa-circle-info me-1"></i> لا توجد ملاحظات</div>`;
    } else {
        const placeholder = container.querySelector('.no-notes-placeholder');
        if (placeholder) placeholder.remove();
        
        rows.forEach((row, idx) => {
            const num = row.querySelector('.row-number');
            if (num) num.textContent = idx + 1;
        });
    }
}

// Handle local file selection preview and async upload
async function handleFileSelect(event, id) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.type !== 'application/pdf') {
        showNotification('يجب اختيار ملف PDF', 'error');
        event.target.value = '';
        return;
    }
    
    event.target.disabled = true;
    showNotification('جاري رفع الملف في الخلفية...', 'info');
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('file', file);
    
    const url = "{{ route('requests.stage_two.upload_file', ['accreditationRequest' => $accreditationRequest->id, 'formSubmission' => $formSubmission->id, 'decisionIndex' => ':id']) }}".replace(':id', id);
    
    try {
        const response = await fetch(url, { method: 'POST', body: formData, headers: {'Accept': 'application/json'} });
        const result = await response.json();
        
        if (result.success) {
            // Hide file input, show preview div
            event.target.classList.remove('block');
            event.target.classList.add('hidden');
            event.target.disabled = false;
            
            const previewDiv = document.getElementById('file_preview_' + id);
            if(previewDiv) {
                previewDiv.classList.remove('hidden');
                previewDiv.classList.add('flex');
            }
            
            // Generate Local Blob URL
            const blobUrl = URL.createObjectURL(file);
            
            // Update URL to blob
            const viewBtn = document.getElementById('file_view_btn_' + id);
            if(viewBtn) {
                if (viewBtn.href && viewBtn.href.startsWith('blob:')) {
                    URL.revokeObjectURL(viewBtn.href); // Clean previous blob
                }
                viewBtn.href = blobUrl;
            }

            // Set existing path to maintain compatibility with save function
            const existingPathInput = document.getElementById('existing_file_path_' + id);
            if (existingPathInput) {
                existingPathInput.value = result.path;
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'existing_file_path_' + id;
                hiddenInput.value = result.path;
                if(previewDiv) previewDiv.appendChild(hiddenInput);
            }
            
            // Trigger Alpine change detection safely
            if (typeof stageTwoApp === 'function') {
                const component = document.querySelector('[x-data]');
                if (component && component.__x) {
                    component.__x.$data.hasChanges = true;
                }
            }
            
            showNotification('تم الرفع للذاكرة المؤقتة!', 'success');
            
            // Clear the file input value to prevent it from being sent again with the main form
            event.target.value = '';
        } else {
            showNotification(result.message || 'حدث خطأ أثناء الرفع', 'error');
            event.target.value = '';
            event.target.disabled = false;
        }
    } catch(e) {
        showNotification('فشل الاتصال بالخادم', 'error');
        event.target.value = '';
        event.target.disabled = false;
    }
}

// Remove uploaded file from UI (Actual deletion happens on Save Draft)
function removeFileLogic(id) {
    
    const existingPath = document.getElementById('existing_file_path_' + id);
    if (existingPath) {
        existingPath.remove();
    }
    
    // Clear the file input
    const fileInput = document.getElementById('decision_file_' + id);
    if (fileInput) { 
        fileInput.value = ''; 
        fileInput.classList.remove('hidden'); 
        fileInput.classList.add('block');
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    // Hide preview div
    const previewDiv = document.getElementById('file_preview_' + id);
    if (previewDiv) {
        previewDiv.classList.remove('flex');
        previewDiv.classList.add('hidden');
    }
    
    // Clean up local blob
    const viewBtn = document.getElementById('file_view_btn_' + id);
    if (viewBtn && viewBtn.href.startsWith('blob:')) {
        URL.revokeObjectURL(viewBtn.href);
        viewBtn.href = '#';
    }
    
    // Mark changes as dirty to trigger Exit modal
    if (typeof stageTwoApp === 'function') {
        const component = document.querySelector('[x-data]');
        if (component && component.__x) {
            component.__x.$data.hasChanges = true;
        }
    }
    
    showNotification('تم إزالة الملف محلياً وسيتم التطبيق فور الحفظ.', 'info');
}

// Show notification toast
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-600', error: 'bg-red-600', info: 'bg-brand-600'
    };
    const area = document.getElementById('notificationArea');
    const el = document.createElement('div');
    el.className = `${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-lg text-sm font-bold flex items-center gap-2 animate-[slideIn_0.3s_ease-out]`;
    const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
    el.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i> ${message}`;
    area.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.3s'; setTimeout(() => el.remove(), 300); }, 3000);
}
</script>

<style>
@keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
<!-- Session Alerts -->
@if(session('success'))
<script>
  document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if(typeof showNotification === 'function') {
            showNotification("{{ session('success') }}", 'success');
        }
    }, 500);
  });
</script>
@endif

</body>
</html>
