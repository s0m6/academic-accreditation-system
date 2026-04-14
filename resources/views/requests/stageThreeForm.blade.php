<!doctype html>
@php
function ratingColor($rating) {
    return match((int)$rating) {
        1 => '#ef4444',
        2 => '#f97316',
        3 => '#eab308',
        4 => '#84cc16',
        5 => '#10b981',
        0 => '#312e81',
        default => ''
    };
}
@endphp
<html lang="ar" dir="rtl" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>الدراسة الذاتية — {{ $programInfo['program_name'] }}</title>

  {{-- Pass Laravel route data and PHP variables to JS --}}
  <script>
    window.SAVE_URL = '{{ route('requests.stage_three.save', [$accreditationRequest, $formSubmission]) }}';
    window.UPLOAD_BASE = '{{ url("/requests/{$accreditationRequest->id}/stage-three/{$formSubmission->id}/upload-evidence") }}';
    window.DELETE_BASE = '{{ url("/requests/{$accreditationRequest->id}/stage-three/{$formSubmission->id}/evidence") }}';
    window.SAVED_FORM_DATA = @json($formData);
    window.SAVED_SCORES = @json($indicatorScores);
    @php
        $mappedEvidences = $evidencesByEvalId->map(function($evs) {
            return $evs->map(function($e) {
                return [
                    'id' => $e->id,
                    'file_name' => $e->file_name,
                    'eval_id' => $e->indicator_evaluation_id,
                ];
            });
        })->values();
    @endphp

    window.SAVED_EVIDENCES = @json($mappedEvidences);
    window.EVAL_ID_MAP = @json($evalIdByIndicatorId);
  </script>


  {{-- Font Awesome 6.4.0 --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  {{-- Vite-compiled assets (Tailwind v4 + FlyonUI) --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    html {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    /* Instant Icon Switching via CSS */
    #sun-icon { display: none; }
    #moon-icon { display: block; }
    
    .dark #sun-icon { display: block !important; }
    .dark #moon-icon { display: none !important; }

    body {
      box-sizing: border-box;
    }

    :root {
      /* Light Mode - Neutral & Professional */
      --primary-bg: #f8fafc;
      --secondary-surface: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --primary-action: #2563eb;
      --border-base: #e2e8f0;
      --bg-input: #ffffff;
      --border-input: #cbd5e1;
      --text-input: #0f172a;
    }

    .dark {
      /* Dark Mode - Deep Indigo / Slate */
      --primary-bg: #020617;
      --secondary-surface: #0f172a;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --primary-action: #3b82f6;
      --border-base: #1e293b;
      --bg-input: #1e293b;
      --border-input: #334155;
      --text-input: #f1f5f9;
    }

    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: var(--primary-bg);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #94a3b8;
      border-radius: 10px;
    }

    .sidebar-item {
      transition: all 0.3s ease;
      position: relative;
    }

    .sidebar-item::before {
      content: '';
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: var(--primary-action);
      transform: scaleY(0);
      transition: transform 0.3s ease;
    }

    .sidebar-item.active::before {
      transform: scaleY(1);
    }

    .accordion-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease;
    }

    .accordion-content.open {
      max-height: 10000px;
    }

    .tab-indicator {
      transition: all 0.3s ease;
    }

    .fade-in {
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-hover {
      transition: all 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .rating-btn {
      transition: all 0.2s ease;
    }

    .rating-btn:hover {
      transform: scale(1.1);
    }

    .rating-btn.selected {
      box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
      border-color: var(--primary-action) !important;
    }



    input,
    textarea,
    select {
      background: var(--bg-input) !important;
      border: 1px solid var(--border-input) !important;
      color: var(--text-input) !important;
    }

    input:focus,
    textarea:focus,
    select:focus {
      border-color: var(--primary-action) !important;
      outline: none !important;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
      transform: translateY(-1px);
    }

    .glass {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(10px);
    }

    .dark .glass {
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(10px);
    }

    .progress-ring {
      transform: rotate(-90deg);
    }

    .btn-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      transition: all 0.3s ease;
    }

    .btn-primary:hover:not(:disabled) {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: #e2e8f0;
      color: #334155;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: #cbd5e1;
    }

    .dark .btn-secondary {
      background: #475569;
      color: #f1f5f9;
    }

    .dark .btn-secondary:hover {
      background: #64748b;
    }

    .indicator-row {
      transition: all 0.3s ease;
    }

    .indicator-row:hover {
      background: rgba(71, 85, 105, 0.1);
    }

    .dark .indicator-row:hover {
      background: rgba(71, 85, 105, 0.3);
    }
  </style>


</head>

<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 italic-arabic">
  <div id="app" class="flex h-full">
    {{--! Sidebar --}}
    <aside id="sidebar"
      class="w-72 bg-white dark:bg-slate-900 h-full flex flex-col fixed right-0 top-0 shadow-xl z-50 border-l border-slate-200 dark:border-slate-800">
      {{-- Logo Section --}}
      <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div
            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h1 id="app-title" class="text-lg font-bold text-slate-900 dark:text-white">الدراسة الذاتية</h1>
            <p class="text-xs text-slate-700 dark:text-slate-300">نظام إدارة البرامج</p>
          </div>
        </div>

      </div>{{-- Progress Overview --}}
      <div class="p-4 border-b border-slate-100 dark:border-slate-800">
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-800">
          <div class="flex items-center justify-between mb-3"><span
              class="text-sm text-slate-600 dark:text-slate-400">نسبة الإنجاز</span>
            <span id="progress-percent" class="text-lg font-bold text-blue-600 dark:text-blue-400">0%</span>
          </div>
          <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
            <div id="progress-bar"
              class="h-full bg-gradient-to-l from-blue-600 to-blue-400 rounded-full transition-all duration-500"
              style="width: 0%"></div>
          </div>
        </div>
      </div>
      {{-- Navigation --}}
      <nav class="flex-1 py-4 overflow-y-auto custom-scrollbar">
        <div class="px-3 mb-2"><span class="text-xs text-slate-500 font-medium px-3">أقسام الدراسة الذاتية</span>
        </div>
        {{-- Part 1 --}}
        <button onclick="switchSection(1)" id="nav-1"
          class="sidebar-item active w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group">
          <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/20 rounded-xl flex items-center justify-center"><span
              class="text-blue-600 dark:text-blue-400 font-bold">١</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-slate-800 dark:text-white">الجزء الأول</span> <span
              class="text-xs text-slate-700 dark:text-slate-300">بيانات البرنامج</span>
          </div>
          <div id="status-1" class="w-3 h-3 rounded-full bg-yellow-500"></div>
        </button>
        {{-- Part 2 --}}
        <button onclick="switchSection(2); switchStandardTab(1)" id="nav-2"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group">
          <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center"><span
              class="text-emerald-600 dark:text-emerald-400 font-bold">٢</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-slate-800 dark:text-white">الجزء الثاني</span> <span
              class="text-xs text-slate-700 dark:text-slate-300">التقييم وفق المعايير</span>
          </div>
        </button>
        {{-- Part 3 --}}
        <button onclick="switchSection(3)" id="nav-3"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group">
          <div
            class="w-10 h-10 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <span class="text-purple-600 dark:text-purple-400 font-bold">٣</span>
          </div>
          <div
            class="flex-1 text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
            <span class="block font-medium">الجزء الثالث</span> <span class="text-xs text-slate-700 dark:text-slate-300">التقييمات
              والنتائج</span>
          </div>
          <div id="status-3" class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700"></div>
        </button>
      </nav>
      {{-- Sidebar Footer --}}
      <div class="p-4 border-t border-slate-100 dark:border-slate-800 mt-auto">
        <button id="save-draft-btn" onclick="saveDraft()"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 transition-all cursor-pointer">
          <i id="save-draft-icon" class="fa-solid fa-floppy-disk"></i>
          <span id="save-draft-text">حفظ كمسودة</span>
        </button>
      </div>


    </aside>
    {{--! Main Content --}}
    <main class="flex-1 mr-72 h-full overflow-y-auto custom-scrollbar bg-slate-100 dark:bg-slate-900">
      {{--? Section 1: program data --}}
      <div id="section-1" class="section-content p-8 fade-in">
        {{--! Header --}}
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">الجزء الأول: الدراسة الذاتية</h2>
          </div>
          <p class="text-slate-700 dark:text-slate-300 mr-5 font-medium">إدخال وتحرير كافة بيانات الدراسة الذاتية
            للبرنامج</p>
        </div>
        {{--! Tabs Navigation --}}
        <div
          class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          <button onclick="switchTab('general')"
            class="tab-btn active px-6 py-2.5 rounded-xl text-sm font-semibold transition-all bg-blue-600 text-white shadow-md shadow-blue-500/20"
            data-tab="general"> معلومات عامة </button> <button onclick="switchTab('program')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-700 dark:text-slate-300 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="program"> بيانات تعريفية بالبرنامج </button> <button onclick="switchTab('profile')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-700 dark:text-slate-300 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="profile"> ملف البرنامج </button> <button onclick="switchTab('tables')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-700 dark:text-slate-300 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="tables"> الجداول والبيانات </button>
        </div>
        {{--! Tab Contents --}}
        <div id="tab-general" class="tab-content">
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl dark:shadow-slate-900/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a. 0z" />
              </svg> المعلومات العامة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">{{--! Auto Fields --}}
              <div class="bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <span class="block text-sm text-slate-700 dark:text-slate-300 mb-1">اسم المؤسسة / الجامعة</span>
                <span class="text-slate-900 dark:text-white font-medium text-lg">{{ $programInfo['university_name'] }}</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <span class="block text-sm text-slate-700 dark:text-slate-300 mb-1">اسم رئيس المؤسسة/ الجامعة</span>
                <span class="text-slate-900 dark:text-white font-medium text-lg">{{ $programInfo['university_president'] }}</span>
              </div>
              <div>
                <label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">اسم رئيس فريق المراجعة الداخلية <span class="text-red-600 dark:text-red-400">*</span></label>
                <input type="text" id="review_team_head" placeholder="أدخل الاسم" class="w-full px-4 py-3 rounded-xl"
                  value="{{ $formData['general']['review_team_head'] ?? '' }}"
                  onchange="setField('general', 'review_team_head', this.value)">
              </div>
              <div>
                <label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">تاريخ التقييم / المراجعة <span class="text-red-600 dark:text-red-400">*</span></label>
                <input type="date" id="review_date" class="w-full px-4 py-3 rounded-xl"
                  value="{{ $formData['general']['review_date'] ?? '' }}"
                  onchange="setField('general', 'review_date', this.value)">
              </div>
            </div>
          </div>
        </div>
        <div id="tab-program" class="tab-content hidden">
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg> البيانات التعريفية بالبرنامج
            </h3>{{--! Auto-filled Institution Info --}}
            <div class="bg-slate-100 dark:bg-slate-700/50 rounded-xl p-5 mb-6">
              <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">الجامعة</span>
                  <span class="text-slate-900 dark:text-white font-medium">{{ $programInfo['university_name'] }}</span>
                </div>
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">الكلية</span>
                  <span class="text-slate-900 dark:text-white font-medium">{{ $programInfo['college_name'] }}</span>
                </div>
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">القسم العلمي</span>
                  <span class="text-slate-900 dark:text-white font-medium">{{ $programInfo['department_name'] }}</span>
                </div>
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">اسم البرنامج</span>
                  <span class="text-slate-900 dark:text-white font-medium">{{ $programInfo['program_name'] }}</span>
                </div>
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">تاريخ التأسيس</span>
                  <span class="text-slate-900 dark:text-white font-medium">{{ $programInfo['establishment_date'] ?: '—' }}</span>
                </div>
                <div><span class="text-xs text-slate-700 dark:text-slate-300 block mb-1">عنوان الموقع الإلكتروني</span>
                  @if($programInfo['website_url'])
                    <a href="{{ $programInfo['website_url'] }}" target="_blank" class="text-blue-400 font-medium hover:underline">{{ $programInfo['website_url'] }}</a>
                  @else
                    <span class="text-slate-500">—</span>
                  @endif
                </div>
              </div>
            </div>
            <div class="space-y-6">
              <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">الملخص التنفيذي للنتيجة الإجمالية لتقييم معايير
                  الاعتماد البرامجي <span class="text-red-600 dark:text-red-400">*</span></label>
                <textarea id="executive_summary" rows="6"
                  placeholder="اكتب ملخصاً تنفيذياً شاملاً يتضمن النتيجة الإجمالية، جوانب القوة، وجوانب التحسين..."
                  class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="setField('program', 'executive_summary', this.value)">{{ $formData['program']['executive_summary'] ?? '' }}</textarea>
                <p class="text-xs text-slate-500 mt-2">يجب أن يتضمن: النتيجة الإجمالية، جوانب القوة، وجوانب التحسين</p>
              </div>
              <div>
                <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg> بيانات منسق إعداد التقرير
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">الاسم</label>
                    <input type="text" id="coordinator_name" placeholder="أدخل اسم المنسق"
                      class="w-full px-4 py-3 rounded-xl"
                      onchange="saveField('program', 'coordinator_name', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">الصفة</label>
                    <input type="text" id="coordinator_title" placeholder="الصفة الوظيفية"
                      class="w-full px-4 py-3 rounded-xl"
                      onchange="saveField('program', 'coordinator_title', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">البريد الإلكتروني</label>
                    <input type="email" id="coordinator_email" placeholder="example@ksu.edu.sa"
                      class="w-full px-4 py-3 rounded-xl text-right" dir="ltr"
                      onchange="saveField('program', 'coordinator_email', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">رقم الهاتف</label>
                    <input type="tel" id="coordinator_phone" placeholder="05xxxxxxxx"
                      class="w-full px-4 py-3 rounded-xl text-right" dir="ltr"
                      onchange="saveField('program', 'coordinator_phone', this.value)">
                  </div>
                  <div class="md:col-span-2"><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">تاريخ إعداد
                      التقرير</label>
                    <input type="date" id="report_date" class="w-full px-4 py-3 rounded-xl"
                      onchange="saveField('program', 'report_date', this.value)">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="tab-profile" class="tab-content hidden">
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg> ملف البرنامج
            </h3>
            <div class="space-y-6">
              <div class="space-y-8">
                <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">رسالة البرنامج <span
                      class="text-red-600 dark:text-red-400">*</span></label> <textarea id="program_mission" rows="4"
                    placeholder="اكتب رسالة البرنامج بوضوح..." class="w-full px-4 py-3 rounded-xl resize-none"
                    onchange="saveField('profile', 'program_mission', this.value)">{{ $formData['profile']['program_mission'] ?? '' }}</textarea>
                </div>

                {{--! Dynamic Objectives --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-bold text-slate-900 dark:text-white flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg> أهداف البرنامج (على شكل نقاط) <span class="text-red-600 dark:text-red-400">*</span>
                    </h4>
                    <button onclick="addObjective()"
                      class="text-blue-600 dark:text-blue-400 text-sm flex items-center gap-1 hover:text-blue-500 dark:hover:text-blue-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إضافة هدف
                    </button>
                  </div>
                  <div id="objectives-list-container" class="space-y-3 mb-4">
                    {{--! Objectives inputs will be rendered here dynamically --}}
                  </div>
                </div>

                {{--! Program System Details --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg> نظام البرنامج وساعاته ومقرراته
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">نظام البرنامج</label>
                      <select id="program_system" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'program_system', this.value)">
                        <option value="semester">نظام فصلي</option>
                        <option value="annual">نظام سنوي</option>
                        <option value="modules">نظام وحدات</option>
                      </select>
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">عدد الساعات المعتمدة للبرنامج</label>
                      <input type="number" id="credit_hours" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'credit_hours', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">عدد المقررات</label>
                      <input type="number" id="courses_total" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'courses_total', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Student Numbers --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0H6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg> عدد الطلبة المقيدين بالبرنامج
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">عدد الذكور</label>
                      <input type="number" id="male_students_count" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'male_students_count', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">عدد الإناث</label>
                      <input type="number" id="female_students_count" placeholder="0"
                        class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'female_students_count', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Approval Dates --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg> تواريخ اعتماد التوصيف الحالي للبرنامج من مجالس الجامعة
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2 text-xs">مجلس القسم</label>
                      <input type="date" id="dept_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'dept_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2 text-xs">مجلس الكلية</label>
                      <input type="date" id="college_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'college_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2 text-xs">المجلس الأكاديمي</label>
                      <input type="date" id="academic_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'academic_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2 text-xs">مجلس الجامعة</label>
                      <input type="date" id="university_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'university_council_date', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Program Context --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> سياق البرنامج وتاريخه
                  </h4>
                  <div class="space-y-6">
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">موجز عن تاريخ البرنامج</label>
                      <textarea id="program_history" rows="4" placeholder="اكتب موجز تاريخ البرنامج هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-100 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'program_history', this.value)"></textarea>
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">التغيرات في البيئة الداخلية والخارجية
                        للبرنامج</label>
                      <textarea id="env_changes" rows="4" placeholder="اكتب التغيرات هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-100 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'env_changes', this.value)"></textarea>
                    </div>
                  </div>
                </div>

                {{--! Self Study Details --}}
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                  <h4 class="text-md font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg> الدراسة الذاتية للبرنامج
                  </h4>
                  <div class="space-y-6">
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">ترتيبات إجراء الدراسة الذاتية</label>
                      <textarea id="self_study_arrangements" rows="4" placeholder="اكتب الترتيبات هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-100 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'self_study_arrangements', this.value)"></textarea>
                    </div>
                    <div><label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">منهجية المقارنة الداخلية والخارجية</label>
                      <textarea id="comparison_methodology" rows="4" placeholder="اكتب المنهجية هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-100 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'comparison_methodology', this.value)"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="tab-tables" class="tab-content hidden">
          {{--! Graduates Table (3 years) --}}
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0H6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg> جدول تقديرات الخريجين (آخر 3 سنوات)
              </h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-right border-collapse border border-slate-200 dark:border-slate-700">
                <thead>
                  <tr class="bg-slate-100 dark:bg-slate-700/50">
                    <th colspan="8"
                      class="py-3 px-4 font-bold text-center border border-slate-200 dark:border-slate-700">تقديرات
                      الطلبة الخريجين في البرنامج خلال الأعوام الثلاثة الأخيرة</th>
                  </tr>
                  <tr class="bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm">
                    <th colspan="2" class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700">
                      التقدير</th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">ممتاز
                    </th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">جيد
                      جداً</th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">جيد
                    </th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">مقبول
                    </th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">ضعيف
                    </th>
                    <th class="py-3 px-4 font-semibold border border-slate-200 dark:border-slate-700 text-center">
                      المجموع</th>
                  </tr>
                </thead>
                <tbody id="graduates-table">
                  @php
                    $years = [
                      'last_year' => '',
                      'prev_year' => '',
                      'two_years_ago' => ''
                    ];
                    $grades = ['excellent', 'very_good', 'good', 'pass', 'fail'];
                  @endphp

                  @foreach($years as $rowKey => $yearLabel)
                    {{--! Year Group: {{ $yearLabel }} --}}
                    <tr class="border-b border-slate-200 dark:border-slate-700" data-row-group="{{ $rowKey }}">
                      <td rowspan="2"
                        class="py-4 px-4 font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 w-44 text-center">
                        <div class="flex flex-col gap-2">
                          <span class="text-[10px] uppercase text-slate-700 dark:text-slate-300 dark:text-slate-500">العام الأكاديمي</span>
                          <input type="text" value=""
                            class="w-full px-2 py-1 text-center bg-transparent border-b border-dashed border-slate-300 dark:border-slate-600 focus:border-blue-500 outline-none font-bold text-sm"
                            placeholder="مثلاً: 2024/2023"
                            onchange="saveField('tables', 'ft_graduates_{{ $rowKey }}_year_display', this.value)">
                        </div>
                      </td>
                      <td
                        class="py-3 px-4 text-sm font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/20">
                        عدد الطلبة</td>
                      @foreach($grades as $grade)
                        <td class="py-2 px-2 border border-slate-200 dark:border-slate-700">
                          <input type="number" min="0"
                            class="w-full px-2 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-center text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/50 outline-none"
                            onchange="updateFixedTable('graduates', '{{ $rowKey }}', '{{ $grade }}', this.value)">
                        </td>
                      @endforeach
                      <td
                        class="py-2 px-2 border border-slate-200 dark:border-slate-700 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <div id="grad-total-{{ $rowKey }}"
                          class="text-center font-bold text-emerald-700 dark:text-emerald-400">0</div>
                      </td>
                    </tr>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-emerald-50 dark:bg-emerald-900/40"
                      data-row-percent="{{ $rowKey }}">
                      <td
                        class="py-3 px-4 text-sm font-medium text-emerald-800 dark:text-emerald-300 border border-slate-200 dark:border-slate-700">
                        %</td>
                      @foreach($grades as $grade)
                        <td class="py-3 px-2 border border-slate-200 dark:border-slate-700">
                          <div id="grad-percent-{{ $rowKey }}-{{ $grade }}"
                            class="text-center text-xs font-bold text-emerald-900 dark:text-emerald-100">0%</div>
                        </td>
                      @endforeach
                      <td
                        class="py-3 px-2 border border-slate-200 dark:border-slate-700 font-bold text-center text-emerald-800/60 dark:text-emerald-200/60">
                        100%</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{--! Research Table (8 indicators) --}}
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg> البحث العلمي والأنشطة البحثية للبرنامج خلال العام السابق للتقييم
            </h3>
            <div class="overflow-x-auto">
              <table class="w-full text-right border-collapse">
                <thead>
                  <tr class="bg-slate-100 dark:bg-slate-700/50 text-slate-800 dark:text-slate-200 text-sm">
                    <th class="py-3 px-4 font-semibold border-b border-slate-300 dark:border-slate-600 w-16">م</th>
                    <th class="py-3 px-4 font-semibold border-b border-slate-300 dark:border-slate-600">نوع النشاط العلمي</th>
                    <th class="py-3 px-4 font-semibold border-b border-slate-300 dark:border-slate-600 w-32">العدد</th>
                  </tr>
                </thead>
                <tbody id="research-table">
                  @php
                    $researchIndicators = [
                      'intl_journals_indexed' => 'البحوث العلمية المنشورة في مجلات دولية متخصصة ومفهرسة',
                      'arabic_journals_reviewed' => 'البحوث العلمية المنشورة في مجلات عربية محكمة',
                      'local_journals_reviewed' => 'البحوث العلمية المنشورة في مجلات محلية محكمة',
                      'faculty_publications' => 'المؤلفات العلمية لأعضاء هيئة التدريس',
                      'faculty_textbooks' => 'الكتب المنهجية لأعضاء هيئة التدريس',
                      'faculty_translated_books' => 'الكتب المترجمة لأعضاء هيئة التدريس',
                      'master_theses_discussed' => 'رسائل الماجستير التي تمت مناقشتها',
                      'phd_dissertations_discussed' => 'رسائل الدكتوراه التي تمت مناقشتها',
                      'conferences_workshops_organized' => 'المؤتمرات والندوات وورش العمل والحلقات الدراسية التي نظمها القسم أو الكلية'
                    ];
                  @endphp
                  @foreach($researchIndicators as $key => $label)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                      <td class="py-3 px-4 text-slate-700 dark:text-slate-300 text-sm font-medium">{{ $loop->iteration }}</td>
                      <td class="py-3 px-4 text-slate-900 dark:text-white text-sm font-medium">{{ $label }}</td>
                      <td class="py-3 px-4"><input type="number" min="0"
                          class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-center text-white"
                          onchange="updateResearchTable('{{ $key }}', 'count', this.value)"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{--! Facilities Table --}}
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg> جدول المرافق التعليمية والخدمية
            </h3>
            <div class="overflow-x-auto">
              <table class="w-full text-right border-collapse">
                <thead>
                  <tr class="bg-slate-100 dark:bg-slate-700/50 text-slate-800 dark:text-slate-200 text-xs">
                    <th class="py-3 px-2 font-semibold border-b border-slate-300 dark:border-slate-600">نوع المرفق</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-300 dark:border-slate-600">العدد</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-300 dark:border-slate-600">المساحة الإجمالية (م²)</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-300 dark:border-slate-600">متوسط عدد المستخدمين</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-300 dark:border-slate-600">متوسط ساعات التشغيل</th>
                  </tr>
                </thead>
                <tbody id="facilities-table">
                  @php
                    $facilityTypes = [
                      'classrooms' => 'قاعات دراسية',
                      'spec_labs' => 'مختبرات تخصصية',
                      'comp_labs' => 'مختبرات الحاسوب',
                      'library' => 'المكتبة',
                      'admin_offices' => 'المكاتب الإدارية',
                      'student_lounges' => 'استراحات الطلاب',
                      'sports' => 'المرافق الرياضية',
                      'others' => 'أخرى'
                    ];
                  @endphp
                  @foreach($facilityTypes as $key => $label)
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                      <td class="py-3 px-2 text-slate-900 dark:text-white text-xs font-medium">{{ $label }}</td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-center text-slate-900 dark:text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'count', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-center text-slate-900 dark:text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'area', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-center text-slate-900 dark:text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'students', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-center text-slate-900 dark:text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'hours', this.value)"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
      {{--? Section 2: Standards Evaluation --}}
      <div id="section-2" class="section-content hidden p-8 fade-in">
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">الجزء الثاني: التقييم وفق معايير الاعتماد</h2>
          </div>
          <p class="text-slate-700 dark:text-slate-300 mr-5 font-medium">تقييم {{ $standards->count() }} معيار رئيسي بناءً على مؤشرات محددة</p>
        </div>

        {{-- Standard Tabs Navigation (dynamic) --}}
        <div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          @foreach($standards as $stdIndex => $std)
            <button onclick="switchStandardTab({{ $std->id }})"
              class="std-tab-btn {{ $stdIndex === 0 ? 'active bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50' }} px-4 py-2.5 rounded-xl text-sm font-semibold transition-all"
              data-std="{{ $std->id }}">{{ $std->number }}. {{ $std->name }}</button>
          @endforeach
        </div>

        {{-- Standards Content Area (dynamic) --}}
        <div id="standards-container">
          @foreach($standards as $stdIndex => $std)
            <div id="standard-{{ $std->id }}-tab-content" class="std-content {{ $stdIndex > 0 ? 'hidden' : '' }} fade-in">

              {{-- Standard Header --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                      <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">{{ $std->number }}</div>
                      المعيار {{ $std->number }}: {{ $std->name }}
                    </h2>
                    @if($std->description)
                      <p class="text-slate-700 dark:text-slate-300 text-sm mt-2">{{ $std->description }}</p>
                    @endif
                  </div>
                  <div class="text-left bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                    <span class="block text-xs text-slate-700 dark:text-slate-300 mb-1 text-center">متوسط التقييم</span>
                    <div class="flex items-center justify-center gap-1">
                      <span id="standard-{{ $std->id }}-score" class="text-3xl font-bold text-emerald-400">—</span>
                      <span class="text-slate-500 text-lg">/5</span>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Substandards --}}
              <div class="space-y-8">
                @foreach($std->subStandards as $sub)
                  <div class="bg-white/80 dark:bg-slate-800/80 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700/50">
                    <div class="bg-slate-100 dark:bg-slate-700/30 p-4 border-b border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                      <div class="flex items-center gap-3 flex-1 min-w-0 pr-2">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm whitespace-nowrap">{{ $sub->number }}</span>
                        <h3 class="font-bold text-lg text-slate-700 dark:text-slate-300 truncate">{{ $sub->name }}</h3>
                        
                        @php $examples = $sub->examples_of_evidence ?? []; @endphp
                        @if(count($examples) > 0)
                          <div class="relative group flex-shrink-0">
                            <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="absolute bottom-full right-1/2 translate-x-1/2 mb-2 w-64 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl text-xs text-slate-800 dark:text-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[9999] pointer-events-none">
                              <strong>أمثلة للأدلة:</strong><br>
                              @foreach($examples as $ex)• {{ $ex }}<br>@endforeach
                            </div>
                          </div>
                        @endif
                      </div>

                      {{-- Collapse Chevron --}}
                      <button type="button" onclick="toggleSubStandard({{ $sub->id }}, this)" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                        <svg class="w-5 h-5 transform rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                      </button>
                    </div>
                    
                    <div id="sub-{{ $sub->id }}-content" class="p-6 space-y-4">
                      @foreach($sub->indicators as $ind)
                        @php
                          $evalId = $evalIdByIndicatorId[$ind->id] ?? null;
                          $savedScore = $indicatorScores[$ind->id] ?? null;
                          $indEvidences = $evalId ? ($evidencesByEvalId[$evalId] ?? collect()) : collect();
                        @endphp
                        <div class="indicator-row rounded-2xl p-6 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all hover:shadow-md"
                          data-indicator-id="{{ $ind->id }}"
                          data-eval-id="{{ $evalId }}">
                          <div class="flex items-start justify-between mb-5">
                            <div class="flex-1 border-r-4 border-blue-500 pr-3">
                              <span class="text-xs text-blue-500 font-bold tracking-wider">مؤشر {{ $sub->number }}-{{ $loop->iteration }}</span>
                              <p class="text-slate-800 dark:text-slate-100 mt-2 font-medium leading-relaxed">{{ $ind->name }}</p>
                            </div>
                          </div>
                          
                          {{-- Rating Buttons --}}
                          <div class="flex flex-wrap items-center gap-4 mb-6 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">درجة التقييم:</span>
                            <div class="flex flex-wrap gap-2">
                              @foreach([1,2,3,4,5] as $r)
                                <button
                                  onclick="setRatingById({{ $ind->id }}, {{ $std->id }}, {{ $r }})"
                                  class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm"
                                  data-indicator-id="{{ $ind->id }}"
                                  data-rating="{{ $r }}"
                                  style="{{ $savedScore == $r ? 'background-color:'.ratingColor($r).';color:#fff; transform: scale(1.05);' : '' }}">{{ $r }}</button>
                              @endforeach
                              <div class="w-px h-8 bg-slate-300 dark:bg-slate-600 mx-2 hidden sm:block"></div>
                              <button
                                onclick="setRatingById({{ $ind->id }}, {{ $std->id }}, 0)"
                                class="rating-btn px-5 h-10 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-2 border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm whitespace-nowrap"
                                data-indicator-id="{{ $ind->id }}"
                                data-rating="0"
                                style="{{ $savedScore === 0 ? 'background-color:#475569;border-color:#475569;color:#fff; transform: scale(1.05);' : '' }}">غير مطابق</button>
                            </div>
                          </div>

                          {{-- Evidences Container --}}
                          <div class="evidence-section bg-slate-50 dark:bg-slate-900/30 rounded-xl border border-slate-100 dark:border-slate-700/50 p-4">
                            <div class="flex items-center justify-between mb-3">
                              <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <i class="fas fa-paperclip text-slate-400"></i> الأدلة المرفقة
                              </label>
                              {{-- Upload new evidence trigger --}}
                              @if($evalId)
                                <button onclick="prepareEvidenceRow({{ $ind->id }}, {{ $evalId }})" class="text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center gap-1.5 hover:text-blue-700 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 py-1.5 px-3 rounded-lg transition-colors">
                                  <i class="fas fa-plus"></i> إرفاق دليل جديد
                                </button>
                              @endif
                            </div>

                            {{-- Saved Evidences --}}
                            <div id="evidences-ind-{{ $ind->id }}" class="space-y-2">
                              @if($indEvidences->isEmpty())
                                <p class="text-xs text-slate-400 dark:text-slate-500 italic px-2 no-evidence-msg">لا توجد أدلة مرفقة لهذا المؤشر.</p>
                              @endif
                              @foreach($indEvidences as $ev)
                                <div class="evidence-item bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-2.5 flex items-center gap-3 shadow-sm group transition-all hover:border-blue-300" 
                                  id="evidence-saved-{{ $ev->id }}"
                                  data-id="{{ $ev->id }}"
                                  data-name="{{ $ev->file_name }}">
                                  
                                  <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                  </div>
                                  <span class="flex-1 text-sm font-medium text-slate-800 dark:text-slate-200 truncate" title="{{ $ev->file_name }}">{{ $ev->file_name }}</span>
                                  
                                  <div class="flex items-center gap-1">
                                    <a href="/stage-three/view-file?path={{ urlencode($ev->file_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors" title="عرض الدليل">
                                      <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                    <button onclick="removeEvidenceRow('evidence-saved-{{ $ev->id }}')"
                                      class="text-red-500 hover:text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors" title="حذف">
                                      <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>

                @endforeach
              </div>

              {{-- Standard Comments Sections --}}
              <div class="mt-12 space-y-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                  <h3 class="text-xl font-bold text-slate-900 dark:text-white">التعليقات الختامية للمعيار</h3>
                </div>
                {{-- Program Comment --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-slate-100 dark:bg-slate-700/30 p-4 border-b border-slate-200 dark:border-slate-700/50">
                    <label class="block text-sm font-medium text-slate-800 dark:text-slate-200">تعليق البرنامج</label>
                  </div>
                  <div class="p-6">
                    <textarea rows="3" placeholder="أدخل تعليق البرنامج على هذا المعيار..."
                      class="w-full px-4 py-3 rounded-xl resize-none bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white text-sm"
                      onchange="saveStdComment({{ $std->id }}, 'program_comment', this.value)">{{ $formData['standard_comments'][$std->id]['program_comment'] ?? '' }}</textarea>
                  </div>
                </div>
                {{-- Strengths --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-emerald-500/10 p-4 border-b border-emerald-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400">جوانب القوة</label>
                    <button onclick="addCommentPoint({{ $std->id }}, 'strengths')" class="text-xs text-emerald-500 hover:text-emerald-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-{{ $std->id }}-strengths-list" class="space-y-3"></div>
                  </div>
                </div>
                {{-- Improvements --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-red-500/10 p-4 border-b border-red-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-red-700 dark:text-red-400">جوانب تحتاج تحسين</label>
                    <button onclick="addCommentPoint({{ $std->id }}, 'improvements')" class="text-xs text-red-500 hover:text-red-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-{{ $std->id }}-improvements-list" class="space-y-3"></div>
                  </div>
                </div>
                {{-- Priorities --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-amber-500/10 p-4 border-b border-amber-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-amber-700 dark:text-amber-400">أولويات التحسين</label>
                    <button onclick="addCommentPoint({{ $std->id }}, 'priorities')" class="text-xs text-amber-500 hover:text-amber-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-{{ $std->id }}-priorities-list" class="space-y-3"></div>
                  </div>
                </div>
              </div>
            </div>{{-- End standard-{{ $std->id }}-tab-content --}}
          @endforeach
        </div> {{-- End standards-container --}}
      </div> {{-- End section-2 --}}

          {{--? Section 3: Independent Evaluations --}}
          <div id="section-3" class="section-content hidden p-8 fade-in">
            <div class="mb-8">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-2 h-8 bg-purple-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">الجزء الثالث: التقييمات المستقلة والنتائج</h2>
              </div>
              <p class="text-slate-700 dark:text-slate-300 mr-5">إدخال نتائج التقييم الخارجي والتوصيات وخطة الاستجابة</p>
            </div>
            <div class="space-y-6">
              {{-- Independent Evaluations --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                  </svg> التقييمات المستقلة
                </h3>
                <div class="space-y-6">

                  {{-- الإجراءات المتبعة --}}
                  <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-blue-500/10 p-4 border-b border-blue-500/20 flex items-center justify-between">
                      <label class="block text-sm font-semibold text-blue-700 dark:text-blue-400 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500 text-xs"><i class="fas fa-list-ul"></i></span>
                        الإجراءات المتبعة للحصول على التقييم <span class="text-red-500">*</span>
                      </label>
                      <button onclick="addSection3Point('evaluations', 'evaluation_procedures')"
                        class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fas fa-plus text-[10px]"></i> إضافة نقطة
                      </button>
                    </div>
                    <div class="p-5">
                      <div id="sec3-evaluations-evaluation_procedures-list" class="space-y-3"></div>
                    </div>
                  </div>

                  {{-- توصيات المقيمين --}}
                  <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-blue-500/10 p-4 border-b border-blue-500/20 flex items-center justify-between">
                      <label class="block text-sm font-semibold text-blue-700 dark:text-blue-400 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500 text-xs"><i class="fas fa-clipboard-check"></i></span>
                        النقاط التي وضعها المقيمون <span class="text-red-500">*</span>
                      </label>
                      <button onclick="addSection3Point('evaluations', 'evaluator_recommendations')"
                        class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fas fa-plus text-[10px]"></i> إضافة نقطة
                      </button>
                    </div>
                    <div class="p-5">
                      <div id="sec3-evaluations-evaluator_recommendations-list" class="space-y-3"></div>
                    </div>
                  </div>

                  {{-- إجراءات الاستجابة --}}
                  <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-blue-500/10 p-4 border-b border-blue-500/20 flex items-center justify-between">
                      <label class="block text-sm font-semibold text-blue-700 dark:text-blue-400 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500 text-xs"><i class="fas fa-reply"></i></span>
                        إجراءات الاستجابة للتوصيات
                      </label>
                      <button onclick="addSection3Point('evaluations', 'actions_taken')"
                        class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fas fa-plus text-[10px]"></i> إضافة نقطة
                      </button>
                    </div>
                    <div class="p-5">
                      <div id="sec3-evaluations-actions_taken-list" class="space-y-3"></div>
                    </div>
                  </div>

                </div>
              </div>

              {{-- Results --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg> النتائج
                </h3>
                <div class="space-y-6">

                  {{-- جوانب النجاح --}}
                  <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-emerald-500/10 p-4 border-b border-emerald-500/20 flex items-center justify-between">
                      <label class="block text-sm font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500 text-xs"><i class="fas fa-check"></i></span>
                        جوانب النجاح
                      </label>
                      <button onclick="addSection3Point('results', 'success_aspects')"
                        class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 bg-emerald-500/10 hover:bg-emerald-500/20 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fas fa-plus text-[10px]"></i> إضافة نقطة
                      </button>
                    </div>
                    <div class="p-5">
                      <div id="sec3-results-success_aspects-list" class="space-y-3"></div>
                    </div>
                  </div>

                  {{-- جوانب التحسين ذات الأولوية --}}
                  <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-amber-500/10 p-4 border-b border-amber-500/20 flex items-center justify-between">
                      <label class="block text-sm font-semibold text-amber-700 dark:text-amber-400 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500 text-xs"><i class="fas fa-arrow-up"></i></span>
                        جوانب التحسين ذات الأولوية
                      </label>
                      <button onclick="addSection3Point('results', 'priority_improvements')"
                        class="flex items-center gap-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 bg-amber-500/10 hover:bg-amber-500/20 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fas fa-plus text-[10px]"></i> إضافة نقطة
                      </button>
                    </div>
                    <div class="p-5">
                      <div id="sec3-results-priority_improvements-list" class="space-y-3"></div>
                    </div>
                  </div>

                </div>
              </div>{{--! Executive Proposals Table --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg> المقترحات التنفيذية
                  </h3><button onclick="addProposalRow()"
                    class="btn-primary px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg> إضافة مقترح </button>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead>
                      <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="text-right py-3 px-4 text-slate-700 dark:text-slate-300 text-sm font-medium">التوصية</th>
                        <th class="text-right py-3 px-4 text-slate-700 dark:text-slate-300 text-sm font-medium">مسؤول التنفيذ</th>
                        <th class="text-right py-3 px-4 text-slate-700 dark:text-slate-300 text-sm font-medium">توقيت التنفيذ</th>
                        <th class="text-right py-3 px-4 text-slate-700 dark:text-slate-300 text-sm font-medium">الموارد المطلوبة</th>
                        <th class="py-3 px-4"></th>
                      </tr>
                    </thead>
                    <tbody id="proposals-table">{{--! Dynamic rows --}}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> {{-- End section-3 --}}
    </main>
  </div>
  {{-- Floating Theme Toggle --}}
  <button id="theme-toggle" onclick="toggleDarkMode()"
    class="fixed bottom-6 left-6 z-[9999] w-14 h-14 rounded-full bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-2xl border border-slate-200 dark:border-slate-700 hover:scale-110 active:scale-95 transition-all flex items-center justify-center group"
    title="تبديل الوضع">
    <i id="sun-icon" class="fas fa-sun text-2xl text-amber-500"></i>
    <i id="moon-icon" class="fas fa-moon text-2xl text-slate-700"></i>
  </button>


  {{-- Notification Container --}}
  <div id="notificationArea" class="fixed top-4 left-4 z-[9999] space-y-2 pointer-events-none"></div>


  <div id="toast"
    class="fixed bottom-24 left-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center gap-3 border border-slate-200 dark:border-slate-700">
    <svg id="toast-icon" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg><span id="toast-message">تم الحفظ بنجاح</span>
  </div>
  {{-- Validation Modal --}}
  <div id="validation-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white">حقول مطلوبة</h3>
          <p class="text-sm text-slate-700 dark:text-slate-300">يرجى إكمال الحقول التالية</p>
        </div>
      </div>
      <div id="validation-list" class="space-y-2 mb-6 max-h-60 overflow-y-auto">
        {{--! Missing fields will be listed here --}}
      </div>
      <div class="flex gap-3"><button onclick="closeValidationModal()"
          class="flex-1 btn-secondary py-3 rounded-xl text-white"> إغلاق </button> <button id="go-to-field-btn"
          onclick="goToFirstMissingField()" class="flex-1 btn-primary py-3 rounded-xl text-white"> الانتقال للحقل
        </button>
      </div>
    </div>
  </div>

  {{-- ✦ Unsaved Changes Navigation Modal --}}
  <div id="unsaved-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[9999] hidden" aria-modal="true" role="dialog">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl border border-slate-200 dark:border-slate-700">
      <div class="flex items-start gap-4 mb-5">
        <div class="w-12 h-12 flex-shrink-0 bg-amber-500/10 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white text-base mb-1">تغييرات غير محفوظة</h3>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">لديك تغييرات لم يتم حفظها بعد. ماذا تريد أن تفعل؟</p>
        </div>
      </div>
      <div class="flex flex-col gap-2">
        <button id="unsaved-save-btn"
          class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all flex items-center justify-center gap-2">
          <i class="fas fa-floppy-disk text-xs"></i> حفظ التغييرات
        </button>
        <button id="unsaved-leave-btn"
          class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 text-slate-700 dark:text-slate-200 font-semibold text-sm transition-all">
          المغادرة بدون حفظ
        </button>
        <button onclick="closeUnsavedModal()"
          class="w-full py-2.5 px-4 rounded-xl bg-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-medium text-sm transition-all">
          إلغاء
        </button>
      </div>
    </div>
  </div>
  <script>
    // Global Endpoints
    window.SAVE_URL = "{{ route('requests.stage_three.save', [$accreditationRequest->id, $formSubmission->id]) }}";
    window.UPLOAD_BASE = "{{ route('requests.stage_three.upload_evidence_temp', [$accreditationRequest->id, $formSubmission->id]) }}";

    // Server-Rendered Persistent Data
    let _sfd = {!! json_encode($formData, JSON_UNESCAPED_UNICODE) !!};
    window.SAVED_FORM_DATA = (Array.isArray(_sfd) && _sfd.length === 0) ? {} : (_sfd || {});
    
    let _ssc = {!! json_encode($indicatorScores, JSON_UNESCAPED_UNICODE) !!};
    window.SAVED_SCORES = (Array.isArray(_ssc) && _ssc.length === 0) ? {} : (_ssc || {});



    // Dark Mode Toggle
    function toggleDarkMode() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    // Initialize theme from localStorage or system preference
    (function() {
      const isDark = localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
      if (isDark) document.documentElement.classList.add('dark');
      else document.documentElement.classList.remove('dark');
    })();

    // ── App State ─────────────────────────────────────────────────────
    let currentSection = 1;
    let currentTab = 'general';
    // formData starts from server-saved data, nested by section key
    let formData = window.SAVED_FORM_DATA || {};
    // Indicator scores: { indicatorId: score }
    let scores = {};
    // Populate scores from saved data
    if (window.SAVED_SCORES) {
      Object.entries(window.SAVED_SCORES).forEach(([id, score]) => {
        if (score !== null && score !== undefined) scores[id] = parseInt(score);
      });
    }
    let standardComments = formData.standard_comments || {};
    let section3Data = {
      evaluations: { evaluation_procedures: [], evaluator_recommendations: [], actions_taken: [] },
      results: { success_aspects: [], priority_improvements: [] }
    };
    // Load from saved formData (handles both array and JSON-string formats)
    function _parseS3Array(val) {
      if (Array.isArray(val)) return val;
      if (typeof val === 'string') { try { const p = JSON.parse(val); return Array.isArray(p) ? p : []; } catch(e) {} }
      return [];
    }
    if (formData.evaluations) {
      ['evaluation_procedures','evaluator_recommendations','actions_taken'].forEach(k => {
        section3Data.evaluations[k] = _parseS3Array(formData.evaluations[k]);
      });
    }
    if (formData.results) {
      ['success_aspects','priority_improvements'].forEach(k => {
        section3Data.results[k] = _parseS3Array(formData.results[k]);
      });
    }
    let tableData = formData.tables || {
      graduates: { last_year: {}, prev_year: {}, two_years_ago: {} },
      research: {}, facilities: {}, proposals: [], objectives: []
    };
    if (!Array.isArray(tableData.objectives)) { tableData.objectives = formData.profile && formData.profile.program_objectives_list ? JSON.parse(formData.profile.program_objectives_list || '[]') : []; }

    let missingFields = [];

    // ── Helper: set a nested formData field ──────────────────────────
    function setField(section, key, value) {
      if (!formData[section]) formData[section] = {};
      formData[section][key] = value;
    }

    // Keep legacy saveField calls working (section3, tables, etc.)
    function saveField(section, key, value) {
      setField(section, key, value);
    }

    function loadDataToUI(data) {
      data.forEach(record => {
        const fieldId = record.field_key;

        // Handle fixed tables
        if (fieldId.startsWith('fixed_table_')) {
          const parts = fieldId.split('__');
          if (parts.length === 3) {
            const [_, tableName, rowKey, field] = [parts[0], parts[1], parts[2], parts[3] || 'value']; // Simplified
            // This logic will be handled specifically in updateFixedTable-like style but for loading
            // For now, let's look for actual inputs
            const input = document.querySelector(`[data-table="${parts[1]}"][data-row="${parts[2]}"][data-field="${parts[3]}"]`);
            if (input) input.value = record.field_value;
            return;
          }
        }

        // Handle dynamic objectives
        if (fieldId === 'program_objectives_list') {
          const list = JSON.parse(record.field_value || '[]');
          tableData.objectives = list;
          renderObjectives();
          return;
        }

        const element = document.getElementById(fieldId);
        if (element) {
          if (element.type === 'radio') {
            const radio = document.querySelector(`input[name="${fieldId}"][value="${record.field_value}"]`);
            if (radio) radio.checked = true;
          } else {
            element.value = record.field_value;
          }
        }
        formData[`${record.section}_${record.field_key}`] = record.field_value;

        // Handle standard comments data
        if (record.section === 'standard_comments') {
          const parts = record.field_key.split('_');
          const std = parts[0];
          const field = parts.slice(1).join('_');
          if (!standardComments[std]) standardComments[std] = {};
          
          try {
            standardComments[std][field] = (record.field_value && record.field_value.startsWith('[')) 
              ? JSON.parse(record.field_value) 
              : record.field_value;
          } catch(e) {
            standardComments[std][field] = record.field_value;
          }
        }

        // Handle section3 lists
        if (['evaluations', 'results'].includes(record.section) && section3Data[record.section]?.[record.field_key] !== undefined) {
          try {
            section3Data[record.section][record.field_key] = (record.field_value && record.field_value.startsWith('[')) 
              ? JSON.parse(record.field_value) 
              : (record.field_value ? [record.field_value] : []);
          } catch(e) {
            section3Data[record.section][record.field_key] = record.field_value ? [record.field_value] : [];
          }
        }
      });

      // Render standard comments after data is loaded
      ['strengths', 'improvements', 'priorities', 'independent_opinion'].forEach(field => {
        renderCommentPoints(1, field);
      });
      
      // Render Section 3 lists
      ['evaluation_procedures', 'evaluator_recommendations', 'response_mechanism', 'actions_taken'].forEach(field => {
        renderSection3Points('evaluations', field);
      });
      ['success_aspects', 'priority_improvements'].forEach(field => {
        renderSection3Points('results', field);
      });
    }

    // Dropdown Functions (Removed based on new Tab design requirements)
    let selectedStandard = 1;

    function switchStandardTab(standardNum) {
      selectedStandard = standardNum;

      // Hide all standard contents
      document.querySelectorAll('.std-content').forEach(el => {
        el.classList.add('hidden');
      });

      // Show selected standard
      const selectedContent = document.getElementById(`standard-${standardNum}-tab-content`);
      if (selectedContent) {
        selectedContent.classList.remove('hidden');
      }

      // Update Tab Styles
      document.querySelectorAll('.std-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        btn.classList.add('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });

      const activeBtn = document.querySelector(`.std-tab-btn[data-std="${standardNum}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        activeBtn.classList.remove('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      }
    }

    // Section Navigation
    function switchSection(sectionNum) {
      document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
      document.getElementById(`section-${sectionNum}`).classList.remove('hidden');

      document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));
      if (document.getElementById(`nav-${sectionNum}`)) {
        document.getElementById(`nav-${sectionNum}`).classList.add('active');
      }

      currentSection = sectionNum;
    }

    // Tab Navigation
    function switchTab(tabName) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
      document.getElementById(`tab-${tabName}`).classList.remove('hidden');

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/20');
        btn.classList.add('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });

      const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/20');
        activeBtn.classList.remove('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      }

      currentTab = tabName;
    }

    // ── Helper: update progress (stub) ───────────────────────────────
    function updateProgress() {
      // Logic for calculating progress based on formData can be added here
    }

    // Stub replaced below — kept for hoisting purposes
    function renderObjectives() { _renderObjectives(); }

    // ── Standard Comments Save Handlers ──────────────────────────────
    function saveStdComment(standard, field, value) {
      if (!standardComments[standard]) standardComments[standard] = {};
      standardComments[standard][field] = value;
    }

    function saveStandardComment(standard, field, value) {
      if (!standardComments[standard]) standardComments[standard] = {};
      try {
        standardComments[standard][field] = JSON.parse(value);
      } catch (e) {
        standardComments[standard][field] = value;
      }
    }

    // Standard Comments
    // Standard Comments Point System
    function addCommentPoint(standard, field) {
      if (!standardComments[standard]) standardComments[standard] = {};
      if (!standardComments[standard][field]) standardComments[standard][field] = [];
      
      const list = standardComments[standard][field];
      if (typeof list === 'string') {
          // Compatibility for old string data if any
          standardComments[standard][field] = list ? [list] : [''];
      } else if (!Array.isArray(list)) {
          standardComments[standard][field] = [''];
      }
      
      standardComments[standard][field].push('');
      renderCommentPoints(standard, field);
      saveStandardComment(standard, field, JSON.stringify(standardComments[standard][field]));
    }

    function removeCommentPoint(standard, field, index) {
      // Immediate deletion — committed to DB only on Save Draft
      if (standardComments[standard] && Array.isArray(standardComments[standard][field])) {
        standardComments[standard][field].splice(index, 1);
        renderCommentPoints(standard, field);
        if (!formData['standard_comments']) formData['standard_comments'] = {};
        if (!formData['standard_comments'][standard]) formData['standard_comments'][standard] = {};
        formData['standard_comments'][standard][field] = standardComments[standard][field];
        hasChanges = true;
      }
    }

    function updateCommentPoint(standard, field, index, value) {
      if (standardComments[standard] && Array.isArray(standardComments[standard][field])) {
        standardComments[standard][field][index] = value;
        saveStandardComment(standard, field, JSON.stringify(standardComments[standard][field]));
      }
    }

    function renderCommentPoints(standard, field) {
      const container = document.getElementById(`std-${standard}-${field}-list`);
      if (!container) return;

      const points = standardComments[standard] && Array.isArray(standardComments[standard][field]) 
        ? standardComments[standard][field] 
        : [];

      if (points.length === 0) {
        container.innerHTML = `
          <div class="py-5 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/20">
            <i class="fas fa-inbox text-slate-300 dark:text-slate-600 text-2xl mb-2 block"></i>
            <p class="text-slate-400 dark:text-slate-500 text-sm">لا توجد نقاط مضافة — اضغط &laquo;إضافة نقطة&raquo; للبدء</p>
          </div>
        `;
        return;
      }


      const colorMap = {
        'strengths': 'emerald',
        'improvements': 'red',
        'priorities': 'amber',
        'independent_opinion': 'blue'
      };
      const color = colorMap[field] || 'slate';

      container.innerHTML = points.map((point, index) => `
        <div class="flex items-center gap-2 group animate-fadeIn">
          <div class="flex-1 relative">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center bg-${color}-500/20 text-${color}-400 rounded-full text-[10px] font-bold">${index + 1}</span>
            <input type="text" 
                   value="${point.replace(/"/g, '&quot;')}" 
                   placeholder="أدخل النقطة..." 
                   class="w-full pr-10 pl-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 focus:border-${color}-500/50 focus:ring-1 focus:ring-${color}-500/30 text-sm text-slate-900 dark:text-white transition-all"
                   onchange="updateCommentPoint(${standard}, '${field}', ${index}, this.value)">
          </div>
          <button onclick="removeCommentPoint(${standard}, '${field}', ${index})" 
                  class="p-2.5 text-slate-500 hover:text-red-600 dark:text-red-400 bg-slate-100 dark:bg-slate-800 hover:bg-red-500/10 rounded-xl transition-all">
            <i class="fas fa-trash-alt text-xs"></i>
          </button>
        </div>
      `).join('');
    }

    function saveStandardComment(standard, field, value) {
      if (!standardComments[standard]) standardComments[standard] = {};
      // If the value is not a string (e.g. from the old textarea sync), don't overwrite if we are in list mode
      // But for simplicity, we assume we either save string or JSON string
      standardComments[standard][field] = value.startsWith('[') ? JSON.parse(value) : value;
      saveField('standard_comments', `${standard}_${field}`, value);
    }

    // Dynamic Objectives
    function addObjective() {
      // Add a new empty objective and render — user fills it in, saved on input
      tableData.objectives.push('');
      _renderObjectives();
      // Don't save immediately with empty string — save happens on input change
    }

    function removeObjective(index) {
      // Immediate deletion — committed to DB only on Save Draft
      if (tableData.objectives.length > 1) {
        tableData.objectives.splice(index, 1);
      } else {
        tableData.objectives[0] = '';
      }
      _renderObjectives();
      saveObjectives();
      hasChanges = true;
    }

    function updateObjective(index, value) {
      tableData.objectives[index] = value;
      saveObjectives();
    }

    function saveObjectives() {
      // Only persist non-empty entries
      const nonEmpty = tableData.objectives.filter(o => o.trim() !== '');
      saveField('profile', 'program_objectives_list', JSON.stringify(nonEmpty.length > 0 ? tableData.objectives : []));
    }

    // Real render function — numbering OUTSIDE the input field
    function _renderObjectives() {
      const container = document.getElementById('objectives-list-container');
      if (!container) return;

      if (tableData.objectives.length === 0) {
        container.innerHTML = `
          <div class="py-4 text-center border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/30">
            <p class="text-slate-500 text-xs italic">لا توجد أهداف مضافة بعد. انقر "إضافة هدف" للبدء.</p>
          </div>
        `;
        return;
      }

      container.innerHTML = tableData.objectives.map((obj, index) => `
        <div class="flex items-center gap-3 animate-slide-in" id="objective-row-${index}">
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-full text-sm font-bold">${index + 1}</div>
          <input type="text"
                 value="${(obj || '').replace(/"/g, '&quot;')}"
                 placeholder="أدخل الهدف هنا..."
                 class="flex-1 px-4 py-3 rounded-xl bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500/50 focus:outline-none text-slate-900 dark:text-white text-sm transition-all"
                 oninput="updateObjective(${index}, this.value)">
          <button onclick="removeObjective(${index})" title="حذف الهدف"
                  class="flex-shrink-0 p-2.5 text-slate-400 hover:text-red-500 dark:text-slate-500 dark:hover:text-red-400 bg-slate-100 dark:bg-slate-700/30 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      `).join('');

      // Focus the last input if it's new and empty
      const lastObj = tableData.objectives[tableData.objectives.length - 1];
      if (lastObj === '') {
        const lastInput = container.querySelector(`#objective-row-${tableData.objectives.length - 1} input`);
        if (lastInput) setTimeout(() => lastInput.focus(), 50);
      }
    }

    // Fixed Tables Update Logic
    function updateFixedTable(tableName, rowKey, field, value) {
      if (!tableData[tableName]) tableData[tableName] = {};
      if (!tableData[tableName][rowKey]) tableData[tableName][rowKey] = {};

      tableData[tableName][rowKey][field] = parseFloat(value) || 0;

      if (tableName === 'graduates') {
        const row = tableData.graduates[rowKey];
        const grades = ['excellent', 'very_good', 'good', 'pass', 'fail'];

        // Calculate Total
        const total = grades.reduce((sum, g) => sum + (row[g] || 0), 0);

        // Update Total Display
        const totalEl = document.getElementById(`grad-total-${rowKey}`);
        if (totalEl) totalEl.textContent = total;

        // Update Percentages
        grades.forEach(grade => {
          const percentEl = document.getElementById(`grad-percent-${rowKey}-${grade}`);
          if (percentEl) {
            const gradeVal = row[grade] || 0;
            const percentage = total > 0 ? ((gradeVal / total) * 100).toFixed(1) : 0;
            percentEl.textContent = `${percentage}%`;
          }
        });
      }

      saveField('tables', `ft_${tableName}_${rowKey}_${field}`, value);
    }

    function updateResearchTable(indicator, year, value) {
      const key = `${indicator}_${year}`;
      saveField('tables', `res_${key}`, value);
    }

    function updateFacilitiesTable(facility, field, value) {
      const key = `${facility}_${field}`;
      saveField('tables', `fac_${key}`, value);
    }

    // Table Functions - Proposals
    function addProposalRow() {
      const tbody = document.getElementById('proposals-table');
      const rowId = Date.now();

      const row = document.createElement('tr');
      row.className = 'border-b border-slate-200 dark:border-slate-700';
      row.id = `proposal-row-${rowId}`;
      row.innerHTML = `
        <td class="py-3 px-4"><input type="text" placeholder="التوصية" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'recommendation', this.value)"></td>
        <td class="py-3 px-4"><input type="text" placeholder="المسؤول" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'responsible', this.value)"></td>
        <td class="py-3 px-4"><input type="date" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'timeline', this.value)"></td>
        <td class="py-3 px-4"><input type="text" placeholder="الموارد" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'resources', this.value)"></td>
        <td class="py-3 px-4">
          <button onclick="removeTableRow('proposals', ${rowId})" class="text-red-600 dark:text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </td>
      `;

      tbody.appendChild(row);
      tableData.proposals.push({ id: rowId, recommendation: '', responsible: '', timeline: '', resources: '' });
    }

    function updateProposalRow(rowId, field, value) {
      const row = tableData.proposals.find(r => r.id === rowId);
      if (row) {
        row[field] = value;
      }
    }

    // Generic Remove Table Row — committed to DB only on Save Draft
    function removeTableRow(tableName, rowId) {
      const row = document.getElementById(`proposal-row-${rowId}`);
      if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(10px)';
        row.style.transition = 'all 0.25s ease';
        setTimeout(() => row.remove(), 250);
      }
      tableData[tableName] = tableData[tableName].filter(r => r.id !== rowId);
      hasChanges = true;
    }

    // Section 3 Lists
    function addSection3Point(section, field) {
      // Ensure section3Data structure is always fully initialized
      if (!section3Data[section]) section3Data[section] = {};
      if (!Array.isArray(section3Data[section][field])) {
        section3Data[section][field] = [];
      }
      section3Data[section][field].push('');
      renderSection3Points(section, field);
      saveSection3Field(section, field);
      hasChanges = true;
      // Focus the new input
      setTimeout(() => {
        const list = document.getElementById(`sec3-${section}-${field}-list`);
        if (list) {
          const inputs = list.querySelectorAll('input');
          if (inputs.length) inputs[inputs.length - 1].focus();
        }
      }, 60);
    }

    function removeSection3Point(section, field, index) {
      // Immediate deletion — committed to DB only on Save Draft
      if (section3Data[section] && Array.isArray(section3Data[section][field])) {
        section3Data[section][field].splice(index, 1);
        renderSection3Points(section, field);
        saveSection3Field(section, field);
        hasChanges = true;
      }
    }


    function updateSection3Point(section, field, index, value) {
      if (section3Data[section] && Array.isArray(section3Data[section][field])) {
        section3Data[section][field][index] = value;
        saveSection3Field(section, field);
      }
    }

    function saveSection3Field(section, field) {
      // Store as actual array in formData (not a JSON string)
      // The full section3Data merge into formData happens at saveDraft time
      if (!formData[section]) formData[section] = {};
      formData[section][field] = section3Data[section][field];
    }

    function renderSection3Points(section, field) {
      const container = document.getElementById(`sec3-${section}-${field}-list`);
      if (!container) return;

      const points = section3Data[section] && Array.isArray(section3Data[section][field])
        ? section3Data[section][field]
        : [];

      // Empty state — matches section 2 style
      if (points.length === 0) {
        container.innerHTML = `
          <div class="py-5 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/20">
            <i class="fas fa-inbox text-slate-300 dark:text-slate-600 text-2xl mb-2 block"></i>
            <p class="text-slate-400 dark:text-slate-500 text-sm">لا توجد نقاط مضافة بعد — اضغط &laquo;إضافة نقطة&raquo; للبدء</p>
          </div>
        `;
        return;
      }

      const colorMap = {
        'evaluation_procedures':     { color: 'blue',    icon: 'fa-list-ul' },
        'evaluator_recommendations': { color: 'blue',    icon: 'fa-clipboard-check' },
        'actions_taken':             { color: 'blue',    icon: 'fa-reply' },
        'success_aspects':           { color: 'emerald', icon: 'fa-check' },
        'priority_improvements':     { color: 'amber',   icon: 'fa-arrow-up' },
      };
      const { color } = colorMap[field] || { color: 'slate' };

      container.innerHTML = points.map((point, index) => `
        <div class="flex items-center gap-2 group animate-fadeIn">
          <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-${color}-500/20 text-${color}-600 dark:text-${color}-400 rounded-full text-[10px] font-bold">${index + 1}</div>
          <input type="text"
                 value="${(point || '').replace(/"/g, '&quot;')}"
                 placeholder="أدخل النقطة..."
                 class="flex-1 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 focus:border-${color}-500/50 focus:ring-1 focus:ring-${color}-500/30 text-sm text-slate-900 dark:text-white transition-all focus:outline-none"
                 oninput="updateSection3Point('${section}', '${field}', ${index}, this.value)">
          <button onclick="removeSection3Point('${section}', '${field}', ${index})"
                  title="حذف"
                  class="flex-shrink-0 p-2.5 text-slate-400 hover:text-red-500 dark:text-slate-500 dark:hover:text-red-400 bg-slate-100 dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all">
            <i class="fas fa-trash-alt text-xs"></i>
          </button>
        </div>
      `).join('');
    }

    // Progress Tracking
    function updateProgress() {
      const requiredFields = [
        'general_review_date', 'general_review_team_head', 'general_institution_president',
        'program_executive_summary',
        'profile_program_mission', 'profile_program_objectives', 'profile_program_system',
        'evaluations_evaluation_procedures', 'evaluations_evaluator_recommendations'
      ];

      let filled = 0;
      requiredFields.forEach(field => {
        if (formData[field] && formData[field].trim() !== '' && formData[field] !== '[]' && formData[field] !== '[""]') {
          filled++;
        }
      });

      const percent = Math.round((filled / requiredFields.length) * 100);

      document.getElementById('progress-percent').textContent = `${percent}%`;
      document.getElementById('progress-bar').style.width = `${percent}%`;

      // Update section statuses
      const section1Fields = requiredFields.filter(f => f.startsWith('general_') || f.startsWith('program_') || f.startsWith('profile_'));
      const section1Filled = section1Fields.filter(f => formData[f] && formData[f].trim() !== '').length;
      updateSectionStatus(1, section1Filled === section1Fields.length ? 'complete' : section1Filled > 0 ? 'progress' : 'empty');

      const section3Fields = requiredFields.filter(f => f.startsWith('evaluations_'));
      const section3Filled = section3Fields.filter(f => formData[f] && formData[f].trim() !== '' && formData[f] !== '[]' && formData[f] !== '[""]').length;
      updateSectionStatus(3, section3Filled === section3Fields.length ? 'complete' : section3Filled > 0 ? 'progress' : 'empty');

      // Update submit button
      const submitBtn = document.getElementById('submit-btn');
      if (submitBtn) {
        submitBtn.disabled = percent < 100;
      }
    }

    function updateSectionStatus(section, status) {
      const statusEl = document.getElementById(`status-${section}`);
      if (!statusEl) return;
      
      statusEl.classList.remove('bg-slate-500', 'bg-yellow-500', 'bg-emerald-500');

      switch (status) {
        case 'complete':
          statusEl.classList.add('bg-emerald-500');
          break;
        case 'progress':
          statusEl.classList.add('bg-yellow-500');
          break;
        default:
          statusEl.classList.add('bg-slate-500');
      }
    }

    // ── Save Draft (real Laravel POST) ──────────────────────────────
    let hasChanges = false;

    // ── Unsaved Changes Navigation Modal ─────────────────────────────
    let _pendingNavigationHref = null;

    function showUnsavedModal(href) {
      _pendingNavigationHref = href;
      const modal = document.getElementById('unsaved-modal');
      modal.classList.remove('hidden');

      document.getElementById('unsaved-save-btn').onclick = async () => {
        closeUnsavedModal();
        await saveDraft();
        if (_pendingNavigationHref) window.location.href = _pendingNavigationHref;
      };

      document.getElementById('unsaved-leave-btn').onclick = () => {
        hasChanges = false;
        closeUnsavedModal();
        if (_pendingNavigationHref) window.location.href = _pendingNavigationHref;
      };
    }

    function closeUnsavedModal() {
      document.getElementById('unsaved-modal').classList.add('hidden');
      _pendingNavigationHref = null;
    }

    // Close unsaved modal on backdrop click
    document.getElementById('unsaved-modal').addEventListener('click', function(e) {
      if (e.target === this) closeUnsavedModal();
    });

    // Intercept all <a> link navigations (sidebar back links etc.)
    document.addEventListener('click', function(e) {
      const anchor = e.target.closest('a[href]');
      if (!anchor) return;
      const href = anchor.getAttribute('href');
      if (!href || href === '#' || href.startsWith('javascript')) return;
      if (hasChanges) {
        e.preventDefault();
        showUnsavedModal(anchor.href);
      }
    });

    // Trap browser-level navigation (reload/tab close) — show custom modal via keyboard interception
    // We still keep a minimal beforeunload, but also intercept F5/Ctrl+R to show our custom modal
    window.addEventListener('beforeunload', (e) => {
      if (hasChanges) {
        e.preventDefault();
        e.returnValue = '';
      }
    });

    // Intercept F5 / Ctrl+R / Ctrl+Shift+R to show the custom unsaved modal instead
    document.addEventListener('keydown', (e) => {
      if (!hasChanges) return;
      const isReload = e.key === 'F5' ||
        (e.ctrlKey && (e.key === 'r' || e.key === 'R')) ||
        (e.metaKey && (e.key === 'r' || e.key === 'R'));
      if (isReload) {
        e.preventDefault();
        showUnsavedModal(window.location.href);
        // Override leave button to reload instead of navigate
        document.getElementById('unsaved-leave-btn').onclick = () => {
          hasChanges = false;
          closeUnsavedModal();
          window.location.reload();
        };
      }
    });

    // Capture changes on any input
    document.addEventListener('input', (e) => {
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
        hasChanges = true;
      }
    });

    async function saveDraft() {
      const btn = document.getElementById('save-draft-btn');
      const icon = document.getElementById('save-draft-icon');
      const text = document.getElementById('save-draft-text');

      if (btn) {
        btn.disabled = true;
        icon.className = 'fa-solid fa-circle-notch fa-spin';
        text.textContent = 'جاري الحفظ...';
      }

      showToast('جاري حفظ التغييرات...');

      // Gather evidences from DOM
      const evidencesPayload = {};
      document.querySelectorAll('.indicator-row').forEach(row => {
        const indId = row.dataset.indicatorId;
        const evs = [];
        row.querySelectorAll('.evidence-item').forEach(evLine => {
          if (evLine.dataset.id) {
            evs.push({ id: evLine.dataset.id, file_name: evLine.dataset.name });
          } else if (evLine.dataset.tempPath) {
            evs.push({ temp_path: evLine.dataset.tempPath, file_name: evLine.dataset.name });
          }
        });
        if (evs.length > 0) {
          evidencesPayload[indId] = evs;
        }
      });

      // Merge section3 and tableData into formData before saving
      formData['standard_comments'] = standardComments;
      formData['evaluations'] = section3Data.evaluations;
      formData['results'] = section3Data.results;
      formData['tables'] = tableData;

      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      try {
        const resp = await fetch(window.SAVE_URL, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            form_data: formData,
            scores: scores,
            evidences: evidencesPayload
          }),
        });
        const json = await resp.json();
        if (json.success) {
          hasChanges = false;
          showToast('تم حفظ المسودة بنجاح', 'success');
        } else {
          showToast('حدث خطأ أثناء الحفظ', 'error');
        }
      } catch (e) {
        showToast('تعذر الاتصال بالخادم', 'error');
      } finally {
        if (btn) {
          btn.disabled = false;
          icon.className = 'fa-solid fa-floppy-disk';
          text.textContent = 'حفظ كمسودة';
        }
      }
    }

    // ── Helper: get Rating Color ─────────────────────────────────────
    function getRatingColor(rating) {
      const map = {
        1: '#ef4444',
        2: '#f97316',
        3: '#eab308',
        4: '#84cc16',
        5: '#10b981',
        0: '#312e81'
      };
      return map[rating] || '';
    }

    // ── Rating by Indicator ID (DB-based) ────────────────────────────
    function setRatingById(indicatorId, standardId, rating) {
      if (scores[indicatorId] === rating) {
        // Toggle off
        scores[indicatorId] = null;
        rating = null;
      } else {
        scores[indicatorId] = rating;
      }

      // Visual update
      document.querySelectorAll(`[data-indicator-id="${indicatorId}"] .rating-btn`).forEach(btn => {
        const btnRating = parseInt(btn.dataset.rating);
        if (btnRating === rating) {
          btn.style.backgroundColor = getRatingColor(rating);
          btn.style.borderColor = getRatingColor(rating);
          btn.style.color = '#fff';
          btn.style.transform = 'scale(1.05)';
        } else {
          btn.style.backgroundColor = '';
          btn.style.borderColor = '';
          btn.style.color = '';
          btn.style.transform = '';
        }
      });

      // Recalculate standard average from this standard's indicators
      updateStandardScoreById(standardId);
      hasChanges = true;
    }

    function updateStandardScoreById(standardId) {
      // Collect all indicator IDs that belong to this standard's tab
      const container = document.getElementById(`standard-${standardId}-tab-content`);
      if (!container) return;
      const indicatorEls = container.querySelectorAll('[data-indicator-id]');
      const vals = [];
      indicatorEls.forEach(el => {
        const id = el.dataset.indicatorId;
        // Include non-compliant (0) or skip it? Usually '0' implies 0 in avg for non-compliant. If you skip, it's not penalized. Let's include it if defined.
        if (scores[id] !== undefined && scores[id] !== null && scores[id] !== '') {
          vals.push(scores[id]);
        }
      });
      const scoreEl = document.getElementById(`standard-${standardId}-score`);
      if (scoreEl && vals.length > 0) {
        const avg = (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1);
        scoreEl.textContent = avg;
        scoreEl.className = `text-3xl font-bold ${avg >= 4 ? 'text-emerald-400' : avg >= 3 ? 'text-yellow-400' : 'text-red-400'}`;
      } else if (scoreEl) {
        scoreEl.textContent = '—';
      }
    }

    // ── Evidence Upload ───────────────────────────────────────────────
    function toggleSubStandard(id, btn) {
      const content = document.getElementById(`sub-${id}-content`);
      const svg = btn.querySelector('svg');
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        svg.classList.add('rotate-180');
      } else {
        content.classList.add('hidden');
        svg.classList.remove('rotate-180');
      }
    }

    function viewEvidenceTemp(btn) {
      const tempPath = btn.dataset.path;
      const savedUrl = btn.dataset.savedUrl;
      
      if (savedUrl) {
        window.open('/stage-three/view-file?path=' + encodeURIComponent(savedUrl), '_blank');
      } else if (tempPath) {
        window.open('/stage-three/view-file?path=' + encodeURIComponent(tempPath), '_blank');
      } else {
        alert('مسار الملف غير متوفر.');
      }
    }

    function prepareEvidenceRow(indicatorId, evalId) {
      const container = document.getElementById(`evidences-ind-${indicatorId}`);
      if (!container) return;

      // Hide "No evidence" message if exists
      const msg = container.querySelector('.no-evidence-msg');
      if (msg) msg.style.display = 'none';
      
      const pendingId = `pending-${Date.now()}`;
      const row = document.createElement('div');
      row.id = pendingId;
      row.className = 'evidence-pending bg-blue-50/50 dark:bg-blue-900/10 border-2 border-dashed border-blue-300 dark:border-blue-700/50 rounded-xl p-3 flex flex-col sm:flex-row items-center gap-3 animate-fadeIn mb-2';
      row.innerHTML = `
        <div class="flex-1 w-full relative">
            <input type="text" placeholder="اسم الدليل (مثال: محضر اجتماع 1)" class="ev-name-input w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition-all">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input type="file" class="hidden ev-file-input" accept=".pdf" onchange="handleEvidenceFileSelection(this, ${indicatorId}, ${evalId}, '${pendingId}')">
            <button onclick="this.previousElementSibling.click()" class="flex-1 sm:flex-none justify-center bg-slate-800 hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-file-upload"></i> إرفاق الدليل
            </button>
            <button onclick="
              this.closest('.evidence-pending').remove(); 
              if(document.getElementById('evidences-ind-${indicatorId}').children.length === 1 && document.getElementById('evidences-ind-${indicatorId}').querySelector('.no-evidence-msg')) {
                document.getElementById('evidences-ind-${indicatorId}').querySelector('.no-evidence-msg').style.display = 'block';
              }
            " class="text-slate-400 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 w-9 h-9 rounded-lg flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
      `;
      container.appendChild(row);
      setTimeout(() => { row.querySelector('.ev-name-input').focus(); }, 100);
    }

    async function handleEvidenceFileSelection(fileInput, indicatorId, evalId, pendingRowId) {
      const file = fileInput.files[0];
      if (!file) return;

      const row = document.getElementById(pendingRowId);
      if (!row) return;

      const nameInput = row.querySelector('.ev-name-input');
      if (!nameInput.value.trim()) {
        nameInput.value = file.name.replace(/\.[^/.]+$/, "");
      }
      const finalName = nameInput.value.trim();

      row.className = 'evidence-item bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-xl p-3 flex items-center gap-3 shadow-sm animate-pulse';
      row.innerHTML = `
          <i class="fas fa-circle-notch fa-spin text-amber-500"></i>
          <span class="flex-1 text-sm font-medium text-amber-800 dark:text-amber-200 truncate">جاري الرفع: ${finalName}</span>
      `;

      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const fd = new FormData();
      fd.append('file', file);
      fd.append('file_name', finalName);
      fd.append('_token', csrfToken);

      try {
        const resp = await fetch(window.UPLOAD_BASE, {
          method: 'POST',
          body: fd,
        });
        const json = await resp.json();
        
        if (json.success) {
          row.remove();
          appendEvidenceRow(indicatorId, { temp_path: json.temp_path, file_name: json.file_name });
          hasChanges = true;
          showToast('تم الرفع!', 'success');
        } else {
          showToast('فشل الرفع', 'error');
          row.remove();
        }
      } catch (e) {
        showToast('تعذر الاتصال بالخادم', 'error');
        row.remove();
      }
    }

    function appendEvidenceRow(indicatorId, evidence) {
      const container = document.getElementById(`evidences-ind-${indicatorId}`);
      if (!container) return;
      
      const msg = container.querySelector('.no-evidence-msg');
      if (msg) msg.style.display = 'none';
      
      const rowId = evidence.id ? `evidence-saved-${evidence.id}` : `evidence-temp-${Date.now()}`;
      
      const row = document.createElement('div');
      row.className = 'evidence-item bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-2.5 flex items-center gap-3 shadow-sm group transition-all hover:border-blue-300';
      row.id = rowId;
      row.dataset.name = evidence.file_name;
      
      if (evidence.id) {
        row.dataset.id = evidence.id;
      } else {
        row.dataset.tempPath = evidence.temp_path;
      }
      
      row.innerHTML = `
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-file-pdf text-red-500"></i>
        </div>
        <span class="flex-1 text-sm font-medium text-slate-800 dark:text-slate-200 truncate" title="${evidence.file_name}">${evidence.file_name}</span>
        
        <div class="flex items-center gap-1">
          <button onclick="viewEvidenceTemp(this)" data-path="${evidence.temp_path || ''}" data-saved-url="${evidence.file_path || ''}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors" title="عرض الدليل">
            <i class="fas fa-external-link-alt text-xs"></i>
          </button>
          <button onclick="removeEvidenceRow('${rowId}')" class="text-red-500 hover:text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 w-8 h-8 flex items-center justify-center rounded-lg transition-colors" title="حذف">
            <i class="fas fa-trash-alt text-xs"></i>
          </button>
        </div>
      `;
      container.appendChild(row);
    }

    function removeEvidenceRow(rowId) {
      // Immediate deletion — committed to DB only on Save Draft
      const row = document.getElementById(rowId);
      if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(10px)';
        row.style.transition = 'all 0.25s ease';
        setTimeout(() => row.remove(), 250);
        hasChanges = true;
        showToast('تم حذف الملف — سيُطبَّق عند حفظ المسودة', 'info');
      }
    }

    // Submit Report
    function submitReport() {
      missingFields = [];

      const requiredFieldsMap = {
        'review_date': 'تاريخ التقييم / المراجعة',
        'review_team_head': 'اسم رئيس فريق المراجعة الداخلية',
        'institution_president': 'اسم رئيس المؤسسة / الجامعة',
        'executive_summary': 'الملخص التنفيذي',
        'program_mission': 'رسالة البرنامج',
        'program_objectives': 'أهداف البرنامج',
        'evaluation_procedures': 'إجراءات التقييم',
        'evaluator_recommendations': 'توصيات المقيمين'
      };

      Object.entries(requiredFieldsMap).forEach(([id, label]) => {
        if (id === 'program_objectives') {
          if (!tableData.objectives.some(o => o.trim() !== '')) missingFields.push({ id, label });
          return;
        }
        if (id === 'evaluation_procedures') {
          if (!section3Data.evaluations.evaluation_procedures.some(o => o.trim() !== '')) missingFields.push({ id, label });
          return;
        }
        if (id === 'evaluator_recommendations') {
          if (!section3Data.evaluations.evaluator_recommendations.some(o => o.trim() !== '')) missingFields.push({ id, label });
          return;
        }

        const el = document.getElementById(id);
        if (el && (!el.value || el.value.trim() === '')) {
          missingFields.push({ id, label });
        }
      });

      // Check radio buttons
      const programSystem = document.querySelector('input[name="program_system"]:checked');
      if (!programSystem) {
        missingFields.push({ id: 'program_system', label: 'نظام البرنامج' });
      }

      if (missingFields.length > 0) {
        showValidationModal();
      } else {
        showToast('تم رفع التقرير للمراجعة بنجاح');
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-btn').innerHTML = `
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          تم الرفع للمراجعة
        `;
      }
    }

    // Validation Modal
    function showValidationModal() {
      const modal = document.getElementById('validation-modal');
      const list = document.getElementById('validation-list');

      list.innerHTML = missingFields.map(field => `
        <div class="bg-slate-100 dark:bg-slate-700/50 rounded-lg p-3 flex items-center justify-between">
          <span class="text-slate-900 dark:text-white text-sm">${field.label}</span>
          <button onclick="goToField('${field.id}')" class="text-blue-400 text-sm hover:text-blue-300">
            انتقال
          </button>
        </div>
      `).join('');

      modal.classList.remove('hidden');
    }

    function closeValidationModal() {
      document.getElementById('validation-modal').classList.add('hidden');
    }

    function goToField(fieldId) {
      closeValidationModal();

      // Determine section and tab
      const fieldSections = {
        'review_date': { section: 1, tab: 'general' },
        'review_team_head': { section: 1, tab: 'general' },
        'institution_president': { section: 1, tab: 'general' },
        'executive_summary': { section: 1, tab: 'program' },
        'program_mission': { section: 1, tab: 'profile' },
        'program_objectives': { section: 1, tab: 'profile' },
        'program_system': { section: 1, tab: 'profile' },
        'evaluation_procedures': { section: 3, tab: null },
        'evaluator_recommendations': { section: 3, tab: null }
      };

      const location = fieldSections[fieldId];
      if (location) {
        switchSection(location.section);
        if (location.tab) {
          switchTab(location.tab);
        }

        setTimeout(() => {
          const el = document.getElementById(fieldId);
          if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.focus();
            el.classList.add('ring-2', 'ring-red-500');
            setTimeout(() => el.classList.remove('ring-2', 'ring-red-500'), 3000);
          } else {
            const radioContainer = document.querySelector('input[name="program_system"]')?.parentElement?.parentElement;
            if (radioContainer) {
              radioContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
              radioContainer.classList.add('ring-2', 'ring-red-500');
              setTimeout(() => radioContainer.classList.remove('ring-2', 'ring-red-500'), 3000);
            }
          }
        }, 300);
      }
    }

    function goToFirstMissingField() {
      if (missingFields.length > 0) {
        goToField(missingFields[0].id);
      }
    }

    // Show notification (Stage 2 style)
    function showNotification(message, type = 'info') {
      const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        info: 'bg-blue-600'
      };
      const area = document.getElementById('notificationArea');
      if (!area) return;
      const el = document.createElement('div');
      el.className = `${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-lg text-sm font-bold flex items-center gap-2 animate-slide-in`;
      const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
      el.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i> ${message}`;
      area.appendChild(el);
      setTimeout(() => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.3s';
        setTimeout(() => el.remove(), 300);
      }, 3000);
    }

    // Replace showToast calls with showNotification compatibility wrapper
    function showToast(message, type = 'success') {
      showNotification(message, type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'));
    }

    // Saving Indicator
    function showSavingIndicator() {
      document.getElementById('save-indicator').classList.remove('bg-emerald-500');
      document.getElementById('save-indicator').classList.add('bg-yellow-500');
      document.getElementById('save-status').textContent = 'جاري الحفظ...';
    }

    function hideSavingIndicator() {
      setTimeout(() => {
        document.getElementById('save-indicator').classList.remove('bg-yellow-500');
        document.getElementById('save-indicator').classList.add('bg-emerald-500');
        document.getElementById('save-status').textContent = 'تم الحفظ تلقائياً';
      }, 500);
    }

    // ── Initialize from saved data ─────────────────────────────────
    (function initFromSaved() {
      // Apply saved scores visually
      Object.entries(scores).forEach(([indId, score]) => {
        if (score === null || score === undefined) return;
        document.querySelectorAll(`[data-indicator-id="${indId}"] .rating-btn`).forEach(btn => {
          const btnRating = parseInt(btn.dataset.rating);
          if (btnRating === parseInt(score)) {
            btn.style.backgroundColor = getRatingColor(parseInt(score));
            btn.style.color = '#fff';
          }
        });
      });

      // Load standard comments from saved formData
      const savedComments = formData['standard_comments'] || {};
      Object.assign(standardComments, savedComments);

      // section3Data is already loaded at declaration time using _parseS3Array


      // Load tables data from saved formData
      if (formData['tables']) {
        const ft = formData['tables'];
        if (ft['graduates']) Object.assign(tableData.graduates, ft['graduates']);
        if (ft['research']) Object.assign(tableData.research, ft['research']);
        if (ft['facilities']) Object.assign(tableData.facilities, ft['facilities']);
        if (Array.isArray(ft['proposals'])) tableData.proposals = ft['proposals'];
        if (Array.isArray(ft['objectives'])) tableData.objectives = ft['objectives'];
      }

      // Fill Section 1 simple text inputs from saved formData
      ['review_team_head','review_date'].forEach(key => {
        const el = document.getElementById(key);
        if (el && formData['general'] && formData['general'][key] != null) el.value = formData['general'][key];
      });
      ['coordinator_name','coordinator_title','coordinator_email','coordinator_phone','report_date'].forEach(key => {
        const el = document.getElementById(key);
        if (el && formData['program'] && formData['program'][key] != null) el.value = formData['program'][key];
      });
      // NOTE: program_mission and other textareas that are server-rendered don't need re-hydration here.
      // But we still do it for fields like selects and number inputs.
      ['program_system','credit_hours','courses_total','male_students_count',
       'female_students_count','dept_council_date','college_council_date','academic_council_date',
       'university_council_date','program_history','env_changes','self_study_arrangements','comparison_methodology'
      ].forEach(key => {
        const el = document.getElementById(key);
        if (el && formData['profile'] && formData['profile'][key] != null) el.value = formData['profile'][key];
      });

      // ── Populate saved graduates table inputs ──────────────────────
      const gradYears = ['last_year', 'prev_year', 'two_years_ago'];
      const gradGrades = ['excellent', 'very_good', 'good', 'pass', 'fail'];
      gradYears.forEach(rowKey => {
        // Year display label
        const yearInput = document.querySelector(`[data-row-group="${rowKey}"] input[type="text"]`);
        if (yearInput && tableData.graduates[rowKey] && tableData.graduates[rowKey]['year_display']) {
          yearInput.value = tableData.graduates[rowKey]['year_display'];
        }
        // Also check for ft_graduates_ROWKEY_year_display stored in formData.tables flat
        if (yearInput && formData['tables'] && formData['tables'][`ft_graduates_${rowKey}_year_display`]) {
          yearInput.value = formData['tables'][`ft_graduates_${rowKey}_year_display`];
        }
        // Grade counts
        const rowEl = document.querySelector(`[data-row-group="${rowKey}"]`);
        if (rowEl) {
          const inputs = rowEl.querySelectorAll('input[type="number"]');
          gradGrades.forEach((grade, gi) => {
            // Check nested object first
            let val = tableData.graduates[rowKey] && tableData.graduates[rowKey][grade];
            // Fallback: flat key in formData.tables
            if ((val === undefined || val === null) && formData['tables']) {
              val = formData['tables'][`ft_graduates_${rowKey}_${grade}`];
            }
            if (val != null && inputs[gi]) {
              inputs[gi].value = val;
              // Trigger total recalculation
              updateFixedTable('graduates', rowKey, grade, val);
            }
          });
        }
      });

      // ── Populate saved research table inputs ───────────────────────
      const researchKeys = ['intl_journals_indexed','arabic_journals_reviewed','local_journals_reviewed',
        'faculty_publications','faculty_textbooks','faculty_translated_books',
        'master_theses_discussed','phd_dissertations_discussed','conferences_workshops_organized'];
      researchKeys.forEach((key, i) => {
        // flat key: res_KEY_count stored in formData.tables
        const val = formData['tables'] && formData['tables'][`res_${key}_count`];
        if (val != null) {
          const input = document.querySelector(`#research-table tr:nth-child(${i + 1}) input`);
          if (input) input.value = val;
        }
      });

      // ── Populate saved facilities table inputs ─────────────────────
      const facilityKeys = ['classrooms','spec_labs','comp_labs','library','admin_offices','student_lounges','sports','others'];
      const facilityFields = ['count','area','students','hours'];
      facilityKeys.forEach((fKey, ri) => {
        facilityFields.forEach((field, fi) => {
          const val = formData['tables'] && formData['tables'][`fac_${fKey}_${field}`];
          if (val != null) {
            const input = document.querySelector(`#facilities-table tr:nth-child(${ri + 1}) td:nth-child(${fi + 2}) input`);
            if (input) input.value = val;
          }
        });
      });

      // ── Render Objectives ──────────────────────────────────────────
      // Load objectives from the profile section if stored there (backward compat)
      if (tableData.objectives.length === 0 && formData['profile'] && formData['profile']['program_objectives_list']) {
        try { tableData.objectives = JSON.parse(formData['profile']['program_objectives_list']); } catch(e) {}
      }
      _renderObjectives();

      // ── Render Section 3 lists ─────────────────────────────────────
      ['evaluation_procedures', 'evaluator_recommendations', 'actions_taken'].forEach(field => {
        renderSection3Points('evaluations', field);
      });
      ['success_aspects', 'priority_improvements'].forEach(field => {
        renderSection3Points('results', field);
      });

      // ── Render standard comment lists for all standards ────────────
      document.querySelectorAll('[id^="std-"][id$="-strengths-list"]').forEach(el => {
        const parts = el.id.split('-');
        const stdId = parts[1];
        ['strengths','improvements','priorities'].forEach(field => renderCommentPoints(stdId, field));
      });

      // ── Render saved proposal rows ─────────────────────────────────
      if (Array.isArray(tableData.proposals) && tableData.proposals.length > 0) {
        const tbody = document.getElementById('proposals-table');
        if (tbody) {
          tableData.proposals.forEach(proposal => {
            const rowId = proposal.id || Date.now();
            const row = document.createElement('tr');
            row.className = 'border-b border-slate-200 dark:border-slate-700';
            row.id = `proposal-row-${rowId}`;
            row.innerHTML = `
              <td class="py-3 px-4"><input type="text" placeholder="التوصية" class="w-full px-3 py-2 rounded-lg" value="${(proposal.recommendation || '').replace(/"/g,'&quot;')}" oninput="updateProposalRow(${rowId}, 'recommendation', this.value)"></td>
              <td class="py-3 px-4"><input type="text" placeholder="المسؤول" class="w-full px-3 py-2 rounded-lg" value="${(proposal.responsible || '').replace(/"/g,'&quot;')}" oninput="updateProposalRow(${rowId}, 'responsible', this.value)"></td>
              <td class="py-3 px-4"><input type="date" class="w-full px-3 py-2 rounded-lg" value="${proposal.timeline || ''}" oninput="updateProposalRow(${rowId}, 'timeline', this.value)"></td>
              <td class="py-3 px-4"><input type="text" placeholder="الموارد" class="w-full px-3 py-2 rounded-lg" value="${(proposal.resources || '').replace(/"/g,'&quot;')}" oninput="updateProposalRow(${rowId}, 'resources', this.value)"></td>
              <td class="py-3 px-4">
                <button onclick="removeTableRow('proposals', ${rowId})" class="text-red-600 dark:text-red-400 hover:text-red-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </td>
            `;
            tbody.appendChild(row);
          });
        }
      }
    })();

    // All rendering is now handled inside initFromSaved above.
  </script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9c162da1c32df9ec',t:'MTc2ODk5MTg2Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9d7c102002e55456',t:'MTc3Mjc0NDU2MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
</body>

</html>