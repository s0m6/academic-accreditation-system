<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التقرير النهائي للجنة المقيمين - {{ $program->program_name }}</title>
    
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
                    النموذج رقم (7)
                </span>
                <h1 class="text-3xl font-black text-[#1a3c5e] tracking-tight">
                    التقرير النهائي للجنة المقيمين والتقدير الكلي
                </h1>
                <p class="text-lg font-bold text-slate-600 mt-3">
                    التقييم النهائي والأولى للجنة المقيمين الخارجية
                </p>
                <div class="w-20 h-1.5 bg-[#1a3c5e] mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Basic Info Card Section -->
            <div class="my-6">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-2xl mx-auto">
                    <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3c5e]"></span>
                        <span>بيانات التقييم الأكاديمي للبرنامج</span>
                    </h3>
                    <div class="space-y-3.5 text-[13px]">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">رقم الطلب الأكاديمي:</span>
                            <span class="font-bold text-slate-800 tracking-wider">#{{ $accreditationRequest->id }}</span>
                        </div>
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
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">اسم البرنامج الدراسي:</span>
                            <span class="font-bold text-[#1a3c5e]">{{ $program->program_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Date of PDF Creation -->
            <div class="text-center text-[10px] text-slate-400 mt-8 border-t border-slate-100 pt-4 flex justify-between">
                <span>تاريخ الطباعة: {{ now()->format('Y/m/d') }}</span>
                <span>مجلس الاعتماد الأكاديمي وضمان الجودة - نظام الاعتماد الالكتروني</span>
            </div>

        </div>

        <div class="page-break"></div>

        <!-- ── PAGE 2: COMMITTEE MEMBERS AND SIGNATURES ── -->
        <div class="flex justify-between items-start border-b-2 border-slate-150 pb-4 mb-6">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <div class="text-left text-[10px] text-slate-400">التقرير النهائي للجنة المقيمين</div>
        </div>

        <div class="space-y-4">
            <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">أعضاء لجنة المقيمين الخارجية والاعتماد</h2>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                بناءً على التقييم الميداني والوثائقي لبرنامج <span class="font-extrabold text-slate-800">{{ $program->program_name }}</span>، يقر رئيس وأعضاء لجنة المقيمين الخارجية بدقة التقييمات والمقاييس الواردة في هذا التقرير النهائي.
            </p>

            <div class="w-full">
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

        <div class="page-break"></div>

        <!-- ── PAGE 3: DETAILED CRITERION ASSESSMENT TABLE ── -->
        <div class="flex justify-between items-start border-b-2 border-slate-150 pb-4 mb-6">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <div class="text-left text-[10px] text-slate-400">جدول التقييم التفصيلي للمعايير</div>
        </div>

        <div class="space-y-4">
            <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">جدول التقييم التفصيلي للمعايير الفرعية ونقاط القوة والتحسين</h2>
            
            <div class="w-full overflow-x-auto">
                <table class="w-full border-collapse border border-[#2c3e50] text-right">
                    <thead>
                        <tr class="bg-[#1a3c5e] text-white font-extrabold text-center">
                            <th class="w-[8%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">ت</th>
                            <th class="w-[30%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">المعيار الفرعي</th>
                            <th class="w-[12%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">الدرجة</th>
                            <th class="w-[25%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">نقاط القوة</th>
                            <th class="w-[25%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">فرص التحسين</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailedStandards as $stdIndex => $standard)
                            <!-- Main Standard Row -->
                            <tr class="bg-slate-100">
                                <td class="font-extrabold text-sm text-[#1a3c5e] border border-[#2c3e50] p-2.5 text-center">{{ $standard['number'] }}</td>
                                <td colspan="4" class="font-extrabold text-sm text-[#1a3c5e] border border-[#2c3e50] p-2.5 text-right">{{ $standard['name'] }}</td>
                            </tr>

                            <!-- Sub-Standard Rows -->
                            @foreach($standard['subs'] as $subIndex => $sub)
                                <tr class="even:bg-slate-50 odd:bg-white">
                                    <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-2 text-center">{{ $sub['number'] }}.{{ $stdIndex + 1 }}</td>
                                    <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-2 text-right leading-normal">{{ $sub['name'] }}</td>
                                    <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-2 text-center">
                                        @if($sub['average'] !== null)
                                            {{ number_format($sub['average'], 2) }}
                                        @else
                                            <span class="text-slate-400 font-medium">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-700 border border-[#2c3e50] p-2 text-right leading-relaxed">
                                        @if(!empty($sub['strengths']))
                                            <ul class="list-decimal list-inside space-y-1">
                                                @foreach($sub['strengths'] as $point)
                                                    <li>{{ $point }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">لا توجد ملاحظات</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-700 border border-[#2c3e50] p-2 text-right leading-relaxed">
                                        @if(!empty($sub['improvements']))
                                            <ul class="list-decimal list-inside space-y-1">
                                                @foreach($sub['improvements'] as $point)
                                                    <li>{{ $point }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">لا توجد ملاحظات</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="page-break"></div>

        <!-- ── PAGE 4: OVERALL PROGRAM RATING AND METRICS ── -->
        <div class="flex justify-between items-start border-b-2 border-slate-150 pb-4 mb-6">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <div class="text-left text-[10px] text-slate-400">التقدير الكلي لبرنامج الاعتماد</div>
        </div>

        <div class="space-y-6">
            <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">التقدير الإجمالي والتقدير الكلي لبرنامج الاعتماد الأكاديمي</h2>
            
            <div class="w-full overflow-x-auto">
                <table class="w-full border-collapse border border-[#2c3e50] text-center">
                    <thead>
                        <tr class="bg-[#1a3c5e] text-white font-extrabold">
                            <th class="w-[8%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">الرقم</th>
                            <th class="w-[44%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">المعيار الرئيسي</th>
                            <th class="w-[16%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">مجموع درجات المؤشرات</th>
                            <th class="w-[16%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">عدد المؤشرات المقيمة</th>
                            <th class="w-[16%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">المتوسط الحسابي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($standardsScores['standards'] as $index => $row)
                            <tr class="even:bg-slate-50 odd:bg-white @if($row['has_null_indicators']) bg-red-50/50 @endif">
                                <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-3">{{ $index + 1 }}</td>
                                <td class="font-bold text-xs text-slate-855 border border-[#2c3e50] p-3 text-right leading-normal">
                                    {{ $row['name'] }}
                                    @if($row['has_null_indicators'])
                                        <span class="block text-[10px] text-rose-600 font-bold mt-1">⚠ يوجد مؤشرات غير مقيّمة</span>
                                    @endif
                                </td>
                                <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-3">
                                    {{ $row['count'] > 0 ? $row['sum'] : '—' }}
                                </td>
                                <td class="font-bold text-xs text-slate-700 border border-[#2c3e50] p-3">
                                    {{ $row['count'] > 0 ? $row['count'] : '—' }}
                                </td>
                                <td class="font-bold text-xs text-[#1a3c5e] border border-[#2c3e50] p-3">
                                    {{ $row['average'] !== null ? number_format($row['average'], 2) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-[#1a3c5e] text-white font-extrabold text-center">
                            <td colspan="2" class="text-white text-right font-extrabold border border-[#2c3e50] p-3 text-xs">المجموع الكلي للبرنامج</td>
                            <td class="text-white text-center font-extrabold border border-[#2c3e50] p-3 text-xs font-mono">
                                {{ $standardsScores['total']['count'] > 0 ? $standardsScores['total']['sum'] : '—' }}
                            </td>
                            <td class="text-white text-center font-extrabold border border-[#2c3e50] p-3 text-xs font-mono">
                                {{ $standardsScores['total']['count'] > 0 ? $standardsScores['total']['count'] : '—' }}
                            </td>
                            <td class="text-white text-center font-extrabold border border-[#2c3e50] p-3 text-xs font-mono">
                                {{ $standardsScores['total']['average'] !== null ? number_format($standardsScores['total']['average'], 2) : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grade and Achievement level highlighted panel -->
            <div class="max-w-[500px] mx-auto mt-8 border border-[#2c3e50] rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse border-none">
                    <tbody>
                        <tr class="border-b border-[#2c3e50]">
                            <th class="w-[50%] bg-[#1a3c5e] text-white font-extrabold text-xs p-3 text-right">الدرجة النهائية للمعيار الكلي</th>
                            <td class="w-[50%] text-center font-black text-xl text-[#1a3c5e] bg-slate-50 p-3 font-mono">
                                {{ $standardsScores['final_grade'] ?? '—' }} / 5
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-[#1a3c5e] text-white font-extrabold text-xs p-3 text-right">مستوى التحقق الإجمالي للبرنامج</th>
                            <td class="text-center font-black text-sm text-emerald-700 bg-slate-50 p-3">
                                {{ $standardsScores['achievement_level'] ?? '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>

    </div>

</body>
</html>
