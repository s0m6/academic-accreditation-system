<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مقاييس تقييم البرنامج - {{ $program->program_name }}</title>
    
    @vite(['resources/css/app.css'])
    @include('print_templates.fonts')
    
    <style>
        @page {
            margin: 15mm;
            size: a4;
        }

        .page-break {
            page-break-before: always;
        }

        /* ── Signature Custom Styling (Borderless & Transparent matching Visit Report) ── */
        .signature-wrapper {
            position: relative !important;
            width: 100% !important;
            height: 90px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            background: transparent !important;
            padding: 8px !important;
            box-sizing: border-box !important;
        }
        .signature-wrapper svg {
            position: relative !important;
            display: block !important;
            max-height: 70px !important;
            max-width: 100% !important;
            width: auto !important;
            height: auto !important;
            margin: 0 auto !important;
        }
        .signature-wrapper svg * {
            position: static !important;
        }
    </style>
</head>
<body class="font-['Tajawal',Arial,sans-serif] bg-white text-slate-800 antialiased [print-color-adjust:exact]">

    @php
        // Construct a lookup map for substandards
        $subStandardsMap = [];
        foreach ($standards as $std) {
            foreach ($std->subStandards as $sub) {
                $subStandardsMap[$sub->id] = $sub->number;
            }
        }

        // Helper to translate numerical scores to levels (Official controller text)
        if (!function_exists('getOverallEvaluationLevel')) {
            function getOverallEvaluationLevel($average) {
                if ($average === null || $average === '—' || $average <= 0) return '—';
                $avg = floatval($average);
                if ($avg >= 4.5) return 'محقق بامتياز';
                if ($avg >= 3.5) return 'محقق بإتقان';
                if ($avg >= 2.5) return 'محقق';
                if ($avg >= 1.5) return 'محقق جزئياً';
                return 'غير محقق';
            }
        }
    @endphp

    <div class="container mx-auto px-4 py-6">
        
        <!-- ── COVER PAGE ── -->
        <div class="min-h-[260mm] flex flex-col justify-between p-2 box-border">
            
            <!-- Header section with Ministry and Logo -->
            <div class="flex justify-between items-start border-b-4 border-[#1a3c5e] pb-6 mb-8">
                <div class="text-right">
                    <p class="font-extrabold text-[15px] text-slate-800">الجمهورية اليمنية</p>
                    <p class="font-bold text-[13px] text-slate-600 mt-0.5">وزارة التعليم العالي والبحث العلمي</p>
                    <p class="font-bold text-[12px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>
                
                <div class="text-center">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" class="h-20 object-contain mx-auto" alt="Logo">
                    @else
                        <!-- Elegant Geometric Fallback representing accreditation -->
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#1a3c5e] to-[#2c3e50] flex items-center justify-center text-white font-black text-xl shadow-md mx-auto">
                            AA
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Title Banner -->
            <div class="text-center my-10 py-12 px-8 rounded-3xl bg-slate-50 border border-slate-200/60 shadow-sm">
                <span class="px-4 py-1.5 bg-[#1a3c5e]/10 text-[#1a3c5e] rounded-full text-xs font-black tracking-widest uppercase mb-4 inline-block">
                    النموذج رقم (6)
                </span>
                <h1 class="text-3xl font-black text-[#1a3c5e] tracking-tight">
                    تقرير مقاييس تقييم البرنامج (الروبريك)
                </h1>
                <p class="text-lg font-bold text-slate-600 mt-3">
                    {{ isset($isFinal) && $isFinal ? 'التقييم الختامي' : 'التقييم الأولي' }} للجنة المقيمين الخارجية
                </p>
                <div class="w-20 h-1.5 bg-[#1a3c5e] mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Double Card Section for Metadata and Committee Members -->
            <div class="grid grid-cols-2 gap-6 my-6">
                
                <!-- Left Panel: Program Details (Removed Degree Field as requested) -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3c5e]"></span>
                        <span>بيانات البرنامج الدراسي</span>
                    </h3>
                    <div class="space-y-3.5 text-[13px]">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">الجامعة:</span>
                            <span class="font-bold text-slate-800">{{ $university->name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">الكلية:</span>
                            <span class="font-bold text-slate-800">{{ $college->name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">القسم العلمي:</span>
                            <span class="font-bold text-slate-800">{{ $department->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">البرنامج الدراسي:</span>
                            <span class="font-bold text-slate-800">{{ $program->program_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Committee Details -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>لجنة المقيمين الخارجية</span>
                        </h3>
                        <div class="space-y-3.5 text-[13px]">
                            @php
                                $chair = collect($membersData)->firstWhere('is_chair', true);
                                $members = collect($membersData)->where('is_chair', false);
                            @endphp
                            @if($chair)
                                <div class="flex items-start gap-2 border-b border-slate-50 pb-2">
                                    <span class="text-slate-500 font-medium whitespace-nowrap">رئيس اللجنة:</span>
                                    <span class="font-extrabold text-[#1a3c5e]">{{ $chair['name'] }}</span>
                                </div>
                            @endif
                            @if($members->count() > 0)
                                <div class="mt-1">
                                    <span class="text-slate-500 font-medium block mb-1">أعضاء اللجنة الموقرين:</span>
                                    <ul class="list-disc list-inside space-y-1 text-slate-700 pr-2">
                                        @foreach($members as $m)
                                            <li class="font-semibold text-xs">{{ $m['name'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">لا توجد أسماء أعضاء مدخلة باللجنة حالياً</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 flex justify-between text-xs text-slate-500">
                        <span>رقم طلب الاعتماد:</span>
                        <span class="font-mono font-bold">req_{{ $accreditationRequest->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="border-t border-slate-200 pt-6 mt-12 flex justify-between items-center text-xs text-slate-400">
                <span>تاريخ إصدار التقرير: {{ \Carbon\Carbon::now()->format('Y/m/d') }}</span>
                <span class="font-bold">العام الجامعي: {{ \Carbon\Carbon::now()->format('Y') }}م</span>
            </div>
        </div>
        
        <div class="page-break"></div>

        <!-- ── Iterating through all 7 standards ── -->
        @foreach($standards as $standard)
            <!-- Standard Header Block -->
            <div class="bg-slate-50 border-r-4 border-[#1a3c5e] p-4 mb-4">
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center justify-between">
                    <span>المعيار {{ $standard->number_arabic ?? $standard->number }}: {{ $standard->name }}</span>
                </h2>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    {{ $standard->description }}
                </p>
            </div>

            <!-- Indicators Table (6 Evaluation Columns + Seq + Name) -->
            <div class="w-full mb-6">
                <table class="w-full border-collapse border border-[#2c3e50]">
                    <tbody>
                        <!-- Singular Header Row (To prevent repetition on browser print page-breaks) -->
                        <tr class="bg-[#1a3c5e] text-white font-extrabold text-center">
                            <td class="w-[10%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">ت</td>
                            <td class="w-[54%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">المعيار الفرعي / المؤشر</td>
                            <td class="w-[5%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">5</td>
                            <td class="w-[5%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">4</td>
                            <td class="w-[5%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">3</td>
                            <td class="w-[5%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">2</td>
                            <td class="w-[5%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">1</td>
                            <td class="w-[11%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">غير مطابق</td>
                        </tr>

                        @php
                            $sum = 0;
                            $ratedCount = 0;
                            $ncCount = 0;
                            $totalCount = 0;
                        @endphp
                        @foreach($standard->subStandards as $sub)
                            <!-- Substandard row -->
                            <tr class="bg-slate-100">
                                <td class="w-[10%] text-center font-bold text-[#1a3c5e] border border-[#2c3e50] p-2 text-[12.5px]">{{ $sub->number }}</td>
                                <td colspan="7" class="font-extrabold text-[#1a3c5e] border border-[#2c3e50] p-2 text-sm text-right">{{ $sub->name }}</td>
                            </tr>
                            
                            @foreach($sub->indicators as $indicator)
                                @php
                                    $totalCount++;
                                    $score = isset($savedScores[$indicator->id]) ? $savedScores[$indicator->id] : null;
                                    
                                    $is5 = ($score !== null && $score == 5);
                                    $is4 = ($score !== null && $score == 4);
                                    $is3 = ($score !== null && $score == 3);
                                    $is2 = ($score !== null && $score == 2);
                                    $is1 = ($score !== null && $score == 1);
                                    $isNc = ($score !== null && $score == 0);

                                    if ($score !== null) {
                                        if ($score == 0) {
                                            $ncCount++;
                                        } else {
                                            $sum += intval($score);
                                            $ratedCount++;
                                        }
                                    }
                                @endphp
                                <tr class="even:bg-slate-50 odd:bg-white">
                                    <td class="w-[10%] text-center font-medium text-slate-600 border border-[#2c3e50] p-2 text-[12.5px] whitespace-nowrap">{{ $indicator->number }}</td>
                                    <td class="w-[54%] leading-relaxed text-slate-800 border border-[#2c3e50] p-2 text-[12.5px] text-right whitespace-normal break-words">{{ $indicator->name }}</td>
                                    <td class="w-[5%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($is5) &#10003; @endif
                                    </td>
                                    <td class="w-[5%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($is4) &#10003; @endif
                                    </td>
                                    <td class="w-[5%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($is3) &#10003; @endif
                                    </td>
                                    <td class="w-[5%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($is2) &#10003; @endif
                                    </td>
                                    <td class="w-[5%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($is1) &#10003; @endif
                                    </td>
                                    <td class="w-[11%] text-center font-extrabold text-lg text-black border border-[#2c3e50] p-2">
                                        @if($isNc) &#10003; @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        @php
                            $average = $ratedCount > 0 ? number_format($sum / $ratedCount, 2) : '—';
                            $overallLevel = getOverallEvaluationLevel($average);
                        @endphp

                        <!-- ── Total Standard Assessment Rows ── -->
                        <tr>
                            <td colspan="8" class="bg-slate-50 p-0 border border-[#2c3e50]">
                                <div class="border-t border-[#2c3e50]">
                                    <div class="bg-[#1a3c5e] text-white font-extrabold text-center py-2 text-xs">
                                        التقييم الكلي للمعيار
                                    </div>
                                    <table class="w-full border-collapse border-none m-0">
                                        <tr class="border-b border-slate-200">
                                            <td class="w-[50%] font-bold bg-slate-50 border-none border-l border-slate-200 px-3 py-2 text-[12.5px] text-right">مجموع تقييم المؤشرات</td>
                                            <td class="w-[50%] text-center font-extrabold border-none px-3 py-2 text-[#1a3c5e] text-[12.5px]">{{ $sum }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-200">
                                            <td class="font-bold bg-slate-50 border-none border-l border-slate-200 px-3 py-2 text-[12.5px] text-right">عدد المؤشرات المقيمة</td>
                                            <td class="text-center font-extrabold border-none px-3 py-2 text-[#1a3c5e] text-[12.5px]">{{ $ratedCount }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-200">
                                            <td class="font-bold bg-slate-50 border-none border-l border-slate-200 px-3 py-2 text-[12.5px] text-right">متوسط تقييم المعيار</td>
                                            <td class="text-center font-extrabold border-none px-3 py-2 text-[#1a3c5e] text-[12.5px]">{{ $average }} / 5.00</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-slate-50 border-none border-l border-slate-200 px-3 py-2 text-[12.5px] text-right">درجة التقويم الإجمالي للمعيار</td>
                                            <td class="text-center font-extrabold border-none px-3 py-2 text-emerald-700 text-sm">{{ $overallLevel }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ── Standard Final Comments ── -->
            <div class="space-y-4 mt-6">
                <h3 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">التعليقات الختامية للمعيار {{ $standard->number_arabic ?? $standard->number }}</h3>
                
                @php
                    $strengths = $savedFormData['standards'][$standard->id]['strengths'] ?? [];
                    $improvements = $savedFormData['standards'][$standard->id]['improvements'] ?? [];
                    $priorities = $savedFormData['standards'][$standard->id]['priorities'] ?? [];
                @endphp

                <!-- 1. Strengths -->
                <div class="border border-[#2c3e50] rounded-lg mb-4 overflow-hidden bg-white shadow-sm">
                    <div class="px-3 py-2 font-extrabold text-xs border-b border-[#2c3e50] bg-teal-50 text-teal-700 text-right">جوانب القوة</div>
                    <div class="p-3 bg-white text-right">
                        @if(count($strengths) > 0)
                            <ul class="list-decimal list-inside space-y-1 text-right">
                                @foreach($strengths as $pt)
                                    @if(isset($pt['text']) && trim($pt['text']) !== '')
                                        <li class="text-xs text-slate-700 leading-relaxed mb-1 text-right">
                                            {{ $pt['text'] }}
                                            @if(isset($pt['subId']) && isset($subStandardsMap[$pt['subId']]))
                                                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-1.5 py-0.5 rounded text-[10px] font-bold mr-1.5 inline-block">المعيار الفرعي {{ $subStandardsMap[$pt['subId']] }}</span>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-slate-400 italic text-xs text-center py-3">لا توجد نقاط قوة مرصودة</div>
                        @endif
                    </div>
                </div>

                <!-- 2. Opportunities for Improvement -->
                <div class="border border-[#2c3e50] rounded-lg mb-4 overflow-hidden bg-white shadow-sm">
                    <div class="px-3 py-2 font-extrabold text-xs border-b border-[#2c3e50] bg-rose-50 text-rose-700 text-right">الجوانب التي تحتاج إلى تحسين (فرص التحسين)</div>
                    <div class="p-3 bg-white text-right">
                        @if(count($improvements) > 0)
                            <ul class="list-decimal list-inside space-y-1 text-right">
                                @foreach($improvements as $pt)
                                    @if(isset($pt['text']) && trim($pt['text']) !== '')
                                        <li class="text-xs text-slate-700 leading-relaxed mb-1 text-right">
                                            {{ $pt['text'] }}
                                            @if(isset($pt['subId']) && isset($subStandardsMap[$pt['subId']]))
                                                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-1.5 py-0.5 rounded text-[10px] font-bold mr-1.5 inline-block">المعيار الفرعي {{ $subStandardsMap[$pt['subId']] }}</span>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-slate-400 italic text-xs text-center py-3">لا توجد جوانب تحتاج تحسين مرصودة</div>
                        @endif
                    </div>
                </div>

                <!-- 3. Priorities for Improvement -->
                <div class="border border-[#2c3e50] rounded-lg mb-4 overflow-hidden bg-white shadow-sm">
                    <div class="px-3 py-2 font-extrabold text-xs border-b border-[#2c3e50] bg-amber-50 text-amber-700 text-right">أولويات التحسين</div>
                    <div class="p-3 bg-white text-right">
                        @if(count($priorities) > 0)
                            <ul class="list-decimal list-inside space-y-1 text-right">
                                @foreach($priorities as $pt)
                                    @if(isset($pt['text']) && trim($pt['text']) !== '')
                                        <li class="text-xs text-slate-700 leading-relaxed mb-1 text-right">{{ $pt['text'] }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <div class="text-slate-400 italic text-xs text-center py-3">لا توجد أولويات تحسين مرصودة</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="page-break"></div>
        @endforeach

        <!-- ── Page: Signatures Page ── -->
        <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">اعتماد وتواقيع أعضاء لجنة المقيمين الخارجية</h2>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            بناءً على التقييم الميداني والوثائقي لبرنامج <span class="font-extrabold text-slate-800">{{ $program->program_name }}</span>، يقر رئيس وأعضاء لجنة المقيمين الخارجية بدقة التقييمات والمقاييس الواردة في هذا التقرير الأولي.
        </p>

        <div class="w-full mb-6">
            <table class="w-full border-collapse border border-[#2c3e50]">
                <thead>
                    <tr class="bg-[#1a3c5e] text-white font-extrabold text-center">
                        <th class="w-[40%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">اسم المقيم والصفة</th>
                        <th class="w-[60%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">التوقيع الإلكتروني والاعتماد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membersData as $member)
                        <tr class="even:bg-slate-50 odd:bg-white">
                            <td class="font-bold text-sm text-slate-700 border border-[#2c3e50] p-3 text-right">
                                {{ $member['name'] }}
                                @if($member['is_chair'])
                                    <span class="text-xs text-[#1a3c5e] block font-medium mt-1">(رئيس اللجنة)</span>
                                @else
                                    <span class="text-xs text-slate-500 block font-medium mt-1">(عضو مقيم)</span>
                                @endif
                            </td>
                            <td class="border border-[#2c3e50] p-3 text-center">
                                <div class="signature-wrapper">
                                    @if($member['signature_path'] && \Illuminate\Support\Facades\Storage::exists($member['signature_path']))
                                        @php
                                            $svg = \Illuminate\Support\Facades\Storage::get($member['signature_path']);
                                        @endphp
                                        {!! $svg !!}
                                    @else
                                        <span class="text-slate-400 font-medium text-xs opacity-75">(لم يتم التوقيع بعد)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
