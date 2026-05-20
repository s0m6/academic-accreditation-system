<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نموذج 5 - تقرير الزيارة الميدانية</title>


  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark ::-webkit-scrollbar-thumb { background: #475569; }

    /* Tab indicator – requires pseudo-class, can't be inlined */
    .tab-btn { border-bottom: 2px solid transparent; transition: color 0.2s, border-color 0.2s; }
    .tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; font-weight: 700; }
    .dark .tab-btn.active { color: #60a5fa; border-bottom-color: #60a5fa; }
    .tab-btn:not(.active):hover { color: #2563eb; border-bottom-color: #93c5fd; }
    .dark .tab-btn:not(.active):hover { color: #93c5fd; border-bottom-color: #93c5fd; }

    /* Tab panels */
    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeInTab 0.3s ease forwards; }
    @keyframes fadeInTab {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Table inputs */
    .tbl-input { width: 100%; background: var(--bg-main); border: 1px solid var(--border-primary); color: var(--text-primary); border-radius: 8px; padding: 8px 10px; font-size: 13px; font-family: 'Cairo', sans-serif; transition: all 0.2s; outline: none; }
    .tbl-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    .tbl-textarea { min-height: 72px; resize: vertical; line-height: 1.7; }
    .dark .tbl-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
    .dark .tbl-input::placeholder { color: #64748b; }

    /* Modern Radio Group */
    .radio-group { display: flex; gap: 0.5rem; width: 100%; min-width: 280px; }
    .radio-card { flex: 1; position: relative; }
    .radio-card input { position: absolute; opacity: 0; width: 0; height: 0; }
    .radio-label {
      display: flex; align-items: center; justify-content: center; gap: 0.4rem;
      padding: 0.6rem 0.4rem; border-radius: 10px; border: 1.5px solid var(--border-primary);
      background: var(--bg-main); cursor: pointer; transition: all 0.2s;
      font-size: 11px; font-weight: 800; color: var(--text-secondary);
      white-space: nowrap;
    }
    .radio-card input:checked + .radio-label.excellent {
      background: rgba(16, 185, 129, 0.1); border-color: #10b981; color: #10b981;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
    .radio-card input:checked + .radio-label.average {
      background: rgba(245, 158, 11, 0.1); border-color: #f59e0b; color: #f59e0b;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }
    .radio-card input:checked + .radio-label.poor {
      background: rgba(239, 68, 68, 0.1); border-color: #ef4444; color: #ef4444;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }
    .radio-label:hover { border-color: #3b82f6; background: rgba(59, 130, 246, 0.05); }
    .dark .radio-label { background: #0f172a; }
    .dark .radio-card input:checked + .radio-label.excellent { background: rgba(16, 185, 129, 0.15); }
    .dark .radio-card input:checked + .radio-label.average { background: rgba(245, 158, 11, 0.15); }
    .dark .radio-card input:checked + .radio-label.poor { background: rgba(239, 68, 68, 0.15); }

    /* Results two-column grid */
    .results-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 768px) { .results-grid { grid-template-columns: 1fr; } }
    .results-col { border: 1px solid var(--border-primary); border-radius: 12px; overflow: hidden; }
    .results-col-header { padding: 12px 16px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .results-col-header.positive { background: linear-gradient(135deg,#d1fae5,#a7f3d0); color: #065f46; border-bottom: 1px solid #6ee7b7; }
    .dark .results-col-header.positive { background: rgba(16,185,129,0.12); color: #34d399; border-bottom: 1px solid rgba(52,211,153,0.2); }
    .results-col-header.negative { background: linear-gradient(135deg,#fee2e2,#fecaca); color: #7f1d1d; border-bottom: 1px solid #fca5a5; }
    .dark .results-col-header.negative { background: rgba(239,68,68,0.12); color: #f87171; border-bottom: 1px solid rgba(248,113,113,0.2); }
    .results-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-bottom: 1px solid var(--border-subtle); }
    .dark .results-item { border-bottom-color: var(--border-primary); }
    .results-item-num { width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; background: var(--border-subtle); color: var(--text-secondary); }
    .dark .results-item-num { background: #1e293b; }
    .results-add-btn { width: 100%; padding: 10px; border: none; cursor: pointer; font-family: 'Cairo',sans-serif; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; }
    .results-add-btn.positive { background: rgba(16,185,129,0.08); color: #065f46; }
    .dark .results-add-btn.positive { background: rgba(52,211,153,0.08); color: #34d399; }
    .results-add-btn.positive:hover { background: rgba(16,185,129,0.18); }
    .results-add-btn.negative { background: rgba(239,68,68,0.08); color: #7f1d1d; }
    .dark .results-add-btn.negative { background: rgba(248,113,113,0.08); color: #f87171; }
    .results-add-btn.negative:hover { background: rgba(239,68,68,0.18); }

    /* Delete button */
    .btn-del { width: 28px; height: 28px; min-width: 28px; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; background: rgba(239,68,68,0.1); color: #dc2626; transition: all 0.2s; }
    .dark .btn-del { background: rgba(239,68,68,0.12); color: #f87171; }
    .btn-del:hover { background: #dc2626; color: #fff; transform: scale(1.1); }

    /* Alert toast */
    #alert-container { position: fixed; top: 24px; right: 24px; z-index: 9999; max-width: 400px; display: flex; flex-direction: column; gap: 10px; }
    .alert-toast { padding: 14px 18px; border-radius: 12px; font-size: 14px; display: flex; align-items: center; gap: 10px; border: 1px solid; font-weight: 600; box-shadow: 0 10px 25px rgba(0,0,0,0.12); font-family: 'Cairo',sans-serif; }
    .alert-toast.success { background: linear-gradient(135deg,#d1fae5,#a7f3d0); color: #065f46; border-color: #6ee7b7; }
    .alert-toast { animation: slideIn 0.35s ease forwards; }
    .alert-toast.removing { animation: slideOut 0.3s ease forwards; }
    @keyframes slideIn { from { opacity: 0; transform: translateX(400px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(400px); } }

    /* View Mode overrides */
    .view-mode input, .view-mode textarea { pointer-events: none; opacity: 0.8; }
    .view-mode .btn-del, .view-mode .results-add-btn, .view-mode button[onclick^="addInterviewRow"], .view-mode button[onclick^="addResultItem"] { display: none !important; }

    /* Color Scheme */
    html.dark {
      color-scheme: dark;
    }

    /* Date and Time picker icons */
    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
      cursor: pointer;
      filter: brightness(0); /* Force to black in light mode */
      transition: all 0.2s;
      font-size: 1.1rem;
    }
    
    .dark input[type="date"]::-webkit-calendar-picker-indicator,
    .dark input[type="time"]::-webkit-calendar-picker-indicator {
      filter: brightness(0) invert(1) !important; /* Force to black then invert to white in dark mode */
    }
  </style>
</head>

<body class="min-h-screen p-4 md:p-8 transition-colors duration-300 {{ (!isset($isEditMode) || !$isEditMode) ? 'view-mode' : '' }}">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


  <!-- Alert Container -->
  <div id="alert-container"></div>

  <!-- DARK MODE TOGGLE -->
  <div class="fixed bottom-6 left-6 z-50 w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg border bg-(--surface-card) border-(--border-primary) text-(--text-secondary) cursor-pointer transition-all"
    id="theme-toggle"
    onclick="toggleDark()"
    title="تبديل الوضع">
    <i class="fa-solid fa-moon text-base"></i>
  </div>

  <div class="max-w-5xl mx-auto">

    <!-- ======= HEADER ======= -->
    <div class="rounded-2xl p-4 md:p-6 mb-4 md:mb-6 flex items-center justify-between shadow-sm border bg-(--surface-card) border-(--border-primary)">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner bg-brand-600/10 text-brand-600 border border-brand-600/20">
          <i class="fa-solid fa-clipboard-list"></i>
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-black text-(--text-primary)">نموذج تقرير الزيارة الميدانية</h1>
        </div>
      </div>
      <div class="flex items-center gap-3">
         @if(isset($isEditMode) && $isEditMode)
         <button type="button" onclick="document.getElementById('save-draft').click()" 
            class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl border-none cursor-pointer transition-all hover:shadow-lg active:scale-95 bg-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-500/20">
            <i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات
         </button>
         @endif
         <a href="{{ route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six']) }}" 
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
             العودة للوحة الطلب <i class="fa-solid fa-arrow-left text-xs ms-1"></i>
         </a>
      </div>
    </div>

    <!-- ======= TABS NAVIGATION ======= -->
    <div class="rounded-2xl shadow-sm border overflow-hidden bg-(--surface-card) border-(--border-primary)">

      <!-- Tab bar -->
      <div class="flex md:justify-center overflow-x-auto border-b border-(--border-primary)">
        <button class="tab-btn active flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap cursor-pointer"
                onclick="switchTab('tab-1', this)" id="btn-tab-1">
          <i class="fa-solid fa-star text-sm text-amber-500"></i>
          الملاحظات العامة
        </button>
        <button class="tab-btn flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap cursor-pointer"
                onclick="switchTab('tab-2', this)" id="btn-tab-2">
          <i class="fa-solid fa-users text-sm text-blue-500"></i>
          المقابلات
        </button>
        <button class="tab-btn flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap cursor-pointer"
                onclick="switchTab('tab-3', this)" id="btn-tab-3">
          <i class="fa-solid fa-chart-bar text-sm text-emerald-500"></i>
          النتائج العامة
        </button>
        <button class="tab-btn flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap cursor-pointer"
                onclick="switchTab('tab-4', this)" id="btn-tab-4">
          <i class="fa-solid fa-route text-sm text-purple-500"></i>
          الجولات الميدانية
        </button>
        <button class="tab-btn flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap cursor-pointer"
                onclick="switchTab('tab-5', this)" id="btn-tab-5">
          <i class="fa-solid fa-file-alt text-sm text-rose-500"></i>
          الاطلاع على الوثائق
        </button>
      </div>

      <!-- ============================================================
           TAB 1: الملاحظات العامة
      ============================================================ -->
      <div id="tab-1" class="tab-panel active p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 bg-brand-600/10 text-brand-600 border border-brand-600/20">
            <i class="fa-solid fa-star"></i>
          </div>
          <div>
            <h2 class="text-base font-black">الملاحظات العامة</h2>
            <p class="text-xs">تقييم مستوى تعاون المؤسسة مع لجنة المقيمين</p>
          </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-(--bg-main)">
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider w-12 text-(--text-secondary)">م</th>
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider text-(--text-secondary)">الملاحظة</th>
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider w-80 text-(--text-secondary)">النتيجة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-(--border-primary)">
              <!-- Row 1 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">1</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  استقبلت المؤسسة التعليمية لجنة المقيمين وأظهرت تجاوبا وتعاونا وهيأت كافة الظروف المناسبة لتمكين اللجنة من أداء مهامها.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q1" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q1" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q1" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 2 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">2</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  جهزت المؤسسة قاعة مناسبة لاجتماع اللجنة وإجراء المقابلات بطاقة استيعابية كافية، مزودة بجميع التجهيزات المطلوبة.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q2" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q2" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q2" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 3 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">3</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  التزمت المؤسسة ببرنامج زيارة اللجنة وتنفيذ المقابلات في مواعيدها المحددة.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q3" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q3" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q3" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 4 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">4</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  التزمت المؤسسة بالتنسيق مع المعنيين بالمقابلات التي حددتها اللجنة مع تأمين حضورهم.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q4" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q4" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q4" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 5 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">5</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  سهلت المؤسسة تنفيذ الجولات الميدانية على المرافق حسب البرنامج المعد.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q5" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q5" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q5" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 6 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">6</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  تم وضع كافة نسخ الوثائق والشواهد المطلوب تقديمها من البرنامج في القاعة المخصصة للجنة.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q6" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q6" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q6" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
              <!-- Row 7 -->
              <tr class="transition-colors hover:opacity-90 bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">7</span>
                </td>
                <td class="px-4 py-4 text-sm leading-relaxed text-(--text-secondary)">
                  وافقت المؤسسة على الملخص الذي قدمته اللجنة في ختام الزيارة الميدانية.
                </td>
                <td class="px-4 py-4">
                  <div class="radio-group">
                    <label class="radio-card">
                      <input type="radio" name="q7" value="ممتاز">
                      <span class="radio-label excellent">
                        <i class="fa-solid fa-circle-check"></i> ممتاز
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q7" value="متوسط">
                      <span class="radio-label average">
                        <i class="fa-solid fa-circle-minus"></i> متوسط
                      </span>
                    </label>
                    <label class="radio-card">
                      <input type="radio" name="q7" value="ضعيف">
                      <span class="radio-label poor">
                        <i class="fa-solid fa-circle-xmark"></i> ضعيف
                      </span>
                    </label>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ============================================================
           TAB 2: المقابلات
      ============================================================ -->
      <div id="tab-2" class="tab-panel p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 bg-brand-600/10 text-brand-600 border border-brand-600/20">
            <i class="fa-solid fa-users"></i>
          </div>
          <div>
            <h2 class="text-base font-black">المقابلات</h2>
            <p class="text-xs">سجل المقابلات التي أجرتها لجنة المقيمين</p>
          </div>
        </div>

        <div class="overflow-x-auto rounded-xl border mb-4">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-(--bg-main)">
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider w-10 text-(--text-secondary)">ت</th>
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider text-(--text-secondary)">الحضور</th>
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider w-28 text-(--text-secondary)">من الساعة</th>
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider w-28 text-(--text-secondary)">إلى الساعة</th>
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider w-32 text-(--text-secondary)">التاريخ</th>
                <th class="px-3 py-3 text-right font-bold text-xs uppercase tracking-wider text-(--text-secondary)">الملاحظات</th>
                <th class="px-3 py-3 w-10"></th>
              </tr>
            </thead>
            <tbody id="interviews-body" class="divide-y divide-(--border-primary)">
              <tr>
                <td class="px-3 py-3">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">1</span>
                </td>
                <td class="px-3 py-3"><input type="text" class="tbl-input" placeholder="اسم الحاضر أو الجهة"></td>
                <td class="px-3 py-3"><input type="time" class="tbl-input"></td>
                <td class="px-3 py-3"><input type="time" class="tbl-input"></td>
                <td class="px-3 py-3"><input type="date" class="tbl-input"></td>
                <td class="px-3 py-3"><textarea class="tbl-input tbl-textarea" placeholder="ملاحظات..."></textarea></td>
                <td class="px-3 py-3 text-center">
                  <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteRow(this)" title="حذف"><i class="fa-solid fa-trash-can text-xs"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <button type="button" onclick="addInterviewRow()"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl border cursor-pointer transition-all hover:shadow-sm bg-brand-600/10 text-brand-600 border-brand-600/25">
          <i class="fa-solid fa-plus text-xs"></i> إضافة صف جديد
        </button>
      </div>

      <!-- ============================================================
           TAB 3: النتائج العامة للمقابلات
      ============================================================ -->
      <div id="tab-3" class="tab-panel p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 bg-brand-600/10 text-brand-600 border border-brand-600/20">
            <i class="fa-solid fa-chart-bar"></i>
          </div>
          <div>
            <h2 class="text-base font-black">النتائج العامة للمقابلات</h2>
            <p class="text-xs">الإيجابيات والسلبيات المستخلصة من المقابلات</p>
          </div>
        </div>

        <!-- Two-column layout: Positives | Negatives -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

          <!-- === Positives Column === -->
          <div class="border border-(--border-primary) rounded-xl overflow-hidden bg-(--surface-card)">
            <div class="p-3 md:p-4 text-sm md:text-base font-bold flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-b border-emerald-100 dark:border-emerald-800/30">
              <i class="fa-solid fa-circle-check"></i>
              الإيجابيات
            </div>
            <!-- Items list -->
            <div id="positives-list">
              <div class="flex items-center gap-2 p-2 md:p-3 border-b border-(--border-subtle) dark:border-(--border-primary)">
                <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-sm font-bold bg-(--border-subtle) dark:bg-slate-800 text-(--text-secondary) positive-num">1</span>
                <input type="text" class="tbl-input flex-1" placeholder="أدخل ملاحظة إيجابية...">
                <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteResultItem(this, 'positives-list', 'positive-num')" title="حذف">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>
            </div>
            <!-- Add button -->
            <button type="button" onclick="addResultItem('positives-list', 'positive-num', 'positive')"
              class="w-full p-3 font-bold text-sm flex items-center justify-center gap-1.5 transition-colors bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
              <i class="fa-solid fa-plus text-xs"></i> إضافة ملاحظة إيجابية
            </button>
          </div>

          <!-- === Negatives Column === -->
          <div class="border border-(--border-primary) rounded-xl overflow-hidden bg-(--surface-card)">
            <div class="p-3 md:p-4 text-sm md:text-base font-bold flex items-center gap-2 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border-b border-rose-100 dark:border-rose-800/30">
              <i class="fa-solid fa-circle-xmark"></i>
              السلبيات
            </div>
            <!-- Items list -->
            <div id="negatives-list">
              <div class="flex items-center gap-2 p-2 md:p-3 border-b border-(--border-subtle) dark:border-(--border-primary)">
                <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-sm font-bold bg-(--border-subtle) dark:bg-slate-800 text-(--text-secondary) negative-num">1</span>
                <input type="text" class="tbl-input flex-1" placeholder="أدخل ملاحظة سلبية...">
                <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteResultItem(this, 'negatives-list', 'negative-num')" title="حذف">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>
            </div>
            <!-- Add button -->
            <button type="button" onclick="addResultItem('negatives-list', 'negative-num', 'negative')"
              class="w-full p-3 font-bold text-sm flex items-center justify-center gap-1.5 transition-colors bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20">
              <i class="fa-solid fa-plus text-xs"></i> إضافة ملاحظة سلبية
            </button>
          </div>

        </div><!-- end results-grid -->
      </div>

      <!-- ============================================================
           TAB 4: الجولات الميدانية
      ============================================================ -->
      <div id="tab-4" class="tab-panel p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 bg-brand-600/10 text-brand-600 border border-brand-600/20">
            <i class="fa-solid fa-route"></i>
          </div>
          <div>
            <h2 class="text-base font-black">الجولات الميدانية على المرافق</h2>
            <p class="text-xs">تسجيل نتائج زيارة المرافق الجامعية</p>
          </div>
        </div>

        <!-- Date field -->
        <div class="mb-5">
          <label class="block text-sm font-bold mb-2">التاريخ:</label>
          <input type="date" id="tours-date" class="tbl-input max-w-xs">
        </div>

        <div class="overflow-x-auto rounded-xl border border-(--border-primary)">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-(--bg-main)">
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider w-10 text-(--text-secondary)">ت</th>
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider w-44 text-(--text-secondary)">المرفق</th>
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider w-24 text-(--text-secondary)">العدد</th>
                <th class="px-4 py-3 text-right font-bold text-xs uppercase tracking-wider text-(--text-secondary)">الملاحظات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-(--border-primary)">
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">1</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">القاعات الدراسية</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">2</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">مكاتب أعضاء هيئة التدريس</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">3</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">المختبرات والورش</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">4</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">المرافق الخدمية للطلاب</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">5</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">عمادة شؤون الطلبة</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
              <tr class="transition-colors bg-(--surface-card)">
                <td class="px-4 py-4">
                  <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">6</span>
                </td>
                <td class="px-4 py-4 font-semibold text-sm">المكتبة</td>
                <td class="px-4 py-4"><input type="text" class="tbl-input" placeholder="العدد"></td>
                <td class="px-4 py-4"><textarea class="tbl-input tbl-textarea" placeholder="الملاحظات..."></textarea></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ============================================================
           TAB 5: الاطلاع على الوثائق
      ============================================================ -->
      <div id="tab-5" class="tab-panel p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 bg-brand-600/10 text-brand-600 border border-brand-600/20">
            <i class="fa-solid fa-file-alt"></i>
          </div>
          <div>
            <h2 class="text-base font-black">الاطلاع على الوثائق</h2>
            <p class="text-xs">ملاحظات مراجعة الوثائق والمستندات المقدمة</p>
          </div>
        </div>

        <!-- Date field -->
        <div class="mb-5">
          <label class="block text-sm font-bold mb-2">التاريخ:</label>
          <input type="date" id="docs-date" class="tbl-input max-w-xs">
        </div>

        <!-- Two-column layout: Positive notes | Negative notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

          <!-- Positive notes Column -->
          <div class="border border-(--border-primary) rounded-xl overflow-hidden bg-(--surface-card)">
            <div class="p-3 md:p-4 text-sm md:text-base font-bold flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-b border-emerald-100 dark:border-emerald-800/30">
              <i class="fa-solid fa-circle-check"></i>
              ملاحظات إيجابية
            </div>
            <div id="docs-positives-list">
              <div class="flex items-center gap-2 p-2 md:p-3 border-b border-(--border-subtle) dark:border-(--border-primary)">
                <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-sm font-bold bg-(--border-subtle) dark:bg-slate-800 text-(--text-secondary) docs-positive-num">1</span>
                <input type="text" class="tbl-input flex-1" placeholder="أدخل ملاحظة إيجابية...">
                <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteResultItem(this, 'docs-positives-list', 'docs-positive-num')" title="حذف">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>
            </div>
            <button type="button" onclick="addResultItem('docs-positives-list', 'docs-positive-num', 'positive')"
              class="w-full p-3 font-bold text-sm flex items-center justify-center gap-1.5 transition-colors bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
              <i class="fa-solid fa-plus text-xs"></i> إضافة ملاحظة إيجابية
            </button>
          </div>

          <!-- Negative notes Column -->
          <div class="border border-(--border-primary) rounded-xl overflow-hidden bg-(--surface-card)">
            <div class="p-3 md:p-4 text-sm md:text-base font-bold flex items-center gap-2 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border-b border-rose-100 dark:border-rose-800/30">
              <i class="fa-solid fa-circle-xmark"></i>
              ملاحظات سلبية
            </div>
            <div id="docs-negatives-list">
              <div class="flex items-center gap-2 p-2 md:p-3 border-b border-(--border-subtle) dark:border-(--border-primary)">
                <span class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-sm font-bold bg-(--border-subtle) dark:bg-slate-800 text-(--text-secondary) docs-negative-num">1</span>
                <input type="text" class="tbl-input flex-1" placeholder="أدخل ملاحظة سلبية...">
                <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteResultItem(this, 'docs-negatives-list', 'docs-negative-num')" title="حذف">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>
            </div>
            <button type="button" onclick="addResultItem('docs-negatives-list', 'docs-negative-num', 'negative')"
              class="w-full p-3 font-bold text-sm flex items-center justify-center gap-1.5 transition-colors bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20">
              <i class="fa-solid fa-plus text-xs"></i> إضافة ملاحظة سلبية
            </button>
          </div>

        </div><!-- end results-grid -->
      </div>

      <!-- ======= ACTION BAR ======= -->
      <div class="border-t border-(--border-primary) px-6 py-5 flex items-center justify-between bg-(--bg-main)">
        <!-- Tab navigation buttons -->
        <div class="flex gap-2">
          <button id="btn-prev" onclick="navigateTab(-1)"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl border cursor-pointer transition-all hover:shadow-sm disabled:opacity-40 bg-(--surface-card) border-(--border-primary) text-(--text-secondary)"
            disabled>
            <i class="fa-solid fa-arrow-right text-xs"></i> السابق
          </button>
          <button id="btn-next" onclick="navigateTab(1)"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl border cursor-pointer transition-all hover:shadow-sm bg-brand-600/10 text-brand-600 border-brand-600/25">
            التالي <i class="fa-solid fa-arrow-left text-xs"></i>
          </button>
        </div>
        <!-- Save/Submit -->
        <div class="flex gap-3">
          @if(isset($isEditMode) && $isEditMode)
          <button id="save-draft"
            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl border-none cursor-pointer transition-all hover:shadow-lg active:scale-95 bg-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-500/20">
            <i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات
          </button>
          @endif
        </div>
      </div>
    </div><!-- end main card -->
  </div><!-- end max-w -->

  <script>
    /* ======================================================
       DARK MODE
    ====================================================== */
    function toggleDark() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      updateThemeIcon(isDark);
    }

    function updateThemeIcon(isDark) {
      const icon = document.querySelector('#theme-toggle i');
      if (isDark) {
        icon.className = 'fa-solid fa-sun text-base text-amber-400';
      } else {
        icon.className = 'fa-solid fa-moon text-base';
      }
    }

    // Initialize theme from localStorage
    const savedTheme = localStorage.getItem('theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
      document.documentElement.classList.add('dark');
      updateThemeIcon(true);
    } else {
      updateThemeIcon(false);
    }

    /* ======================================================
       UNSAVED CHANGES WARNING
    ====================================================== */
    let isDirty = false;
    document.addEventListener('input', () => { isDirty = true; });
    document.addEventListener('change', () => { isDirty = true; });

    window.addEventListener('beforeunload', function (e) {
      if (isDirty) {
        e.preventDefault();
        e.returnValue = '';
      }
    });

    /* ======================================================
       TABS
    ====================================================== */
    let currentTab = 1;
    const totalTabs = 5;

    function switchTab(tabId, btnEl) {
      // Hide all panels
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      // Remove active from all buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      // Show selected
      document.getElementById(tabId).classList.add('active');
      btnEl.classList.add('active');
      // Update current tab index
      currentTab = parseInt(tabId.replace('tab-', ''));
      updateNavButtons();
    }

    function navigateTab(dir) {
      const next = currentTab + dir;
      if (next < 1 || next > totalTabs) return;
      const btn = document.getElementById('btn-tab-' + next);
      switchTab('tab-' + next, btn);
    }

    function updateNavButtons() {
      const prevBtn = document.getElementById('btn-prev');
      const nextBtn = document.getElementById('btn-next');
      prevBtn.disabled = currentTab === 1;
      nextBtn.disabled = currentTab === totalTabs;
      prevBtn.style.opacity = currentTab === 1 ? '0.4' : '1';
      nextBtn.style.opacity = currentTab === totalTabs ? '0.4' : '1';
    }

    /* ======================================================
       INTERVIEWS TABLE
    ====================================================== */
    function addInterviewRow() {
      const tbody = document.getElementById('interviews-body');
      const rowCount = tbody.rows.length + 1;
      const row = document.createElement('tr');
      row.style.backgroundColor = 'var(--surface-card)';
      row.innerHTML = `
        <td class="px-3 py-3">
          <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-bold bg-brand-600/10 text-brand-600">${rowCount}</span>
        </td>
        <td class="px-3 py-3"><input type="text" class="tbl-input" placeholder="اسم الحاضر أو الجهة"></td>
        <td class="px-3 py-3"><input type="time" class="tbl-input"></td>
        <td class="px-3 py-3"><input type="time" class="tbl-input"></td>
        <td class="px-3 py-3"><input type="date" class="tbl-input"></td>
        <td class="px-3 py-3"><textarea class="tbl-input tbl-textarea" placeholder="ملاحظات..."></textarea></td>
        <td class="px-3 py-3 text-center">
          <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteRow(this)" title="حذف"><i class="fa-solid fa-trash-can text-xs"></i></button>
        </td>
      `;
      tbody.appendChild(row);
      renumberRows(tbody, 'span');
    }

    function deleteRow(btn) {
      const row = btn.closest('tr');
      const tbody = row.closest('tbody');
      row.style.transition = 'opacity 0.2s';
      row.style.opacity = '0';
      setTimeout(() => {
        row.remove();
        renumberRows(tbody, 'span');
      }, 200);
    }

    function renumberRows(tbody, selector) {
      tbody.querySelectorAll('tr').forEach((row, i) => {
        const el = row.querySelector(selector);
        if (el) el.textContent = i + 1;
      });
    }

    /* ======================================================
       RESULTS / DOCS - Two-column pros/cons
    ====================================================== */
    function addResultItem(listId, numClass, type) {
      const list = document.getElementById(listId);
      const count = list.querySelectorAll('.results-item').length + 1;
      const placeholder = type === 'positive' ? 'أدخل ملاحظة إيجابية...' : 'أدخل ملاحظة سلبية...';

      const div = document.createElement('div');
      div.className = 'results-item';
      div.innerHTML = `
        <span class="results-item-num ${numClass}">${count}</span>
        <input type="text" class="tbl-input flex-1" placeholder="${placeholder}">
        <button class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-sm bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-transform hover:scale-105 btn-del" onclick="deleteResultItem(this, '${listId}', '${numClass}')" title="حذف">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      `;
      // Animate in
      div.style.opacity = '0';
      div.style.transform = 'translateY(-6px)';
      list.appendChild(div);
      requestAnimationFrame(() => {
        div.style.transition = 'all 0.25s ease';
        div.style.opacity = '1';
        div.style.transform = 'translateY(0)';
      });
      // Focus the new input
      setTimeout(() => div.querySelector('input').focus(), 50);
    }

    function deleteResultItem(btn, listId, numClass) {
      const item = btn.closest('.results-item');
      const list = document.getElementById(listId);
      // Don't delete if only one item remains
      if (list.querySelectorAll('.results-item').length <= 1) {
        item.querySelector('input').value = '';
        return;
      }
      item.style.transition = 'all 0.2s';
      item.style.opacity = '0';
      item.style.transform = 'translateX(10px)';
      setTimeout(() => {
        item.remove();
        renumberResultItems(list, numClass);
      }, 200);
    }

    function renumberResultItems(list, numClass) {
      list.querySelectorAll('.' + numClass).forEach((el, i) => {
        el.textContent = i + 1;
      });
    }

    /* ======================================================
       ALERTS
    ====================================================== */
    function showAlert(message, type = 'success') {
      const container = document.getElementById('alert-container');
      const toast = document.createElement('div');
      toast.className = `alert-toast ${type}`;
      toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${message}`;
      container.appendChild(toast);
      setTimeout(() => {
        toast.classList.add('removing');
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    }

    /* ======================================================
       COLLECT AND SUBMIT DATA
    ====================================================== */
    function collectData() {
      const data = {
        general_notes: {},
        interviews: [],
        interview_positives: [],
        interview_negatives: [],
        tours_date: document.getElementById('tours-date').value,
        tours: [],
        docs_date: document.getElementById('docs-date').value,
        docs_positives: [],
        docs_negatives: []
      };

      // Tab 1
      for(let i=1; i<=7; i++) {
        const radio = document.querySelector(`input[name="q${i}"]:checked`);
        if(radio) data.general_notes[`q${i}`] = radio.value;
      }

      // Tab 2
      const interviewRows = document.querySelectorAll('#interviews-body tr');
      interviewRows.forEach(row => {
        const inputs = row.querySelectorAll('input, textarea');
        if(inputs.length >= 4) {
          data.interviews.push({
            name: inputs[0].value,
            from: inputs[1].value,
            to: inputs[2].value,
            date: inputs[3].value,
            notes: inputs[4].value
          });
        }
      });

      // Tab 3
      document.querySelectorAll('#positives-list input').forEach(inp => { if(inp.value) data.interview_positives.push(inp.value) });
      document.querySelectorAll('#negatives-list input').forEach(inp => { if(inp.value) data.interview_negatives.push(inp.value) });

      // Tab 4
      const tourRows = document.querySelectorAll('#tab-4 tbody tr');
      tourRows.forEach(row => {
        const facility = row.querySelector('td:nth-child(2)').innerText;
        const inputs = row.querySelectorAll('input, textarea');
        if(inputs.length >= 2) {
          data.tours.push({
            facility: facility,
            count: inputs[0].value,
            notes: inputs[1].value
          });
        }
      });

      // Tab 5
      document.querySelectorAll('#docs-positives-list input').forEach(inp => { if(inp.value) data.docs_positives.push(inp.value) });
      document.querySelectorAll('#docs-negatives-list input').forEach(inp => { if(inp.value) data.docs_negatives.push(inp.value) });

      return data;
    }

    const saveBtn = document.getElementById('save-draft');
    if (saveBtn) {
      saveBtn.addEventListener('click', () => {
          isDirty = false; // Reset dirty flag before submit
          const data = collectData();
          document.getElementById('report_data_input').value = JSON.stringify(data);
          document.getElementById('report-form').submit();
      });
    }

    /* ======================================================
       PREFILL DATA
    ====================================================== */
    const existingData = @json($report->form5_data ?? []);
    if (Object.keys(existingData).length > 0) {
      // Tab 1
      if (existingData.general_notes) {
        for(let i=1; i<=7; i++) {
          if(existingData.general_notes[`q${i}`]) {
            const radio = document.querySelector(`input[name="q${i}"][value="${existingData.general_notes[`q${i}`]}"]`);
            if(radio) { radio.checked = true; radio.setAttribute('data-was-checked', 'true'); }
          }
        }
      }
      
      // Tab 2
      if (existingData.interviews && existingData.interviews.length > 0) {
        const tbody = document.getElementById('interviews-body');
        tbody.innerHTML = '';
        existingData.interviews.forEach((inv, idx) => {
          addInterviewRow();
          const row = tbody.lastElementChild;
          const inputs = row.querySelectorAll('input, textarea');
          inputs[0].value = inv.name || '';
          inputs[1].value = inv.from || '';
          inputs[2].value = inv.to || '';
          inputs[3].value = inv.date || '';
          inputs[4].value = inv.notes || '';
        });
      }

      // Tab 3
      if (existingData.interview_positives && existingData.interview_positives.length > 0) {
        document.getElementById('positives-list').innerHTML = '';
        existingData.interview_positives.forEach(val => {
          addResultItem('positives-list', 'positive-num', 'positive');
          const list = document.getElementById('positives-list');
          list.lastElementChild.querySelector('input').value = val;
        });
      }
      if (existingData.interview_negatives && existingData.interview_negatives.length > 0) {
        document.getElementById('negatives-list').innerHTML = '';
        existingData.interview_negatives.forEach(val => {
          addResultItem('negatives-list', 'negative-num', 'negative');
          const list = document.getElementById('negatives-list');
          list.lastElementChild.querySelector('input').value = val;
        });
      }

      // Tab 4
      if (existingData.tours_date) document.getElementById('tours-date').value = existingData.tours_date;
      if (existingData.tours && existingData.tours.length > 0) {
        const tourRows = document.querySelectorAll('#tab-4 tbody tr');
        existingData.tours.forEach((tour, i) => {
          if (tourRows[i]) {
            const inputs = tourRows[i].querySelectorAll('input, textarea');
            inputs[0].value = tour.count || '';
            inputs[1].value = tour.notes || '';
          }
        });
      }

      // Tab 5
      if (existingData.docs_date) document.getElementById('docs-date').value = existingData.docs_date;
      if (existingData.docs_positives && existingData.docs_positives.length > 0) {
        document.getElementById('docs-positives-list').innerHTML = '';
        existingData.docs_positives.forEach(val => {
          addResultItem('docs-positives-list', 'docs-positive-num', 'positive');
          const list = document.getElementById('docs-positives-list');
          list.lastElementChild.querySelector('input').value = val;
        });
      }
      if (existingData.docs_negatives && existingData.docs_negatives.length > 0) {
        document.getElementById('docs-negatives-list').innerHTML = '';
        existingData.docs_negatives.forEach(val => {
          addResultItem('docs-negatives-list', 'docs-negative-num', 'negative');
          const list = document.getElementById('docs-negatives-list');
          list.lastElementChild.querySelector('input').value = val;
        });
      }
    }

  </script>

  <form id="report-form" method="POST" action="{{ route('requests.stage_six.visit_report.save', $accreditationRequest) }}">
      @csrf
      <input type="hidden" name="report_data" id="report_data_input">
  </form>

</body>
</html>