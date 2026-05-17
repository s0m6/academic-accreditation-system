<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج 9 - رد المؤسسة على توصيات لجنة المقيمين - {{ $program->program_name }}</title>

    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        @page {
            margin: 15mm;
            size: a4;
        }

        .page-break {
            page-break-before: always;
        }

        /* Force Chromium PDF to respect column widths strictly */
        .response-table {
            table-layout: fixed;
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .response-table td,
        .response-table th {
            word-wrap: break-word;
            overflow-wrap: break-word;
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
                    <p class="font-extrabold text-[15px] text-slate-800">الجمهورية </p>
                    <p class="font-bold text-[13px] text-slate-600 mt-0.5">وزارة التعليم العالي والبحث العلمي</p>
                    <p class="font-bold text-[12px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>

                <div class="text-center">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" class="h-20 object-contain mx-auto" alt="Logo">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#1a3c5e] to-[#2c3e50] flex items-center justify-center text-white font-black text-xl shadow-md mx-auto">
                            AA
                        </div>
                    @endif
                </div>
            </div>
            <!-- Main Title Banner -->
            <div class="text-center my-10 py-12 px-8 rounded-3xl bg-slate-50 border border-slate-200/60 shadow-sm">
                <span class="px-4 py-1.5 bg-[#1a3c5e]/10 text-[#1a3c5e] rounded-full text-xs font-black tracking-widest uppercase mb-4 inline-block">
                    النموذج رقم (9)
                </span>
                <h1 class="text-3xl font-black text-[#1a3c5e] tracking-tight">
                    رد المؤسسة على خطاب توصيات اللجنة
                </h1>
                <p class="text-lg font-bold text-slate-600 mt-3">
                    الخطة التصحيحية والردود التفصيلية للبرنامج الدراسي
                </p>
                <div class="w-20 h-1.5 bg-[#1a3c5e] mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Basic Info Card Section -->
            <div class="my-6">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-2xl mx-auto">
                    <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3c5e]"></span>
                        <span>بيانات البرنامج والمؤسسة التعليمية</span>
                    </h3>
                    <div class="space-y-3.5 text-[13px]">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">رقم الطلب الأكاديمي:</span>
                            <span class="font-bold text-slate-800 tracking-wider">#{{ $accreditationRequest->id }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">المؤسسة التعليمية:</span>
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
                <span>تاريخ التصدير: {{ now()->format('Y/m/d') }}</span>
                <span>مجلس الاعتماد الأكاديمي وضمان الجودة - نظام الاعتماد الالكتروني</span>
            </div>

        </div>

        <div class="page-break"></div>

        <!-- ── PAGE 2: DETAILED RESPONSE TABLE ── -->
        <div class="flex justify-between items-start border-b-2 border-slate-150 pb-4 mb-6">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <div class="text-left text-[10px] text-slate-400">رد المؤسسة على توصيات اللجنة - نموذج 9</div>
        </div>

        <div class="space-y-4">
            <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">الردود التفصيلية على المعايير الفرعية وتوصيات لجنة المقيمين</h2>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                فيما يلي تفصيل متوسط درجات التقييم وفرص التحسين المعتمدة، مع توثيق الرد الرسمي الصادر من البرنامج الدراسي للموافقة أو إيضاح أسباب التحفظ وعدم الموافقة:
            </p>

            <div class="w-full">
                <table class="response-table w-full border-collapse border border-[#2c3e50]">
                    <thead>
                        <tr class="bg-[#1a3c5e] text-white font-extrabold">
                            <th class="w-[12%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">ت</th>
                            <th class="w-[32%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">المعيار الفرعي / المؤشر</th>
                            <th class="w-[10%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">الدرجة</th>
                            <th class="w-[46%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">توصيات المقيمين</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailedStandards as $stdIndex => $standard)
                            <!-- Main Standard Row -->
                            <tr class="bg-slate-100 font-extrabold">
                                <td class="font-bold text-xs text-slate-800 border border-[#2c3e50] p-2.5 text-center bg-slate-200">
                                    {{ $standard['number'] }}
                                </td>
                                <td colspan="3" class="font-black text-xs text-[#1a3c5e] border border-[#2c3e50] p-2.5 text-right">
                                    {{ $standard['name'] }}
                                </td>
                            </tr>

                            <!-- Sub-Standard Rows -->
                            @foreach($standard['subs'] as $subIndex => $sub)
                                <!-- Row A: Substandard & Recommendations -->
                                <tr class="even:bg-slate-50/60 odd:bg-white">
                                    <td class="font-bold text-xs text-slate-600 border border-[#2c3e50] p-2.5 text-center font-mono">
                                        {{ $sub['number'] }}.{{ $stdIndex + 1 }}
                                    </td>
                                    <td class="font-bold text-xs text-slate-800 border border-[#2c3e50] p-2.5 text-right leading-normal">
                                        {{ $sub['name'] }}
                                    </td>
                                    <td class="font-bold text-xs text-center border border-[#2c3e50] p-2.5 font-mono text-slate-700">
                                        @if($sub['average'] !== null)
                                            {{ number_format($sub['average'], 2) }}
                                        @else
                                            <span class="text-slate-400 font-normal">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs border border-[#2c3e50] p-3 text-right">
                                        @if(!empty($sub['improvements']))
                                            <ul class="list-disc list-inside space-y-1.5 text-slate-700">
                                                @foreach($sub['improvements'] as $point)
                                                    <li class="leading-relaxed">{{ $point }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">لا توجد فرص تحسين مدونة</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Row B: Institution Response -->
                                <tr class="bg-slate-50">
                                    <td colspan="4" class="border border-[#2c3e50] p-2.5 text-right bg-slate-50">
                                        <span class="font-extrabold text-[11px] text-[#1a3c5e]">رد المؤسسة بالموافقة أو عدم الموافقة مع الأسباب: </span>
                                        @if($sub['decision'] === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 text-xs font-black">
                                                موافقة
                                            </span>
                                        @elseif($sub['decision'] === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200 text-xs font-black">
                                                عدم موافقة
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-xs">لم يتم تحديد الرد بعد</span>
                                        @endif

                                        @if($sub['decision'] === 'rejected' && !empty($sub['rejection_points']))
                                            <div class="mt-2 text-[11px] text-slate-700 bg-red-50 p-2 rounded border border-red-200">
                                                <span class="font-extrabold text-red-800 block mb-1">أسباب عدم الموافقة:</span>
                                                <ul class="list-decimal list-inside space-y-1">
                                                    @foreach($sub['rejection_points'] as $p)
                                                        <li class="leading-relaxed font-medium text-slate-700">{{ $p }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ── SIGNATURE TABLE SECTION ── -->
            <div class="mt-14 pt-8 border-t-2 border-slate-200">
                <h3 class="text-center font-extrabold text-[13px] text-slate-800 mb-6">الاعتماد والتوقيعات الرسمية للمؤسسة التعليمية</h3>

                <table class="w-full border-collapse border border-slate-300">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="w-1/3 p-3 font-extrabold text-right text-xs text-slate-700 border border-slate-300">الصفة الوظيفية</th>
                            <th class="w-1/3 p-3 font-extrabold text-right text-xs text-slate-700 border border-slate-300">الاسم الكامل</th>
                            <th class="w-1/3 p-3 font-extrabold text-center text-xs text-slate-700 border border-slate-300">التوقيع والختم الرسمي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-3.5 font-bold text-xs text-slate-800 border border-slate-300">عميد الكلية</td>
                            <td class="p-3.5 font-black text-xs text-[#1a3c5e] border border-slate-300">{{ $college->dean_name ?? '—' }}</td>
                            <td class="p-3.5 border border-slate-300 h-20"></td>
                        </tr>
                        <tr>
                            <td class="p-3.5 font-bold text-xs text-slate-800 border border-slate-300">رئيس القسم العلمي</td>
                            <td class="p-3.5 font-black text-xs text-[#1a3c5e] border border-slate-300">{{ $department->head_name ?? '—' }}</td>
                            <td class="p-3.5 border border-slate-300 h-20"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-[10px] text-slate-400 mt-4 leading-normal text-right">
                    * يتم توقيع وختم هذا النموذج رسمياً من قبل عمادة الكلية ورئاسة القسم العلمي قبل الرفع الإلكتروني النهائي للرد.
                </div>
            </div>

        </div>

    </div>

</body>
</html>
