<!doctype html>
<html lang="ar" dir="rtl" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الدراسة الذاتية — نموذج التقييم</title>

  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  {{-- Vite Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    html {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    #sun-icon { display: none; }
    #moon-icon { display: block; }
    
    .dark #sun-icon { display: block !important; }
    .dark #moon-icon { display: none !important; }

    body {
      box-sizing: border-box;
    }

    :root {
      --primary-bg: #f8fafc;
    }

    .dark {
      --primary-bg: #020617;
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

    .fade-in {
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Rating Colors match Stage Three */
    .rating-1 { background-color: #ef4444 !important; color: white !important; }
    .rating-2 { background-color: #f97316 !important; color: white !important; }
    .rating-3 { background-color: #f59e0b !important; color: white !important; }
    .rating-4 { background-color: #84cc16 !important; color: white !important; }
    .rating-5 { background-color: #10b981 !important; color: white !important; }
    .rating-nc { background-color: #475569 !important; border-color: #475569 !important; color: white !important; }
  </style>
</head>

<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 italic-arabic">
  <div id="app" class="flex h-full">
    <main class="flex-1 h-full overflow-y-auto custom-scrollbar bg-slate-100 dark:bg-slate-900">
      
      <div id="section-2" class="section-content p-8 fade-in">
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">الجزء الثاني: التقييم وفق معايير الاعتماد</h2>
          </div>
          <p class="text-slate-700 dark:text-slate-300 mr-5 font-medium">تقييم 7 معايير رئيسية بناءً على مؤشرات محددة</p>
        </div>

        {{-- Standard Tabs Navigation --}}
        <div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          <button onclick="switchStandardTab(1)" data-std="1" class="std-tab-btn active bg-emerald-600 text-white shadow-md shadow-emerald-500/20 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">1. الرسالة والأهداف</button>
          <button onclick="switchStandardTab(2)" data-std="2" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">2. الحوكمة والقيادة</button>
          <button onclick="switchStandardTab(3)" data-std="3" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">3. التعليم والتعلم</button>
          <button onclick="switchStandardTab(4)" data-std="4" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">4. الطلاب</button>
          <button onclick="switchStandardTab(5)" data-std="5" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">5. هيئة التدريس</button>
          <button onclick="switchStandardTab(6)" data-std="6" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">6. الموارد التعليمية</button>
          <button onclick="switchStandardTab(7)" data-std="7" class="std-tab-btn text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">7. ضمان الجودة</button>
        </div>

        <div id="standards-container">
          
          {{-- Standard 1 --}}
          <div id="standard-1-tab-content" class="std-content fade-in">
            {{-- Standard Header --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
              <div class="flex flex-col md:flex-row items-start justify-between gap-6">
                <div class="flex-1 min-w-0">
                  <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 flex-shrink-0 font-bold text-xl">1</div>
                    <span class="leading-tight">المعيار الأول: الرسالة والأهداف</span>
                  </h2>
                  <div class="max-w-3xl mt-4">
                    <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed border-r-2 border-emerald-500/20 pr-4">
                      يجب أن يكون للبرنامج رسالة تتسق مع رسالة المؤسسة، وتوجه جميع أنشطته، وتصاغ بوضوح، وتعتمد رسمياً، وتعلن للمستفيدين، ويتم تقويمها بشكل دوري.
                    </p>
                  </div>
                </div>
                
                <div class="flex-shrink-0 w-full md:w-auto bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm min-w-[280px]">
                  <div class="flex flex-col gap-4">
                    {{-- Average Score --}}
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                      <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">متوسط تقييم المعيار</span>
                      <div class="flex items-center gap-1.5">
                        <span id="standard-1-score" class="text-3xl font-black text-slate-400 dark:text-slate-500">—</span>
                        <span class="text-slate-400 dark:text-slate-600 font-bold text-lg">/5</span>
                      </div>
                    </div>
                    
                    {{-- Detailed Stats --}}
                    <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                      <div class="flex items-center justify-between gap-4">
                        <span class="text-xs text-slate-500 font-medium">إجمالي المؤشرات:</span>
                        <span id="std-1-count-total" class="text-sm font-bold text-slate-700 dark:text-slate-300">4</span>
                      </div>
                      <div class="flex items-center justify-between gap-4">
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">المؤشرات المقيمة:</span>
                        <span id="std-1-count-rated" class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0</span>
                      </div>
                      <div class="flex items-center justify-between gap-4">
                        <span class="text-xs text-slate-400 font-medium">غير مقيمة:</span>
                        <span id="std-1-count-unrated" class="text-sm font-bold text-slate-500 dark:text-slate-400">4</span>
                      </div>
                      <div class="flex items-center justify-between gap-4">
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">غير متطابقة:</span>
                        <span id="std-1-count-nc" class="text-sm font-bold text-amber-600 dark:text-amber-400">0</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Substandards --}}
            <div class="space-y-8">
              {{-- Substandard 1.1 --}}
              <div class="bg-white/80 dark:bg-slate-800/80 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700/50">
                <div class="bg-slate-50 dark:bg-slate-700/30 p-4 border-b border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                  <div class="flex items-center gap-3 flex-1 min-w-0 pr-2">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm whitespace-nowrap">1-1</span>
                    <h3 class="font-bold text-lg text-slate-700 dark:text-slate-300 truncate">رسالة البرنامج</h3>
                  </div>
                  
                  {{-- Substandard Stats --}}
                  <div class="hidden md:flex items-center gap-4 px-4 border-r border-slate-200 dark:border-slate-700/50">
                    <div class="flex items-center gap-1.5" title="إجمالي المؤشرات">
                      <span class="text-[10px] text-slate-500 font-bold uppercase">الإجمالي:</span>
                      <span class="text-xs font-bold text-slate-700 dark:text-slate-300">2</span>
                    </div>
                    <div class="flex items-center gap-1.5" title="المؤشرات المقيمة">
                      <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                      <span id="sub-1-1-count-rated" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">0</span>
                    </div>
                  </div>

                  {{-- Collapse Chevron --}}
                  <button type="button" onclick="toggleSubStandard('1-1', this)" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                    <svg class="w-5 h-5 transform rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                  </button>
                </div>
                
                <div id="sub-1-1-content" class="p-6 space-y-4" data-sub-id="1-1">
                  {{-- Indicator 1.1.1 --}}
                  <div class="indicator-row rounded-2xl p-6 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all hover:shadow-md" data-indicator-id="101">
                    <div class="flex items-start justify-between mb-5">
                      <div class="flex-1 border-r-4 border-blue-500 pr-3">
                        <span class="text-xs text-blue-500 font-bold tracking-wider">مؤشر <span dir="ltr" class="inline-block">1-1-1</span></span>
                        <p class="text-slate-800 dark:text-slate-100 mt-2 font-medium leading-relaxed">تتسق رسالة البرنامج مع رسالة المؤسسة.</p>
                      </div>
                    </div>
                    
                    {{-- Rating Buttons --}}
                    <div class="flex flex-wrap items-center gap-4 mb-2 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                      <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">درجة التقييم:</span>
                      <div class="flex flex-wrap gap-2">
                        <button onclick="setRatingById(101, 1, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="101" data-rating="1">1</button>
                        <button onclick="setRatingById(101, 1, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="101" data-rating="2">2</button>
                        <button onclick="setRatingById(101, 1, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="101" data-rating="3">3</button>
                        <button onclick="setRatingById(101, 1, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="101" data-rating="4">4</button>
                        <button onclick="setRatingById(101, 1, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="101" data-rating="5">5</button>
                        <div class="w-px h-8 bg-slate-300 dark:bg-slate-600 mx-2 hidden sm:block"></div>
                        <button onclick="setRatingById(101, 1, 0)" class="rating-btn px-5 h-10 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-2 border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm whitespace-nowrap" data-indicator-id="101" data-rating="0">غير مطابق</button>
                      </div>
                    </div>
                  </div>

                  {{-- Indicator 1.1.2 --}}
                  <div class="indicator-row rounded-2xl p-6 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all hover:shadow-md" data-indicator-id="102">
                    <div class="flex items-start justify-between mb-5">
                      <div class="flex-1 border-r-4 border-blue-500 pr-3">
                        <span class="text-xs text-blue-500 font-bold tracking-wider">مؤشر <span dir="ltr" class="inline-block">1-1-2</span></span>
                        <p class="text-slate-800 dark:text-slate-100 mt-2 font-medium leading-relaxed">تصاغ رسالة البرنامج بوضوح ودقة بما يعكس طبيعته واتجاهاته.</p>
                      </div>
                    </div>
                    
                    {{-- Rating Buttons --}}
                    <div class="flex flex-wrap items-center gap-4 mb-2 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                      <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">درجة التقييم:</span>
                      <div class="flex flex-wrap gap-2">
                        <button onclick="setRatingById(102, 1, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="102" data-rating="1">1</button>
                        <button onclick="setRatingById(102, 1, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="102" data-rating="2">2</button>
                        <button onclick="setRatingById(102, 1, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="102" data-rating="3">3</button>
                        <button onclick="setRatingById(102, 1, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="102" data-rating="4">4</button>
                        <button onclick="setRatingById(102, 1, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" data-indicator-id="102" data-rating="5">5</button>
                        <div class="w-px h-8 bg-slate-300 dark:bg-slate-600 mx-2 hidden sm:block"></div>
                        <button onclick="setRatingById(102, 1, 0)" class="rating-btn px-5 h-10 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-2 border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm whitespace-nowrap" data-indicator-id="102" data-rating="0">غير مطابق</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Standard Comments Sections --}}
            <div class="mt-12 space-y-6">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">التعليقات الختامية للمعيار</h3>
              </div>
              
              {{-- Strengths --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="bg-emerald-500/10 p-4 border-b border-emerald-500/20 flex items-center justify-between">
                  <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400">جوانب القوة</label>
                  <button onclick="addCommentPoint(currentStdId, 'strengths')" class="text-xs text-emerald-500 hover:text-emerald-400 flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> إضافة نقطة
                  </button>
                </div>
                <div class="p-6">
                  <div id="std-comments-strengths-list" class="space-y-4">
                    {{-- Points rendered here --}}
                  </div>
                </div>
              </div>

              {{-- Opportunities for Improvement --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="bg-red-500/10 p-4 border-b border-red-500/20 flex items-center justify-between">
                  <label class="block text-sm font-medium text-red-700 dark:text-red-400">فرص التحسين</label>
                  <button onclick="addCommentPoint(currentStdId, 'improvements')" class="text-xs text-red-500 hover:text-red-400 flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> إضافة نقطة
                  </button>
                </div>
                <div class="p-6">
                  <div id="std-comments-improvements-list" class="space-y-4"></div>
                </div>
              </div>

              {{-- Priorities --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="bg-amber-500/10 p-4 border-b border-amber-500/20 flex items-center justify-between">
                  <label class="block text-sm font-medium text-amber-700 dark:text-amber-400">أولويات التحسين</label>
                  <button onclick="addCommentPoint(currentStdId, 'priorities')" class="text-xs text-amber-500 hover:text-amber-400 flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> إضافة نقطة
                  </button>
                </div>
                <div class="p-6">
                  <div id="std-comments-priorities-list" class="space-y-4"></div>
                </div>
              </div>
            </div>
          </div>

          {{-- Standard 2 (Placeholder) --}}
          <div id="standard-2-tab-content" class="std-content hidden fade-in">
             <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500 text-center py-20">
                <i class="fas fa-shield-halved text-6xl text-slate-200 mb-4 block"></i>
                <h2 class="text-2xl font-bold">المعيار الثاني: الحوكمة والقيادة والإدارة</h2>
                <p class="mt-4 text-slate-500">محتوى تجريبي مماثل للمعيار الأول...</p>
             </div>
          </div>
          
          {{-- Other Standards (Stubs) --}}
          <div id="standard-3-tab-content" class="std-content hidden fade-in"><div class="bg-white dark:bg-slate-800 rounded-2xl p-20 shadow-xl text-center"><h2 class="text-2xl font-bold">المعيار الثالث: التعليم والتعلم</h2></div></div>
          <div id="standard-4-tab-content" class="std-content hidden fade-in"><div class="bg-white dark:bg-slate-800 rounded-2xl p-20 shadow-xl text-center"><h2 class="text-2xl font-bold">المعيار الرابع: الطلاب</h2></div></div>
          <div id="standard-5-tab-content" class="std-content hidden fade-in"><div class="bg-white dark:bg-slate-800 rounded-2xl p-20 shadow-xl text-center"><h2 class="text-2xl font-bold">المعيار الخامس: هيئة التدريس</h2></div></div>
          <div id="standard-6-tab-content" class="std-content hidden fade-in"><div class="bg-white dark:bg-slate-800 rounded-2xl p-20 shadow-xl text-center"><h2 class="text-2xl font-bold">المعيار السادس: الموارد التعليمية</h2></div></div>
          <div id="standard-7-tab-content" class="std-content hidden fade-in"><div class="bg-white dark:bg-slate-800 rounded-2xl p-20 shadow-xl text-center"><h2 class="text-2xl font-bold">المعيار السابع: ضمان الجودة</h2></div></div>

        </div>
      </div>
    </main>
  </div>

  {{-- Floating Theme Toggle --}}
  <button id="theme-toggle" onclick="toggleDarkMode()" class="fixed bottom-6 left-6 z-[9999] w-14 h-14 rounded-full bg-white dark:bg-slate-800 shadow-2xl flex items-center justify-center border border-slate-200 dark:border-slate-700 transition-transform hover:scale-110">
    <i id="sun-icon" class="fas fa-sun text-2xl text-amber-500"></i>
    <i id="moon-icon" class="fas fa-moon text-2xl text-slate-700"></i>
  </button>

  <script>
    // State
    let scores = {};
    let currentStdId = 1;
    
    // Substandards data for dropdowns
    const substandardsByStd = {
      1: [
        { id: '1-1', name: '1-1 رسالة البرنامج' },
        { id: '1-2', name: '1-2 أهداف البرنامج' }
      ],
      2: [
        { id: '2-1', name: '2-1 الحوكمة' },
        { id: '2-2', name: '2-2 القيادة' }
      ],
      3: [{ id: '3-1', name: '3-1 محتوى تعليمي' }],
      4: [{ id: '4-1', name: '4-1 خدمات الطلاب' }],
      5: [{ id: '5-1', name: '5-1 الكفاءة' }],
      6: [{ id: '6-1', name: '6-1 المرافق' }],
      7: [{ id: '7-1', name: '7-1 إدارة الجودة' }]
    };

    let standardComments = {
      1: { strengths: [], improvements: [], priorities: [] },
      2: { strengths: [], improvements: [], priorities: [] },
      3: { strengths: [], improvements: [], priorities: [] },
      4: { strengths: [], improvements: [], priorities: [] },
      5: { strengths: [], improvements: [], priorities: [] },
      6: { strengths: [], improvements: [], priorities: [] },
      7: { strengths: [], improvements: [], priorities: [] }
    };

    // Dark Mode
    function toggleDarkMode() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    (function() {
      const isDark = localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
      if (isDark) document.documentElement.classList.add('dark');
    })();

    // Tabs
    function switchStandardTab(num) {
      currentStdId = num;
      document.querySelectorAll('.std-content').forEach(el => el.classList.add('hidden'));
      document.getElementById(`standard-${num}-tab-content`).classList.remove('hidden');

      document.querySelectorAll('.std-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        btn.classList.add('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });
      const activeBtn = document.querySelector(`.std-tab-btn[data-std="${num}"]`);
      if(activeBtn) {
        activeBtn.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        activeBtn.classList.remove('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      }
      
      // Update global list containers
      renderCommentPoints(num, 'strengths');
      renderCommentPoints(num, 'improvements');
      renderCommentPoints(num, 'priorities');
    }

    // Collapse
    function toggleSubStandard(id, btn) {
      const content = document.getElementById(`sub-${id}-content`);
      content.classList.toggle('hidden');
      btn.querySelector('svg').classList.toggle('rotate-180');
    }

    // Rating Logic
    function setRatingById(indId, stdId, rating) {
      const prevRating = scores[indId];
      if (prevRating === rating) scores[indId] = null;
      else scores[indId] = rating;

      document.querySelectorAll(`[data-indicator-id="${indId}"] .rating-btn`).forEach(btn => {
        const btnRating = parseInt(btn.dataset.rating);
        btn.classList.remove('rating-1', 'rating-2', 'rating-3', 'rating-4', 'rating-5', 'rating-nc');
        
        if (btnRating === scores[indId]) {
          if (btnRating === 0) btn.classList.add('rating-nc');
          else btn.classList.add(`rating-${btnRating}`);
        }
      });
      updateScore(stdId);
    }

    function updateScore(stdId) {
      const container = document.getElementById(`standard-${stdId}-tab-content`);
      if(!container) return;

      let sum = 0, ratedCount = 0, ncCount = 0, totalCount = 0;
      
      container.querySelectorAll('.indicator-row').forEach(row => {
        totalCount++;
        const id = row.dataset.indicatorId;
        const score = scores[id];
        if (score !== undefined && score !== null) {
          if (score === 0) {
            ncCount++;
          } else {
            sum += score;
            ratedCount++;
          }
        }
      });

      // Update UI
      const scoreEl = document.getElementById(`standard-${stdId}-score`);
      const ratedEl = document.getElementById(`std-${stdId}-count-rated`);
      const unratedEl = document.getElementById(`std-${stdId}-count-unrated`);
      const ncEl = document.getElementById(`std-${stdId}-count-nc`);

      if (ratedEl) ratedEl.textContent = ratedCount;
      if (ncEl) ncEl.textContent = ncCount;
      if (unratedEl) unratedEl.textContent = totalCount - (ratedCount + ncCount);
      if (scoreEl) scoreEl.textContent = ratedCount > 0 ? (sum / ratedCount).toFixed(1) : '—';
    }

    // Comments Point System
    function addCommentPoint(stdId, field) {
      standardComments[stdId][field].push({ text: '', subId: '' });
      renderCommentPoints(stdId, field);
    }

    function removeCommentPoint(stdId, field, index) {
      standardComments[stdId][field].splice(index, 1);
      renderCommentPoints(stdId, field);
    }

    function updateCommentPoint(stdId, field, index, key, value) {
      standardComments[stdId][field][index][key] = value;
    }

    function renderCommentPoints(stdId, field) {
      const container = document.getElementById(`std-comments-${field}-list`);
      if (!container) return;

      const points = standardComments[stdId][field];
      if (points.length === 0) {
        container.innerHTML = `
          <div class="py-5 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/20">
            <p class="text-slate-400 text-sm">لا توجد نقاط مضافة</p>
          </div>
        `;
        return;
      }

      const colorMap = { 'strengths': 'emerald', 'improvements': 'red', 'priorities': 'amber' };
      const color = colorMap[field];
      
      const subOptions = substandardsByStd[stdId] || [];
      const subSelectHtml = (currentIndex, currentSubId) => `
        <select onchange="updateCommentPoint(${stdId}, '${field}', ${currentIndex}, 'subId', this.value)" 
                class="w-48 px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500/30 transition-all outline-none">
          <option value="">اختر المعيار الفرعي...</option>
          ${subOptions.map(opt => `<option value="${opt.id}" ${opt.id === currentSubId ? 'selected' : ''}>${opt.name}</option>`).join('')}
        </select>
      `;

      container.innerHTML = points.map((point, index) => `
        <div class="flex items-center gap-3 fade-in group">
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-${color}-500/10 text-${color}-600 dark:text-${color}-400 rounded-full text-xs font-bold border border-${color}-500/20">
            ${index + 1}
          </div>
          <div class="flex-1">
            <input type="text" value="${point.text}" placeholder="أدخل النقطة..." 
                   oninput="updateCommentPoint(${stdId}, '${field}', ${index}, 'text', this.value)"
                   class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-${color}-500/30 text-sm text-slate-900 dark:text-white transition-all outline-none">
          </div>
          ${(field === 'strengths' || field === 'improvements') ? subSelectHtml(index, point.subId) : ''}
          <button onclick="removeCommentPoint(${stdId}, '${field}', ${index})" class="p-2.5 text-slate-400 hover:text-red-600 transition-colors bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <i class="fas fa-trash-alt text-xs"></i>
          </button>
        </div>
      `).join('');
    }

    // Initialize
    window.onload = () => {
      renderCommentPoints(1, 'strengths');
      renderCommentPoints(1, 'improvements');
      renderCommentPoints(1, 'priorities');
    };
  </script>
</body>
</html>
