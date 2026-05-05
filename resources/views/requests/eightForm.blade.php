<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطاب توصيات لجنة المقيمين</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');

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
            max-width: 210mm; /* A4 Portrait */
            min-height: 297mm;
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
            font-size: 24px;
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
            padding: 12px 15px;
            vertical-align: middle;
        }

        .info-table th {
            background: var(--header-bg);
            color: var(--primary-color);
            font-weight: 700;
            width: 35%;
            text-align: right;
            font-size: 14px;
        }

        .info-table td {
            text-align: right;
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
        }

        /* Detailed Assessment Table */
        .detail-table .main-criterion-row td {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-align: right;
            font-size: 14px;
        }

        .detail-table thead th {
            background: var(--header-bg);
            color: var(--primary-color);
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
        }

        .detail-table .num-col {
            text-align: center;
            vertical-align: middle;
            width: 50px;
            font-weight: bold;
        }

        .detail-table .name-col {
            width: 200px;
            font-weight: 500;
            vertical-align: top;
        }

        .detail-table .grade-col {
            text-align: center;
            vertical-align: middle;
            width: 70px;
            font-weight: bold;
            font-size: 15px;
        }

        .detail-table .text-col {
            vertical-align: top;
            color: #444;
            font-size: 13px;
            line-height: 1.6;
        }

        .detail-table tr:not(.main-criterion-row):nth-child(even) {
            background-color: var(--row-bg-alt);
        }

        .detail-table .text-col ul {
            margin: 0;
            padding-right: 15px;
        }

        .detail-table .text-col li {
            margin-bottom: 3px;
        }

        .detail-table .text-col .empty-placeholder {
            color: #aaa;
            font-style: italic;
            font-size: 11px;
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
                padding: 10px;
            }

            .detail-table .main-criterion-row td {
                background: #d9d9d9 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead th,
            .info-table th {
                background: #f2f2f2 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-title {
                color: #000 !important;
                border-bottom-color: #000 !important;
            }
        }
    </style>
</head>

<body>

    <div class="page-container">
        <div class="report-header center">
            <h1 class="report-title">خطاب توصيات لجنة المقيمين</h1>
        </div>

        {{-- Section 1: Info Table --}}
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

        {{-- Section 2: Detailed Assessment Table (NO STRENGTHS COLUMN) --}}
        <div class="table-responsive">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th class="num-col">ت</th>
                        <th class="name-col">المعيار الفرعي</th>
                        <th class="grade-col">الدرجة</th>
                        {{-- Points of Strength Removed --}}
                        <th>فرص التحسين</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailedStandards as $stdIndex => $standard)
                        {{-- Main Standard Row --}}
                        <tr class="main-criterion-row">
                            <td class="num-col">{{ $standard['number'] }}</td>
                            <td colspan="3">{{ $standard['name'] }}</td>
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
                                {{-- Strengths Column Removed --}}
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

</body>

</html>
