<!doctype html>
<html lang="ar" dir="rtl" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نظام الدراسة الذاتية للبرنامج</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;600;700&amp;display=swap"
    rel="stylesheet">
  <style>
    body {
      box-sizing: border-box;
      font-family: 'Noto Kufi Arabic', sans-serif;
    }

    :root {
      --primary-bg: #0f172a;
      --secondary-surface: #1e293b;
      --text-color: #f1f5f9;
      --primary-action: #3b82f6;
      --secondary-action: #64748b;
    }

    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: var(--secondary-surface);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: var(--secondary-action);
      border-radius: 3px;
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
      border-color: #3b82f6 !important;
      outline: none !important;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
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

  <script src="https://cdn.tailwindcss.com/3.4.17" type="text/javascript"></script>
</head>

<body class="h-full bg-slate-900 text-slate-100">
  <div id="app" class="flex h-full"><!-- Sidebar -->
    <aside id="sidebar" class="w-72 bg-slate-800 h-full flex flex-col fixed right-0 top-0 shadow-2xl z-50">
      <!-- Logo Section -->
      <div class="p-6 border-b border-slate-700">
        <div class="flex items-center gap-3">
          <div
            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h1 id="app-title" class="text-lg font-bold text-white">الدراسة الذاتية</h1>
            <p class="text-xs text-slate-400">نظام إدارة البرامج</p>
          </div>
        </div>
      </div><!-- Progress Overview -->
      <div class="p-4 border-b border-slate-700">
        <div class="bg-slate-700/50 rounded-xl p-4">
          <div class="flex items-center justify-between mb-3"><span class="text-sm text-slate-300">نسبة الإنجاز</span>
            <span id="progress-percent" class="text-lg font-bold text-blue-400">0%</span>
          </div>
          <div class="h-2 bg-slate-600 rounded-full overflow-hidden">
            <div id="progress-bar"
              class="h-full bg-gradient-to-l from-blue-500 to-blue-400 rounded-full transition-all duration-500"
              style="width: 0%"></div>
          </div>
        </div>
      </div><!-- Navigation -->
      <nav class="flex-1 py-4 overflow-y-auto custom-scrollbar">
        <div class="px-3 mb-2"><span class="text-xs text-slate-500 font-medium px-3">أقسام التقرير</span>
        </div><button onclick="switchSection(1)" id="nav-1"
          class="sidebar-item active w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-700/50">
          <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center"><span
              class="text-blue-400 font-bold">١</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-white">الجزء الأول</span> <span
              class="text-xs text-slate-400">الدراسة الذاتية</span>
          </div>
          <div id="status-1" class="w-3 h-3 rounded-full bg-yellow-500"></div>
        </button> <!-- Part 2 with Dropdown --> <button onclick="toggleStandardsDropdown()" id="nav-2"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-700/50">
          <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center"><span
              class="text-emerald-400 font-bold">٢</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-white">الجزء الثاني</span> <span
              class="text-xs text-slate-400">التقييم وفق المعايير</span>
          </div>
          <svg id="standards-arrow" class="w-5 h-5 text-slate-400 transition-transform" fill="none"
            stroke="currentColor" viewbox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button> <!-- Standards Dropdown Menu -->
        <div id="standards-dropdown-menu" class="hidden bg-slate-700/50 border-r-4 border-emerald-500">
          <button onclick="selectStandardFromDropdown(1)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ١. الرسالة والأهداف </button> <button onclick="selectStandardFromDropdown(2)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٢. إدارة البرنامج وضمان الجودة </button> <button onclick="selectStandardFromDropdown(3)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٣. التعليم والتعلم </button> <button onclick="selectStandardFromDropdown(4)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٤. الطلاب </button> <button onclick="selectStandardFromDropdown(5)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٥. هيئة التدريس </button> <button onclick="selectStandardFromDropdown(6)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٦. مصادر التعلم والمرافق </button> <button onclick="selectStandardFromDropdown(7)"
            class="w-full text-right px-8 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
            ٧. البحث العلمي والابتكار </button>
        </div><button onclick="switchSection(3)" id="nav-3"
          class="sidebar-item w-full text-right px-6 py-4 flex items-center gap-4 hover:bg-slate-700/50">
          <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center"><span
              class="text-purple-400 font-bold">٣</span>
          </div>
          <div class="flex-1"><span class="block font-medium text-white">الجزء الثالث</span> <span
              class="text-xs text-slate-400">التقييمات والنتائج</span>
          </div>
          <div id="status-3" class="w-3 h-3 rounded-full bg-slate-500"></div>
        </button>
      </nav><!-- Submit Button -->
      <div class="p-4 border-t border-slate-700"><button id="submit-btn" onclick="submitReport()"
          class="w-full btn-primary text-white py-3 px-4 rounded-xl font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          disabled>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a. 0z" />
          </svg> رفع التقرير للمراجعة </button>
      </div>
    </aside><!-- Main Content -->
    <main class="flex-1 mr-72 h-full overflow-y-auto custom-scrollbar bg-slate-900"><!-- Section 1: Self Study -->
      <div id="section-1" class="section-content p-8 fade-in"><!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-white">الجزء الأول: الدراسة الذاتية</h2>
          </div>
          <p class="text-slate-400 mr-5">إدخال وتحرير كافة بيانات الدراسة الذاتية للبرنامج</p>
        </div><!-- Tabs Navigation -->
        <div class="bg-slate-800 rounded-2xl p-2 mb-6 flex flex-wrap gap-2"><button onclick="switchTab('general')"
            class="tab-btn active px-5 py-3 rounded-xl text-sm font-medium transition-all bg-blue-500 text-white"
            data-tab="general"> معلومات عامة </button> <button onclick="switchTab('program')"
            class="tab-btn px-5 py-3 rounded-xl text-sm font-medium transition-all text-slate-400 hover:bg-slate-700"
            data-tab="program"> بيانات تعريفية بالبرنامج </button> <button onclick="switchTab('profile')"
            class="tab-btn px-5 py-3 rounded-xl text-sm font-medium transition-all text-slate-400 hover:bg-slate-700"
            data-tab="profile"> ملف البرنامج </button> <button onclick="switchTab('tables')"
            class="tab-btn px-5 py-3 rounded-xl text-sm font-medium transition-all text-slate-400 hover:bg-slate-700"
            data-tab="tables"> الجداول والبيانات </button>
        </div><!-- Tab Contents -->
        <div id="tab-general" class="tab-content">
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a. 0z" />
              </svg> المعلومات العامة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6"><!-- Auto Fields -->
              <div><label class="block text-sm text-slate-400 mb-2">اسم المؤسسة / الجامعة</label> <input type="text"
                  value="جامعة الملك سعود" disabled class="w-full px-4 py-3 rounded-xl opacity-60 cursor-not-allowed">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">تاريخ التقييم / المراجعة <span
                    class="text-red-400">*</span></label> <input type="date" id="review_date"
                  class="w-full px-4 py-3 rounded-xl" onchange="saveField('general', 'review_date', this.value)">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">اسم رئيس فريق المراجعة الداخلية <span
                    class="text-red-400">*</span></label> <input type="text" id="review_team_head"
                  placeholder="أدخل الاسم" class="w-full px-4 py-3 rounded-xl"
                  onchange="saveField('general', 'review_team_head', this.value)">
              </div>
              <div><label class="block text-sm text-slate-400 mb-2"> اسم رئيس المؤسسة/ الجامعة</label> <input
                  type="text" value="سعود بن عبدالعزيز الحوثري" disabled
                  class="w-full px-4 py-3 rounded-xl opacity-60 cursor-not-allowed">
              </div>
              <div class="md:col-span-2"><label class="block text-sm text-slate-400 mb-2">التوقيعات</label>
                <div class="bg-slate-700/30 rounded-xl p-4 border-2 border-dashed border-slate-600">
                  <p class="text-slate-400 text-sm">سيتم إضافة التوقيعات تلقائياً عند طباعة التقرير</p>
                </div>
              </div>
            </div><!-- Long Text -->
          </div>
        </div>
        <div id="tab-program" class="tab-content hidden">
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg> البيانات التعريفية بالبرنامج
            </h3><!-- Auto-filled Institution Info -->
            <div class="bg-slate-700/50 rounded-xl p-5 mb-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><span class="text-xs text-slate-400 block mb-1">الجامعة</span> <span
                    class="text-white font-medium">جامعة الملك سعود</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">الكلية</span> <span
                    class="text-white font-medium">كلية الهندسة</span>
                </div>
                <div><span class="text-xs text-slate-400 block mb-1">القسم العلمي</span> <span
                    class="text-white font-medium">الحاسبات</span>
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
              <div><label class="block text-sm text-slate-400 mb-2">نبذة عن البرنامج</label> <textarea
                  id="program_description" rows="4" placeholder="وصف موجز للبرنامج وتخصصاته..."
                  class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('program', 'program_description', this.value)"></textarea>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm text-slate-400 mb-2">سنة تأسيس البرنامج</label> <input type="number"
                    id="establishment_year" placeholder="مثال: 2010" min="1900" max="2030"
                    class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('program', 'establishment_year', this.value)">
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">موقع البرنامج</label> <input type="url"
                    id="program_website" placeholder="https://example.edu.sa" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('program', 'program_website', this.value)">
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
              <div><label class="block text-sm text-slate-400 mb-2">رسالة البرنامج <span
                    class="text-red-400">*</span></label> <textarea id="program_mission" rows="4"
                  placeholder="اكتب رسالة البرنامج بوضوح..." class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('profile', 'program_mission', this.value)"></textarea>
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">أهداف البرنامج <span
                    class="text-red-400">*</span></label> <textarea id="program_objectives" rows="6" placeholder="• الهدف الأول
