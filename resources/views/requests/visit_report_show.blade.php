<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج 5 - تقرير الزيارة الميدانية</title>
    @vite(['resources/css/app.css'])
    <style>
        @import url("{{ asset('fonts/fonts.css') }}");

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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', Arial, sans-serif;
            direction: rtl;
            background: #e2e8f0;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .action-bar {
            max-width: 210mm;
            margin: 20px auto 0 auto;
            padding: 15px 25px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .action-bar-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #122c47;
        }

        .btn-secondary {
            background-color: #fff;
            color: #4a5568;
            border-color: #cbd5e0;
        }

        .btn-secondary:hover {
            background-color: #f7fafc;
        }

        .page-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 20px auto 40px auto;
            padding: 25mm 20mm;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            position: relative;
        }

        .report-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
        }

        .report-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .report-subtitle {
            font-size: 14px;
            color: #718096;
            font-weight: 500;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 35px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 12px 14px;
            vertical-align: middle;
        }

        .info-table th {
            background: var(--header-bg);
            color: var(--primary-color);
            font-weight: 700;
            width: 30%;
            text-align: right;
            font-size: 14px;
        }

        .info-table td {
            text-align: right;
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
        }

        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary-color);
            margin: 30px 0 15px 0;
            position: relative;
            padding-right: 15px;
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

        /* General Notes Badge Styles */
        .rating-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
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

        /* Generic styled tables */
        .styled-table th {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-align: center;
            font-size: 14px;
        }

        .styled-table td {
            font-size: 13.5px;
            color: #2d3748;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: var(--row-bg-alt);
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .lead-text {
            font-weight: 600;
            color: #1a202c;
        }

        /* Side by Side lists for pros/cons */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 35px;
        }

        .grid-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            overflow: hidden;
        }

        .grid-card-header {
            padding: 10px 15px;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
        }

        .grid-card-header.positives {
            background-color: #0f766e;
        }

        .grid-card-header.negatives {
            background-color: #be123c;
        }

        .grid-card-body {
            padding: 15px;
            min-height: 120px;
        }

        .grid-card-body ul {
            padding-right: 20px;
            margin: 0;
        }

        .grid-card-body li {
            margin-bottom: 8px;
            font-size: 13.5px;
            color: #2d3748;
            line-height: 1.5;
        }

        .empty-text {
            color: #a0aec0;
            font-style: italic;
            font-size: 13px;
            text-align: center;
            padding: 20px 0;
        }

        /* Signature Blocks */
        .committee-table th {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-align: center;
            font-size: 14px;
        }

        .committee-table th.name-col {
            width: 40%;
        }

        .committee-table th.signature-col {
            width: 60%;
        }

        .committee-table td.name-cell {
            text-align: right;
            font-weight: 700;
            color: #2d3748;
        }

        .committee-table td.signature-cell {
            height: 100px;
            background: #fcfcfc;
            padding: 0;
        }

        .signature-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .signature-wrapper svg {
            max-height: 80px;
            max-width: 100%;
            width: auto !important;
            height: auto !important;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                background: #fff;
            }

            .action-bar {
                display: none;
            }

            .page-container {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                height: auto;
            }

            th, td {
                border: 1pt solid #000;
                padding: 10px;
            }

            .styled-table th, .committee-table th {
                background: #e2e2e2 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .info-table th {
                background: #f2f2f2 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .rating-badge {
                border: none !important;
                padding: 0 !important;
                font-weight: bold !important;
            }

            .rating-badge.excellent {
                color: #000 !important;
                background: none !important;
            }

            .rating-badge.average {
                color: #000 !important;
                background: none !important;
            }

            .rating-badge.poor {
                color: #000 !important;
                background: none !important;
            }

            .grid-card {
                border: 1pt solid #000;
            }

            .grid-card-header.positives, .grid-card-header.negatives {
                background: #f2f2f2 !important;
                color: #000 !important;
                border-bottom: 1pt solid #000;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-title {
                color: #000 !important;
            }

            .section-title {
                color: #000 !important;
            }

            .section-title::before {
                background-color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    <!-- ======= Screen-only Action Bar ======= -->
    <div class="action-bar print:hidden">
        <div class="action-bar-title">
            <i class="fa-solid fa-file-lines text-2xl"></i>
            <span>تقرير الزيارة الميدانية للبرنامج الدراسي</span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('requests.stage_six.visit_report.print', $accreditationRequest) }}" class="btn btn-primary">
                <i class="fa-solid fa-download"></i> تحميل كملف PDF
            </a>
            <a href="{{ route('requests.stage', ['accreditationRequest' => $accreditationRequest, 'stage' => 'stage_six']) }}" class="btn btn-secondary">
                العودة للوحة الطلب <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- ======= Printable Page 1: Metadata & General Notes ======= -->
    <div class="page-container">
        <!-- Header -->
        <div class="report-header">
            <h1 class="report-title">نموذج تقرير الزيارة الميدانية (نموذج 5)</h1>
            <p class="report-subtitle">متطلبات الاعتماد الأكاديمي للبرامج الدراسية - مرحلة التقييم الميداني</p>
        </div>

        <!-- Info Table -->
        <div class="table-responsive">
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>رقم طلب الاعتماد</th>
                        <td>{{ $accreditationRequest->id }}</td>
                    </tr>
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
        <p style="font-size: 13.5px; color: #4a5568; margin-bottom: 15px;">تقييم مدى تعاون وتجاوب المؤسسة التعليمية مع لجنة المقيمين وتسهيل مهامها:</p>
        
        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th style="width: 6%">ت</th>
                        <th style="width: 58%">الملاحظة / المؤشر</th>
                        <th style="width: 12%">ممتاز</th>
                        <th style="width: 12%">متوسط</th>
                        <th style="width: 12%">ضعيف</th>
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
                            <td class="text-center font-bold">{{ substr($key, 1) }}</td>
                            <td>{{ $text }}</td>
                            <td class="text-center text-emerald-600 font-extrabold text-xl">
                                @if($isExcellent)
                                    &#10003;
                                @endif
                            </td>
                            <td class="text-center text-amber-500 font-extrabold text-xl">
                                @if($isAverage)
                                    &#10003;
                                @endif
                            </td>
                            <td class="text-center text-rose-500 font-extrabold text-xl">
                                @if($isPoor)
                                    &#10003;
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======= Printable Page 2: Interviews ======= -->
    <div class="page-container page-break">
        <h2 class="section-title">ثانياً: المقابلات التي تم إجراؤها</h2>
        <p style="font-size: 13.5px; color: #4a5568; margin-bottom: 15px;">سجل المقابلات التي عقدتها لجنة المقيمين مع منسوبي البرنامج والقيادات الأكاديمية والطلبة:</p>

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
                                <td class="text-center font-bold">{{ $idx + 1 }}</td>
                                <td class="lead-text">{{ $item['name'] ?? '—' }}</td>
                                <td class="text-center" dir="ltr">{{ $item['from'] ?? '—' }} - {{ $item['to'] ?? '—' }}</td>
                                <td class="text-center">{{ $item['date'] ?? '—' }}</td>
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
                    <i class="fa-solid fa-circle-plus align-middle" style="font-size: 1.25rem; margin-left: 5px;"></i> الجوانب الإيجابية ونقاط القوة المرصودة في المقابلات
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

            <!-- Negatives -->
            <div class="grid-card">
                <div class="grid-card-header negatives">
                    <i class="fa-solid fa-circle-minus align-middle" style="font-size: 1.25rem; margin-left: 5px;"></i> الجوانب السلبية وفرص التحسين المرصودة في المقابلات
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
    </div>

    <!-- ======= Printable Page 3: Site Tours, Document Reviews & Signatures ======= -->
    <div class="page-container page-break">
        <h2 class="section-title">رابعاً: الجولات الميدانية للمرافق والتجهيزات</h2>
        <p style="font-size: 13.5px; color: #4a5568; margin-bottom: 10px;">
            تاريخ الجولات: <span class="lead-text">{{ $form5Data['tours_date'] ?? '—' }}</span>
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
                                <td class="text-center font-bold">{{ $idx + 1 }}</td>
                                <td class="lead-text">{{ $tour['facility'] ?? '—' }}</td>
                                <td class="text-center font-bold bg-slate-50">{{ $tour['count'] ?? '—' }}</td>
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
        <p style="font-size: 13.5px; color: #4a5568; margin-bottom: 10px;">
            تاريخ الاطلاع: <span class="lead-text">{{ $form5Data['docs_date'] ?? '—' }}</span>
        </p>

        <div class="grid-container">
            <!-- Positives -->
            <div class="grid-card">
                <div class="grid-card-header positives">
                    <i class="fa-solid fa-circle-plus align-middle" style="font-size: 1.25rem; margin-left: 5px;"></i> الجوانب الإيجابية المرتبطة بالشواهد والملفات
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

            <!-- Negatives -->
            <div class="grid-card">
                <div class="grid-card-header negatives">
                    <i class="fa-solid fa-circle-minus align-middle" style="font-size: 1.25rem; margin-left: 5px;"></i> النواقص والجوانب السلبية في الوثائق
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
                        <th class="name-col">الاسم</th>
                        <th class="signature-col">التوقيع والاعتماد</th>
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
                                        <span style="color: #94a3b8; font-weight: 500; font-size: 13px; opacity: 0.7;">(لم يتم التوقيع بعد)</span>
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
