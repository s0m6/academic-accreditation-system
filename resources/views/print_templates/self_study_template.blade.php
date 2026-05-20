<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <title>نموذج الدراسة الذاتية - {{ $program->program_name }}</title>
    
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

        .value-box {
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            background: #ffffff;
            min-height: 40px;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        /* Adjustments for Section 3 boxes */
        .sec3-box {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            background: #fff;
            min-height: 30px;
        }
    </style>
</head>
<body class="bg-white">

    <div class="w-full">
        <!-- ── Header ── -->
        <div class="report-header pb-4 mb-6 flex justify-between items-start">
            <div class="report-header-left">
                <h1 class="text-3xl font-extrabold text-[#1a3c5e] mb-1">نموذج الدراسة الذاتية</h1>
                <p class="text-sm text-gray-500 font-medium">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي - المرحلة الثالثة</p>
            </div>
            <div class="report-meta bg-[#f5e9c8] border border-[#b8860b] rounded-lg px-4 py-3 text-sm text-[#1a3c5e] leading-relaxed">
                <div class="font-bold">الجامعة: {{ $university->name }}</div>
                <div class="font-bold">الكلية: {{ $college->name }}</div>
                <div class="mt-1 text-xs text-gray-600">تاريخ الاستخراج: {{ now()->format('Y/m/d') }}</div>
            </div>
        </div>

        <!-- ── SECTION 1 ── -->
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">١</span>
            <span class="font-bold text-base">  الدراسة الذاتية – المعلومات العامة</span>
        </div>

        <!-- General Info -->
        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">المعلومات العامة</h3>
        <table class="mb-6 text-sm">
            <tbody>
                <tr>
                    <td class="label-cell">اسم المؤسسة / الجامعة</td>
                    <td class="font-bold">{{ $university->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">اسم رئيس المؤسسة / الجامعة</td>
                    <td>{{ $university->president_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">اسم رئيس فريق المراجعة الداخلية</td>
                    <td>{{ $formData['general']['review_team_head'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">تاريخ التقييم / المراجعة</td>
                    <td>{{ $formData['general']['review_date'] ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Program Identification (Cleaned layout) -->
        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">البيانات التعريفية بالبرنامج</h3>
        <table class="mb-6 text-sm">
            <tbody>
                <tr>
                    <td class="label-cell">الكلية</td>
                    <td>{{ $college->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">القسم العلمي</td>
                    <td>{{ $department->name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">اسم البرنامج</td>
                    <td class="font-bold text-[#1a3c5e]">{{ $program->program_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">تاريخ تأسيس البرنامج</td>
                    <td>{{ $program->program_details['establishment_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">عنوان الموقع الإلكتروني للبرنامج</td>
                    <td dir="ltr">{{ $program->program_details['website_url'] ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Coordinator Data -->
        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">بيانات منسق إعداد التقرير</h3>
        <table class="mb-6 text-sm">
            <tbody>
                <tr>
                    <td class="label-cell">الاسم</td>
                    <td>{{ $formData['program']['coordinator_name'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">الصفة</td>
                    <td>{{ $formData['program']['coordinator_title'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">البريد الإلكتروني</td>
                    <td dir="ltr">{{ $formData['program']['coordinator_email'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">رقم الهاتف</td>
                    <td dir="ltr">{{ $formData['program']['coordinator_phone'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">تاريخ إعداد التقرير</td>
                    <td>{{ $formData['program']['report_date'] ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Executive Summary (Moved to Page 2) -->
        <div class="page-break"></div>
        <div class="mb-6">
            <h4 class="text-sm font-bold text-[#1a3c5e] mb-2">الملخص التنفيذي للنتيجة الإجمالية لتقييم معايير الاعتماد البرامجي</h4>
            <div class="value-box italic bg-slate-50 min-h-[100px]">
                {{ $formData['program']['executive_summary'] ?? 'لم يتم إدخال الملخص التنفيذي.' }}
            </div>
        </div>

        <!-- Program Profile -->
        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">ملف البرنامج</h3>
        
        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">رسالة البرنامج</h4>
            <div class="value-box">{{ $formData['profile']['program_mission'] ?? '—' }}</div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">أهداف البرنامج</h4>
            <div class="value-box">
                @php 
                    $objectives = $formData['profile']['program_objectives_list'] ?? [];
                    if (is_string($objectives)) $objectives = json_decode($objectives, true) ?? [];
                @endphp
                <ul class="list-disc pr-5">
                @forelse($objectives as $obj)
                    @if(!empty(trim($obj))) <li>{{ $obj }}</li> @endif
                @empty
                    <li>—</li>
                @endforelse
                </ul>
            </div>
        </div>

        <table class="mb-6 text-sm">
            <thead>
                <tr>
                    <th colspan="2">نظام البرنامج وساعاته ومقرراته</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label-cell">نظام البرنامج</td>
                    <td>{{ ($formData['profile']['program_system'] ?? '') == 'semester' ? 'نظام فصلي' : (($formData['profile']['program_system'] ?? '') == 'annual' ? 'نظام سنوي' : (($formData['profile']['program_system'] ?? '') == 'modules' ? 'نظام وحدات' : '—')) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">عدد الساعات المعتمدة للبرنامج</td>
                    <td>{{ $formData['profile']['credit_hours'] ?? '—' }} ساعة</td>
                </tr>
                <tr>
                    <td class="label-cell">عدد المقررات</td>
                    <td>{{ $formData['profile']['courses_total'] ?? '—' }} مقرر</td>
                </tr>
                <tr>
                    <td class="label-cell">عدد الطلبة المقيدين (ذكور / إناث)</td>
                    <td>{{ $formData['profile']['male_students_count'] ?? 0 }} ذكور / {{ $formData['profile']['female_students_count'] ?? 0 }} إناث</td>
                </tr>
            </tbody>
        </table>

        <table class="mb-6 text-sm">
            <thead>
                <tr>
                    <th colspan="2">تواريخ اعتماد التوصيف الحالي للبرنامج من مجالس الجامعة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label-cell">مجلس القسم</td>
                    <td>{{ $formData['profile']['dept_council_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">مجلس الكلية</td>
                    <td>{{ $formData['profile']['college_council_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">المجلس الأكاديمي</td>
                    <td>{{ $formData['profile']['academic_council_date'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">مجلس الجامعة</td>
                    <td>{{ $formData['profile']['university_council_date'] ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">موجز عن تاريخ البرنامج</h4>
            <div class="value-box">{{ $formData['profile']['program_history'] ?? '—' }}</div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">التغيرات في البيئة الداخلية والخارجية للبرنامج</h4>
            <div class="value-box">{{ $formData['profile']['env_changes'] ?? '—' }}</div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">ترتيبات إجراء الدراسة الذاتية</h4>
            <div class="value-box">{{ $formData['profile']['self_study_arrangements'] ?? '—' }}</div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">منهجية المقارنة الداخلية والخارجية</h4>
            <div class="value-box">{{ $formData['profile']['comparison_methodology'] ?? '—' }}</div>
        </div>

        <!-- Tables -->
        <div class="page-break"></div>
        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">الجداول والبيانات</h3>

        <h4 class="text-xs font-bold text-slate-600 mb-2">١. جدول تقديرات الخريجين (آخر ٣ سنوات)</h4>
        <table class="mb-6 text-[10px] text-center">
            <thead>
                <tr>
                    <th class="text-right">العام الأكاديمي</th>
                    <th>ممتاز</th>
                    <th>جيد جداً</th>
                    <th>جيد</th>
                    <th>مقبول</th>
                    <th>ضعيف</th>
                    <th class="bg-gray-100 font-bold">المجموع</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $years = ['last_year', 'prev_year', 'two_years_ago'];
                    $grades = ['excellent', 'very_good', 'good', 'pass', 'fail'];
                @endphp
                @foreach($years as $y)
                    @php
                        $total = 0;
                        foreach($grades as $g) { $total += (int)($formData['tables']["ft_graduates_{$y}_{$g}"] ?? 0); }
                    @endphp
                    <tr>
                        <td class="text-right font-bold bg-slate-50">{{ $formData['tables']["ft_graduates_{$y}_year_display"] ?? '—' }}</td>
                        @foreach($grades as $g)
                            <td>{{ $formData['tables']["ft_graduates_{$y}_{$g}"] ?? 0 }}</td>
                        @endforeach
                        <td class="font-bold bg-gray-100">{{ $total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4 class="text-xs font-bold text-slate-600 mb-2">٢. البحث العلمي والأنشطة البحثية (العام السابق)</h4>
        <table class="mb-6 text-[10px]">
            <thead>
                <tr>
                    <th class="w-12 text-center">م</th>
                    <th>نوع النشاط العلمي</th>
                    <th class="w-24 text-center">العدد</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $researchItems = [
                        'intl_journals_indexed' => 'البحوث العلمية المنشورة في مجلات دولية متخصصة ومفهرسة',
                        'arabic_journals_reviewed' => 'البحوث العلمية المنشورة في مجلات عربية محكمة',
                        'faculty_publications' => 'المؤلفات العلمية لأعضاء هيئة التدريس',
                        'faculty_textbooks' => 'الكتب المنهجية لأعضاء هيئة التدريس',
                        'faculty_translated_books' => 'الكتب المترجمة لأعضاء هيئة التدريس',
                        'master_theses_discussed' => 'رسائل الماجستير التي تمت مناقشتها',
                        'phd_dissertations_discussed' => 'رسائل الدكتوراه التي تمت مناقشتها',
                        'conferences_workshops_organized' => 'المؤتمرات والندوات وورش العمل المنظمة'
                    ];
                @endphp
                @foreach($researchItems as $key => $label)
                    <tr>
                        <td class="text-center bg-slate-50 font-bold">{{ $loop->iteration }}</td>
                        <td>{{ $label }}</td>
                        <td class="text-center font-bold">{{ $formData['tables']["res_{$key}_count"] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4 class="text-xs font-bold text-slate-600 mb-2">٣. جدول المرافق التعليمية والخدمية</h4>
        <table class="mb-6 text-[10px] text-center">
            <thead>
                <tr>
                    <th class="text-right">نوع المرفق</th>
                    <th>العدد</th>
                    <th>المساحة الإجمالية (م²)</th>
                    <th>متوسط عدد المستخدمين</th>
                    <th>متوسط ساعات التشغيل</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $facilityTypes = [
                      'classrooms' => 'قاعات دراسية',
                      'spec_labs' => 'مختبرات تخصصية',
                      'comp_labs' => 'مختبرات الحاسوب',
                      'library' => 'المكتبة',
                      'admin_offices' => 'المكاتب الإدارية',
                      'student_lounges' => 'استراحات الطلاب',
                      'sports' => 'المرافق الرياضية'
                    ];
                @endphp
                @foreach($facilityTypes as $key => $label)
                    <tr>
                        <td class="text-right font-bold bg-slate-50">{{ $label }}</td>
                        <td>{{ $formData['tables']["fac_{$key}_count"] ?? 0 }}</td>
                        <td>{{ $formData['tables']["fac_{$key}_area"] ?? 0 }}</td>
                        <td>{{ $formData['tables']["fac_{$key}_students"] ?? 0 }}</td>
                        <td>{{ $formData['tables']["fac_{$key}_hours"] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ── SECTION 3 ── -->
        <div class="page-break"></div>
        <div class="section-header rounded flex items-center gap-3 px-4 py-2 mb-3 mt-6">
            <span class="gold-circle w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">٢</span>
            <span class="font-bold text-base">  التقييمات المستقلة والنتائج</span>
        </div>

        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">التقييمات المستقلة</h3>
        
        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">الإجراءات المتبعة للحصول على التقييم المستقل</h4>
            <div class="sec3-box text-xs">
                @php $procs = $formData['evaluations']['evaluation_procedures'] ?? []; if(is_string($procs)) $procs = json_decode($procs, true) ?? []; @endphp
                <ul class="list-disc pr-5"> @forelse($procs as $p) <li>{{ $p }}</li> @empty <li>—</li> @endforelse </ul>
            </div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">توصيات المقيمين المستقلين</h4>
            <div class="sec3-box text-xs">
                @php $recs = $formData['evaluations']['evaluator_recommendations'] ?? []; if(is_string($recs)) $recs = json_decode($recs, true) ?? []; @endphp
                <ul class="list-disc pr-5"> @forelse($recs as $r) <li>{{ $r }}</li> @empty <li>—</li> @endforelse </ul>
            </div>
        </div>

        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1">الإجراءات المتخذة حيال التوصيات</h4>
            <div class="sec3-box text-xs">
                @php $acts = $formData['evaluations']['actions_taken'] ?? []; if(is_string($acts)) $acts = json_decode($acts, true) ?? []; @endphp
                <ul class="list-disc pr-5"> @forelse($acts as $a) <li>{{ $a }}</li> @empty <li>—</li> @endforelse </ul>
            </div>
        </div>

        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">النتائج</h3>
        
        <div class="mb-4">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1 font-bold">جوانب النجاح (القوة)</h4>
            <div class="sec3-box text-xs border-green-200 bg-green-50/30 text-green-800">
                @php $wins = $formData['results']['success_aspects'] ?? []; if(is_string($wins)) $wins = json_decode($wins, true) ?? []; @endphp
                <ul class="list-disc pr-5"> @forelse($wins as $w) <li>{{ $w }}</li> @empty <li>—</li> @endforelse </ul>
            </div>
        </div>

        <div class="mb-6">
            <h4 class="text-xs font-bold text-[#1a3c5e] mb-1 font-bold">جوانب التحسين ذات الأولوية</h4>
            <div class="sec3-box text-xs border-amber-200 bg-amber-50/30 text-amber-800">
                @php $improve = $formData['results']['priority_improvements'] ?? []; if(is_string($improve)) $improve = json_decode($improve, true) ?? []; @endphp
                <ul class="list-disc pr-5"> @forelse($improve as $i) <li>{{ $i }}</li> @empty <li>—</li> @endforelse </ul>
            </div>
        </div>

        <h3 class="text-sm font-bold text-[#1a3c5e] mb-2 px-2 border-r-4 border-gold">المقترحات التنفيذية</h3>
        <table class="mb-12 text-[10px] text-center">
            <thead>
                <tr>
                    <th class="w-8">ت</th>
                    <th class="text-right">التوصية / المقترح</th>
                    <th>مسؤول التنفيذ</th>
                    <th>توقيت التنفيذ</th>
                    <th>الموارد المطلوبة</th>
                </tr>
            </thead>
            <tbody>
                @php $proposals = $formData['tables']['proposals'] ?? []; @endphp
                @forelse($proposals as $p)
                    <tr>
                        <td class="bg-slate-50 font-bold">{{ $loop->iteration }}</td>
                        <td class="text-right font-medium">{{ $p['recommendation'] ?? '—' }}</td>
                        <td>{{ $p['responsible'] ?? '—' }}</td>
                        <td>{{ $p['timeline'] ?? '—' }}</td>
                        <td>{{ $p['resources'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-gray-400">لا يوجد مقترحات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- ── Authorization ── -->
        <div class="mt-12 pt-6 border-t-2 border-[#1a3c5e] grid grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-sm font-bold text-[#1a3c5e] mb-1">منسق إعداد التقرير</div>
                <div class="text-xs text-gray-500 mb-6">{{ $formData['program']['coordinator_name'] ?? '—' }}</div>
                <div class="border-t border-gray-300 pt-1 text-xs font-bold text-[#1a3c5e]">التوقيع</div>
            </div>
            <div class="flex justify-center items-center">
                <div class="seal-box w-20 h-20 rounded-full flex items-center justify-center text-[10px] text-center font-bold">الختم الرسمي</div>
            </div>
            <div class="text-center">
                <div class="text-sm font-bold text-[#1a3c5e] mb-1">رئيس المؤسسة التعليمية</div>
                <div class="text-xs text-gray-500 mb-6">{{ $university->president_name }}</div>
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