• الهدف الثاني
• الهدف الثالث" class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('profile', 'program_objectives', this.value)"></textarea>
                <p class="text-xs text-slate-500 mt-2">يفضل كتابة كل هدف في سطر منفصل</p>
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">لغة البرنامج</label> <select id="program_language"
                  class="w-full px-4 py-3 rounded-xl" onchange="saveField('profile', 'program_language', this.value)">
                  <option value="">اختر اللغة</option>
                  <option value="arabic">العربية</option>
                  <option value="english">الإنجليزية</option>
                  <option value="bilingual">ثنائية اللغة</option>
                </select>
              </div>
              <div><label class="block text-sm text-slate-400 mb-3">نظام البرنامج <span
                    class="text-red-400">*</span></label>
                <div class="flex gap-6"><label class="flex items-center gap-3 cursor-pointer"> <input type="radio"
                      name="program_system" value="semester" class="w-5 h-5 accent-blue-500"
                      onchange="saveField('profile', 'program_system', this.value)"> <span class="text-white">نظام
                      فصلي</span> </label> <label class="flex items-center gap-3 cursor-pointer"> <input type="radio"
                      name="program_system" value="annual" class="w-5 h-5 accent-blue-500"
                      onchange="saveField('profile', 'program_system', this.value)"> <span class="text-white">نظام
                      سنوي</span> </label>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div><label class="block text-sm text-slate-400 mb-2">عدد الساعات المعتمدة</label> <input type="number"
                    id="credit_hours" placeholder="0" min="0" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('profile', 'credit_hours', this.value)">
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">عدد المقررات</label> <input type="number"
                    id="courses_count" placeholder="0" min="0" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('profile', 'courses_count', this.value)">
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">عدد أعضاء هيئة التدريس</label> <input
                    type="number" id="faculty_count" placeholder="0" min="0" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('profile', 'faculty_count', this.value)">
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">عدد الطلاب (ذكور)</label> <input type="number"
                    id="male_students" placeholder="0" min="0" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('profile', 'male_students', this.value)">
                </div>
                <div><label class="block text-sm text-slate-400 mb-2">عدد الطلاب (إناث)</label> <input type="number"
                    id="female_students" placeholder="0" min="0" class="w-full px-4 py-3 rounded-xl"
                    onchange="saveField('profile', 'female_students', this.value)">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="tab-tables" class="tab-content hidden"><!-- Graduates Table (3 years) -->
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0H6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg> جدول تقديرات الخريجين (آخر 3 سنوات)
              </h3><button onclick="addGraduateRow()"
                class="btn-primary px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> إضافة صف </button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-700">
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">السنة</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">ممتاز %</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">جيد جداً %</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">جيد %</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">مقبول %</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">المجموع</th>
                    <th class="py-3 px-4"></th>
                  </tr>
                </thead>
                <tbody id="graduates-table"><!-- Dynamic rows -->
                </tbody>
              </table>
            </div>
            <p id="graduates-validation" class="text-xs text-red-400 mt-3 hidden">⚠️ مجموع النسب يجب أن يساوي 100%</p>
          </div><!-- Research Table (8 indicators) -->
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg> جدول البحث العلمي (8 مؤشرات)
              </h3><button onclick="addResearchRow()"
                class="btn-primary px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> إضافة صف </button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-slate-700">
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">السنة</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">أبحاث منشورة</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">أبحاث محكّمة</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">مؤتمرات دولية</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">مؤتمرات محلية</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">براءات اختراع</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">كتب منشورة</th>
                    <th class="text-right py-3 px-3 text-slate-400 text-xs font-medium">مشاريع بحثية</th>
                    <th class="py-3 px-3"></th>
                  </tr>
                </thead>
                <tbody id="research-table"><!-- Dynamic rows -->
                </tbody>
              </table>
            </div>
          </div><!-- Facilities Table -->
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg> جدول المرافق التعليمية
              </h3><button onclick="addFacilityRow()"
                class="btn-primary px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> إضافة صف </button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-700">
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">نوع المرفق</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">المساحة (م²)</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">العدد</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">عدد المستخدمين</th>
                    <th class="text-right py-3 px-4 text-slate-400 text-sm font-medium">ساعات الاستخدام/أسبوع</th>
                    <th class="py-3 px-4"></th>
                  </tr>
                </thead>
                <tbody id="facilities-table"><!-- Dynamic rows -->
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- Save Status -->
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
      </div><!-- Section 2: Standards Evaluation -->
      <div id="section-2" class="section-content hidden p-8 fade-in">
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-white">الجزء الثاني: التقييم وفق معايير الاعتماد</h2>
          </div>
          <p class="text-slate-400 mr-5">تقييم 7 معايير رئيسية بناءً على مؤشرات محددة</p>
        </div><!-- Standards List -->
        <div id="standards-list" class="space-y-4"><!-- Standard 1 -->
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(1)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-blue-400 font-bold text-lg">1</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">الرسالة والأهداف</h3>
                  <p class="text-sm text-slate-400">5 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span id="standard-1-score" class="text-2xl font-bold text-slate-400">0.0</span>
                  <span class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-1-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700"><!-- Indicators Table -->
                <div class="space-y-3 mb-6">
                  <div class="indicator-row rounded-xl p-4 border border-slate-600" data-indicator="1-1">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-1</span>
                        <p class="text-white mt-1">وضوح رسالة البرنامج واتساقها مع رسالة المؤسسة</p>
                      </div>
                      <div class="tooltip-container mr-2">
                        <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor"
                          viewbox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a. 0z" />
                        </svg>
                        <div class="tooltip-text"><strong>أمثلة للأدلة:</strong><br>
                          • وثيقة الرسالة المعتمدة<br>
                          • محاضر اجتماعات المراجعة<br>
                          • استبيانات أصحاب المصلحة
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2"><button onclick="setRating(1, 1, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button> <button onclick="setRating(1, 1, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button> <button onclick="setRating(1, 1, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button> <button onclick="setRating(1, 1, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button> <button onclick="setRating(1, 1, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-1" class="space-y-2 mb-3"><!-- Evidence rows will be added here -->
                    </div><button onclick="addEvidence(1, 1)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل </button>
                  </div>
                  <div class="indicator-row rounded-xl p-4 border border-slate-600" data-indicator="1-2">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-2</span>
                        <p class="text-white mt-1">مشاركة أصحاب المصلحة في صياغة الرسالة والأهداف</p>
                      </div>
                      <div class="tooltip-container mr-2">
                        <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor"
                          viewbox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a. 0z" />
                        </svg>
                        <div class="tooltip-text"><strong>أمثلة للأدلة:</strong><br>
                          • محاضر الاجتماعات<br>
                          • قوائم الحضور<br>
                          • نماذج الاستبيانات
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2"><button onclick="setRating(1, 2, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button> <button onclick="setRating(1, 2, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button> <button onclick="setRating(1, 2, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button> <button onclick="setRating(1, 2, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button> <button onclick="setRating(1, 2, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-2" class="space-y-2 mb-3"></div><button onclick="addEvidence(1, 2)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل </button>
                  </div>
                  <div class="indicator-row rounded-xl p-4 border border-slate-600" data-indicator="1-3">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-3</span>
                        <p class="text-white mt-1">قابلية قياس أهداف البرنامج</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2"><button onclick="setRating(1, 3, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button> <button onclick="setRating(1, 3, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button> <button onclick="setRating(1, 3, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button> <button onclick="setRating(1, 3, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button> <button onclick="setRating(1, 3, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-3" class="space-y-2 mb-3"></div><button onclick="addEvidence(1, 3)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل </button>
                  </div>
                  <div class="indicator-row rounded-xl p-4 border border-slate-600" data-indicator="1-4">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-4</span>
                        <p class="text-white mt-1">توافق أهداف البرنامج مع رسالته</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2"><button onclick="setRating(1, 4, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button> <button onclick="setRating(1, 4, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button> <button onclick="setRating(1, 4, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button> <button onclick="setRating(1, 4, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button> <button onclick="setRating(1, 4, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-4" class="space-y-2 mb-3"></div><button onclick="addEvidence(1, 4)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل </button>
                  </div>
                  <div class="indicator-row rounded-xl p-4 border border-slate-600" data-indicator="1-5">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-5</span>
                        <p class="text-white mt-1">آلية المراجعة الدورية للرسالة والأهداف</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                      <div class="flex gap-2"><button onclick="setRating(1, 5, 1)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500"
                          data-rating="1">1</button> <button onclick="setRating(1, 5, 2)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500"
                          data-rating="2">2</button> <button onclick="setRating(1, 5, 3)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500"
                          data-rating="3">3</button> <button onclick="setRating(1, 5, 4)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500"
                          data-rating="4">4</button> <button onclick="setRating(1, 5, 5)"
                          class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500"
                          data-rating="5">5</button>
                      </div>
                    </div>
                    <div id="evidences-1-5" class="space-y-2 mb-3"></div><button onclick="addEvidence(1, 5)"
                      class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg> إرفاق دليل </button>
                  </div>
                </div><!-- Comments Section -->
                <div class="border-t border-slate-600 pt-5 space-y-4">
                  <h4 class="font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg> التعليقات الختامية للمعيار
                  </h4>
                  <div><label class="block text-sm text-slate-400 mb-2">تعليق البرنامج</label> <textarea rows="3"
                      placeholder="أدخل تعليق البرنامج على هذا المعيار..."
                      class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveStandardComment(1, 'program_comment', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">جوانب القوة</label> <textarea rows="3"
                      placeholder="• نقطة قوة 1
• نقطة قوة 2" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveStandardComment(1, 'strengths', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">جوانب تحتاج تحسين</label> <textarea rows="3"
                      placeholder="• جانب للتحسين 1
• جانب للتحسين 2" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveStandardComment(1, 'improvements', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">أولويات التحسين</label> <textarea rows="3"
                      placeholder="• أولوية 1
• أولوية 2" class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveStandardComment(1, 'priorities', this.value)"></textarea>
                  </div>
                  <div><label class="block text-sm text-slate-400 mb-2">الرأي المستقل</label> <textarea rows="3"
                      placeholder="أدخل الرأي المستقل..." class="w-full px-4 py-3 rounded-xl resize-none"
                      onchange="saveStandardComment(1, 'independent_opinion', this.value)"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- Standard 2-7 (Collapsed templates) -->
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(2)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-emerald-400 font-bold text-lg">2</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">إدارة البرنامج وضمان الجودة</h3>
                  <p class="text-sm text-slate-400">6 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span id="standard-2-score" class="text-2xl font-bold text-slate-400">0.0</span>
                  <span class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-2-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 6 مؤشرات مع نظام التقييم والأدلة</p>
              </div>
            </div>
          </div>
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(3)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-purple-400 font-bold text-lg">3</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">التعليم والتعلم</h3>
                  <p class="text-sm text-slate-400">8 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span class="text-2xl font-bold text-slate-400">0.0</span> <span
                    class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-3-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 8 مؤشرات</p>
              </div>
            </div>
          </div>
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(4)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-amber-400 font-bold text-lg">4</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">الطلاب</h3>
                  <p class="text-sm text-slate-400">7 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span class="text-2xl font-bold text-slate-400">0.0</span> <span
                    class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-4-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 7 مؤشرات</p>
              </div>
            </div>
          </div>
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(5)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-cyan-400 font-bold text-lg">5</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">هيئة التدريس</h3>
                  <p class="text-sm text-slate-400">6 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span class="text-2xl font-bold text-slate-400">0.0</span> <span
                    class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-5-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 6 مؤشرات</p>
              </div>
            </div>
          </div>
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(6)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-rose-400 font-bold text-lg">6</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">مصادر التعلم والمرافق</h3>
                  <p class="text-sm text-slate-400">5 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span class="text-2xl font-bold text-slate-400">0.0</span> <span
                    class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-6-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 5 مؤشرات</p>
              </div>
            </div>
          </div>
          <div class="bg-slate-800 rounded-2xl overflow-hidden card-hover"><button onclick="toggleStandard(7)"
              class="w-full p-5 flex items-center justify-between hover:bg-slate-700/50 transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center"><span
                    class="text-indigo-400 font-bold text-lg">7</span>
                </div>
                <div class="text-right">
                  <h3 class="font-bold text-white">البحث العلمي والابتكار</h3>
                  <p class="text-sm text-slate-400">4 مؤشرات</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-left"><span class="text-2xl font-bold text-slate-400">0.0</span> <span
                    class="text-slate-500 text-sm">/5</span>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform standard-arrow" fill="none"
                  stroke="currentColor" viewbox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div id="standard-7-content" class="accordion-content">
              <div class="p-5 pt-0 border-t border-slate-700">
                <p class="text-slate-400 text-center py-8">نفس البنية – 4 مؤشرات</p>
              </div>
            </div>
          </div>
        </div>
      </div><!-- Section 3: Independent Evaluations -->
      <div id="section-3" class="section-content hidden p-8 fade-in">
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-purple-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-white">الجزء الثالث: التقييمات المستقلة والنتائج</h2>
          </div>
          <p class="text-slate-400 mr-5">إدخال نتائج التقييم الخارجي والتوصيات وخطة الاستجابة</p>
        </div>
        <div class="space-y-6"><!-- Independent Evaluations -->
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
                    class="text-red-400">*</span></label> <textarea id="evaluator_recommendations" rows="4" placeholder="• التوصية الأولى
• التوصية الثانية
• التوصية الثالثة" class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('evaluations', 'evaluator_recommendations', this.value)"></textarea>
              </div>
            </div>
          </div><!-- Response to Recommendations -->
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
          </div><!-- Results -->
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg> النتائج
            </h3>
            <div class="space-y-4">
              <div><label class="block text-sm text-slate-400 mb-2">جوانب النجاح</label> <textarea id="success_aspects"
                  rows="4" placeholder="• جانب نجاح 1
• جانب نجاح 2" class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('results', 'success_aspects', this.value)"></textarea>
              </div>
              <div><label class="block text-sm text-slate-400 mb-2">جوانب التحسين ذات الأولوية</label> <textarea
                  id="priority_improvements" rows="4" placeholder="• جانب تحسين 1
• جانب تحسين 2" class="w-full px-4 py-3 rounded-xl resize-none"
                  onchange="saveField('results', 'priority_improvements', this.value)"></textarea>
              </div>
            </div>
          </div><!-- Executive Proposals Table -->
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
                <tbody id="proposals-table"><!-- Dynamic rows -->
                </tbody>
              </table>
            </div>
          </div><!-- Attachments -->
          <div class="bg-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
              </svg> المرفقات
            </h3>
            <div id="attachments-list" class="space-y-3 mb-4"><!-- Attachment items will be added here -->
            </div>
            <div
              class="border-2 border-dashed border-slate-600 rounded-xl p-8 text-center hover:border-blue-500 transition-colors cursor-pointer"
              onclick="document.getElementById('file-upload').click()">
              <svg class="w-12 h-12 text-slate-500 mx-auto mb-3" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p class="text-slate-400 mb-1">اسحب الملفات هنا أو انقر للتحميل</p>
              <p class="text-xs text-slate-500">PDF, DOC, DOCX, XLS, XLSX - حد أقصى 10 ميجابايت</p><input type="file"
                id="file-upload" class="hidden" multiple accept=".pdf,.doc,.docx,.xls,.xlsx"
                onchange="handleFileUpload(this.files)">
            </div>
          </div>
        </div>
      </div>
    </main>
  </div><!-- Toast Notification -->
  <div id="toast"
    class="fixed bottom-6 left-6 bg-slate-800 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center gap-3">
    <svg id="toast-icon" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewbox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg><span id="toast-message">تم الحفظ بنجاح</span>
  </div><!-- Validation Modal -->
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
        <!-- Missing fields will be listed here -->
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
      graduates: [],
      research: [],
      facilities: [],
      proposals: []
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
    }

    // Dropdown Functions
    let selectedStandard = null;

    function toggleStandardsDropdown() {
      const dropdown = document.getElementById('standards-dropdown-menu');
      const arrow = document.getElementById('standards-arrow');

      if (dropdown) {
        dropdown.classList.toggle('hidden');
        arrow.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
      }
    }

    function selectStandardFromDropdown(standardNum) {
      selectedStandard = standardNum;

      // Switch to section 2
      switchSection(2);

      // Hide all standards
      document.querySelectorAll('#standards-list > div').forEach(el => {
        el.style.display = 'none';
      });

      // Show only selected standard
      const selectedStandardEl = document.querySelector(`#standard-${standardNum}-content`).parentElement;
      selectedStandardEl.style.display = 'block';

      setTimeout(() => {
        const standardContent = document.getElementById(`standard-${standardNum}-content`);
        if (standardContent && !standardContent.classList.contains('open')) {
          toggleStandard(standardNum);
        }

        const standardButton = standardContent?.previousElementSibling;
        if (standardButton) {
          standardButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }, 300);

      // Close dropdown
      const dropdown = document.getElementById('standards-dropdown-menu');
      if (dropdown) {
        dropdown.classList.add('hidden');
        document.getElementById('standards-arrow').style.transform = 'rotate(0deg)';
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

      // Reset standards view if switching to section 2
      if (sectionNum === 2) {
        document.querySelectorAll('#standards-list > div').forEach(el => {
          el.style.display = 'block';
        });
        selectedStandard = null;
      }

      currentSection = sectionNum;
    }

    // Tab Navigation
    function switchTab(tabName) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
      document.getElementById(`tab-${tabName}`).classList.remove('hidden');

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white');
        btn.classList.add('text-slate-400');
      });

      const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
      activeBtn.classList.add('bg-blue-500', 'text-white');
      activeBtn.classList.remove('text-slate-400');

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

    // Table Functions - Graduates
    function addGraduateRow() {
      const tbody = document.getElementById('graduates-table');
      const rowId = Date.now();

      const row = document.createElement('tr');
      row.className = 'border-b border-slate-700';
      row.id = `graduate-row-${rowId}`;
      row.innerHTML = `
        <td class="py-3 px-4"><input type="number" placeholder="2024" class="w-full px-3 py-2 rounded-lg" onchange="updateGraduateRow(${rowId}, 'year', this.value); validateGraduateRow(${rowId})"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" max="100" class="w-full px-3 py-2 rounded-lg" onchange="updateGraduateRow(${rowId}, 'excellent', this.value); validateGraduateRow(${rowId})"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" max="100" class="w-full px-3 py-2 rounded-lg" onchange="updateGraduateRow(${rowId}, 'very_good', this.value); validateGraduateRow(${rowId})"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" max="100" class="w-full px-3 py-2 rounded-lg" onchange="updateGraduateRow(${rowId}, 'good', this.value); validateGraduateRow(${rowId})"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" max="100" class="w-full px-3 py-2 rounded-lg" onchange="updateGraduateRow(${rowId}, 'pass', this.value); validateGraduateRow(${rowId})"></td>
        <td class="py-3 px-4"><span class="total-badge px-3 py-1 rounded-lg bg-slate-600 text-white text-sm">0%</span></td>
        <td class="py-3 px-4">
          <button onclick="removeTableRow('graduates', ${rowId})" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </td>
      `;

      tbody.appendChild(row);
      tableData.graduates.push({ id: rowId, year: '', excellent: 0, very_good: 0, good: 0, pass: 0 });
    }

    function updateGraduateRow(rowId, field, value) {
      const row = tableData.graduates.find(r => r.id === rowId);
      if (row) {
        row[field] = field === 'year' ? value : parseFloat(value) || 0;
      }
    }

    function validateGraduateRow(rowId) {
      const row = tableData.graduates.find(r => r.id === rowId);
      if (row) {
        const total = row.excellent + row.very_good + row.good + row.pass;
        const rowEl = document.getElementById(`graduate-row-${rowId}`);
        const badge = rowEl.querySelector('.total-badge');

        badge.textContent = `${total}%`;

        if (total === 100) {
          badge.classList.remove('bg-red-500');
          badge.classList.add('bg-emerald-500');
        } else {
          badge.classList.remove('bg-emerald-500');
          badge.classList.add('bg-red-500');
        }

        const validationMsg = document.getElementById('graduates-validation');
        const allValid = tableData.graduates.every(r => {
          const t = r.excellent + r.very_good + r.good + r.pass;
          return t === 100 || t === 0;
        });

        validationMsg.classList.toggle('hidden', allValid);
      }
    }

    // Table Functions - Research (8 indicators)
    function addResearchRow() {
      const tbody = document.getElementById('research-table');
      const rowId = Date.now();

      const row = document.createElement('tr');
      row.className = 'border-b border-slate-700';
      row.id = `research-row-${rowId}`;
      row.innerHTML = `
        <td class="py-3 px-3"><input type="number" placeholder="2024" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'year', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'published', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'reviewed', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'intl_conf', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'local_conf', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'patents', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'books', this.value)"></td>
        <td class="py-3 px-3"><input type="number" placeholder="0" min="0" class="w-full px-2 py-2 rounded-lg text-sm" onchange="updateResearchRow(${rowId}, 'projects', this.value)"></td>
        <td class="py-3 px-3">
          <button onclick="removeTableRow('research', ${rowId})" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </td>
      `;

      tbody.appendChild(row);
      tableData.research.push({ id: rowId, year: '', published: 0, reviewed: 0, intl_conf: 0, local_conf: 0, patents: 0, books: 0, projects: 0 });
    }

    function updateResearchRow(rowId, field, value) {
      const row = tableData.research.find(r => r.id === rowId);
      if (row) {
        row[field] = field === 'year' ? value : parseInt(value) || 0;
      }
    }

    // Table Functions - Facilities
    function addFacilityRow() {
      const tbody = document.getElementById('facilities-table');
      const rowId = Date.now();

      const row = document.createElement('tr');
      row.className = 'border-b border-slate-700';
      row.id = `facility-row-${rowId}`;
      row.innerHTML = `
        <td class="py-3 px-4">
          <select class="w-full px-3 py-2 rounded-lg" onchange="updateFacilityRow(${rowId}, 'type', this.value)">
            <option value="">اختر النوع</option>
            <option value="classroom">قاعة دراسية</option>
            <option value="lab">معمل</option>
            <option value="library">مكتبة</option>
            <option value="computer_lab">معمل حاسب</option>
            <option value="meeting_room">قاعة اجتماعات</option>
          </select>
        </td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" class="w-full px-3 py-2 rounded-lg" onchange="updateFacilityRow(${rowId}, 'area', this.value)"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" class="w-full px-3 py-2 rounded-lg" onchange="updateFacilityRow(${rowId}, 'count', this.value)"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" class="w-full px-3 py-2 rounded-lg" onchange="updateFacilityRow(${rowId}, 'users', this.value)"></td>
        <td class="py-3 px-4"><input type="number" placeholder="0" min="0" class="w-full px-3 py-2 rounded-lg" onchange="updateFacilityRow(${rowId}, 'hours', this.value)"></td>
        <td class="py-3 px-4">
          <button onclick="removeTableRow('facilities', ${rowId})" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </td>
      `;

      tbody.appendChild(row);
      tableData.facilities.push({ id: rowId, type: '', area: 0, count: 0, users: 0, hours: 0 });
    }

    function updateFacilityRow(rowId, field, value) {
      const row = tableData.facilities.find(r => r.id === rowId);
      if (row) {
        row[field] = ['area', 'count', 'users', 'hours'].includes(field) ? parseInt(value) || 0 : value;
      }
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
  </script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9c162da1c32df9ec',t:'MTc2ODk5MTg2Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
  <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9d7c102002e55456',t:'MTc3Mjc0NDU2MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
</body>

</html>