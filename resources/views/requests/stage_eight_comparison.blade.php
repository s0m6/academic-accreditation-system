<!doctype html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مقارنة التقييم (الأولي vs الختامي) — {{ $accreditationRequest->program->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .diff-added { background-color: rgba(16, 185, 129, 0.05); border-right: 4px solid #10b981; }
        .diff-removed { background-color: rgba(239, 68, 68, 0.05); border-right: 4px solid #ef4444; }
        .rating-badge { 
            min-width: 38px; 
            height: 38px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 10px; 
            font-weight: 800; 
            font-size: 0.95rem;
            background-color: #f8fafc;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .rating-nc {
            padding: 0 12px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.75rem;
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 italic-arabic min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-12">
        {{-- Header --}}
        <div class="mb-10 text-center">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 text-xs font-bold mb-4">
                <i class="fa-solid fa-code-compare"></i>
                أداة مقارنة التقييمات الاحترافية
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mb-2">مقارنة التغيرات بين التقييم الأولي والختامي</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl mx-auto italic">
                يستعرض هذا التقرير العناصر التي طرأ عليها تغيير في الدرجات أو المحتوى النصي.
            </p>
        </div>

        <div class="space-y-12">
            {{-- Section 1: Indicator Scores Comparison --}}
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <h2 class="text-lg font-bold">الاختلافات في درجات المؤشرات</h2>
                    </div>
                    <span class="text-xs text-slate-400 italic">يتم عرض المؤشرات المعدلة فقط</span>
                </div>

                @php $hasScoreDiffs = false; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                    <table class="w-full text-start">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                                <th class="px-6 py-4 text-xs font-black uppercase text-slate-500">المؤشر</th>
                                <th class="px-6 py-4 text-center text-xs font-black uppercase text-slate-500">المرحلة السادسة (الأولي)</th>
                                <th class="px-6 py-4 text-center text-xs font-black uppercase text-slate-500">المرحلة الثامنة (الختامي)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @foreach($standards as $standard)
                                @foreach($standard->subStandards as $sub)
                                    @foreach($sub->indicators as $indicator)
                                        @php
                                            $iScore = $initialScores[$indicator->id] ?? null;
                                            $fScore = $finalScores[$indicator->id] ?? null;
                                            $isDifferent = ($iScore !== $fScore);
                                        @endphp
                                        @if($isDifferent)
                                            @php $hasScoreDiffs = true; @endphp
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                                <td class="px-6 py-5">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-black text-blue-600 dark:text-blue-400 mb-1.5 block">مؤشر {{ $indicator->number }} — {{ $sub->name }}</span>
                                                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-relaxed">{{ $indicator->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5">
                                                    <div class="flex justify-center">
                                                        @if($iScore === null)
                                                            <span class="text-slate-400">—</span>
                                                        @elseif($iScore == 0)
                                                            <div class="rating-nc">غير مطابق</div>
                                                        @else
                                                            <div class="rating-badge">{{ $iScore }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5">
                                                    <div class="flex justify-center">
                                                        @if($fScore === null)
                                                            <span class="text-slate-400">—</span>
                                                        @elseif($fScore == 0)
                                                            <div class="rating-nc">غير مطابق</div>
                                                        @else
                                                            <div class="rating-badge">{{ $fScore }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endforeach
                            @if(!$hasScoreDiffs)
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400">
                                        <i class="fa-solid fa-check-double text-2xl mb-3 block"></i>
                                        <span class="text-sm">لا توجد اختلافات في درجات المؤشرات.</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Section 2: Narrative Comparison (Strengths, Improvements, Priorities) --}}
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <h2 class="text-lg font-bold">الاختلافات في النصوص والتعليقات</h2>
                    </div>
                    <span class="text-xs text-slate-400 italic">يتم عرض التعديلات فقط</span>
                </div>

                @php
                    if (!function_exists('getPointsDiff')) {
                        function getPointsDiff($initial, $final) {
                            $diff = [];
                            $initial = is_array($initial) ? $initial : [];
                            $final = is_array($final) ? $final : [];

                            $finalMapped = [];
                            foreach($final as $idx => $p) {
                                $key = trim($p['text'] ?? '') . '|' . ($p['subId'] ?? '');
                                $finalMapped[$key] = $idx;
                            }

                            $initialMapped = [];
                            foreach($initial as $idx => $p) {
                                $key = trim($p['text'] ?? '') . '|' . ($p['subId'] ?? '');
                                $initialMapped[$key] = $idx;
                            }

                            foreach($initial as $p) {
                                $key = trim($p['text'] ?? '') . '|' . ($p['subId'] ?? '');
                                if (!isset($finalMapped[$key])) {
                                    $diff[] = ['type' => 'removed', 'point' => $p];
                                }
                            }

                            foreach($final as $p) {
                                $key = trim($p['text'] ?? '') . '|' . ($p['subId'] ?? '');
                                if (!isset($initialMapped[$key])) {
                                    $diff[] = ['type' => 'added', 'point' => $p];
                                }
                            }
                            
                            return $diff;
                        }
                    }
                    
                    $hasAnyNarrativeDiff = false;
                @endphp

                <div class="space-y-8">
                    @foreach($standards as $standard)
                        @php
                            $stdInitial = $initialData['standards'][$standard->id] ?? null;
                            $stdFinal = $finalData['standards'][$standard->id] ?? null;
                            $types = [
                                'strengths' => ['label' => 'جوانب القوة', 'color' => 'indigo'],
                                'improvements' => ['label' => 'فرص التحسين', 'color' => 'amber'],
                                'priorities' => ['label' => 'أولويات التحسين', 'color' => 'rose']
                            ];
                            
                            $standardDiffs = [];
                            foreach($types as $key => $info) {
                                $d = getPointsDiff($stdInitial[$key] ?? [], $stdFinal[$key] ?? []);
                                if(!empty($d)) {
                                    $standardDiffs[$key] = $d;
                                }
                            }
                        @endphp

                        @if(!empty($standardDiffs))
                            @php $hasAnyNarrativeDiff = true; @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-md">{{ $standard->number }}</div>
                                        <h3 class="font-black text-base text-slate-800 dark:text-slate-200">{{ $standard->name }}</h3>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-[10px] font-black uppercase">معيار رئيسي</span>
                                </div>

                                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                    @foreach($standardDiffs as $key => $diffs)
                                        <div class="p-6">
                                            <h4 class="text-xs font-black text-slate-400 mb-5 flex items-center gap-2 uppercase tracking-widest">
                                                <i class="fa-solid fa-layer-group text-{{ $types[$key]['color'] }}-500"></i>
                                                {{ $types[$key]['label'] }}
                                            </h4>
                                            
                                            <div class="space-y-4">
                                                @foreach($diffs as $item)
                                                    @php 
                                                        $p = $item['point'];
                                                        $type = $item['type'];
                                                        $subName = $subNames[$p['subId'] ?? ''] ?? 'غير محدد';
                                                    @endphp
                                                    <div class="relative p-5 rounded-2xl {{ $type === 'added' ? 'diff-added' : 'diff-removed' }} group transition-all">
                                                        <div class="flex items-start gap-4">
                                                            <div class="shrink-0 mt-1">
                                                                @if($type === 'added')
                                                                    <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs shadow-sm">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </div>
                                                                @else
                                                                    <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs shadow-sm">
                                                                        <i class="fa-solid fa-minus"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <span class="text-xs font-black {{ $type === 'added' ? 'text-green-600' : 'text-red-600' }} uppercase">
                                                                        {{ $type === 'added' ? 'إضافة جديدة' : 'نقطة محذوفة' }}
                                                                    </span>
                                                                    <span class="text-slate-300">•</span>
                                                                    <span class="text-xs font-bold text-slate-500">المعيار الفرعي: {{ $subName }}</span>
                                                                </div>
                                                                <p class="text-sm leading-relaxed {{ $type === 'added' ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 line-through decoration-red-300/50' }}">
                                                                    {{ $p['text'] ?? '—' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasAnyNarrativeDiff)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 p-10 text-center text-slate-400">
                            <i class="fa-solid fa-file-circle-check text-3xl mb-4 block"></i>
                            <span class="text-sm italic">لا توجد تعديلات نصية في هذا التقرير.</span>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Footer --}}
        <div class="mt-16 pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-widest">
            <span>نظام الاعتماد الأكاديمي — تقرير المقارنة الذكي</span>
            <span>تاريخ العرض: {{ now()->format('Y/m/d') }}</span>
        </div>
    </div>
</body>
</html>
