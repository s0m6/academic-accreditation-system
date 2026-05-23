@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/council-secretariat/dashboard',
    ];

    $stages = [
        'stage_one'   => 'طلب الاعتماد الأولي',
        'stage_two'   => 'البيانات الأساسية',
        'stage_three' => 'تقرير الدراسة الذاتية',
        'stage_four'  => 'اختيار لجنة التقييم',
        'stage_five'  => 'تحديد جدول الزيارة',
        'stage_six'   => 'تقارير التقييم الأولية',
        'stage_seven' => 'توصيات اللجنة والردود',
        'stage_eight' => 'تقارير التقييم الختامية',
        'stage_nine'  => 'القرار النهائي والشهادة',
    ];

    $stageColors = [
        'stage_one'   => ['bg' => 'bg-blue-100 dark:bg-blue-500/15',   'text' => 'text-blue-700 dark:text-blue-300',   'dot' => 'bg-blue-500'],
        'stage_two'   => ['bg' => 'bg-indigo-100 dark:bg-indigo-500/15', 'text' => 'text-indigo-700 dark:text-indigo-300', 'dot' => 'bg-indigo-500'],
        'stage_three' => ['bg' => 'bg-violet-100 dark:bg-violet-500/15', 'text' => 'text-violet-700 dark:text-violet-300', 'dot' => 'bg-violet-500'],
        'stage_four'  => ['bg' => 'bg-pink-100 dark:bg-pink-500/15',   'text' => 'text-pink-700 dark:text-pink-300',   'dot' => 'bg-pink-500'],
        'stage_five'  => ['bg' => 'bg-amber-100 dark:bg-amber-500/15', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500'],
        'stage_six'   => ['bg' => 'bg-orange-100 dark:bg-orange-500/15', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
        'stage_seven' => ['bg' => 'bg-teal-100 dark:bg-teal-500/15',   'text' => 'text-teal-700 dark:text-teal-300',   'dot' => 'bg-teal-500'],
        'stage_eight' => ['bg' => 'bg-cyan-100 dark:bg-cyan-500/15',   'text' => 'text-cyan-700 dark:text-cyan-300',   'dot' => 'bg-cyan-500'],
        'stage_nine'  => ['bg' => 'bg-emerald-100 dark:bg-emerald-500/15', 'text' => 'text-emerald-700 dark:text-emerald-300', 'dot' => 'bg-emerald-500'],
    ];

    $universitiesCount  = \App\Models\University::count();
    $coordinatorsCount  = \App\Models\User::where('role', 'council_coordinator')->count();
    $evaluatorsCount    = \App\Models\Evaluator::count();
    $totalRequests      = \App\Models\AccreditationRequest::count();
    $activeRequests     = \App\Models\AccreditationRequest::where('request_status', 'Active')->count();
    $completedRequests  = \App\Models\AccreditationRequest::where('request_status', 'completed')->count();
    $draftRequests      = \App\Models\AccreditationRequest::where('request_status', 'draft')->count();
    $certificatesCount  = \App\Models\AccreditationCertificate::where('is_active', true)->count();

    $latestRequests = \App\Models\AccreditationRequest::with(['program.department.college.university'])
        ->orderBy('updated_at', 'desc')
        ->take(5)
        ->get();

    $requestsPerStage = \App\Models\AccreditationRequest::selectRaw('current_stage, count(*) as count')
        ->groupBy('current_stage')
        ->pluck('count', 'current_stage')
        ->toArray();
@endphp

@section('title', 'الصفحة الرئيسية')
@section('title2', 'الأمانة العامة للمجلس')
@section('description', 'مرحباً بك في لوحة تحكم نظام الاعتماد الأكاديمي الموحد')

@section('content')
<div class="w-full text-start space-y-6">

    {{-- ═══════════════ HERO BANNER ═══════════════ --}}
    <div class="relative overflow-hidden rounded-3xl text-white shadow-xl"
         style="background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 40%, #0f172a 100%);">

        {{-- Decorative blobs --}}
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-10 bottom-0 h-48 w-48 rounded-full bg-blue-400/10 blur-2xl"></div>
        <div class="pointer-events-none absolute right-1/3 -top-8 h-32 w-32 rounded-full bg-indigo-400/10 blur-2xl"></div>

        {{-- Grid texture overlay --}}
        <div class="pointer-events-none absolute inset-0 opacity-5"
             style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div class="relative z-10 flex flex-col gap-6 p-6 md:flex-row md:items-center md:justify-between md:p-8 lg:p-10">

            {{-- Left: welcome text --}}
            <div class="space-y-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                    <span class="h-2 w-2 animate-ping rounded-full bg-emerald-400"></span>
                    النظام يعمل بكفاءة كاملة
                </span>
                <h1 class="text-2xl font-black leading-snug md:text-3xl lg:text-4xl">
                    أهلاً بك، الأمانة العامة للمجلس 🏢
                </h1>
                <p class="max-w-xl text-sm leading-relaxed text-blue-100/90 md:text-base">
                    منصة إدارة الاعتماد الأكاديمي الموحدة — تابع الجامعات، المنسقين، الخبراء، وطلبات الاعتماد من مكان واحد.
                </p>

            </div>

            {{-- Right: date & summary pill --}}
            <div class="flex shrink-0 flex-col gap-3">
                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/8 px-5 py-3 backdrop-blur-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                        <i class="fa-regular fa-calendar-days text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">تاريخ اليوم</p>
                        <p class="text-sm font-black">{{ now()->locale('ar')->translatedFormat('l، d F Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/8 px-5 py-3 backdrop-blur-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-400/20">
                        <i class="fa-solid fa-file-signature text-amber-300"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">الطلبات النشطة</p>
                        <p class="text-sm font-black">{{ $activeRequests }} طلب قيد المعالجة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ STATISTICS GRID ═══════════════ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Card 1: Universities --}}
        <div class="group relative overflow-hidden rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm transition-all duration-300 hover:scale-[1.02] hover:shadow-md">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-blue-600 shadow-sm dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-building-columns text-xl"></i>
                </div>
                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-black text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    مسجلة
                </span>
            </div>
            <h3 class="text-3xl font-black text-(--text-primary)">{{ $universitiesCount }}</h3>
            <p class="mt-1 text-xs font-bold text-(--text-secondary)">الجامعات المسجلة</p>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-bold text-blue-600 dark:text-blue-400">
                <i class="fa-solid fa-circle-check text-[9px]"></i>
                <span>نشطة ومتابَعة في النظام</span>
            </div>
        </div>

        {{-- Card 2: Requests --}}
        <div class="group relative overflow-hidden rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm transition-all duration-300 hover:scale-[1.02] hover:shadow-md">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-amber-100 bg-amber-50 text-amber-600 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-file-signature text-xl"></i>
                </div>
                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-black text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    طلبات
                </span>
            </div>
            <h3 class="text-3xl font-black text-(--text-primary)">{{ $totalRequests }}</h3>
            <p class="mt-1 text-xs font-bold text-(--text-secondary)">طلبات الاعتماد</p>
            <div class="mt-3 flex items-center gap-3 text-[11px] font-bold text-(--text-secondary)">
                <span class="flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    {{ $activeRequests }} نشط
                </span>
                <span class="flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $completedRequests }} مكتمل
                </span>
                <span class="flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    {{ $draftRequests }} مسودة
                </span>
            </div>
        </div>

        {{-- Card 3: Evaluators --}}
        <div class="group relative overflow-hidden rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm transition-all duration-300 hover:scale-[1.02] hover:shadow-md">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-emerald-100 bg-emerald-50 text-emerald-600 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                </div>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-black text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    خبراء
                </span>
            </div>
            <h3 class="text-3xl font-black text-(--text-primary)">{{ $evaluatorsCount }}</h3>
            <p class="mt-1 text-xs font-bold text-(--text-secondary)">خبراء التقييم</p>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                <i class="fa-solid fa-user-check text-[9px]"></i>
                <span>مقيمون مسجلون ومؤهلون</span>
            </div>
        </div>

        {{-- Card 4: Certificates --}}
        <div class="group relative overflow-hidden rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm transition-all duration-300 hover:scale-[1.02] hover:shadow-md">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-violet-100 bg-violet-50 text-violet-600 shadow-sm dark:border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fa-solid fa-award text-xl"></i>
                </div>
                <span class="rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-black text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    شهادات
                </span>
            </div>
            <h3 class="text-3xl font-black text-(--text-primary)">{{ $certificatesCount }}</h3>
            <p class="mt-1 text-xs font-bold text-(--text-secondary)">الشهادات الصادرة</p>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-bold text-violet-600 dark:text-violet-400">
                <i class="fa-solid fa-ribbon text-[9px]"></i>
                <span>شهادات اعتماد وطنية نشطة</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════ REQUESTS PER STAGE ═══════════════ --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm">
        <div class="mb-4 flex items-center gap-2 border-b border-(--border-primary) pb-3">
            <i class="fa-solid fa-chart-column text-brand-600 dark:text-brand-400"></i>
            <h3 class="text-sm font-bold text-(--text-primary)">الطلبات حسب المرحلة</h3>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-9 gap-3">
            @foreach($stages as $stageKey => $stageName)
                @php
                    $count = $requestsPerStage[$stageKey] ?? 0;
                    $colors = $stageColors[$stageKey] ?? ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'dot' => 'bg-slate-400'];
                @endphp
                <a href="{{ route('council_secretariat.requests.' . $stageKey) }}" class="group flex flex-col items-center justify-center rounded-xl border border-(--border-primary) bg-(--bg-main) p-3 text-center shadow-sm transition-all hover:scale-[1.02] hover:shadow hover:border-brand-300 dark:hover:border-brand-600">
                    <span class="mb-2 text-2xl font-black {{ $colors['text'] }} transition-transform group-hover:-translate-y-0.5">{{ $count }}</span>
                    <span class="text-[10px] font-bold text-(--text-secondary) leading-tight transition-colors group-hover:text-brand-600 dark:group-hover:text-brand-400">{{ $stageName }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════ MAIN CONTENT GRID ═══════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- ─── Recent Requests Table (8 cols) ─── --}}
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm">

                {{-- Table Header --}}
                <div class="flex items-center justify-between border-b border-(--border-primary) bg-(--bg-main) px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-brand-100 bg-brand-50 text-brand-600 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-400">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-(--text-primary)">أحدث طلبات الاعتماد الأكاديمي</h3>
                            <p class="text-[11px] text-(--text-secondary)">آخر 5 طلبات تم تحديثها في النظام</p>
                        </div>
                    </div>
                    <a href="{{ route('council_secretariat.requests.stage_one') }}"
                       class="flex items-center gap-1 text-xs font-black text-brand-600 transition-colors hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                        عرض الكل <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </a>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-(--bg-main) text-[11px] font-black uppercase tracking-wider text-(--text-secondary)">
                                <th class="px-6 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">البرنامج والجامعة</th>
                                <th class="px-4 py-3 text-center">المرحلة</th>
                                <th class="px-4 py-3 text-center">الحالة</th>
                                <th class="px-4 py-3 text-center">آخر تحديث</th>
                                <th class="px-4 py-3 text-center">عرض</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-(--border-primary)">
                            @forelse($latestRequests as $index => $requestRecord)
                                @php
                                    $programObj = $requestRecord->program;
                                    $univName   = $programObj?->department?->college?->university?->name;
                                    $colors     = $stageColors[$requestRecord->current_stage] ?? ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'dot' => 'bg-slate-400'];
                                @endphp
                                <tr class="transition-colors duration-150 hover:bg-(--bg-main)">
                                    <td class="px-6 py-4 text-right">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-(--bg-main) text-[11px] font-black text-(--text-secondary)">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-(--border-primary) bg-(--bg-main) text-brand-600 dark:text-brand-400">
                                                <i class="fa-solid fa-graduation-cap text-sm"></i>
                                            </div>
                                            <div class="leading-tight">
                                                <p class="text-sm font-black text-(--text-primary)">
                                                    {{ $programObj ? $programObj->program_name : 'برنامج غير معروف' }}
                                                </p>
                                                <p class="mt-0.5 text-[11px] text-(--text-secondary)">
                                                    {{ $univName ?? '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-black {{ $colors['bg'] }} {{ $colors['text'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $colors['dot'] }}"></span>
                                            {{ $stages[$requestRecord->current_stage] ?? 'غير معروفة' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($requestRecord->request_status === 'Active')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-black text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400">
                                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span> نشط
                                            </span>
                                        @elseif($requestRecord->request_status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-black text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> مكتمل
                                            </span>
                                        @elseif($requestRecord->request_status === 'draft')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-[11px] font-black text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> مسودة
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-[11px] font-black text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> ملغي
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center text-[11px] text-(--text-secondary)">
                                        {{ $requestRecord->updated_at->format('Y/m/d') }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <a href="{{ route('requests.show', $requestRecord) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50 px-3 py-1.5 text-[11px] font-black text-brand-700 transition-all hover:scale-[1.05] hover:bg-brand-100 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20">
                                            <i class="fa-solid fa-eye text-xs"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex max-w-xs flex-col items-center gap-4">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full border border-(--border-primary) bg-(--bg-main)">
                                                <i class="fa-solid fa-file-invoice text-2xl text-(--text-secondary)"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-(--text-primary)">لا توجد طلبات بعد</p>
                                                <p class="mt-1 text-xs text-(--text-secondary)">ستظهر هنا أحدث طلبات الاعتماد المسجلة</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ─── Quick Actions + System Info (4 cols) ─── --}}
        <div class="flex flex-col gap-6 lg:col-span-4">

            {{-- System Health / Info Panel --}}
            <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) p-5 shadow-sm">
                <div class="mb-4 flex items-center gap-3 border-b border-(--border-primary) pb-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-teal-100 bg-teal-50 text-teal-600 dark:border-teal-500/20 dark:bg-teal-500/10 dark:text-teal-400">
                        <i class="fa-solid fa-signal text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-(--text-primary)">حالة النظام</h3>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-(--text-secondary)">قاعدة البيانات</span>
                        <span class="flex items-center gap-1.5 text-[11px] font-black text-emerald-600 dark:text-emerald-400">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> تعمل
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-(--text-secondary)">خادم الملفات</span>
                        <span class="flex items-center gap-1.5 text-[11px] font-black text-emerald-600 dark:text-emerald-400">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> تعمل
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-(--text-secondary)">آخر تحديث للنظام</span>
                        <span class="text-[11px] font-black text-(--text-primary)">{{ now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-(--text-secondary)">إجمالي المستخدمين</span>
                        <span class="text-[11px] font-black text-(--text-primary)">
                            {{ \App\Models\User::count() }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-teal-100 bg-teal-50/50 p-3 dark:border-teal-500/15 dark:bg-teal-500/5">
                    <p class="text-[11px] font-bold leading-relaxed text-teal-700 dark:text-teal-400">
                        <i class="fa-solid fa-circle-info ml-1"></i>
                        جميع خدمات النظام تعمل بشكل طبيعي. آخر فحص كان اليوم.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
