<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج البيانات الأساسية - {{ $program->program_name }}</title>
    
    <script src="{{ public_path('js/tailwind-browser.js') }}"></script>
    @include('print_templates.fonts')
    
    <style>
        :root {
            --primary-color: #1a3c5e;
            --secondary-color: #f8f9fa;
            --border-color: #2c3e50;
            --header-bg: #e9ecef;
            --row-bg-alt: #f8f9fa;
            --gold: #b8860b;
            --gold-light: #f5e9c8;
        }

        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: white;
            color: #333;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 15mm;
            size: a4;
        }

        .report-header {
            border-bottom: 4px solid var(--primary-color);
        }

        .section-header {
            background-color: var(--primary-color);
            color: white;
        }

        .gold-circle {
            background-color: var(--gold);
            color: white;
        }

        .prog-cell {
            border: 1px solid #cbd5e1;
            background-color: var(--secondary-color);
        }

        .group-cell {
            background-color: #f1f5f9;
            color: var(--primary-color);
            font-weight: 700;
        }

        .total-row {
            background-color: var(--gold-light);
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 8px 12px;
        }

        thead th {
            background-color: var(--header-bg);
            color: var(--primary-color);
            font-size: 11px;
        }

        .seal-box {
            border: 2px dashed var(--gold);
            background-color: var(--gold-light);
            color: var(--gold);
        }

        .page-break {
            page-break-before: always;
        }
        
        tr {
            page-break-inside: avoid;
        }

        .label-cell {
            background-color: #f8f9fa;
            font-weight: 700;
            color: var(--primary-color);
            width: 35%;
        }
    </style>
