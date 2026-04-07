<!doctype html>
<html lang="ar" dir="rtl" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نظام الدراسة الذاتية للبرنامج</title>


  {{-- Font Awesome 6.4.0 --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  {{-- Vite-compiled assets (Tailwind v4 + FlyonUI) --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
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
      transform: scale(1.15);
      box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
    }

    .tooltip-container {
      position: relative;
    }

    .tooltip-text {
      visibility: hidden;
      opacity: 0;
      position: absolute;
      z-index: 100;
      background: #334155;
      color: #f1f5f9;
      padding: 12px;
      border-radius: 8px;
      font-size: 12px;
      width: 250px;
      bottom: 100%;
      right: 50%;
      transform: translateX(50%);
      margin-bottom: 8px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .tooltip-container:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
    }

    input,
    textarea,
    select {
      background: #1e293b !important;
      border: 1px solid #334155 !important;
      color: #f1f5f9 !important;
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
      background: #475569;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: #64748b;
    }

    .indicator-row {
      transition: all 0.3s ease;
    }

    .indicator-row:hover {
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
            <p class="text-xs text-slate-500 dark:text-slate-400">نظام إدارة البرامج</p>
          </div>
        </div>
        <div class="mt-4 flex justify-center">
          <button onclick="toggleDarkMode()"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2 text-sm font-medium border border-slate-200 dark:border-slate-700">
            <i class="fas fa-moon dark:hidden"></i>
            <i class="fas fa-sun hidden dark:block"></i>
            <span class="dark:hidden">الوضع الليلي</span>
            <span class="hidden dark:inline">الوضع النهاري</span>
          </button>
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
          class="sidebar-item active w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-700/50">
          <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center"><span
              class="text-blue-400 font-bold">١</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-white">الجزء الأول</span> <span
              class="text-xs text-slate-400">بيانات البرنامج</span>
          </div>
          <div id="status-1" class="w-3 h-3 rounded-full bg-yellow-500"></div>
        </button>
        {{-- Part 2 --}}
        <button onclick="switchSection(2); switchStandardTab(1)" id="nav-2"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-700/50">
          <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center"><span
              class="text-emerald-400 font-bold">٢</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-white">الجزء الثاني</span> <span
              class="text-xs text-slate-400">التقييم وفق المعايير</span>
          </div>
        </button>
        {{-- Part 3 --}}
        <button onclick="switchSection(3)" id="nav-3"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all group">
          <div
            class="w-10 h-10 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <span class="text-purple-600 dark:text-purple-400 font-bold">٣</span>
          </div>
          <div
            class="flex-1 text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
            <span class="block font-medium">الجزء الثالث</span> <span class="text-xs text-slate-400">التقييمات
              والنتائج</span>
          </div>
          <div id="status-3" class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700"></div>
        </button>
      </nav>

    </aside>
    {{--! Main Content --}}
    <main class="flex-1 mr-72 h-full overflow-y-auto custom-scrollbar bg-slate-900">
      {{--? Section 1: program data --}}
      <div id="section-1" class="section-content p-8 fade-in">
        {{--! Header --}}
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">الجزء الأول: الدراسة الذاتية</h2>
          </div>
          <p class="text-slate-500 dark:text-slate-400 mr-5 font-medium">إدخال وتحرير كافة بيانات الدراسة الذاتية
            للبرنامج</p>
        </div>
        {{--! Tabs Navigation --}}
        <div
          class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          <button onclick="switchTab('general')"
            class="tab-btn active px-6 py-2.5 rounded-xl text-sm font-semibold transition-all bg-blue-600 text-white shadow-md shadow-blue-500/20"
            data-tab="general"> معلومات عامة </button> <button onclick="switchTab('program')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="program"> بيانات تعريفية بالبرنامج </button> <button onclick="switchTab('profile')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="profile"> ملف البرنامج </button> <button onclick="switchTab('tables')"
            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-tab="tables"> الجداول والبيانات </button>
        </div>
        {{--! Tab Contents --}}
        <div id="tab-general" class="tab-content">
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a. 0z" />
              </svg> المعلومات العامة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">{{--! Auto Fields --}}
              <div><label class="block text-sm text-slate-400 mb-2">اسم المؤسسة / الجامعة</label> <input type="text"
                  value="جامعة الملك سعود" disabled class="w-full px-4 py-3 rounded-xl opacity-60 cursor-not-allowed">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2"> اسم رئيس المؤسسة/ الجامعة</label> <input
                  type="text" value="سعود بن عبدالعزيز الحوثري" disabled
                  class="w-full px-4 py-3 rounded-xl opacity-60 cursor-not-allowed">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">اسم رئيس فريق المراجعة الداخلية <span
                    class="text-red-400">*</span></label> <input type="text" id="review_team_head"
                  placeholder="أدخل الاسم" class="w-full px-4 py-3 rounded-xl"
                  onchange="saveField('general', 'review_team_head', this.value)">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">تاريخ التقييم / المراجعة <span
                    class="text-red-400">*</span></label> <input type="date" id="review_date"
                  class="w-full px-4 py-3 rounded-xl" onchange="saveField('general', 'review_date', this.value)">
              </div>
            </div>
          </div>
        </div>
        <div id="tab-program" class="tab-content hidden">
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg> البيانات التعريفية بالبرنامج
            </h3>{{--! Auto-filled Institution Info --}}
            <div class="bg-slate-700/50 rounded-xl p-5 mb-6">
              <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                <div><span class="text-xs text-slate-400 block mb-1">الجامعة</span> <span
                    class="text-white font-medium">جامعة الملك سعود</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">الكلية</span> <span
                    class="text-white font-medium">كلية الهندسة</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">القسم العلمي</span> <span
                    class="text-white font-medium">الحاسبات</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">اسم البرنامج</span> <span
                    class="text-white font-medium">هندسة الحاسب الآلي</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">تاريخ التأسيس</span> <span
                    class="text-white font-medium">1430 هـ</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">عنوان الموقع الإلكتروني</span> <span
                    class="link text-blue-400 font-medium">www.ksu.edu.sa/ce</span>
                </div>
              </div>
            </div>
            <div class="space-y-6">
              <div><label class="block text-sm text-slate-400 mb-2">الملخص التنفيذي للنتيجة الإجمالية لتقييم معايير
                  الاعتماد البرامجي <span class="text-red-400">*</span></label> <textarea id="executive_summary"
                  rows="6"
                  placeholder="اكتب ملخصاً تنفيذياً شاملاً يتضمن النتيجة الإجمالية، جوانب القوة، وجوانب التحسين..."
                  class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('program', 'executive_summary', this.value)"></textarea>
                <p class="text-xs text-slate-500 mt-2">يجب أن يتضمن: النتيجة الإجمالية، جوانب القوة، وجوانب التحسين</p>
              </div>
              <div>
                <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg> بيانات منسق إعداد التقرير
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div><label class="block text-sm text-slate-400 mb-2">الاسم</label>
                    <input type="text" id="coordinator_name" placeholder="أدخل اسم المنسق"
                      class="w-full px-4 py-3 rounded-xl"
                      onchange="saveField('program', 'coordinator_name', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">الصفة</label>
                    <input type="text" id="coordinator_title" placeholder="الصفة الوظيفية"
                      class="w-full px-4 py-3 rounded-xl"
                      onchange="saveField('program', 'coordinator_title', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">البريد الإلكتروني</label>
                    <input type="email" id="coordinator_email" placeholder="example@ksu.edu.sa"
                      class="w-full px-4 py-3 rounded-xl text-right" dir="ltr"
                      onchange="saveField('program', 'coordinator_email', this.value)">
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">رقم الهاتف</label>
                    <input type="tel" id="coordinator_phone" placeholder="05xxxxxxxx"
                      class="w-full px-4 py-3 rounded-xl text-right" dir="ltr"
                      onchange="saveField('program', 'coordinator_phone', this.value)">
                  </div>
                  <div class="md:col-span-2"><label class="block text-sm text-slate-400 mb-2">تاريخ إعداد
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
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg> ملف البرنامج
            </h3>
            <div class="space-y-6">
              <div class="space-y-8">
                <div><label class="block text-sm text-slate-400 mb-2">رسالة البرنامج <span
                      class="text-red-400">*</span></label> <textarea id="program_mission" rows="4"
                    placeholder="اكتب رسالة البرنامج بوضوح..." class="w-full px-4 py-3 rounded-xl resize-none"
                    onchange="saveField('profile', 'program_mission', this.value)"></textarea>
                </div>

                {{--! Dynamic Objectives --}}
                <div class="pt-6 border-t border-slate-700">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-bold text-white flex items-center gap-2">
                      <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg> أهداف البرنامج (على شكل نقاط) <span class="text-red-400">*</span>
                    </h4>
                    <button onclick="addObjective()"
                      class="text-blue-400 text-sm flex items-center gap-1 hover:text-blue-300">
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
                <div class="pt-6 border-t border-slate-700">
                  <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg> نظام البرنامج وساعاته ومقرراته
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div><label class="block text-sm text-slate-400 mb-2">نظام البرنامج</label>
                      <select id="program_system" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'program_system', this.value)">
                        <option value="semester">نظام فصلي</option>
                        <option value="annual">نظام سنوي</option>
                        <option value="modules">نظام وحدات</option>
                      </select>
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2">عدد الساعات المعتمدة للبرنامج</label>
                      <input type="number" id="credit_hours" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'credit_hours', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2">عدد المقررات</label>
                      <input type="number" id="courses_total" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'courses_total', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Student Numbers --}}
                <div class="pt-6 border-t border-slate-700">
                  <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0H6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg> عدد الطلبة المقيدين بالبرنامج
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block text-sm text-slate-400 mb-2">عدد الذكور</label>
                      <input type="number" id="male_students_count" placeholder="0" class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'male_students_count', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2">عدد الإناث</label>
                      <input type="number" id="female_students_count" placeholder="0"
                        class="w-full px-4 py-3 rounded-xl"
                        onchange="saveField('profile', 'female_students_count', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Approval Dates --}}
                <div class="pt-6 border-t border-slate-700">
                  <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg> تواريخ اعتماد التوصيف الحالي للبرنامج من مجالس الجامعة
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div><label class="block text-sm text-slate-400 mb-2 text-xs">مجلس القسم</label>
                      <input type="date" id="dept_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'dept_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2 text-xs">مجلس الكلية</label>
                      <input type="date" id="college_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'college_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2 text-xs">المجلس الأكاديمي</label>
                      <input type="date" id="academic_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'academic_council_date', this.value)">
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2 text-xs">مجلس الجامعة</label>
                      <input type="date" id="university_council_date" class="w-full px-3 py-3 rounded-xl text-sm"
                        onchange="saveField('profile', 'university_council_date', this.value)">
                    </div>
                  </div>
                </div>

                {{--! Program Context --}}
                <div class="pt-6 border-t border-slate-700">
                  <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> سياق البرنامج وتاريخه
                  </h4>
                  <div class="space-y-6">
                    <div><label class="block text-sm text-slate-400 mb-2">موجز عن تاريخ البرنامج</label>
                      <textarea id="program_history" rows="4" placeholder="اكتب موجز تاريخ البرنامج هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-700/50 border border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'program_history', this.value)"></textarea>
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2">التغيرات في البيئة الداخلية والخارجية
                        للبرنامج</label>
                      <textarea id="env_changes" rows="4" placeholder="اكتب التغيرات هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-700/50 border border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'env_changes', this.value)"></textarea>
                    </div>
                  </div>
                </div>

                {{--! Self Study Details --}}
                <div class="pt-6 border-t border-slate-700">
                  <h4 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg> الدراسة الذاتية للبرنامج
                  </h4>
                  <div class="space-y-6">
                    <div><label class="block text-sm text-slate-400 mb-2">ترتيبات إجراء الدراسة الذاتية</label>
                      <textarea id="self_study_arrangements" rows="4" placeholder="اكتب الترتيبات هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-700/50 border border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        onchange="saveField('profile', 'self_study_arrangements', this.value)"></textarea>
                    </div>
                    <div><label class="block text-sm text-slate-400 mb-2">منهجية المقارنة الداخلية والخارجية</label>
                      <textarea id="comparison_methodology" rows="4" placeholder="اكتب المنهجية هنا..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-700/50 border border-slate-600 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
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
                  <tr class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm">
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
                          <span class="text-[10px] uppercase text-slate-400 dark:text-slate-500">العام الأكاديمي</span>
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
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg> البحث العلمي والأنشطة البحثية للبرنامج خلال العام السابق للتقييم
            </h3>
            <div class="overflow-x-auto">
              <table class="w-full text-right border-collapse">
                <thead>
                  <tr class="bg-slate-700/50 text-slate-300 text-sm">
                    <th class="py-3 px-4 font-semibold border-b border-slate-600 w-16">م</th>
                    <th class="py-3 px-4 font-semibold border-b border-slate-600">نوع النشاط العلمي</th>
                    <th class="py-3 px-4 font-semibold border-b border-slate-600 w-32">العدد</th>
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
                    <tr class="border-b border-slate-700">
                      <td class="py-3 px-4 text-slate-400 text-sm font-medium">{{ $loop->iteration }}</td>
                      <td class="py-3 px-4 text-white text-sm font-medium">{{ $label }}</td>
                      <td class="py-3 px-4"><input type="number" min="0"
                          class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-600 text-center text-white"
                          onchange="updateResearchTable('{{ $key }}', 'count', this.value)"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{--! Facilities Table --}}
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg> جدول المرافق التعليمية والخدمية
            </h3>
            <div class="overflow-x-auto">
              <table class="w-full text-right border-collapse">
                <thead>
                  <tr class="bg-slate-700/50 text-slate-300 text-xs">
                    <th class="py-3 px-2 font-semibold border-b border-slate-600">نوع المرفق</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-600">العدد</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-600">المساحة الإجمالية (م²)</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-600">متوسط عدد المستخدمين</th>
                    <th class="py-3 px-2 font-semibold border-b border-slate-600">متوسط ساعات التشغيل</th>
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
                    <tr class="border-b border-slate-700">
                      <td class="py-3 px-2 text-white text-xs font-medium">{{ $label }}</td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-900 border border-slate-600 text-center text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'count', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-900 border border-slate-600 text-center text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'area', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-900 border border-slate-600 text-center text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'students', this.value)"></td>
                      <td class="py-3 px-2"><input type="number" min="0"
                          class="w-full px-2 py-2 rounded-lg bg-slate-900 border border-slate-600 text-center text-white text-xs"
                          onchange="updateFacilitiesTable('{{ $key }}', 'hours', this.value)"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        {{--! Save Status --}}
        <div class="mt-6 flex items-center justify-between">
          <div class="flex items-center gap-2 text-slate-400 text-sm">
            <div id="save-indicator" class="w-2 h-2 rounded-full bg-emerald-500"></div><span id="save-status">تم الحفظ
              تلقائياً</span>
          </div><button onclick="saveDraft()"
            class="btn-secondary px-6 py-2 rounded-xl text-white text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg> حفظ كمسودة </button>
        </div>
      </div>
      {{--? Section 2: Standards Evaluation --}}
      <div id="section-2" class="section-content hidden p-8 fade-in">
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">الجزء الثاني: التقييم وفق معايير الاعتماد</h2>
          </div>
          <p class="text-slate-500 dark:text-slate-400 mr-5 font-medium">تقييم 7 معايير رئيسية بناءً على مؤشرات محددة
          </p>
        </div>

        {{-- Standard Tabs Navigation --}}
        <div
          class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          <button onclick="switchStandardTab(1)"
            class="std-tab-btn active px-4 py-2.5 rounded-xl text-sm font-semibold transition-all bg-emerald-600 text-white shadow-md shadow-emerald-500/20"
            data-std="1">١. الرسالة والأهداف</button>
          <button onclick="switchStandardTab(2)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="2">٢. البرنامج والجودة</button>
          <button onclick="switchStandardTab(3)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="3">٣. التعليم والتعلم</button>
          <button onclick="switchStandardTab(4)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="4">٤. الطلاب</button>
          <button onclick="switchStandardTab(5)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="5">٥. هيئة التدريس</button>
          <button onclick="switchStandardTab(6)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="6">٦. مصادر التعلم والمرافق</button>
          <button onclick="switchStandardTab(7)"
            class="std-tab-btn px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50"
            data-std="7">٧. البحث والابتكار</button>
        </div>

        {{-- Standards Content Area --}}
        <div id="standards-container">
          {{-- main Standard  --}}
          <div id="standard-1-tab-content" class="std-content  fade-in">
            {{--Standard Header --}}
            <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <div
                      class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">
                      1</div>
                    المعيار الأول: الرسالة والأهداف
                  </h2>
                  <p class="text-slate-400 text-sm mt-2">وصف المعيار الرئيس</p>
                </div>
                <div class="text-left bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                  <span class="block text-xs text-slate-400 mb-1 text-center">التقييم</span>
                  <div class="flex items-center justify-center gap-1">
                    <span id="standard-1-score" class="text-3xl font-bold text-emerald-400">0.0</span>
                    <span class="text-slate-500 text-lg">/5</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Substandards Container --}}
            <div class="space-y-8">

              {{-- Substandard --}}
              <div class="bg-slate-800/80 rounded-2xl shadow-lg border border-slate-700/50 overflow-hidden">
                <div class="bg-slate-700/30 p-4 border-b border-slate-700/50 flex items-center gap-3">
                  <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm">1.1</span>
                  <h3 class="font-bold text-lg text-white">رسالة البرنامج</h3>
                </div>
                <div class="p-6 space-y-4">

                  {{-- Indicators --}}
                  <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50"
                    data-indicator="1-1">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-1</span>
                        <p class="text-white mt-1">وضوح رسالة البرنامج واتساقها مع رسالة المؤسسة</p>
                      </div>
                      <div class="tooltip-container mr-2">
                        <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="tooltip-text"><strong>أمثلة للأدلة:</strong><br>• وثيقة الرسالة المعتمدة<br>• محاضر
                          اجتماعات المراجعة<br>• استبيانات أصحاب المصلحة</div>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2">
                        <button onclick="setRating(1, 1, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button>
                        <button onclick="setRating(1, 1, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button>
                        <button onclick="setRating(1, 1, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button>
                        <button onclick="setRating(1, 1, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button>
                        <button onclick="setRating(1, 1, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-1" class="space-y-2 mb-3"></div>
                    <button onclick="addEvidence(1, 1)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل
                    </button>
                  </div>

                </div>

              </div>
            </div>
            {{-- Standard Comments --}}
                <div class="mt-8 bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-700">
                  <div class="bg-slate-700/50 p-4 border-b border-slate-600">
                    <h4 class="font-bold text-white flex items-center gap-2">
                      <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                      </svg> التعليقات الختامية للمعيار
                    </h4>
                  </div>
                  <div class="p-6 space-y-5">
                    <div>
                      <label class="block text-sm font-medium text-slate-300 mb-2">تعليق البرنامج</label>
                      <textarea rows="3" placeholder="أدخل تعليق البرنامج على هذا المعيار..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-slate-900 border border-slate-600 focus:ring-2 focus:ring-blue-500 text-white"
                        onchange="saveStandardComment(1, 'program_comment', this.value)"></textarea>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-emerald-400 mb-2">جوانب القوة</label>
                      <textarea rows="3" placeholder="• نقطة قوة 1"
                        class="w-full px-4 py-3 rounded-xl resize-none bg-emerald-900/10 border border-emerald-800/50 focus:ring-2 focus:ring-emerald-500 text-white"
                        onchange="saveStandardComment(1, 'strengths', this.value)"></textarea>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-red-400 mb-2">جوانب تحتاج تحسين</label>
                      <textarea rows="3" placeholder="• جانب للتحسين 1"
                        class="w-full px-4 py-3 rounded-xl resize-none bg-red-900/10 border border-red-800/50 focus:ring-2 focus:ring-red-500 text-white"
                        onchange="saveStandardComment(1, 'improvements', this.value)"></textarea>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-amber-400 mb-2">أولويات التحسين</label>
                      <textarea rows="3" placeholder="• أولوية 1"
                        class="w-full px-4 py-3 rounded-xl resize-none bg-amber-900/10 border border-amber-800/50 focus:ring-2 focus:ring-amber-500 text-white"
                        onchange="saveStandardComment(1, 'priorities', this.value)"></textarea>
                    </div>
                    <div class="pt-4 border-t border-slate-700">
                      <label class="block text-sm font-medium text-blue-400 mb-2">الرأي المستقل</label>
                      <textarea rows="3" placeholder="أدخل الرأي المستقل..."
                        class="w-full px-4 py-3 rounded-xl resize-none bg-blue-900/10 border border-blue-800/50 focus:ring-2 focus:ring-blue-500 text-white"
                        onchange="saveStandardComment(1, 'independent_opinion', this.value)"></textarea>
                    </div>
                  </div>
                </div>
          </div>
          {{--? Section 3: Independent Evaluations --}}
          <div id="section-3" class="section-content hidden p-8 fade-in">
            <div class="mb-8">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-2 h-8 bg-purple-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-white">الجزء الثالث: التقييمات المستقلة والنتائج</h2>
              </div>
              <p class="text-slate-400 mr-5">إدخال نتائج التقييم الخارجي والتوصيات وخطة الاستجابة</p>
            </div>
            <div class="space-y-6">
              {{-- Independent Evaluations --}}
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                  </svg> التقييمات المستقلة
                </h3>
                <div class="space-y-4">
                  <div><label class="block text-sm text-slate-400 mb-2">الإجراءات المتبعة للحصول على التقييم <span
                        class="text-red-400">*</span></label> <textarea id="evaluation_procedures" rows="4"
                      placeholder="صف الإجراءات المتبعة للحصول على التقييم المستقل..."
                      class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('evaluations', 'evaluation_procedures', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">توصيات المقيمين <span
                        class="text-red-400">*</span></label> <textarea id="evaluator_recommendations" rows="4"
                      placeholder="" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('evaluations', 'evaluator_recommendations', this.value)"></textarea>
                  </div>
                </div>
              </div>{{--! Response to Recommendations --}}
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg> الاستجابة للتوصيات
                </h3>
                <div class="space-y-4">
                  <div><label class="block text-sm text-slate-400 mb-2">آلية الاستجابة</label> <textarea
                      id="response_mechanism" rows="4" placeholder="صف آلية الاستجابة للتوصيات..."
                      class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('evaluations', 'response_mechanism', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">الإجراءات المتخذة</label> <textarea
                      id="actions_taken" rows="4" placeholder="• الإجراء الأول
• الإجراء الثاني" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('evaluations', 'actions_taken', this.value)"></textarea>
                  </div>
                </div>
              </div>{{--! Results --}}
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg> النتائج
                </h3>
                <div class="space-y-4">
                  <div><label class="block text-sm text-slate-400 mb-2">جوانب النجاح</label> <textarea
                      id="success_aspects" rows="4" placeholder="• جانب نجاح 1
• جانب نجاح 2" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('results', 'success_aspects', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">جوانب التحسين ذات الأولوية</label> <textarea
                      id="priority_improvements" rows="4" placeholder="• جانب تحسين 1
• جانب تحسين 2" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveField('results', 'priority_improvements', this.value)"></textarea>
                  </div>
                </div>
              </div>{{--! Executive Proposals Table --}}
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
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
                      <tr class="border-b border-slate-700">
                        <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">التوصية</th>
                        <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">مسؤول التنفيذ</th>
                        <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">توقيت التنفيذ</th>
                        <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">الموارد المطلوبة</th>
                        <th class="py-3 px-4"></th>
                      </tr>
                    </thead>
                    <tbody id="proposals-table">{{--! Dynamic rows --}}
                    </tbody>
                  </table>
                </div>
              </div>{{--! Attachments --}}
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                  <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg> المرفقات
                </h3>
                <div id="attachments-list" class="space-y-3 mb-4">{{--! Attachment items will be added here --}}
                </div>
                <div
                  class="border-2 border-dashed border-slate-600 rounded-xl p-8 text-center hover:border-blue-500 transition-colors cursor-pointer"
                  onclick="document.getElementById('file-upload').click()">
                  <svg class="w-12 h-12 text-slate-500 mx-auto mb-3" fill="none" stroke="currentColor"
                    viewbox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p class="text-slate-400 mb-1">اسحب الملفات هنا أو انقر للتحميل</p>
                  <p class="text-xs text-slate-500">PDF, DOC, DOCX, XLS, XLSX - حد أقصى 10 ميجابايت</p><input
                    type="file" id="file-upload" class="hidden" multiple accept=".pdf,.doc,.docx,.xls,.xlsx"
                    onchange="handleFileUpload(this.files)">
                </div>
              </div>
            </div>
          </div>
    </main>
  </div>
  {{-- Toast Notification --}}
  <div id="toast"
    class="fixed bottom-6 left-6 bg-slate-800 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center gap-3">
    <svg id="toast-icon" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg><span id="toast-message">تم الحفظ بنجاح</span>
  </div>
  {{-- Validation Modal --}}
  <div id="validation-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-slate-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-white">حقول مطلوبة</h3>
          <p class="text-sm text-slate-400">يرجى إكمال الحقول التالية</p>
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
  <script>
    // App State
    let currentSection = 1;
    let currentTab = 'general';
    let formData = {};
    let ratings = {};
    let evidences = {};
    let standardComments = {};
    let tableData = {
      graduates: { last_year: {}, prev_year: {}, two_years_ago: {} },
      research: {},
      facilities: {},
      proposals: [],
      objectives: []
    };
    let attachments = [];
    let missingFields = [];

    // Default Config
    const defaultConfig = {
      app_title: 'الدراسة الذاتية',
      primary_color: '#3b82f6',
      secondary_surface: '#1e293b',
      text_color: '#f1f5f9',
      primary_action: '#3b82f6',
      secondary_action: '#64748b'
    };

    // Element SDK Integration
    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange: async (config) => {
          document.getElementById('app-title').textContent = config.app_title || defaultConfig.app_title;
          document.documentElement.style.setProperty('--primary-action', config.primary_action || defaultConfig.primary_action);
        },
        mapToCapabilities: (config) => ({
          recolorables: [
            {
              get: () => config.primary_action || defaultConfig.primary_action,
              set: (value) => {
                config.primary_action = value;
                window.elementSdk.setConfig({ primary_action: value });
              }
            }
          ],
          borderables: [],
          fontEditable: undefined,
          fontSizeable: undefined
        }),
        mapToEditPanelValues: (config) => new Map([
          ['app_title', config.app_title || defaultConfig.app_title]
        ])
      });
    }

    // Data SDK Integration
    let allData = [];

    const dataHandler = {
      onDataChanged(data) {
        allData = data;
        loadDataToUI(data);
        updateProgress();
      }
    };

    async function initDataSdk() {
      if (window.dataSdk) {
        const result = await window.dataSdk.init(dataHandler);
        if (!result.isOk) {
          console.error('Failed to initialize Data SDK');
        }
      }
    }

    initDataSdk();

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
      });

      // Additional check for table inputs that might use distinct onchange but common ID patterns
      // (We'll use data- attributes in the next step to make this more robust)
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
        btn.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });

      const activeBtn = document.querySelector(`.std-tab-btn[data-std="${standardNum}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        activeBtn.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
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
        btn.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });

      const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/20');
        activeBtn.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      }

      currentTab = tabName;
    }

    // Save Field
    async function saveField(section, fieldKey, value) {
      const existingRecord = allData.find(r => r.section === section && r.field_key === fieldKey);

      showSavingIndicator();

      if (window.dataSdk) {
        if (existingRecord) {
          const result = await window.dataSdk.update({
            ...existingRecord,
            field_value: value,
            updated_at: new Date().toISOString()
          });
          if (!result.isOk) {
            showToast('حدث خطأ أثناء الحفظ', 'error');
          }
        } else {
          if (allData.length >= 999) {
            showToast('تم الوصول للحد الأقصى من السجلات', 'error');
            return;
          }
          const result = await window.dataSdk.create({
            id: `${section}_${fieldKey}_${Date.now()}`,
            section: section,
            field_key: fieldKey,
            field_value: value,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          });
          if (!result.isOk) {
            showToast('حدث خطأ أثناء الحفظ', 'error');
          }
        }
      }

      formData[`${section}_${fieldKey}`] = value;
      updateProgress();
      hideSavingIndicator();
    }

    // Standard Accordion
    function toggleStandard(standardNum) {
      const content = document.getElementById(`standard-${standardNum}-content`);
      const arrow = content.previousElementSibling.querySelector('.standard-arrow');

      if (content.classList.contains('open')) {
        content.classList.remove('open');
        arrow.style.transform = 'rotate(0deg)';
      } else {
        content.classList.add('open');
        arrow.style.transform = 'rotate(180deg)';
      }
    }

    // Rating System
    function setRating(standard, indicator, rating) {
      const key = `${standard}-${indicator}`;
      ratings[key] = rating;

      const container = document.querySelector(`[data-indicator="${key}"]`);
      if (container) {
        container.querySelectorAll('.rating-btn').forEach(btn => {
          btn.classList.remove('selected');
          const btnRating = parseInt(btn.dataset.rating);
          if (btnRating === rating) {
            btn.classList.add('selected');
            btn.style.backgroundColor = getRatingColor(rating);
          } else {
            btn.style.backgroundColor = '';
          }
        });
      }

      updateStandardScore(standard);
      saveField('ratings', key, rating.toString());
    }

    function getRatingColor(rating) {
      const colors = {
        1: '#ef4444',
        2: '#f97316',
        3: '#eab308',
        4: '#84cc16',
        5: '#10b981'
      };
      return colors[rating] || '#64748b';
    }

    function updateStandardScore(standard) {
      const standardRatings = Object.entries(ratings)
        .filter(([key]) => key.startsWith(`${standard}-`))
        .map(([, value]) => value);

      if (standardRatings.length > 0) {
        const avg = (standardRatings.reduce((a, b) => a + b, 0) / standardRatings.length).toFixed(1);
        const scoreEl = document.getElementById(`standard-${standard}-score`);
        if (scoreEl) {
          scoreEl.textContent = avg;
          scoreEl.className = `text-2xl font-bold ${avg >= 4 ? 'text-emerald-400' : avg >= 3 ? 'text-yellow-400' : 'text-red-400'}`;
        }
      }
    }

    // Evidence System
    let evidenceCounter = {};

    function addEvidence(standard, indicator) {
      const key = `${standard}-${indicator}`;
      if (!evidenceCounter[key]) evidenceCounter[key] = 0;
      evidenceCounter[key]++;

      const container = document.getElementById(`evidences-${key}`);
      const evidenceNum = evidenceCounter[key];
      const evidenceId = `${evidenceNum}-${indicator}-${standard}`;

      const row = document.createElement('div');
      row.className = 'bg-slate-600/50 rounded-lg p-3 flex items-center gap-3';
      row.id = `evidence-row-${evidenceId}`;
      row.innerHTML = `
        <span class="text-xs text-slate-400 whitespace-nowrap">دليل ${evidenceId}</span>
        <input type="text" placeholder="اسم الدليل" class="flex-1 px-3 py-2 rounded-lg text-sm" onchange="updateEvidence('${key}', ${evidenceNum}, 'name', this.value)">
        <label class="btn-secondary px-3 py-2 rounded-lg text-sm cursor-pointer flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
          </svg>
          ملف
          <input type="file" class="hidden" onchange="updateEvidence('${key}', ${evidenceNum}, 'file', this.files[0]?.name)">
        </label>
        <input type="text" placeholder="ملاحظة (اختياري)" class="w-32 px-3 py-2 rounded-lg text-sm" onchange="updateEvidence('${key}', ${evidenceNum}, 'note', this.value)">
        <button onclick="removeEvidence('${key}', ${evidenceNum}, '${evidenceId}')" class="text-red-400 hover:text-red-300 p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      `;

      container.appendChild(row);

      if (!evidences[key]) evidences[key] = [];
      evidences[key].push({ num: evidenceNum, name: '', file: '', note: '' });
    }

    function updateEvidence(key, num, field, value) {
      if (evidences[key]) {
        const evidence = evidences[key].find(e => e.num === num);
        if (evidence) {
          evidence[field] = value;
        }
      }
    }

    function removeEvidence(key, num, id) {
      const row = document.getElementById(`evidence-row-${id}`);
      if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => row.remove(), 300);
      }
      if (evidences[key]) {
        evidences[key] = evidences[key].filter(e => e.num !== num);
      }
    }

    // Standard Comments
    function saveStandardComment(standard, field, value) {
      if (!standardComments[standard]) standardComments[standard] = {};
      standardComments[standard][field] = value;
      saveField('standard_comments', `${standard}_${field}`, value);
    }

    // Dynamic Objectives
    function addObjective() {
      tableData.objectives.push('');
      renderObjectives();
      saveObjectives();
    }

    function removeObjective(index) {
      if (tableData.objectives.length > 1) {
        tableData.objectives.splice(index, 1);
        renderObjectives();
        saveObjectives();
      } else {
        showToast('يجب وجود هدف واحد على الأقل', 'error');
      }
    }

    function updateObjective(index, value) {
      tableData.objectives[index] = value;
      // We don't re-render here to keep focus, but we save
      saveObjectives();
    }

    function saveObjectives() {
      saveField('profile', 'program_objectives_list', JSON.stringify(tableData.objectives));
    }

    function renderObjectives() {
      const container = document.getElementById('objectives-list-container');
      if (!container) return;

      if (tableData.objectives.length === 0) {
        tableData.objectives.push('');
      }

      container.innerHTML = tableData.objectives.map((obj, index) => `
        <div class="flex items-center gap-3 animate-slide-in">
          <div class="flex-1 relative">
            <span class="absolute right-4 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded-full text-xs">${index + 1}</span>
            <input type="text" 
                   value="${obj}" 
                   placeholder="ادخل الهدف هنا..." 
                   class="w-full pr-12 pl-4 py-3 rounded-xl bg-slate-700/50 border border-slate-600 focus:ring-2 focus:ring-blue-500/50 appearance-none focus:outline-none text-white"
                   onchange="updateObjective(${index}, this.value)">
          </div>
          <button onclick="removeObjective(${index})" class="p-3 text-slate-500 hover:text-red-400 transition-colors bg-slate-700/30 rounded-xl hover:bg-red-400/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      `).join('');
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
      row.className = 'border-b border-slate-700';
      row.id = `proposal-row-${rowId}`;
      row.innerHTML = `
        <td class="py-3 px-4"><input type="text" placeholder="التوصية" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'recommendation', this.value)"></td>
        <td class="py-3 px-4"><input type="text" placeholder="المسؤول" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'responsible', this.value)"></td>
        <td class="py-3 px-4"><input type="date" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'timeline', this.value)"></td>
        <td class="py-3 px-4"><input type="text" placeholder="الموارد" class="w-full px-3 py-2 rounded-lg" onchange="updateProposalRow(${rowId}, 'resources', this.value)"></td>
        <td class="py-3 px-4">
          <button onclick="removeTableRow('proposals', ${rowId})" class="text-red-400 hover:text-red-300">
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

    // Generic Remove Table Row
    function removeTableRow(tableName, rowId) {
      const prefix = tableName === 'graduates' ? 'graduate' : tableName === 'research' ? 'research' : tableName === 'facilities' ? 'facility' : 'proposal';
      const row = document.getElementById(`${prefix}-row-${rowId}`);
      if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        row.style.transition = 'all 0.3s ease';
        setTimeout(() => row.remove(), 300);
      }
      tableData[tableName] = tableData[tableName].filter(r => r.id !== rowId);
    }

    // File Upload
    function handleFileUpload(files) {
      const container = document.getElementById('attachments-list');

      Array.from(files).forEach(file => {
        const fileId = Date.now() + Math.random();
        attachments.push({ id: fileId, name: file.name, size: file.size });

        const item = document.createElement('div');
        item.className = 'bg-slate-700/50 rounded-xl p-4 flex items-center justify-between';
        item.id = `attachment-${fileId}`;
        item.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <p class="text-white text-sm font-medium">${file.name}</p>
              <p class="text-slate-400 text-xs">${formatFileSize(file.size)}</p>
            </div>
          </div>
          <button onclick="removeAttachment(${fileId})" class="text-red-400 hover:text-red-300 p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        `;

        container.appendChild(item);
      });

      showToast('تم رفع الملفات بنجاح');
    }

    function removeAttachment(fileId) {
      const item = document.getElementById(`attachment-${fileId}`);
      if (item) {
        item.style.opacity = '0';
        setTimeout(() => item.remove(), 300);
      }
      attachments = attachments.filter(a => a.id !== fileId);
    }

    function formatFileSize(bytes) {
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
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
        if (formData[field] && formData[field].trim() !== '') {
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
      const section3Filled = section3Fields.filter(f => formData[f] && formData[f].trim() !== '').length;
      updateSectionStatus(3, section3Filled === section3Fields.length ? 'complete' : section3Filled > 0 ? 'progress' : 'empty');

      // Update submit button
      const submitBtn = document.getElementById('submit-btn');
      submitBtn.disabled = percent < 100;
    }

    function updateSectionStatus(section, status) {
      const statusEl = document.getElementById(`status-${section}`);
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

    // Save Draft
    function saveDraft() {
      showToast('تم حفظ المسودة بنجاح');
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
        <div class="bg-slate-700/50 rounded-lg p-3 flex items-center justify-between">
          <span class="text-white text-sm">${field.label}</span>
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

    // Toast Notifications
    function showToast(message, type = 'success') {
      const toast = document.getElementById('toast');
      const icon = document.getElementById('toast-icon');
      const msg = document.getElementById('toast-message');

      msg.textContent = message;

      if (type === 'error') {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
        icon.classList.remove('text-emerald-400');
        icon.classList.add('text-red-400');
      } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        icon.classList.remove('text-red-400');
        icon.classList.add('text-emerald-400');
      }

      toast.classList.remove('translate-y-20', 'opacity-0');

      setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
      }, 3000);
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

    // Initialize
    updateProgress();
    renderObjectives();
  </script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9c162da1c32df9ec',t:'MTc2ODk5MTg2Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9d7c102002e55456',t:'MTc3Mjc0NDU2MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
</body>

</html>