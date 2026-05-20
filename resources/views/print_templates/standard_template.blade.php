<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <title>تقرير المعيار: {{ $standard->name }}</title>
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
            --score-5: #059669;
            --score-4: #10b981;
            --score-3: #f59e0b;
            --score-2: #f97316;
            --score-1: #ef4444;
        }

        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: white;
            color: #333;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 15mm;
            size: a4;
        }

        .container { width: 100%; }

        .report-header {
            border-bottom: 4px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            margin: 0;
        }

        .header-title p {
            font-size: 13px;
            color: #666;
            margin: 5px 0 0 0;
        }

        .header-meta {
            background: var(--gold-light);
            border: 1px solid var(--gold);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--primary-color);
        }

        .standard-banner {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .std-number {
            background: var(--gold);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
        }

        .std-name { font-size: 18px; font-weight: 700; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: right;
        }

        thead th {
            background: #f1f5f9;
            color: var(--primary-color);
            font-weight: 700;
        }

        .sub-std-row {
            background: #f8fafc;
            font-weight: 700;
            color: var(--primary-color);
        }



        .comment-section {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .comment-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 10px;
            border-right: 4px solid var(--gold);
            padding-right: 10px;
        }

        .comment-content {
            font-size: 12px;
            line-height: 1.6;
            color: #334155;
        }

        .points-list {
            margin: 0;
            padding-right: 20px;
        }

        .points-list li {
            margin-bottom: 5px;
        }

        .bg-green { background-color: #f0fdf4; border-color: #bbf7d0; }
        .bg-red { background-color: #fef2f2; border-color: #fecaca; }
        .bg-amber { background-color: #fffbeb; border-color: #fef3c7; }

        .page-break { page-break-before: always; }
        tr { page-break-inside: avoid; }

        .footer {
            margin-top: 30px;
            border-top: 2px solid var(--primary-color);
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-title">
                <h1>تقرير تقييم المعايير</h1>
                <p>المرحلة الثالثة - التقييم الذاتي للبرنامج</p>
            </div>
            <div class="header-meta">
                <strong>الجامعة:</strong> {{ $university->name }}<br>
                <strong>الكلية:</strong> {{ $college->name }}
            </div>
        </div>

        <!-- Standard Title -->
        <div class="standard-banner">
            <div class="std-number">{{ $standard->number }}</div>
            <div class="std-name">{{ $standard->name }}</div>
        </div>

        <!-- Detailed Indicators Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">الرقم</th>
                    <th>المؤشر / التوصيف</th>
                    <th style="width: 15%; text-align: center;">الدرجة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($standard->subStandards as $sub)
                    <tr class="sub-std-row">
                        <td colspan="3">
                            المعيار الفرعي {{ $standard->number }}-{{ $sub->number }}: {{ $sub->name }}
                        </td>
                    </tr>
                    @foreach($sub->indicators as $ind)
                        @php
                            $eval = $indicatorEvaluations->get($ind->id);
                            $score = $eval ? $eval->score : 0;
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: 700;">{{ $ind->number }}</td>
                            <td>
                                {{ $ind->name }}
                                @php
                                    $evidences = $evidencesByIndicatorId[$ind->id] ?? collect();
                                @endphp
                                @if($evidences->isNotEmpty())
                                    <div style="margin-top: 8px; font-size: 9px; color: #475569;">
                                        <div style="font-weight: 800; margin-bottom: 4px; color: #1e293b; border-bottom: 1px solid #e2e8f0; display: inline-block; padding-bottom: 1px;">قائمة الأدلة:</div>
                                        <ul style="list-style: none; padding: 0; margin: 0;">
                                            @foreach($evidences as $ev)
                                                <li style="margin-bottom: 2px; padding-right: 12px; position: relative; line-height: 1.4;">
                                                    <span style="position: absolute; right: 0; top: 0; color: #94a3b8;">•</span>
                                                    {{ $ev->file_name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700; font-size: 13px; color: #000;">
                                {{ $score == 0 ? 'غير مطابق' : $score }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- Comments and Analysis Sections -->
        @php
            $comments = $formData['standard_comments'][$standard->id] ?? [];
            
            $strengths = $comments['strengths'] ?? [];
            if (is_string($strengths)) $strengths = json_decode($strengths, true) ?? [];
            
            $improvements = $comments['improvements'] ?? [];
            if (is_string($improvements)) $improvements = json_decode($improvements, true) ?? [];
            
            $priorities = $comments['priorities'] ?? [];
            if (is_string($priorities)) $priorities = json_decode($priorities, true) ?? [];
        @endphp

        <!-- Program Comment -->
        @if(!empty($comments['program_comment']))
            <div class="comment-section">
                <div class="comment-title">تعليق البرنامج</div>
                <div class="comment-content">
                    {{ $comments['program_comment'] }}
                </div>
            </div>
        @endif

        <!-- Strengths -->
        @if(!empty($strengths))
            <div class="comment-section bg-green">
                <div class="comment-title" style="border-right-color: var(--score-5);">جوانب القوة</div>
                <div class="comment-content">
                    <ul class="points-list">
                        @foreach($strengths as $point)
                            @if(!empty(trim($point))) <li>{{ $point }}</li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Improvements -->
        @if(!empty($improvements))
            <div class="comment-section bg-red">
                <div class="comment-title" style="border-right-color: var(--score-1);">جوانب تحتاج تحسين</div>
                <div class="comment-content">
                    <ul class="points-list">
                        @foreach($improvements as $point)
                            @if(!empty(trim($point))) <li>{{ $point }}</li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Priorities -->
        @if(!empty($priorities))
            <div class="comment-section bg-amber">
                <div class="comment-title" style="border-right-color: var(--score-3);">أولويات التحسين</div>
                <div class="comment-content">
                    <ul class="points-list">
                        @foreach($priorities as $point)
                            @if(!empty(trim($point))) <li>{{ $point }}</li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            نظام الاعتماد الأكاديمي الموحد | {{ now()->format('Y/m/d') }}
        </div>
    </div>
</body>
</html>