</head>
<body class="bg-white">

    <div class="w-full">
        <!-- ── Header ── -->
        <div class="report-header pb-4 mb-6 flex justify-between items-start">
            <div class="report-header-left">
                <h1 class="text-3xl font-extrabold text-[#1a3c5e] mb-1">نموذج البيانات الأساسية</h1>
                <p class="text-sm text-gray-500 font-medium">متطلبات الاعتماد الأكاديمي - المرحلة الثانية</p>
            </div>
            <div class="report-meta bg-[#f5e9c8] border border-[#b8860b] rounded-lg px-4 py-3 text-sm text-[#1a3c5e] leading-relaxed">
                <div class="font-bold">الجامعة: {{ $university->name }}</div>
                <div class="font-bold">الكلية: {{ $college->name }}</div>
                <div class="mt-1 text-xs text-gray-600">تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</div>
            </div>
        </div>

        <!-- ── Section 1: Basic Data ── -->
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">١</span>
            <span class="font-bold text-base">البيانات الأساسية للبرنامج</span>
        </div>
        <table class="mb-6">
            <tbody>
                <tr>
                    <td class="label-cell">المرحلة الدراسية</td>
                    <td>
                        @php $levels = ['diploma' => 'دبلوم', 'bachelor' => 'بكالوريوس', 'master' => 'ماجستير', 'phd' => 'دكتوراه']; @endphp
                        {{ $levels[$program->degree_level] ?? $program->degree_level }}
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">اسم البرنامج</td>
                    <td class="font-bold text-[#1a3c5e]">{{ $program->program_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">القسم العلمي</td>
                    <td>{{ $department->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">الكلية</td>
                    <td>{{ $college->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">الجامعة</td>
                    <td>{{ $university->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">لغة البرنامج</td>
                    <td>{{ ($program->program_details['language'] ?? '') == 'arabic' ? 'العربية' : (($program->program_details['language'] ?? '') == 'english' ? 'الإنجليزية' : ($program->program_details['language'] ?? '—')) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">الساعات المعتمدة لإكمال البرنامج</td>
                    <td>{{ $program->program_details['credit_hours'] ?? '—' }} ساعة</td>
                </tr>
                <tr>
                    <td class="label-cell">تاريخ تأسيس البرنامج</td>
                    <td>{{ $program->program_details['establishment_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">مدة الدراسة</td>
                    <td>{{ $program->program_details['study_duration'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">عنوان الموقع الالكتروني للبرنامج</td>
                    <td dir="ltr" class="text-right">{{ $program->program_details['website_url'] ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ── Section 2: Contact Addresses ── -->
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٢</span>
            <span class="font-bold text-base">عناوين التواصل</span>
        </div>
        <table class="mb-6 text-xs">
            <thead>
                <tr>
                    <th class="w-[18%]">الصفة</th>
                    <th>الاسم</th>
                    <th class="w-[15%]">الهاتف</th>
                    <th class="w-[15%]">الجوال</th>
                    <th>الايميل</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $officer = $university->officer;
                    $coordinator = $accreditationRequest->programCoordinator;
                @endphp
                <tr>
                    <td class="group-cell">رئيس الجامعة</td>
                    <td>{{ $university->president_name ?? '—' }}</td>
                    <td class="text-center">{{ $university->president_phone ?? '—' }}</td>
                    <td class="text-center">{{ $university->president_mobile ?? '—' }}</td>
                    <td>{{ $university->president_email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="group-cell">مسؤول الاعتماد</td>
                    <td>{{ $officer->name ?? '—' }}</td>
                    <td class="text-center">{{ $officer->phone ?? '—' }}</td>
                    <td class="text-center">{{ $officer->mobile ?? '—' }}</td>
                    <td>{{ $officer->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="group-cell">عميد الكلية</td>
                    <td>{{ $college->dean_name ?? '—' }}</td>
                    <td class="text-center">{{ $college->dean_phone ?? '—' }}</td>
                    <td class="text-center">{{ $college->dean_mobile ?? '—' }}</td>
                    <td>{{ $college->dean_email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="group-cell">رئيس القسم</td>
                    <td>{{ $department->head_name ?? '—' }}</td>
                    <td class="text-center">{{ $department->head_phone ?? '—' }}</td>
                    <td class="text-center">{{ $department->head_mobile ?? '—' }}</td>
                    <td>{{ $department->head_email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="group-cell">منسق البرنامج</td>
                    <td>{{ $coordinator->name ?? '—' }}</td>
                    <td class="text-center">{{ $coordinator->phone ?? '—' }}</td>
                    <td class="text-center">{{ $coordinator->mobile ?? '—' }}</td>
                    <td>{{ $coordinator->email ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ── Section 3: Decisions (Moved to New Page) ── -->
        <div class="page-break"></div>
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3 mt-6">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٣</span>
            <span class="font-bold text-base">القرارات المتعلقة بالبرنامج</span>
        </div>
        <table class="mb-6 text-xs">
            <thead>
                <tr>
                    <th class="w-[36%] text-right">القرار</th>
                    <th class="w-[16%] text-center">رقم القرار</th>
                    <th class="w-[24%] text-right">الجهة المصدرة</th>
                    <th class="w-[14%] text-center">تاريخ القرار</th>
                    <th class="w-[10%] text-center">مرفق</th>
                </tr>
            </thead>
            <tbody>
                @php $decisionsData = $data['decisions'] ?? []; @endphp
                @foreach($decisionNames as $id => $label)
                    @php
                        $d = collect($decisionsData)->firstWhere('id', $id);
                        $hasFile = isset($data['decision_files'][$id]);
                    @endphp
                    <tr>
                        <td class="font-medium">{{ $label }}</td>
                        <td class="text-center bg-gray-50">{{ $d['number'] ?? '—' }}</td>
                        <td>{{ $d['authority'] ?? '—' }}</td>
                        <td class="text-center bg-gray-50">{{ $d['date'] ?? '—' }}</td>
                        <td class="text-center font-bold text-green-700">{{ $hasFile ? '✓' : '□' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ── Section 4: Student Stats ── -->
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٤</span>
            <span class="font-bold text-base">البيانات الإحصائية للطلاب</span>
        </div>
        <table class="mb-6">
            <thead>
                <tr>
                    <th colspan="2" class="w-[42%] text-right">الفئة / التصنيف</th>
                    <th class="w-[19%] text-center">العام الماضي</th>
                    <th class="w-[19%] text-center">العام الحالي</th>
                    <th class="w-[20%] text-center">المتوقع (القادم)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $studentStats = $data['student_stats'] ?? [];
                    $studentSections = [
                        'planned' => 'الطلبة المخطط قبولهم بالبرنامج',
                        'total' => 'إجمالي الطلاب المقيدين بالبرنامج',
                        'average' => 'متوسط عدد الطلبة في الشعبة',
                        'graduates_higher_ed' => 'الخريجون لمواصلة الدراسات العليا',
                        'graduates_employed' => 'الخريجون الملتحقون بوظائف'
                    ];
                    $rowLabels = ['general' => 'قبول عام', 'special' => 'قبول خاص', 'international' => 'قبول دولي', 'male' => 'ذكور', 'female' => 'إناث'];
                @endphp

                @foreach($studentSections as $sKey => $sLabel)
                    @php
                        $section = $studentStats[$sKey] ?? [];
                        $rows = ['male', 'female'];
                        if (in_array($sKey, ['planned', 'total'])) $rows = ['general', 'special', 'international'];
                        $rowCount = count($rows);
                    @endphp
                    @foreach($rows as $rKey)
                        <tr>
                            @if($loop->first) <td rowspan="{{ $rowCount }}" class="group-cell text-[10px]">{{ $sLabel }}</td> @endif
                            <td class="bg-white text-gray-600 text-[10px] text-center">{{ $rowLabels[$rKey] ?? $rKey }}</td>
                            <td class="text-center bg-gray-50">{{ $section[$rKey]['past'] ?? 0 }}</td>
                            <td class="text-center bg-gray-50">{{ $section[$rKey]['current'] ?? 0 }}</td>
                            <td class="text-center bg-gray-50">{{ $section[$rKey]['next'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    @if(in_array($sKey, ['average', 'graduates_higher_ed', 'graduates_employed']))
                        <tr class="total-row">
                            <td class="text-center text-[10px]" colspan="2">الإجمالي</td>
                            <td class="text-center">{{ ($section['male']['past'] ?? 0) + ($section['female']['past'] ?? 0) }}</td>
                            <td class="text-center">{{ ($section['male']['current'] ?? 0) + ($section['female']['current'] ?? 0) }}</td>
                            <td class="text-center">{{ ($section['male']['next'] ?? 0) + ($section['female']['next'] ?? 0) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- ── Section 5: Faculty Stats ── -->
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٥</span>
            <span class="font-bold text-base">إحصائيات أعضاء هيئة التدريس</span>
        </div>
        <table class="mb-6">
            <thead>
                <tr>
                    <th class="w-[28%] text-right">الدرجة العلمية</th>
                    <th class="w-[18%] text-center">ذكور</th>
                    <th class="w-[18%] text-center">إناث</th>
                    <th class="w-[22%] text-center">متوسط العبء</th>
                    <th class="w-[14%] text-center">غير المتفرغين</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $facultyStats = $data['faculty_stats'] ?? [];
                    $facultyRanks = ['professor' => 'أستاذ', 'associate' => 'أستاذ مشارك', 'assistant' => 'أستاذ مساعد', 'lecturer' => 'مدرس', 'teaching_assistant' => 'معيد'];
                    $totals = ['male' => 0, 'female' => 0, 'load' => 0, 'parttime' => 0];
                @endphp
                @foreach($facultyRanks as $rKey => $rLabel)
                    @php
                        $rank = $facultyStats[$rKey] ?? [];
                        $totals['male'] += ($rank['male'] ?? 0); $totals['female'] += ($rank['female'] ?? 0);
                        $totals['load'] += ($rank['load'] ?? 0); $totals['parttime'] += ($rank['parttime'] ?? 0);
                    @endphp
                    <tr>
                        <td class="group-cell">{{ $rLabel }}</td>
                        <td class="text-center bg-gray-50">{{ $rank['male'] ?? 0 }}</td>
                        <td class="text-center bg-gray-50">{{ $rank['female'] ?? 0 }}</td>
                        <td class="text-center bg-gray-50">{{ $rank['load'] ?? 0 }}</td>
                        <td class="text-center bg-gray-50">{{ $rank['parttime'] ?? 0 }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>الإجمالي</td>
                    <td class="text-center">{{ $totals['male'] }}</td>
                    <td class="text-center">{{ $totals['female'] }}</td>
                    <td class="text-center">--</td>
                    <td class="text-center">{{ $totals['parttime'] }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ── Section 6: Faculty Members ── -->
        @if(!empty($data['faculty_members']))
            <div class="page-break"></div>
            <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3 mt-6">
                <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٦</span>
                <span class="font-bold text-base">بيانات أعضاء هيئة التدريس التفصيلية</span>
            </div>
            <table class="text-[9px]">
                <thead>
                    <tr>
                        <th class="w-[5%] text-center">ت</th>
                        <th class="w-[25%] text-right">الاسم الكامل</th>
                        <th class="w-[15%] text-center">الدرجة</th>
                        <th class="text-right">التخصص العام / الدقيق</th>
                        <th class="w-[12%] text-center">بلد التخرج</th>
                        <th class="w-[8%] text-center">التعيين</th>
                    </tr>
                </thead>
                <tbody>
                    @php $facultyRanksMap = ['prof' => 'أستاذ', 'assoc' => 'أستاذ مشارك', 'assist' => 'أستاذ مساعد', 'lecturer' => 'مدرس', 'ta' => 'معيد']; @endphp
                    @foreach($data['faculty_members'] as $index => $member)
                        <tr>
                            <td class="text-center bg-gray-50">{{ $index + 1 }}</td>
                            <td class="font-bold">{{ $member['name'] ?? '—' }}</td>
                            <td class="text-center">{{ $facultyRanksMap[$member['degree']] ?? $member['degree'] ?? '—' }}</td>
                            <td>{{ $member['major'] ?? '—' }} / {{ $member['minor'] ?? '—' }}</td>
                            <td class="text-center">{{ $member['country'] ?? '—' }}</td>
                            <td class="text-center bg-gray-50">{{ $member['year'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- ── Authorization ── -->
        <div class="mt-12 pt-6 border-t-2 border-[#1a3c5e] grid grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-sm font-bold text-[#1a3c5e] mb-1">عميد الكلية</div>
                <div class="text-xs text-gray-500 mb-6">{{ $college->dean_name ?? '—' }}</div>
                <div class="border-t border-gray-300 pt-1 text-xs font-bold text-[#1a3c5e]">التوقيع</div>
            </div>
            <div class="flex justify-center items-center">
                <div class="seal-box w-20 h-20 rounded-full flex items-center justify-center text-[10px] text-center font-bold">الختم الرسمي</div>
            </div>
            <div class="text-center">
                <div class="text-sm font-bold text-[#1a3c5e] mb-1">رئيس المؤسسة التعليمية</div>
                <div class="text-xs text-gray-500 mb-6">{{ $university->president_name ?? '—' }}</div>
                <div class="border-t border-gray-300 pt-1 text-xs font-bold text-[#1a3c5e]">التوقيع والاعتماد</div>
            </div>
        </div>

        <!-- ── Footer ── -->
        <div class="mt-12 pt-4 border-t-2 border-[#1a3c5e] text-center text-[10px] text-gray-500">
            نظام الاعتماد الأكاديمي الموحد | تم استخراج هذا التقرير آلياً بتاريخ {{ now()->format('Y/m/d') }}
        </div>
    </div>

</body>
</html>
