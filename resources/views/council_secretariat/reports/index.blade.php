@extends('partials.app')

@php
    $breadcrumbs = [
        'الصفحة الرئيسية' => '/council-secretariat/dashboard',
        'التقارير والإحصائيات' => '/council-secretariat/reports',
    ];
@endphp

@section('title', 'التقارير والإحصائيات')
@section('title2', 'إدارة وتوليد التقارير')
@section('description', 'توليد وتنزيل تقارير إحصائية وتفصيلية عن الجامعات، طلبات الاعتماد، والخبراء المقيمين')

@section('content')
<div class="w-full text-start" x-data="reportsManager()">

    {{-- Alerts / Messages --}}
    @if(session('success'))
        <div class="mb-6 text-green-700 bg-green-50 p-4 rounded-xl flex items-center shadow-sm border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
            <i class="fa-solid fa-circle-check text-xl me-3"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 text-red-700 bg-red-50 p-4 rounded-xl flex items-center shadow-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
            <i class="fa-solid fa-triangle-exclamation text-xl me-3"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══════════════ KPI STATS GRID ═══════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Total Universities --}}
        <div class="bg-(--surface-card) border border-(--border-primary) p-5 rounded-2xl shadow-xs flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                <i class="fa-solid fa-building-columns text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-(--text-secondary) font-bold mb-1">الجامعات المسجلة</p>
                <h3 class="text-2xl font-black text-(--text-primary)">{{ $totalUniversities }}</h3>
            </div>
        </div>

        {{-- Total Requests --}}
        <div class="bg-(--surface-card) border border-(--border-primary) p-5 rounded-2xl shadow-xs flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                <i class="fa-solid fa-file-signature text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-(--text-secondary) font-bold mb-1">طلبات الاعتماد</p>
                <h3 class="text-2xl font-black text-(--text-primary)">{{ $totalRequests }}</h3>
            </div>
        </div>

        {{-- Total Evaluators --}}
        <div class="bg-(--surface-card) border border-(--border-primary) p-5 rounded-2xl shadow-xs flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <i class="fa-solid fa-users-gear text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-(--text-secondary) font-bold mb-1">الخبراء والمقيمين</p>
                <h3 class="text-2xl font-black text-(--text-primary)">{{ $totalEvaluators }}</h3>
            </div>
        </div>

        {{-- Total Certificates --}}
        <div class="bg-(--surface-card) border border-(--border-primary) p-5 rounded-2xl shadow-xs flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                <i class="fa-solid fa-award text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-(--text-secondary) font-bold mb-1">شهادات الجودة النشطة</p>
                <h3 class="text-2xl font-black text-(--text-primary)">{{ $totalCertificates }}</h3>
            </div>
        </div>
    </div>

    {{-- ═══════════════ REPORTS SELECTION SECTION ═══════════════ --}}
    <h3 class="text-lg font-bold text-(--text-primary) mb-5 flex items-center gap-2">
        <i class="fa-solid fa-gears text-brand-600 dark:text-brand-400"></i>
        اختر التقرير المطلوب لتوليده
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Card 1: University Accreditation Status --}}
        <div class="bg-(--surface-card) border border-(--border-primary) rounded-2xl shadow-xs p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 flex items-center justify-center border border-blue-200 dark:border-blue-500/20">
                        <i class="fa-solid fa-list-check text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-(--text-primary) text-[15px]">تقرير حالة طلبات الاعتماد للجامعات</h4>
                        <p class="text-xs text-(--text-secondary) mt-0.5">مراحل الطلبات الجارية وحالتها الحالية</p>
                    </div>
                </div>
                <p class="text-sm text-(--text-secondary) leading-relaxed">
                    تقرير مفصل ومصنف حسب الجامعات لجميع برامجها وتفاصيلها الأكاديمية والمراحل المنجزة في طلبات الاعتماد الأكاديمي مع فلاتر مخصصة للمراحل والحالات.
                </p>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="openModal('university_status')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer">
                    <i class="fa-solid fa-sliders text-xs"></i> تصفية وتوليد التقرير
                </button>
            </div>
        </div>

        {{-- Card 2: Evaluator Directory & Stats --}}
        <div class="bg-(--surface-card) border border-(--border-primary) rounded-2xl shadow-xs p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 flex items-center justify-center border border-emerald-200 dark:border-emerald-500/20">
                        <i class="fa-solid fa-users-viewfinder text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-(--text-primary) text-[15px]">تقرير بيانات وإحصائيات المقيمين</h4>
                        <p class="text-xs text-(--text-secondary) mt-0.5">دليل الخبراء المقيمين وتوزيعهم العلمي والجغرافي</p>
                    </div>
                </div>
                <p class="text-sm text-(--text-secondary) leading-relaxed">
                    تقرير دليل الخبراء والمقيمين المسجلين في النظام، يستعرض تخصصاتهم العلمية والأكاديمية، درجاتهم، وعضوياتهم الجارية في لجان التقييم المختلفة.
                </p>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="openModal('evaluator_stats')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer">
                    <i class="fa-solid fa-sliders text-xs"></i> تصفية وتوليد التقرير
                </button>
            </div>
        </div>

        {{-- Card 3: Issued Decisions and Certificates --}}
        <div class="bg-(--surface-card) border border-(--border-primary) rounded-2xl shadow-xs p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 flex items-center justify-center border border-purple-200 dark:border-purple-500/20">
                        <i class="fa-solid fa-certificate text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-(--text-primary) text-[15px]">تقرير القرارات والشهادات الصادرة</h4>
                        <p class="text-xs text-(--text-secondary) mt-0.5">متابعة كشوف القرارات النهائية وشهادات الاعتماد</p>
                    </div>
                </div>
                <p class="text-sm text-(--text-secondary) leading-relaxed">
                    متابعة سجل قرارات الجودة ومنح الاعتماد، مع تفاصيل الشهادات الممنوحة وفترات سريانها في النظام بالإضافة إلى فلاتر تحديد تاريخ ومستوى القرار.
                </p>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="openModal('issued_decisions')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer">
                    <i class="fa-solid fa-sliders text-xs"></i> تصفية وتوليد التقرير
                </button>
            </div>
        </div>

        {{-- Card 4: General Statistical Summary --}}
        <div class="bg-(--surface-card) border border-(--border-primary) rounded-2xl shadow-xs p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 flex items-center justify-center border border-amber-200 dark:border-amber-500/20">
                        <i class="fa-solid fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-(--text-primary) text-[15px]">التقرير الإحصائي الشامل لمجلس الاعتماد</h4>
                        <p class="text-xs text-(--text-secondary) mt-0.5">لوحة مؤشرات إحصائية لعموم النظام</p>
                    </div>
                </div>
                <p class="text-sm text-(--text-secondary) leading-relaxed">
                    تقرير مباشر لا يتطلب تصنيفات أو فلاتر؛ يلخص أهم إحصائيات التقييم، وتوزيع نسب طلبات الاعتماد على مستوى الجمهورية بأسلوب كلاسيكي منظم.
                </p>
            </div>
            <div class="mt-6 flex justify-end">
                <form action="{{ route('council_secretariat.reports.generate') }}" method="POST" @submit="triggerDownload()">
                    @csrf
                    <input type="hidden" name="report_type" value="general_summary">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer">
                        <i class="fa-solid fa-file-pdf text-xs"></i> تحميل التقرير الشامل مباشرة
                    </button>
                </form>
            </div>
        </div>

        {{-- Card 5: Criteria & Indicators Analysis --}}
        <div class="bg-(--surface-card) border border-(--border-primary) rounded-2xl shadow-xs p-6 flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all md:col-span-2">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 flex items-center justify-center border border-rose-200 dark:border-rose-500/20 shrink-0">
                        <i class="fa-solid fa-ranking-star text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-(--text-primary) text-[15px]">تقرير تحليل أداء المعايير والمؤشرات الأكاديمية</h4>
                        <p class="text-xs text-(--text-secondary) mt-0.5">تصنيف المعايير والمؤشرات الأعلى والأدنى درجةً وفق تقييمات المقيمين</p>
                        <p class="text-sm text-(--text-secondary) leading-relaxed mt-2">
                            يستعرض هذا التقرير ترتيب المعايير والمؤشرات الأكاديمية حسب متوسط درجات المقيمين، ويُبرز أعلى 10 مؤشرات أداءً وأدناها، مع نسبة الاستيفاء لكل معيار رئيسي لتسهيل اتخاذ القرارات التحسينية.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex justify-end mt-2 md:mt-0">
                    <form action="{{ route('council_secretariat.reports.generate') }}" method="POST" @submit="triggerDownload()">
                        @csrf
                        <input type="hidden" name="report_type" value="criteria_analysis">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-file-pdf text-xs"></i> تحميل تقرير المعايير مباشرة
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════ MODAL FOR FILTERS ═══════════════ --}}
    <template x-teleport="body">
        <div x-show="showModal" style="display: none;" class="relative z-[100]" role="dialog" aria-modal="true">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                    <div x-show="showModal" @click.away="showModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-(--surface-card) border border-(--border-primary) text-right shadow-2xl transition-all sm:my-8 w-full max-w-lg">
                        
                        <form action="{{ route('council_secretariat.reports.generate') }}" method="POST" @submit="triggerDownload()">
                            @csrf
                            <input type="hidden" name="report_type" :value="selectedType">

                            <div class="p-6 border-b border-(--border-primary)">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-(--text-primary)">
                                        <i class="fa-solid fa-filter text-brand-500 me-1"></i>
                                        تصفية ومحددات التقرير
                                    </h3>
                                    <button type="button" @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-(--text-secondary) hover:text-(--text-primary) bg-(--bg-main) hover:scale-110 transition-transform shrink-0 cursor-pointer">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                {{-- 1. Filters for: University Accreditation Status --}}
                                <div x-show="selectedType === 'university_status'" class="space-y-4">
                                    <div>
                                        <label for="university_id" class="block text-sm font-bold text-(--text-primary) mb-2">الجامعة المحددة</label>
                                        <select name="university_id" id="university_id" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                            <option value="">جميع الجامعات</option>
                                            @foreach($universities as $uni)
                                                <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="current_stage" class="block text-sm font-bold text-(--text-primary) mb-2">المرحلة الحالية</label>
                                            <select name="current_stage" id="current_stage" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                                <option value="all">جميع المراحل</option>
                                                <option value="stage_one">الطلب الأولي (1)</option>
                                                <option value="stage_two">البيانات الأساسية (2)</option>
                                                <option value="stage_three">تقرير الدراسة الذاتية (3)</option>
                                                <option value="stage_four">اختيار لجنة التقييم (4)</option>
                                                <option value="stage_five">تحديد جدول الزيارة (5)</option>
                                                <option value="stage_six">تقارير التقييم الأولية (6)</option>
                                                <option value="stage_seven">توصيات اللجنة والردود (7)</option>
                                                <option value="stage_eight">تقارير التقييم الختامية (8)</option>
                                                <option value="stage_nine">القرار النهائي والشهادة (9)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="request_status" class="block text-sm font-bold text-(--text-primary) mb-2">حالة الطلب</label>
                                            <select name="request_status" id="request_status" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                                <option value="all">جميع الحالات</option>
                                                <option value="Active">نشط</option>
                                                <option value="draft">مسودة</option>
                                                <option value="completed">مكتمل</option>
                                                <option value="canceled">ملغي</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Filters for: Evaluator Directory & Stats --}}
                                <div x-show="selectedType === 'evaluator_stats'" class="space-y-4">
                                    <div>
                                        <label for="specialty" class="block text-sm font-bold text-(--text-primary) mb-2">التخصص العلمي العام</label>
                                        <select name="specialty" id="specialty" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                            <option value="all">جميع التخصصات</option>
                                            @foreach($specialties as $spec)
                                                <option value="{{ $spec }}">{{ $spec }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="academic_rank" class="block text-sm font-bold text-(--text-primary) mb-2">الرتبة الأكاديمية</label>
                                        <select name="academic_rank" id="academic_rank" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                            <option value="all">جميع الرتب</option>
                                            <option value="professor">أستاذ (بروفيسور)</option>
                                            <option value="associate_professor">أستاذ مشارك</option>
                                            <option value="assistant_professor">أستاذ مساعد</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 3. Filters for: Issued Decisions and Certificates --}}
                                <div x-show="selectedType === 'issued_decisions'" class="space-y-4">
                                    <div>
                                        <label for="decision_type" class="block text-sm font-bold text-(--text-primary) mb-2">نوع قرار الاعتماد</label>
                                        <select name="decision_type" id="decision_type" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                                            <option value="all">جميع القرارات</option>
                                            <option value="approved_achieved">محقق</option>
                                            <option value="approved_with_mastery">محقق بإتقان</option>
                                            <option value="approved_with_excellence">محقق بتميز</option>
                                            <option value="rejected_partial">محقق جزئياً (غير معتمد)</option>
                                            <option value="rejected_not_achieved">غير محقق (غير معتمد)</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="date_from" class="block text-sm font-bold text-(--text-primary) mb-2">من تاريخ</label>
                                            <input type="date" name="date_from" id="date_from" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                        </div>
                                        <div>
                                            <label for="date_to" class="block text-sm font-bold text-(--text-primary) mb-2">إلى تاريخ</label>
                                            <input type="date" name="date_to" id="date_to" class="bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-lg block w-full p-3 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 items-start mt-5 p-3 rounded-lg bg-blue-50 border border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20">
                                    <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                    <p class="text-xs font-bold leading-relaxed text-blue-800 dark:text-blue-300">
                                        سيقوم النظام بإنتاج ملف التقرير بهيئة PDF منسقة للطباعة الفورية. قد تستغرق العملية عدة ثوانٍ.
                                    </p>
                                </div>
                            </div>

                            <div class="bg-(--bg-main) px-6 py-5 flex flex-col-reverse sm:flex-row sm:justify-end items-center gap-3 rounded-b-2xl">
                                <button type="button" @click="showModal = false" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-(--surface-card) px-6 py-3 text-sm font-bold text-(--text-primary) shadow-sm border border-(--border-primary) hover:bg-(--bg-main) hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    إلغاء
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-brand-600 dark:bg-brand-500 px-8 py-3 text-sm font-black text-white shadow-lg shadow-brand-500/20 hover:bg-brand-700 dark:hover:bg-brand-600 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    توليد وتنزيل التقرير
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════ LOADING OVERLAY ═══════════════ --}}
    <div x-show="loading" style="display: none;" class="fixed inset-0 bg-black/60 backdrop-blur-md z-[200] flex flex-col items-center justify-center text-white">
        <div class="relative flex items-center justify-center mb-4">
            <div class="w-16 h-16 border-4 border-brand-500 border-t-transparent rounded-full animate-spin"></div>
            <i class="fa-solid fa-file-pdf text-xl text-brand-400 absolute"></i>
        </div>
        <p class="text-base font-black">جاري توليد تقرير PDF المنسق...</p>
        <p class="text-xs text-gray-300 mt-2">يرجى عدم إغلاق هذه الصفحة أو تحديثها لحين انتهاء التنزيل.</p>
    </div>

</div>

@push('scripts')
<script>
    function reportsManager() {
        return {
            showModal: false,
            loading: false,
            selectedType: '',

            openModal(type) {
                this.selectedType = type;
                this.showModal = true;
            },

            triggerDownload() {
                this.showModal = false;
                this.loading = true;
                
                // Hide loader after a safe timeout since download triggers asynchronously in browser
                setTimeout(() => {
                    this.loading = false;
                }, 8000);
            }
        }
    }
</script>
@endpush
@endsection
