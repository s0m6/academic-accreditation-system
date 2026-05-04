<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير النهائي للجنة المقيمين والتقدير الكلي</title>
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

        .committee-table td.signature-cell {
            height: 120px;
            background: #fdfdfd;
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
            max-height: 100px;
            max-width: 100%;
            width: auto !important;
            height: auto !important;
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
        }
    </style>
</head>

<body>

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
                                @if($member['signature_path'] && \Illuminate\Support\Facades\Storage::exists($member['signature_path']))
                                    @php
                                        $svg = \Illuminate\Support\Facades\Storage::get($member['signature_path']);
                                    @endphp
                                    <div class="signature-wrapper">
                                        {!! $svg !!}
                                    </div>
                                @else
                                    <div class="signature-wrapper">
                                        <span style="color: #94a3b8; font-weight: 500; font-size: 14px; opacity: 0.7;">(لم يتم التوقيع بعد)</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
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
                    {{-- Standard rows will go here --}}
                    <tr>
                        <td id="standard_num_1">1</td>
                        <td class="standard-name" id="standard_name_1">الرسالة والأهداف ومخرجات التعلم</td>
                        <td id="standard_sum_1"></td>
                        <td id="standard_count_1"></td>
                        <td id="standard_avg_1"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_2">2</td>
                        <td class="standard-name" id="standard_name_2">إدارة البرنامج الأكاديمي</td>
                        <td id="standard_sum_2"></td>
                        <td id="standard_count_2"></td>
                        <td id="standard_avg_2"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_3">3</td>
                        <td class="standard-name" id="standard_name_3">المنهج الدراسي</td>
                        <td id="standard_sum_3"></td>
                        <td id="standard_count_3"></td>
                        <td id="standard_avg_3"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_4">4</td>
                        <td class="standard-name" id="standard_name_4">مرافق وتجهيزات البرنامج</td>
                        <td id="standard_sum_4"></td>
                        <td id="standard_count_4"></td>
                        <td id="standard_avg_4"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_5">5</td>
                        <td class="standard-name" id="standard_name_5">الطلبة</td>
                        <td id="standard_sum_5"></td>
                        <td id="standard_count_5"></td>
                        <td id="standard_avg_5"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_6">6</td>
                        <td class="standard-name" id="standard_name_6">هيئة التدريس</td>
                        <td id="standard_sum_6"></td>
                        <td id="standard_count_6"></td>
                        <td id="standard_avg_6"></td>
                    </tr>
                    <tr>
                        <td id="standard_num_7">7</td>
                        <td class="standard-name" id="standard_name_7">التقييم والتحسين المستمر</td>
                        <td id="standard_sum_7"></td>
                        <td id="standard_count_7"></td>
                        <td id="standard_avg_7"></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; padding-right: 20px;">المجموع الكلي</td>
                        <td id="total_sum_all"></td>
                        <td id="total_count_all"></td>
                        <td id="total_average_all"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive summary-table-wrapper">
            <table class="summary-table">
                <tbody>
                    <tr>
                        <th id="label_final_grade">الدرجة النهائية</th>
                        <td id="value_final_grade"></td>
                    </tr>
                    <tr>
                        <th id="label_achieved_level">المستوى المتحقق</th>
                        <td id="value_achieved_level"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const indicatorsCount = [2, 2, 3, 2, 3, 3, 3];

            let totalSum = 0;
            let totalCount = 0;

            for (let i = 1; i <= 7; i++) {
                let count = indicatorsCount[i - 1];
                let randomAvg = (Math.random() * 3 + 2).toFixed(2);
                let sum = (randomAvg * count).toFixed(2);

                document.getElementById("standard_count_" + i).textContent = count;
                document.getElementById("standard_sum_" + i).textContent = sum;
                document.getElementById("standard_avg_" + i).textContent = randomAvg;

                totalSum += parseFloat(sum);
                totalCount += count;
            }

            document.getElementById("total_count_all").textContent = totalCount;
            document.getElementById("total_sum_all").textContent = totalSum.toFixed(2);

            let finalAvg = (totalSum / totalCount).toFixed(2);
            document.getElementById("total_average_all").textContent = finalAvg;

            let finalGrade = Math.round(finalAvg);
            document.getElementById("value_final_grade").textContent = finalGrade;

            let achievedLevel = "";
            if (finalGrade >= 5) {
                achievedLevel = "متميز";
            } else if (finalGrade == 4) {
                achievedLevel = "متقن";
            } else if (finalGrade == 3) {
                achievedLevel = "مقبول";
            } else {
                achievedLevel = "غير مكتمل";
            }

            document.getElementById("value_achieved_level").textContent = achievedLevel;
        });
    </script>

</body>

</html>