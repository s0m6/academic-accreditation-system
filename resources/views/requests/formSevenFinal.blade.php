<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير النهائي للجنة المقيمين والتقدير الكلي</title>
    <style>
        @import url("{{ asset('fonts/fonts.css') }}");

        :root {
            --primary-color: #1a3c5e;
            --secondary-color: #f8f9fa;
            --border-color: #2c3e50;
            --header-bg: #e9ecef;
            --row-bg-alt: #f8f9fa;
            --highlight-bg: #d1ecf1;
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

        .page-container {
            max-width: 297mm;
            min-height: 210mm;
            margin: 30px auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .report-header {
            text-align: right;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
        }

        .report-header.center {
            text-align: center;
        }

        .report-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary-color);
            display: inline-block;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            border: 1px solid var(--border-color);
            padding: 14px 15px;
            vertical-align: middle;
        }

        .info-table th {
            background: var(--header-bg);
            color: var(--primary-color);
            font-weight: 700;
            width: 35%;
            text-align: right;
            font-size: 16px;
        }

        .info-table td {
            text-align: right;
            font-size: 16px;
            font-weight: 500;
            color: #2c3e50;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            position: relative;
            padding-right: 15px;
        }

        .section-title::before {
            content: "";
            position: absolute;
            right: 0;
            top: 5px;
            bottom: 5px;
            width: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        .committee-table th {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-align: center;
            font-size: 16px;
        }

        .committee-table th.name-col {
            width: 40%;
        }

        .committee-table th.signature-col {
            width: 60%;
        }

        .committee-table td.name-cell {
            text-align: right;
            font-weight: 500;
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

        .main-table th.num-col {
            width: 80px;
        }

        .main-table th.standard-col {
            width: 35%;
            text-align: right;
            padding-right: 20px;
        }

        .main-table td.standard-name {
            text-align: right;
            padding-right: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .main-table tr.total-row {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            font-size: 16px;
        }

        .main-table tr.total-row td {
            border-color: #fff;
        }

        .main-table tbody tr:not(.total-row):nth-child(even) {
            background-color: var(--row-bg-alt);
        }

        .summary-table-wrapper {
            max-width: 600px;
            margin: 0 auto;
        }

        .summary-table th {
            background: var(--primary-color);
            color: #fff;
            width: 40%;
            text-align: right;
            padding-right: 20px;
            font-size: 16px;
        }

        .summary-table td {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            background: var(--highlight-bg);
            color: var(--primary-color);
        }

        /* ── Detailed Assessment Table (Section 2) ── */
        .detail-table .main-criterion-row td {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-align: right;
            font-size: 15px;
        }

        .detail-table thead th {
            background: var(--header-bg);
            color: var(--primary-color);
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            font-size: 15px;
        }

        .detail-table .num-col {
            text-align: center;
            vertical-align: middle;
            width: 55px;
            font-weight: bold;
        }

        .detail-table .name-col {
            width: 220px;
            font-weight: 500;
            vertical-align: top;
        }

        .detail-table .grade-col {
            text-align: center;
            vertical-align: middle;
            width: 75px;
            font-weight: bold;
            font-size: 16px;
        }

        .detail-table .text-col {
            vertical-align: top;
            color: #444;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .detail-table tr:not(.main-criterion-row):nth-child(even) {
            background-color: var(--row-bg-alt);
        }

        .detail-table .text-col ul {
            margin: 0;
            padding-right: 18px;
        }

        .detail-table .text-col li {
            margin-bottom: 3px;
        }

        .detail-table .text-col .empty-placeholder {
            color: #aaa;
            font-style: italic;
            font-size: 12px;
        }

        @media print {
            body {
                background: #fff;
            }

            .page-container {
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                height: auto;
            }

            th,
            td {
                border: 1pt solid #000;
                padding: 12px;
            }

            .main-table tr.total-row td {
                background: #d9d9d9 !important;
                color: #000 !important;
                border-color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .detail-table .main-criterion-row td {
                background: #d9d9d9 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead th,
            .info-table th,
            .committee-table th,
            .summary-table th {
                background: #f2f2f2 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-table td {
                background: #fff !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-title {
                color: #000 !important;
                border-bottom-color: #000 !important;
            }

            .section-title {
                color: #000 !important;
            }

            .section-title::before {
                background-color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }

        /* ── Sticky Top Bar Styling ── */
        .no-print-bar {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: #1a3c5e;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(26, 60, 94, 0.15);
        }
        .btn-primary:hover {
            background: #122b44;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    <!-- ── Interactive Sticky Navigation Bar ── -->
    <div class="no-print no-print-bar">
        <a href="{{ route('requests.stage_six.final_report.print', $accreditationRequest) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 6px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg>
            تحميل التقرير كـ PDF
        </a>
        <a href="{{ route('requests.stage_six.visit_report.show', $accreditationRequest) }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    <div class="page-container">
        <div class="report-header center">
            <h1 class="report-title">التقرير النهائي للجنة المقيمين</h1>
        </div>

        <div class="table-responsive">
            <table class="info-table">
                <tbody>
                    <tr>
                        <th id="label_request_number">رقم الطلب</th>
                        <td id="data_request_number">{{ $accreditationRequest->id }}</td>
                    </tr>
                    <tr>
                        <th id="label_program_name">اسم البرنامج</th>
                        <td id="data_program_name">{{ $program->program_name }}</td>
                    </tr>
                    <tr>
                        <th id="label_department_name">القسم</th>
                        <td id="data_department_name">{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <th id="label_college_name">الكلية</th>
                        <td id="data_college_name">{{ $college->name }}</td>
                    </tr>
                    <tr>
                        <th id="label_institution_name">المؤسسة التعليمية</th>
                        <td id="data_institution_name">{{ $university->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="section-title">أعضاء لجنة المقيمين</h2>
        <div class="table-responsive">
            <table class="committee-table">
                <thead>
                    <tr>
                        <th class="name-col" id="header_member_name">الاسم</th>
                        <th class="signature-col" id="header_member_signature">التوقيع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membersData as $member)
                        <tr>
                            <td class="name-cell">
                                {{ $member['name'] }}
                                @if($member['is_chair'])
                                    <span style="font-size: 12px; color: var(--primary-color); display: block;">(رئيس اللجنة)</span>
                                @endif
                            </td>
                            <td class="signature-cell">
                                <div class="signature-wrapper">
                                    @if($member['signature_path'] && \Illuminate\Support\Facades\Storage::exists($member['signature_path']))
                                        @php
                                            $svg = \Illuminate\Support\Facades\Storage::get($member['signature_path']);
                                        @endphp
                                        {!! $svg !!}
                                    @else
                                        <span style="color: #94a3b8; font-weight: 500; font-size: 12px; opacity: 0.7;">(لم يتم التوقيع بعد)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Section 2: Detailed Assessment Table ===== --}}
    <div class="page-container">
        <div class="report-header">
            <h1 class="report-title">جدول التقييم التفصيلي للمعايير</h1>
        </div>

        <div class="table-responsive">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th class="num-col">ت</th>
                        <th class="name-col">المعيار الفرعي</th>
                        <th class="grade-col">الدرجة</th>
                        <th>نقاط القوة</th>
                        <th>فرص التحسين</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailedStandards as $stdIndex => $standard)
                        {{-- Main Standard Row --}}
                        <tr class="main-criterion-row">
                            <td class="num-col">{{ $standard['number'] }}</td>
                            <td colspan="4">{{ $standard['name'] }}</td>
                        </tr>

                        {{-- Sub-Standard Rows --}}
                        @foreach($standard['subs'] as $subIndex => $sub)
                            <tr>
                                <td class="num-col">{{ $sub['number'] }}.{{ $stdIndex + 1 }}</td>
                                <td class="name-col">{{ $sub['name'] }}</td>
                                <td class="grade-col">
                                    @if($sub['average'] !== null)
                                        {{ number_format($sub['average'], 2) }}
                                    @else
                                        <span style="color:#aaa; font-size:12px;">—</span>
                                    @endif
                                </td>
                                <td class="text-col">
                                    @if(!empty($sub['strengths']))
                                        <ul>
                                            @foreach($sub['strengths'] as $point)
                                                <li>{{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="empty-placeholder">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                                <td class="text-col">
                                    @if(!empty($sub['improvements']))
                                        <ul>
                                            @foreach($sub['improvements'] as $point)
                                                <li>{{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="empty-placeholder">لا توجد ملاحظات</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="page-container">
        <div class="report-header">
            <h1 class="report-title">التقدير الكلي لبرنامج الاعتماد</h1>
        </div>

        <div class="table-responsive">
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="num-col">الرقم</th>
                        <th class="standard-col">المعيار</th>
                        <th id="header_sum_scores">مجموع الدرجات</th>
                        <th id="header_count_indicators">عدد المؤشرات</th>
                        <th id="header_average">المتوسط الحسابي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($standardsScores['standards'] as $index => $row)
                        <tr @if($row['has_null_indicators']) style="background-color: #ffe4e4;" @endif>
                            <td id="standard_num_{{ $index + 1 }}">{{ $index + 1 }}</td>
                            <td class="standard-name" id="standard_name_{{ $index + 1 }}">
                                {{ $row['name'] }}
                                @if($row['has_null_indicators'])
                                    <span style="display:block; font-size:11px; color:#c0392b; font-weight:600; margin-top:2px;">
                                        ⚠ يوجد مؤشرات غير مقيّمة
                                    </span>
                                @endif
                            </td>
                            <td id="standard_sum_{{ $index + 1 }}">
                                {{ $row['count'] > 0 ? $row['sum'] : '—' }}
                            </td>
                            <td id="standard_count_{{ $index + 1 }}">
                                {{ $row['count'] > 0 ? $row['count'] : '—' }}
                            </td>
                            <td id="standard_avg_{{ $index + 1 }}">
                                {{ $row['average'] !== null ? $row['average'] : '—' }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; padding-right: 20px;">المجموع الكلي</td>
                        <td id="total_sum_all">
                            {{ $standardsScores['total']['count'] > 0 ? $standardsScores['total']['sum'] : '—' }}
                        </td>
                        <td id="total_count_all">
                            {{ $standardsScores['total']['count'] > 0 ? $standardsScores['total']['count'] : '—' }}
                        </td>
                        <td id="total_average_all">
                            {{ $standardsScores['total']['average'] !== null ? $standardsScores['total']['average'] : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive summary-table-wrapper">
            <table class="summary-table">
                <tbody>
                    <tr>
                        <th id="label_final_grade">الدرجة النهائية</th>
                        <td id="value_final_grade">
                            {{ $standardsScores['final_grade'] ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th id="label_achieved_level">مستوى التحقق</th>
                        <td id="value_achieved_level">
                            {{ $standardsScores['achievement_level'] ?? '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>