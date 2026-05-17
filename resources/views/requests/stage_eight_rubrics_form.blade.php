<!doctype html>
<html lang="ar" dir="rtl" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مقاييس تقييم البرنامج — نموذج 6</title>

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
    .rating-nc { background-color: #3730a3 !important; border-color: #312e81 !important; color: white !important; font-weight: 800 !important; box-shadow: 0 0 0 2px rgba(55, 48, 163, 0.2), 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important; }

    .view-mode .rating-btn { pointer-events: none; }
    .view-mode button:not(.std-tab-btn):not(#theme-toggle):not(.back-btn):not(.rating-btn) { display: none !important; }
    .view-mode input, .view-mode select, .view-mode textarea { pointer-events: none; }
  </style>
</head>

<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 italic-arabic {{ !$isEditMode ? 'view-mode' : '' }}">
  <div id="app" class="flex h-full">
    <main class="flex-1 h-full overflow-y-auto custom-scrollbar bg-slate-100 dark:bg-slate-900">
      
      <div id="section-2" class="section-content p-8 fade-in">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
             <a href="{{ route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_eight']) }}" 
                class="back-btn w-10 h-10 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-right"></i>
             </a>
             <div>
                <div class="flex items-center gap-3 mb-1">
                  <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
                  <h2 class="text-2xl font-bold text-slate-800 dark:text-white">مقاييس تقييم البرنامج (الروبريك الختامي)</h2>
                </div>
                <p class="text-slate-700 dark:text-slate-300 mr-5 font-medium">التقييم الختامي وفق معايير الاعتماد - المرحلة الثامنة</p>
             </div>
          </div>

          @if($isEditMode)
            <button onclick="saveData()" id="saveBtn" class="flex items-center gap-2 px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
              <i class="fa-solid fa-floppy-disk"></i>
              <span>حفظ التغييرات</span>
            </button>
          @endif
        </div>

        {{-- Standard Tabs Navigation --}}
        <div class="bg-white dark:bg-slate-800 p-1.5 rounded-2xl mb-8 flex flex-wrap gap-1.5 shadow-sm border border-slate-100 dark:border-slate-700/50">
          @foreach($standards as $standard)
            <button onclick="switchStandardTab({{ $standard->id }})" 
                    data-std="{{ $standard->id }}" 
                    class="std-tab-btn {{ $loop->first ? 'active bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50' }} px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
              {{ $standard->number }}. {{ $standard->name }}
            </button>
          @endforeach
        </div>

        <div id="standards-container">
          @foreach($standards as $standard)
            <div id="standard-{{ $standard->id }}-tab-content" class="std-content {{ $loop->first ? '' : 'hidden' }} fade-in">
              {{-- Standard Header --}}
              <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
                <div class="flex flex-col md:flex-row items-start justify-between gap-6">
                  <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                      <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 flex-shrink-0 font-bold text-xl">{{ $standard->number }}</div>
                      <span class="leading-tight">المعيار {{ $standard->number_arabic ?? $standard->number }}: {{ $standard->name }}</span>
                    </h2>
                    <div class="max-w-3xl mt-4">
                      <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed border-r-2 border-emerald-500/20 pr-4">
                        {{ $standard->description }}
                      </p>
                    </div>
                  </div>
                  
                  <div class="flex-shrink-0 w-full md:w-auto bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm min-w-[280px]">
                    <div class="flex flex-col gap-4">
                      <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">متوسط تقييم المعيار</span>
                        <div class="flex items-center gap-1.5">
                          <span id="standard-{{ $standard->id }}-score" class="text-3xl font-black text-slate-400 dark:text-slate-500">—</span>
                          <span class="text-slate-400 dark:text-slate-600 font-bold text-lg">/5</span>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                        <div class="flex items-center justify-between gap-4">
                          <span class="text-xs text-slate-500 font-medium">إجمالي المؤشرات:</span>
                          <span id="std-{{ $standard->id }}-count-total" class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            {{ $standard->subStandards->flatMap->indicators->count() }}
                          </span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                          <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">المؤشرات المقيمة:</span>
                          <span id="std-{{ $standard->id }}-count-rated" class="text-sm font-bold text-emerald-600 dark:text-emerald-400">0</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                          <span class="text-xs text-slate-400 font-medium">غير مقيمة:</span>
                          <span id="std-{{ $standard->id }}-count-unrated" class="text-sm font-bold text-slate-500 dark:text-slate-400">0</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                          <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">غير متطابقة:</span>
                          <span id="std-{{ $standard->id }}-count-nc" class="text-sm font-bold text-amber-600 dark:text-amber-400">0</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Substandards --}}
              <div class="space-y-8">
                @foreach($standard->subStandards as $sub)
                  <div class="bg-white/80 dark:bg-slate-800/80 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700/50">
                    <div class="bg-slate-50 dark:bg-slate-700/30 p-4 border-b border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                      <div class="flex items-center gap-3 flex-1 min-w-0 pr-2">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm whitespace-nowrap">{{ $sub->number }}</span>
                        <h3 class="font-bold text-lg text-slate-700 dark:text-slate-300 truncate">{{ $sub->name }}</h3>
                      </div>
                      
                      <div class="hidden md:flex items-center gap-4 px-4 border-r border-slate-200 dark:border-slate-700/50">
                        <div class="flex items-center gap-1.5" title="إجمالي المؤشرات">
                          <span class="text-[10px] text-slate-500 font-bold uppercase">الإجمالي:</span>
                          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $sub->indicators->count() }}</span>
                        </div>
                        <div class="flex items-center gap-1.5" title="المؤشرات المقيمة">
                          <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                          <span id="sub-{{ str_replace('.', '-', $sub->number) }}-count-rated" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">0</span>
                        </div>
                      </div>

                      <button type="button" onclick="toggleSubStandard('{{ str_replace('.', '-', $sub->number) }}', this)" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                        <svg class="w-5 h-5 transform rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                      </button>
                    </div>
                    
                    <div id="sub-{{ str_replace('.', '-', $sub->number) }}-content" class="p-6 space-y-4" data-sub-id="{{ $sub->id }}">
                      @foreach($sub->indicators as $indicator)
                        <div class="indicator-row rounded-2xl p-6 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-all hover:shadow-md" data-indicator-id="{{ $indicator->id }}">
                          <div class="flex items-start justify-between mb-5 gap-4">
                            <div class="flex-1 border-r-4 border-blue-500 pr-3">
                              <span class="text-xs text-blue-500 font-bold tracking-wider">مؤشر <span dir="ltr" class="inline-block">{{ $indicator->number }}</span></span>
                              <p class="text-slate-800 dark:text-slate-100 mt-2 font-medium leading-relaxed">{{ $indicator->name }}</p>
                            </div>

                            {{-- Display Initial Score (Stage 6) for reference --}}
                            @php $initialScore = $initialScores[$indicator->id] ?? null; @endphp
                            @if($initialScore !== null)
                              <div class="flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-sm shrink-0 min-w-[100px]" title="التقييم الذي تم في المرحلة السادسة">
                                <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-tight mb-1">التقييم السابق</span>
                                <div class="flex items-center gap-1.5">
                                  @if($initialScore == 0)
                                    <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">غير مطابق</span>
                                  @else
                                    <span class="text-2xl font-black {{ $initialScore >= 4 ? 'text-emerald-500' : ($initialScore >= 3 ? 'text-amber-500' : 'text-red-500') }}">{{ $initialScore }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-600 font-bold">/5</span>
                                  @endif
                                </div>
                              </div>
                            @endif
                          </div>
                          
                          <div class="flex flex-wrap items-center gap-4 mb-2 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">درجة التقييم:</span>
                            <div class="flex flex-wrap gap-2">
                              @for($r = 1; $r <= 5; $r++)
                                <button onclick="setRatingById({{ $indicator->id }}, {{ $standard->id }}, {{ $r }})" 
                                        class="rating-btn w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors shadow-sm" 
                                        data-indicator-id="{{ $indicator->id }}" data-rating="{{ $r }}">{{ $r }}</button>
                              @endfor
                              <div class="w-px h-8 bg-slate-300 dark:bg-slate-600 mx-2 hidden sm:block"></div>
                              <button onclick="setRatingById({{ $indicator->id }}, {{ $standard->id }}, 0)" 
                                      class="rating-btn px-5 h-10 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-2 border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm whitespace-nowrap" 
                                      data-indicator-id="{{ $indicator->id }}" data-rating="0">غير مطابق</button>
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
                
                {{-- Strengths --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-emerald-500/10 p-4 border-b border-emerald-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-emerald-700 dark:text-emerald-400">جوانب القوة</label>
                    <button onclick="addCommentPoint({{ $standard->id }}, 'strengths')" class="text-xs text-emerald-500 hover:text-emerald-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-comments-strengths-list-{{ $standard->id }}" class="space-y-4"></div>
                  </div>
                </div>

                {{-- Opportunities for Improvement --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-red-500/10 p-4 border-b border-red-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-red-700 dark:text-red-400">فرص التحسين</label>
                    <button onclick="addCommentPoint({{ $standard->id }}, 'improvements')" class="text-xs text-red-500 hover:text-red-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-comments-improvements-list-{{ $standard->id }}" class="space-y-4"></div>
                  </div>
                </div>

                {{-- Priorities --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                  <div class="bg-amber-500/10 p-4 border-b border-amber-500/20 flex items-center justify-between">
                    <label class="block text-sm font-medium text-amber-700 dark:text-amber-400">أولويات التحسين</label>
                    <button onclick="addCommentPoint({{ $standard->id }}, 'priorities')" class="text-xs text-amber-500 hover:text-amber-400 flex items-center gap-1">
                      <i class="fas fa-plus-circle"></i> إضافة نقطة
                    </button>
                  </div>
                  <div class="p-6">
                    <div id="std-comments-priorities-list-{{ $standard->id }}" class="space-y-4"></div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
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
    // Server data
    window.SAVE_URL = "{{ route('requests.stage_eight.rubrics_save', $accreditationRequest) }}";
    window.INITIAL_SCORES = @json($savedScores);
    window.INITIAL_FORM_DATA = @json($savedFormData);
    window.IS_EDIT_MODE = {{ $isEditMode ? 'true' : 'false' }};
    window.SUBSTANDARDS_BY_STD = @json($standards->mapWithKeys(fn($s) => [$s->id => $s->subStandards->map(fn($ss) => ['id' => $ss->id, 'name' => $ss->number . ' ' . $ss->name])]));

    // State
    let scores = { ...window.INITIAL_SCORES };
    let currentStdId = {{ $standards->first()->id }};
    let standardComments = window.INITIAL_FORM_DATA && window.INITIAL_FORM_DATA.standards ? window.INITIAL_FORM_DATA.standards : {};
    let isDirty = false;

    // Warning before leaving with unsaved changes
    window.addEventListener('beforeunload', (e) => {
      if (isDirty && window.IS_EDIT_MODE) {
        e.preventDefault();
        e.returnValue = ''; // Standard way to show confirmation
      }
    });

    // Ensure all standards have empty arrays if not present
    @foreach($standards as $standard)
      if (!standardComments[{{ $standard->id }}]) {
        standardComments[{{ $standard->id }}] = { strengths: [], improvements: [], priorities: [] };
      }
    @endforeach

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
      const panel = document.getElementById(`standard-${num}-tab-content`);
      if (panel) panel.classList.remove('hidden');

      document.querySelectorAll('.std-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        btn.classList.add('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      });
      const activeBtn = document.querySelector(`.std-tab-btn[data-std="${num}"]`);
      if(activeBtn) {
        activeBtn.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-md', 'shadow-emerald-500/20');
        activeBtn.classList.remove('text-slate-700', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
      }
      
      // Update totals
      updateScore(num);
      
      // Re-render lists for this standard
      renderCommentPoints(num, 'strengths');
      renderCommentPoints(num, 'improvements');
      renderCommentPoints(num, 'priorities');
    }

    // Collapse
    function toggleSubStandard(id, btn) {
      const content = document.getElementById(`sub-${id}-content`);
      if (content) {
        content.classList.toggle('hidden');
        btn.querySelector('svg').classList.toggle('rotate-180');
      }
    }

    // Rating Logic
    function setRatingById(indId, stdId, rating) {
      if (!window.IS_EDIT_MODE) return;
      
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
      markDirty();
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
            sum += parseInt(score);
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
      if (!window.IS_EDIT_MODE) return;
      if (!standardComments[stdId]) standardComments[stdId] = { strengths: [], improvements: [], priorities: [] };
      standardComments[stdId][field].push({ text: '', subId: '' });
      renderCommentPoints(stdId, field);
      markDirty();
    }

    function removeCommentPoint(stdId, field, index) {
      if (!window.IS_EDIT_MODE) return;
      standardComments[stdId][field].splice(index, 1);
      renderCommentPoints(stdId, field);
      markDirty();
    }

    function updateCommentPoint(stdId, field, index, key, value) {
      if (!window.IS_EDIT_MODE) return;
      standardComments[stdId][field][index][key] = value;
      markDirty();
    }

    function renderCommentPoints(stdId, field) {
      const container = document.getElementById(`std-comments-${field}-list-${stdId}`);
      if (!container) return;

      const points = standardComments[stdId] ? standardComments[stdId][field] : [];
      if (!points || points.length === 0) {
        container.innerHTML = `
          <div class="py-5 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/20">
            <p class="text-slate-400 text-sm">لا توجد نقاط مضافة</p>
          </div>
        `;
        return;
      }

      const colorMap = { 'strengths': 'emerald', 'improvements': 'red', 'priorities': 'amber' };
      const color = colorMap[field];
      
      const subOptions = window.SUBSTANDARDS_BY_STD[stdId] || [];
      const subSelectHtml = (currentIndex, currentSubId) => `
        <select onchange="updateCommentPoint(${stdId}, '${field}', ${currentIndex}, 'subId', this.value)" 
                class="w-48 px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500/30 transition-all outline-none">
          <option value="">اختر المعيار الفرعي...</option>
          ${subOptions.map(opt => `<option value="${opt.id}" ${opt.id == currentSubId ? 'selected' : ''}>${opt.name}</option>`).join('')}
        </select>
      `;

      container.innerHTML = points.map((point, index) => `
        <div class="flex items-center gap-3 fade-in group">
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-${color}-500/10 text-${color}-600 dark:text-${color}-400 rounded-full text-xs font-bold border border-${color}-500/20">
            ${index + 1}
          </div>
          <div class="flex-1">
            <input type="text" value="${point.text || ''}" placeholder="أدخل النقطة..." 
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

    function markDirty() {
      isDirty = true;
      const btn = document.getElementById('saveBtn');
      if (btn) {
        btn.classList.remove('bg-emerald-600');
        btn.classList.add('bg-blue-600');
        btn.innerHTML = '<i class="fa-solid fa-circle-dot"></i> <span>حفظ التغييرات *</span>';
      }
    }

    async function saveData() {
      const btn = document.getElementById('saveBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري الحفظ...</span>';

      const payload = {
        scores: scores,
        form_data: { standards: standardComments },
        _token: "{{ csrf_token() }}"
      };

      try {
        const response = await fetch(window.SAVE_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        if (result.success) {
          isDirty = false;
          btn.classList.remove('bg-blue-600');
          btn.classList.add('bg-green-600');
          btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>تم الحفظ</span>';
          setTimeout(() => {
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-emerald-600');
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> <span>حفظ التغييرات</span>';
            btn.disabled = false;
          }, 2000);
        }
      } catch (err) {
        console.error(err);
        alert('حدث خطأ أثناء الحفظ');
        btn.disabled = false;
      }
    }

    // Initialize UI
    window.onload = () => {
      // Set initial colors for rating buttons
      Object.entries(scores).forEach(([indId, rating]) => {
        if (rating !== null) {
          const btn = document.querySelector(`[data-indicator-id="${indId}"] .rating-btn[data-rating="${rating}"]`);
          if (btn) {
            if (rating == 0) btn.classList.add('rating-nc');
            else btn.classList.add(`rating-${rating}`);
          }
        }
      });

      @foreach($standards as $standard)
        updateScore({{ $standard->id }});
        renderCommentPoints({{ $standard->id }}, 'strengths');
        renderCommentPoints({{ $standard->id }}, 'improvements');
        renderCommentPoints({{ $standard->id }}, 'priorities');
      @endforeach

      isDirty = false; // Reset after initialization
    };
  </script>
</body>
</html>
