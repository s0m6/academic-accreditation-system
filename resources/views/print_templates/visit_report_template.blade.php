<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <title>تقرير الزيارة الميدانية - {{ $program->program_name }}</title>
    
    <script src="{{ public_path('js/tailwind-browser.js') }}"></script>
    @include('print_templates.fonts')
    
    <style>
        :root {
            --primary-color: #1a3c5e;
            --secondary-color: #f8f9fa;
            --border-color: #2c3e50;
            --header-bg: #e9ecef;
            --row-bg-alt: #f8f9fa;
            --highlight-bg: #d1ecf1;
            
            --success-color: #10b981;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;
            
            --warning-color: #f59e0b;
            --warning-bg: #fffbeb;
            --warning-border: #fde68a;
            
            --danger-color: #ef4444;
            --danger-bg: #fef2f2;
            --danger-border: #fca5a5;
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

        .table-responsive {
            width: 100%;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 10px 12px;
            font-size: 13px;
        }

        .info-table th {
            background-color: var(--header-bg);
            color: var(--primary-color);
            font-weight: 800;
            text-align: right;
            width: 30%;
        }

        .info-table td {
            text-align: right;
            font-weight: 500;
        }

        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-color);
            margin: 25px 0 12px 0;
            position: relative;
            padding-right: 12px;
        }

        .section-title::before {
            content: "";
            position: absolute;
            right: 0;
            top: 4px;
            bottom: 4px;
            width: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        .styled-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 800;
            text-align: center;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: var(--row-bg-alt);
        }

        .rating-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .rating-badge.excellent {
            background-color: var(--success-bg);
            color: var(--success-color);
            border-color: var(--success-border);
        }

        .rating-badge.average {
            background-color: var(--warning-bg);
            color: var(--warning-color);
            border-color: var(--warning-border);
        }

        .rating-badge.poor {
            background-color: var(--danger-bg);
            color: var(--danger-color);
            border-color: var(--danger-border);
        }

        .grid-container {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 30px;
        }

        .grid-card {
            display: table-cell;
            width: 50%;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            vertical-align: top;
        }

        .grid-spacer {
            display: table-cell;
            width: 4%;
        }

        .grid-card-header {
            padding: 8px 12px;
            font-weight: 800;
            font-size: 13px;
            color: white;
        }

        .grid-card-header.positives {
            background-color: #0f766e;
        }

        .grid-card-header.negatives {
            background-color: #be123c;
        }

        .grid-card-body {
            padding: 12px;
            min-height: 100px;
        }

        .grid-card-body ul {
            padding-right: 18px;
            margin: 0;
        }

        .grid-card-body li {
            margin-bottom: 6px;
            font-size: 12px;
            color: #2d3748;
            line-height: 1.5;
        }

        .empty-text {
            color: #a0aec0;
            font-style: italic;
            font-size: 12px;
            text-align: center;
            padding: 15px 0;
        }

        .committee-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 800;
            text-align: center;
        }

        .committee-table td.name-cell {
            text-align: right;
            font-weight: 800;
        }

        .committee-table td.signature-cell {
            height: 130px;
            background: #fdfdfd;
            padding: 0;
        }

        .signature-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .signature-wrapper svg {
            max-height: 110px;
            max-width: 100%;
            width: auto !important;
            height: auto !important;
        }

        .page-break {
            page-break-before: always;
        }
        
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body class="bg-white">

    <div class="w-full">
        <!-- ── Page 1: Metadata & General Notes ── -->
        <div class="report-header pb-4 mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-extrabold text-[#1a3c5e] mb-1">نموذج تقرير الزيارة الميدانية (نموذج 5)</h1>
                <p class="text-xs text-gray-500 font-medium">متطلبات الاعتماد الأكاديمي للبرامج الدراسية - مرحلة التقييم الميداني</p>
            </div>
            <div class="bg-[#f5e9c8] border border-[#b8860b] rounded-lg px-4 py-2.5 text-xs text-[#1a3c5e] leading-relaxed">
                <div class="font-bold">رقم الطلب: {{ $accreditationRequest->id }}</div>
                <div class="font-bold">البرنامج: {{ $program->program_name }}</div>
                <div class="mt-1 text-[10px] text-gray-600">تاريخ الطباعة: {{ now()->format('Y/m/d') }}</div>
            </div>
        </div>

        <!-- Info Table -->
        <div class="table-responsive">
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>البرنامج الدراسي</th>
                        <td>{{ $program->program_name }}</td>
                    </tr>
                    <tr>
                        <th>القسم العلمي</th>
                        <td>{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <th>الكلية</th>
                        <td>{{ $college->name }}</td>
                    </tr>
                    <tr>
                        <th>المؤسسة التعليمية</th>
                        <td>{{ $university->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 1: General Notes -->
        <h2 class="section-title">أولاً: الملاحظات العامة</h2>
        <p style="font-size: 12.5px; color: #4a5568; margin-bottom: 12px;">تقييم مدى تعاون وتجاوب المؤسسة التعليمية مع لجنة المقيمين وتسهيل مهامها:</p>
        
        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th style="width: 6%">ت</th>
                        <th style="width: 58%">الملاحظة / المؤشر</th>
                        <th style="width: 12%; text-align: center;">ممتاز</th>
                        <th style="width: 12%; text-align: center;">متوسط</th>
                        <th style="width: 12%; text-align: center;">ضعيف</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $questions = [
                            'q1' => 'استقبلت المؤسسة التعليمية لجنة المقيمين وأظهرت تجاوبا وتعاونا وهيأت كافة الظروف المناسبة لتمكين اللجنة من أداء مهامها.',
                            'q2' => 'جهزت المؤسسة قاعة مناسبة لاجتماع اللجنة وإجراء المقابلات بطاقة استيعابية كافية، مزودة بجميع التجهيزات المطلوبة.',
                            'q3' => 'التزمت المؤسسة ببرنامج زيارة اللجنة وتنفيذ المقابلات في مواعيدها المحددة.',
                            'q4' => 'التزمت المؤسسة بالتنسيق مع المعنيين بالمقابلات التي حددتها اللجنة مع تأمين حضورهم.',
                            'q5' => 'سهلت المؤسسة تنفيذ الجولات الميدانية على المرافق حسب البرنامج المعد.',
                            'q6' => 'تم وضع كافة نسخ الوثائق والشواهد المطلوب تقديمها من البرنامج في القاعة المخصصة للجنة.',
                            'q7' => 'وافقت المؤسسة على الملخص الذي قدمته اللجنة في ختام الزيارة الميدانية.'
                        ];
                    @endphp
                    @foreach($questions as $key => $text)
                        @php
                            $val = isset($form5Data['general_notes'][$key]) ? trim($form5Data['general_notes'][$key]) : null;
                            $isExcellent = ($val === 'ممتاز' || strtolower($val) === 'excellent');
                            $isAverage = ($val === 'متوسط' || strtolower($val) === 'average');
                            $isPoor = ($val === 'ضعيف' || strtolower($val) === 'poor');
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ substr($key, 1) }}</td>
                            <td>{{ $text }}</td>
                            <td style="text-align: center; font-weight: bold; color: #10b981; font-size: 18px;">
                                @if($isExcellent) &#10003; @endif
                            </td>
                            <td style="text-align: center; font-weight: bold; color: #d97706; font-size: 18px;">
                                @if($isAverage) &#10003; @endif
                            </td>
                            <td style="text-align: center; font-weight: bold; color: #e11d48; font-size: 18px;">
                                @if($isPoor) &#10003; @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ── Page 2: Interviews ── -->
        <div class="page-break"></div>
        <h2 class="section-title">ثانياً: المقابلات التي تم إجراؤها</h2>
        <p style="font-size: 12.5px; color: #4a5568; margin-bottom: 12px;">سجل المقابلات التي عقدتها لجنة المقيمين مع منسوبي البرنامج والقيادات الأكاديمية والطلبة:</p>

        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th style="width: 6%">ت</th>
                        <th style="width: 25%">اسم الحاضر أو الجهة</th>
                        <th style="width: 20%">الوقت (من - إلى)</th>
                        <th style="width: 15%">التاريخ</th>
                        <th style="width: 34%">ملاحظات وإيضاحات</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($form5Data['interviews']) && count($form5Data['interviews']) > 0)
                        @foreach($form5Data['interviews'] as $idx => $item)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                                <td style="font-weight: 800; color: #1a202c;">{{ $item['name'] ?? '—' }}</td>
                                <td style="text-align: center;" dir="ltr">{{ $item['from'] ?? '—' }} - {{ $item['to'] ?? '—' }}</td>
                                <td style="text-align: center;">{{ $item['date'] ?? '—' }}</td>
                                <td>{{ $item['notes'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="empty-text">لا توجد مقابلات مسجلة</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <h2 class="section-title">ثالثاً: النتائج العامة للمقابلات</h2>
        <div class="grid-container">
            <!-- Positives -->
            <div class="grid-card">
                <div class="grid-card-header positives">
                    الجوانب الإيجابية ونقاط القوة المرصودة في المقابلات
                </div>
                <div class="grid-card-body">
                    @if(isset($form5Data['interview_positives']) && count($form5Data['interview_positives']) > 0)
                        <ul>
                            @foreach($form5Data['interview_positives'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-text">لا توجد ملاحظات إيجابية مرصودة</div>
                    @endif
                </div>
            </div>

            <!-- Spacer -->
            <div class="grid-spacer"></div>

            <!-- Negatives -->
            <div class="grid-card">
                <div class="grid-card-header negatives">
                    الجوانب السلبية وفرص التحسين المرصودة في المقابلات
                </div>
                <div class="grid-card-body">
                    @if(isset($form5Data['interview_negatives']) && count($form5Data['interview_negatives']) > 0)
                        <ul>
                            @foreach($form5Data['interview_negatives'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-text">لا توجد ملاحظات سلبية مرصودة</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ── Page 3: Site Tours, Document Reviews & Signatures ── -->
        <div class="page-break"></div>
        <h2 class="section-title">رابعاً: الجولات الميدانية للمرافق والتجهيزات</h2>
        <p style="font-size: 12.5px; color: #4a5568; margin-bottom: 8px;">
            تاريخ الجولات: <span style="font-weight: bold; color: #1a202c;">{{ $form5Data['tours_date'] ?? '—' }}</span>
        </p>
        
        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th style="width: 8%">ت</th>
                        <th style="width: 32%">المرفق أو التجهيز</th>
                        <th style="width: 15%">العدد الإجمالي</th>
                        <th style="width: 45%">الملاحظات والتوصيات</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($form5Data['tours']) && count($form5Data['tours']) > 0)
                        @foreach($form5Data['tours'] as $idx => $tour)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                                <td style="font-weight: 800; color: #1a202c;">{{ $tour['facility'] ?? '—' }}</td>
                                <td style="text-align: center; font-weight: bold; background-color: #f8fafc;">{{ $tour['count'] ?? '—' }}</td>
                                <td>{{ $tour['notes'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="empty-text">لا توجد جولات ميدانية مسجلة</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <h2 class="section-title">خامساً: الاطلاع على الوثائق والمستندات والشواهد</h2>
        <p style="font-size: 12.5px; color: #4a5568; margin-bottom: 8px;">
            تاريخ الاطلاع: <span style="font-weight: bold; color: #1a202c;">{{ $form5Data['docs_date'] ?? '—' }}</span>
        </p>

        <div class="grid-container">
            <!-- Positives -->
            <div class="grid-card">
                <div class="grid-card-header positives">
                    الجوانب الإيجابية المرتبطة بالشواهد والملفات
                </div>
                <div class="grid-card-body">
                    @if(isset($form5Data['docs_positives']) && count($form5Data['docs_positives']) > 0)
                        <ul>
                            @foreach($form5Data['docs_positives'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-text">لا توجد جوانب إيجابية مرصودة</div>
                    @endif
                </div>
            </div>

            <!-- Spacer -->
            <div class="grid-spacer"></div>

            <!-- Negatives -->
            <div class="grid-card">
                <div class="grid-card-header negatives">
                    النواقص والجوانب السلبية في الوثائق
                </div>
                <div class="grid-card-body">
                    @if(isset($form5Data['docs_negatives']) && count($form5Data['docs_negatives']) > 0)
                        <ul>
                            @foreach($form5Data['docs_negatives'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-text">لا توجد ملاحظات سلبية مرصودة</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 6: Committee Signatures -->
        <h2 class="section-title">سادساً: اعتماد وتواقيع أعضاء لجنة المقيمين</h2>
        <div class="table-responsive">
            <table class="committee-table">
                <thead>
                    <tr>
                        <th style="width: 40%">الاسم</th>
                        <th style="width: 60%">التوقيع والاعتماد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membersData as $member)
                        <tr>
                            <td class="name-cell">
                                {{ $member['name'] }}
                                @if($member['is_chair'])
                                    <span style="font-size: 11px; color: var(--primary-color); display: block; font-weight: 500;">(رئيس اللجنة)</span>
                                @else
                                    <span style="font-size: 11px; color: #718096; display: block; font-weight: 500;">(عضو مقيم)</span>
                                @endif
                            </td>
                            <td class="signature-cell">
                                @if($member['signature_path'] && \Illuminate\Support\Facades\Storage::exists($member['signature_path']))
                                    @php
                                        $svg = \Illuminate\Support\Facades\Storage::get($member['signature_path']);
                                    @endphp
                                    <div class="signature-wrapper">
                                        {!! $svg !!}
                                    </div>
                                @else
                                    <div class="signature-wrapper">
                                        <span style="color: #94a3b8; font-weight: 500; font-size: 12px; opacity: 0.7;">(لم يتم التوقيع بعد)</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
