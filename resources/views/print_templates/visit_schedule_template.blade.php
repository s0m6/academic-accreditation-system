<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جدول الزيارة الميدانية - {{ $program->program_name }}</title>
    
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a3c5e;
            --secondary-color: #f8f9fa;
            --border-color: #2c3e50;
            --header-bg: #e9ecef;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 10px 12px;
            font-size: 12px;
        }

        thead th {
            background-color: var(--header-bg);
            color: var(--primary-color);
            font-weight: 800;
            text-align: center;
        }

        .day-header {
            background-color: #f1f5f9;
            color: var(--primary-color);
            font-weight: 800;
            font-size: 14px;
        }

        .page-break {
            page-break-before: always;
        }
        
        tr {
            page-break-inside: avoid;
        }

        .seal-box {
            border: 2px dashed var(--gold);
            background-color: var(--gold-light);
            color: var(--gold);
        }
    </style>
</head>
<body class="bg-white">

    <div class="w-full">
        <!-- ── Header ── -->
        <div class="report-header pb-4 mb-8 flex justify-between items-start">
            <div class="report-header-left">
                <h1 class="text-3xl font-extrabold text-[#1a3c5e] mb-1">جدول الزيارة الميدانية</h1>
                <p class="text-sm text-gray-500 font-medium">متطلبات الاعتماد الأكاديمي - المرحلة الخامسة</p>
            </div>
            <div class="report-meta bg-[#f5e9c8] border border-[#b8860b] rounded-lg px-4 py-3 text-sm text-[#1a3c5e] leading-relaxed">
                <div class="font-bold">الجامعة: {{ $university->name }}</div>
                <div class="font-bold">البرنامج: {{ $program->program_name }}</div>
                <div class="mt-1 text-xs text-gray-600">تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</div>
            </div>
        </div>

        @if(isset($scheduleData['days']) && is_array($scheduleData['days']))
            @foreach($scheduleData['days'] as $index => $day)
                @if(!empty($day['rows']))
                    <div class="mb-8">
                        <!-- ── Day Section Header ── -->
                        <div class="section-header rounded flex items-center justify-between px-4 py-2.5 mb-0">
                            <div class="flex items-center gap-3">
                                <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">{{ $index + 1 }}</span>
                                <span class="font-bold text-base">{{ $day['label'] ?? ('اليوم ' . ($index + 1)) }}</span>
                            </div>
                            <div class="text-sm font-bold opacity-90">
                                التاريخ: {{ $day['date'] ?? '—' }}
                            </div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 8%">ت</th>
                                    <th style="width: 22%">التوقيت</th>
                                    <th style="width: 45%">المهمة / النشاط</th>
                                    <th style="width: 25%">التوضيح / ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($day['rows'] as $rowIndex => $row)
                                    <tr>
                                        <td class="text-center font-bold bg-gray-50">{{ $rowIndex + 1 }}</td>
                                        <td class="text-center font-bold" dir="ltr">{{ $row['time'] ?? '—' }}</td>
                                        <td class="font-medium leading-relaxed">{{ $row['task'] ?? '—' }}</td>
                                        <td class="text-gray-600 italic">{{ $row['notes'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        @else
            <div class="py-20 text-center text-gray-500 italic">
                لا توجد بيانات متاحة لجدول الزيارة.
            </div>
        @endif

        <!-- ── Authorization ── -->
        <div class="mt-20 pt-10 border-t-2 border-[#1a3c5e] flex justify-center">
            <div class="flex items-center gap-32">
                <!-- Signature Area -->
                <div class="flex flex-col items-center gap-3">
                    <div class="text-lg font-bold text-[#1a3c5e]">توقيع رئيس المجلس</div>
                    <div class="w-72 border-b-2 border-dashed border-gray-400 mt-8"></div>
                </div>
                
                <!-- Seal Area (Empty Space) -->
                <div class="flex flex-col items-center gap-3">
                    <div class="text-lg font-bold text-[#1a3c5e]">الختم</div>
                    <div class="w-32 h-32 border border-dashed border-gray-200 rounded-lg"></div>
                </div>
            </div>
        </div>

        <!-- ── Footer ── -->
        <div class="mt-16 pt-4 border-t-2 border-[#1a3c5e] text-center text-[10px] text-gray-500">
            نظام الاعتماد الأكاديمي الموحد | تم استخراج هذا الجدول آلياً بتاريخ {{ now()->format('Y/m/d') }}
        </div>
    </div>

</body>
</html>
