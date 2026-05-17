<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خطاب توصيات لجنة المقيمين - {{ $program->program_name }}</title>
    
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
                    النموذج رقم (8)
                </span>
                <h1 class="text-3xl font-black text-[#1a3c5e] tracking-tight">
                    خطاب توصيات لجنة المقيمين
                </h1>
                <p class="text-lg font-bold text-slate-600 mt-3">
                    الموجه للمؤسسة التعليمية والبرنامج الدراسي
                </p>
                <div class="w-20 h-1.5 bg-[#1a3c5e] mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Basic Info Card Section -->
            <div class="my-6">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-2xl mx-auto">
                    <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3c5e]"></span>
                        <span>بيانات التوصيات والاعتماد الأكاديمي</span>
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

        <!-- ── PAGE 2: DETAILED RECOMMENDATIONS TABLE ── -->
        <div class="flex justify-between items-start border-b-2 border-slate-150 pb-4 mb-6">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <div class="text-left text-[10px] text-slate-400">توصيات لجنة المقيمين الخارجية</div>
        </div>

        <div class="space-y-4">
            <h2 class="border-r-4 border-[#1a3c5e] pr-3 text-[15px] font-extrabold text-[#1a3c5e] my-4">جدول توصيات وفرص تحسين البرنامج المعتمدة</h2>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                فيما يلي تفصيل متوسطات درجات التقييم وفرص التحسين المعتمدة لكل معيار فرعي للبرنامج الدراسي بناء على تقرير المراجعة النهائية:
            </p>

            <div class="w-full overflow-x-auto">
                <table class="w-full border-collapse border border-[#2c3e50]">
                    <thead>
                        <tr class="bg-[#1a3c5e] text-white font-extrabold">
                            <th class="w-[8%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">ت</th>
                            <th class="w-[42%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">المعيار الفرعي</th>
                            <th class="w-[12%] text-white text-center font-extrabold border border-[#2c3e50] p-2 text-xs">الدرجة</th>
                            <th class="w-[38%] text-white text-right font-extrabold border border-[#2c3e50] p-2 text-xs">فرص التحسين والتوصيات المعتمدة</th>
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
                                            <span class="text-[10px] text-slate-400 italic">لا توجد ملاحظات أو توصيات مدونة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ── SIGNATURE AND SEAL OF THE CHAIRMAN (WITH SPACIOUS DEDICATED MANUAL CONTAINER) ── -->
            <div class="mt-14 pt-8 border-t-2 border-slate-200">
                <div class="flex justify-between items-start">
                    <div class="text-right w-[45%]">
                        <p class="text-[10px] text-slate-500 mb-1">جهة التوصيات والاعتماد:</p>
                        <p class="font-extrabold text-[12px] text-slate-800 leading-normal">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                        <p class="text-[11px] text-slate-600 mt-1 leading-normal">الجمهورية اليمنية - عدن</p>
                        <p class="text-[10px] text-slate-400 mt-4">صدر هذا الخطاب رسمياً عبر منصة الاعتماد الالكتروني للمجلس.</p>
                    </div>
                    
                    <div class="text-center w-[45%] flex flex-col items-center justify-center">
                        <p class="font-extrabold text-[13px] text-slate-800">توقيع وختم رئيس المجلس</p>
                        
                        <!-- Generous blank space for manual physical signing and official council stamping -->
                        <div class="w-56 h-36 my-3 border-2 border-dashed border-slate-300 rounded-3xl flex items-center justify-center bg-slate-50/40">
                            <span class="text-[10px] text-slate-400 font-bold leading-relaxed">(مساحة كافية ومخصصة للتوقيع اليدوي والختم الرسمي للمجلس)</span>
                        </div>
                        
                        <p class="text-[12px] font-extrabold text-[#1a3c5e]">أ.د. رئيس مجلس الاعتماد الأكاديمي</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
