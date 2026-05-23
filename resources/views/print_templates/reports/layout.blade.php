<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'تقرير مجلس الاعتماد الأكاديمي')</title>
    @include('print_templates.fonts')
    <style>
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: white;
            color: #1e293b;
            margin: 0;
            padding: 0;
            direction: rtl;
            font-size: 13px;
            line-height: 1.6;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
            @bottom-center {
                content: "صفحة " counter(page) " من " counter(pages);
                font-family: 'Tajawal', sans-serif;
                font-size: 9px;
                color: #64748b;
            }
        }
        
        /* Header styling */
        .report-header {
            border-bottom: 2px solid #002546;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: table;
            width: 100%;
        }
        .header-logo-cell {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }
        .header-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .header-title-cell {
            display: table-cell;
            vertical-align: middle;
            padding-right: 15px;
            text-align: right;
        }
        .header-title-cell h1 {
            font-size: 18px;
            font-weight: 800;
            color: #002546;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .header-title-cell p {
            font-size: 11px;
            color: #c39f58;
            font-weight: 700;
            margin: 3px 0 0 0;
            padding: 0;
        }
        .header-meta-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            font-size: 10px;
            color: #64748b;
        }

        /* Title Area */
        .report-title-section {
            margin-bottom: 25px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 4px solid #c39f58;
            padding: 15px;
            border-radius: 8px;
        }
        .report-title-section h2 {
            font-size: 15px;
            font-weight: 700;
            color: #002546;
            margin: 0 0 5px 0;
        }
        .report-title-section p {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }

        /* KPIs and Stats */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px 0;
        }
        .stat-card {
            display: table-cell;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            width: 25%;
        }
        .stat-val {
            font-size: 20px;
            font-weight: 800;
            color: #002546;
            margin-bottom: 2px;
        }
        .stat-lbl {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
        }

        /* Tables styling */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: auto;
        }
        table.report-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.report-table th {
            background-color: #002546;
            color: white;
            font-weight: 700;
            font-size: 11px;
            padding: 8px 10px;
            border: 1px solid #002546;
            text-align: right;
        }
        table.report-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #334155;
        }
        table.report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 700;
            border-radius: 4px;
            text-align: center;
        }
        .badge-primary { background-color: #dbeafe; color: #1e40af; }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef9c3; color: #854d0e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-neutral { background-color: #f1f5f9; color: #475569; }

        /* Footer / Page numbering */
        .report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header -->
    <div class="report-header">
        <div class="header-logo-cell">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="header-logo" alt="Logo">
        </div>
        <div class="header-title-cell">
            <h1>مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</h1>
            <p>نظام الاعتماد الأكاديمي الموحد - الجمهورية اليمنية</p>
        </div>
        <div class="header-meta-cell">
            <div>تاريخ التقرير: {{ now()->format('Y/m/d') }}</div>
            <div>وقت التصدير: {{ now()->format('H:i') }}</div>
            <div>جهة التصدير: الأمانة العامة للمجلس</div>
        </div>
    </div>

    <!-- Title Section -->
    <div class="report-title-section">
        <h2>@yield('report_title')</h2>
        <p>@yield('report_subtitle')</p>
    </div>

    <!-- Main Content -->
    <div class="report-content">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="report-footer">
        هذا التقرير تم توليده تلقائياً من نظام الاعتماد الأكاديمي الموحد للأمانة العامة للمجلس.
    </div>

</body>
</html>
