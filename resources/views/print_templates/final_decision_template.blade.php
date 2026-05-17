<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج 10 - القرار النهائي - {{ $program->program_name }}</title>

    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        @page { margin: 15mm; size: a4; }
        .page-break { page-break-before: always; }
        .signature-wrapper { width: 100%; height: 70px; display: flex; align-items: center; justify-content: center; }
        .signature-wrapper svg { max-height: 55px; max-width: 100%; display: block; margin: 0 auto; }
    </style>
</head>
<body class="font-['Tajawal',Arial,sans-serif] bg-white text-slate-800 antialiased [print-color-adjust:exact]">

    <div class="container mx-auto px-4 py-6">

        {{-- ── COVER PAGE ── --}}
        <div class="min-h-[230mm] flex flex-col justify-between p-2 box-border">

            {{-- Header --}}
            <div class="flex justify-between items-start border-b-4 border-[#1a3c5e] pb-4 mb-5">
                <div class="text-right">
                    <p class="font-extrabold text-[15px] text-slate-800">الجمهورية اليمنية</p>
                    <p class="font-bold text-[13px] text-slate-600 mt-0.5">وزارة التعليم العالي والبحث العلمي</p>
                    <p class="font-bold text-[12px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>
                <div class="text-center">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" class="h-20 object-contain mx-auto" alt="Logo">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#1a3c5e] to-[#2c3e50] flex items-center justify-center text-white font-black text-xl shadow-md mx-auto">AA</div>
                    @endif
                </div>
            </div>

            {{-- Title Banner --}}
            <div class="text-center my-6 py-8 px-8 rounded-3xl bg-slate-50 border border-slate-200/60 shadow-sm">
                <span class="px-4 py-1.5 bg-[#1a3c5e]/10 text-[#1a3c5e] rounded-full text-xs font-black tracking-widest uppercase mb-4 inline-block">
                    النموذج رقم (10)
                </span>
                <h1 class="text-3xl font-black text-[#1a3c5e] tracking-tight">
                    القرار النهائي وتوصيات لجنة المقيمين
                </h1>
                <p class="text-lg font-bold text-slate-600 mt-3">
                    بخصوص اعتماد البرنامج الدراسي
                </p>
                <div class="w-20 h-1.5 bg-[#1a3c5e] mx-auto mt-6 rounded-full"></div>
            </div>

            {{-- Info Card --}}
            <div class="my-4">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm max-w-2xl mx-auto">
                    <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3c5e]"></span>
                        <span>بيانات البرنامج والمؤسسة التعليمية</span>
                    </h3>
                    <div class="space-y-3.5 text-[13px]">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <span class="text-slate-500 font-medium">رقم الطلب:</span>
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
                        <div class="flex justify-between pb-2">
                            <span class="text-slate-500 font-medium">اسم البرنامج الدراسي:</span>
                            <span class="font-bold text-[#1a3c5e]">{{ $program->program_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center text-[10px] text-slate-400 mt-4 border-t border-slate-100 pt-3 flex justify-between">
                <span>تاريخ التصدير: {{ now()->format('Y/m/d') }}</span>
                <span>مجلس الاعتماد الأكاديمي وضمان الجودة - نظام الاعتماد الالكتروني</span>
            </div>
        </div>

        <div class="page-break"></div>

        {{-- ── PAGE 2: CONTENT ── --}}
        <div class="flex justify-between items-start border-b-2 border-slate-200 pb-3 mb-4">
            <div class="text-right">
                <p class="font-extrabold text-[12px] text-slate-800">الجمهورية اليمنية</p>
                <p class="text-[10px] text-slate-500 mt-0.5">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            </div>
            <p class="text-[11px] font-bold text-[#1a3c5e] self-end">نموذج رقم (10) — القرار النهائي</p>
        </div>

        {{-- Intro text --}}
        <div class="text-[13px] leading-relaxed mb-4 space-y-1">
            <p class="font-bold">الأستاذ الدكتور / رئيس مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
            <p>تحية طيبة وبعد ...</p>
            <p><span class="font-bold">الموضوع:</span> التوصية النهائية بخصوص الطلب حسب البيانات أدناه:</p>
        </div>

        {{-- Score summary --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 mb-4 text-[13px] text-justify leading-relaxed">
            بناءً على تقرير الزيارة الميدانية نموذج رقم (5) والتقرير النهائي للجنة المقيمين نموذج رقم (7)،
            وبعد إجراء التعديلات اللازمة عليه على ضوء رد المؤسسة التعليمية على توصيات اللجنة،
            والتي تبين أن الدرجة المتحققة للبرنامج حسب معايير مجلس الاعتماد الأكاديمي هي
            <span class="font-black text-[#1a3c5e] text-base border-b-2 border-[#1a3c5e] px-2">{{ number_format($grandAverage, 2) }}</span>
            من أصل (5) درجات فإننا نوصي بالآتي:
        </div>

        {{-- Decision Box --}}
        <div class="border border-slate-300 rounded-xl overflow-hidden mb-5">
            <div class="bg-[#1a3c5e] text-white px-5 py-3 font-bold text-sm">
                التوصية النهائية لاعتماد البرنامج
            </div>
            <div class="p-4 space-y-4">
                {{-- Approval group --}}
                <div>
                    <p class="font-bold text-[#1a3c5e] text-[13px] mb-3">الموافقة على منح البرنامج الاعتماد الأكاديمي، بمستوى:</p>
                    <div class="space-y-2">
                        @foreach([
                            'محقق'       => 'محقق (متابعة الاعتماد بعد ثلاث سنوات)',
                            'محقق بإتقان'  => 'محقق بإتقان (متابعة الاعتماد بعد أربع سنوات)',
                            'محقق بامتياز' => 'محقق بتميز (متابعة الاعتماد بعد خمس سنوات)',
                        ] as $key => $label)
                            <div class="flex items-center gap-3 border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] {{ $achievementLevel === $key ? 'bg-[#1a3c5e]/5 border-[#1a3c5e]/40' : 'bg-white' }}">
                                <span class="w-5 h-5 border-2 border-slate-400 rounded flex items-center justify-center shrink-0 text-[#1a3c5e] font-black text-sm {{ $achievementLevel === $key ? 'border-[#1a3c5e]' : '' }}">
                                    {{ $achievementLevel === $key ? '✔' : '' }}
                                </span>
                                <span class="{{ $achievementLevel === $key ? 'font-bold text-[#1a3c5e]' : 'text-slate-600' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-dashed border-slate-300">

                {{-- Rejection group --}}
                <div>
                    <p class="font-bold text-[#1a3c5e] text-[13px] mb-3">عدم الموافقة على منح البرنامج الاعتماد الأكاديمي، لأن البرنامج بمستوى:</p>
                    <div class="space-y-2">
                        @foreach([
                            'غير محقق'    => 'غير محقق (يمنح مهلة سنتين لإعادة التقدم)',
                            'محقق جزئياً'  => 'محقق جزئياً (يمنح مهلة سنة لإعادة التقدم)',
                        ] as $key => $label)
                            <div class="flex items-center gap-3 border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] {{ $achievementLevel === $key ? 'bg-[#1a3c5e]/5 border-[#1a3c5e]/40' : 'bg-white' }}">
                                <span class="w-5 h-5 border-2 border-slate-400 rounded flex items-center justify-center shrink-0 text-[#1a3c5e] font-black text-sm {{ $achievementLevel === $key ? 'border-[#1a3c5e]' : '' }}">
                                    {{ $achievementLevel === $key ? '✔' : '' }}
                                </span>
                                <span class="{{ $achievementLevel === $key ? 'font-bold text-[#1a3c5e]' : 'text-slate-600' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Signatures Table --}}
        @php
            $chair   = collect($membersData)->firstWhere('is_chair', true);
            $members = collect($membersData)->where('is_chair', false);
        @endphp
        <table class="w-full border-collapse text-[12px]">
            <thead>
                <tr class="bg-slate-100 text-[#1a3c5e] font-bold">
                    <th class="border border-slate-300 px-3 py-2.5 text-center w-[18%]">الصفة</th>
                    <th class="border border-slate-300 px-3 py-2.5 text-center w-[27%]">الاسم</th>
                    <th class="border border-slate-300 px-3 py-2.5 text-center w-[38%]">التوقيع</th>
                    <th class="border border-slate-300 px-3 py-2.5 text-center w-[17%]">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $m)
                <tr>
                    <td class="border border-slate-300 px-3 py-2 text-center bg-slate-50 font-bold text-slate-700">عضو اللجنة</td>
                    <td class="border border-slate-300 px-3 py-2 font-medium">{{ $m['name'] }}</td>
                    <td class="border border-slate-300 px-1 py-1">
                        @if(!empty($m['signature_path']) && \Illuminate\Support\Facades\Storage::exists($m['signature_path']))
                            <div class="signature-wrapper">{!! \Illuminate\Support\Facades\Storage::get($m['signature_path']) !!}</div>
                        @else
                            <div class="text-center text-slate-400 text-[11px] py-2">(لم يتم التوقيع)</div>
                        @endif
                    </td>
                    <td class="border border-slate-300 px-3 py-2 text-center text-slate-600">{{ !empty($m['signed_at']) ? $m['signed_at']->format('Y/m/d') : '—' }}</td>
                </tr>
                @endforeach

                @if($chair)
                <tr>
                    <td class="border border-slate-300 px-3 py-2 text-center bg-slate-50 font-bold text-[#1a3c5e]">رئيس اللجنة</td>
                    <td class="border border-slate-300 px-3 py-2 font-medium">{{ $chair['name'] }}</td>
                    <td class="border border-slate-300 px-1 py-1">
                        @if(!empty($chair['signature_path']) && \Illuminate\Support\Facades\Storage::exists($chair['signature_path']))
                            <div class="signature-wrapper">{!! \Illuminate\Support\Facades\Storage::get($chair['signature_path']) !!}</div>
                        @else
                            <div class="text-center text-slate-400 text-[11px] py-2">(لم يتم التوقيع)</div>
                        @endif
                    </td>
                    <td class="border border-slate-300 px-3 py-2 text-center text-slate-600">{{ !empty($chair['signed_at']) ? $chair['signed_at']->format('Y/m/d') : '—' }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="text-center font-bold text-[#1a3c5e] border-t-2 border-[#1a3c5e] pt-3 mt-5 text-sm">
            مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي
        </div>

    </div>
</body>
</html>
