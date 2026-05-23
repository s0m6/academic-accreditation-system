@extends('print_templates.reports.layout')

@section('title', 'التقرير الإحصائي الشامل لمجلس الاعتماد')
@section('report_title', 'التقرير الإحصائي الشامل لمجلس الاعتماد الأكاديمي')
@section('report_subtitle', 'إحصائيات عامة ومؤشرات أداء حول مسار طلبات الاعتماد، الجامعات، والخبراء في النظام')

@section('content')

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card" style="width: 20%;">
            <div class="stat-val">{{ $totalUniversities }}</div>
            <div class="stat-lbl">الجامعات المسجلة</div>
        </div>
        <div class="stat-card" style="width: 20%;">
            <div class="stat-val">{{ $totalPrograms }}</div>
            <div class="stat-lbl">البرامج الأكاديمية</div>
        </div>
        <div class="stat-card" style="width: 20%;">
            <div class="stat-val">{{ $totalRequests }}</div>
            <div class="stat-lbl">إجمالي طلبات الاعتماد</div>
        </div>
        <div class="stat-card" style="width: 20%;">
            <div class="stat-val" style="color: #16a34a;">{{ $activeRequests }}</div>
            <div class="stat-lbl">الطلبات النشطة حالياً</div>
        </div>
        <div class="stat-card" style="width: 20%;">
            <div class="stat-val" style="color: #2563eb;">{{ $totalCertificates }}</div>
            <div class="stat-lbl">الشهادات الممنوحة</div>
        </div>
    </div>

    <div style="display: table; width: 100%; margin-bottom: 25px; border-spacing: 15px 0;">
        <!-- Left Column: Stage Distribution -->
        <div style="display: table-cell; width: 50%; vertical-align: top; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
            <h3 style="font-size: 13px; color: #002546; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin: 0 0 12px 0; font-weight: 700;">توزيع الطلبات حسب مراحل الاعتماد</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 700;">
                        <th style="text-align: right; padding: 6px 0;">المرحلة الأكاديمية</th>
                        <th style="text-align: center; padding: 6px 0; width: 25%;">عدد الطلبات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stagesDistribution as $stage => $count)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 8px 0; color: #334155;">
                                {{ match($stage) {
                                    'stage_one' => 'الطلب الأولي (1)',
                                    'stage_two' => 'البيانات الأساسية (2)',
                                    'stage_three' => 'تقرير الدراسة الذاتية (3)',
                                    'stage_four' => 'اختيار لجنة التقييم (4)',
                                    'stage_five' => 'تحديد جدول الزيارة (5)',
                                    'stage_six' => 'تقارير التقييم الأولية (6)',
                                    'stage_seven' => 'توصيات اللجنة والردود (7)',
                                    'stage_eight' => 'تقارير التقييم الختامية (8)',
                                    'stage_nine' => 'القرار النهائي والشهادة (9)',
                                    default => str_replace('_', ' ', $stage)
                                } }}
                            </td>
                            <td style="padding: 8px 0; text-align: center; font-weight: bold; color: #0f172a;">{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Right Column: Top Universities and Specialties -->
        <div style="display: table-cell; width: 50%; vertical-align: top; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
            <h3 style="font-size: 13px; color: #002546; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin: 0 0 12px 0; font-weight: 700;">أكثر الجامعات طلباً للاعتماد الأكاديمي</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 20px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 700;">
                        <th style="text-align: right; padding: 6px 0;">الجامعة</th>
                        <th style="text-align: center; padding: 6px 0; width: 25%;">الطلبات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topUniversities as $uni)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 6px 0; color: #334155; font-weight: bold;">{{ $uni->name }}</td>
                            <td style="padding: 6px 0; text-align: center; font-weight: bold; color: #1e40af;">{{ $uni->requests_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 10px; color: #64748b;">لا توجد طلبات مسجلة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <h3 style="font-size: 13px; color: #002546; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin: 0 0 12px 0; font-weight: 700;">أكثر التخصصات العلمية للمقيمين والخبراء</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 700;">
                        <th style="text-align: right; padding: 6px 0;">التخصص العام</th>
                        <th style="text-align: center; padding: 6px 0; width: 25%;">الخبراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSpecialties as $spec)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 6px 0; color: #334155;">{{ $spec->general_specialty }}</td>
                            <td style="padding: 6px 0; text-align: center; font-weight: bold; color: #16a34a;">{{ $spec->count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 10px; color: #64748b;">لا توجد تخصصات مسجلة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Breakdown Summary -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-top: 15px;">
        <h3 style="font-size: 12px; color: #002546; margin: 0 0 10px 0; font-weight: 700;">ملاحظة وتوجيهات الأمانة العامة للمجلس</h3>
        <p style="font-size: 10px; color: #475569; margin: 0; line-height: 1.5;">
            تظهر البيانات أعلاه كشفاً إحصائياً دقيقاً بالعمليات الجارية في النظام. يوصى بمراجعة وتيرة إنجاز طلبات الاعتماد في المراحل 3 (تقرير الدراسة الذاتية) والمرحلة 6 (تقارير التقييم الأولية) لمعالجة أي تراكم، وحث المقيمين والخبراء على تعبئة التقارير وتحديث بياناتهم بشكل دوري.
        </p>
    </div>

@endsection
