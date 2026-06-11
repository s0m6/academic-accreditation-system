@extends('print_templates.reports.layout')

@section('title', 'تقرير مقارنة أداء الجامعات')
@section('report_title', 'تقرير مقارنة الأداء الأكاديمي للجامعات')
@section('report_subtitle', 'تصنيف وترتيب الجامعات بناءً على تقييمات الجودة والمعايير الأكاديمية')

@section('content')

    {{-- Summary Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val">{{ $universityScores->count() }}</div>
            <div class="stat-lbl">إجمالي الجامعات المقارنة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #2563eb;">{{ $universityScores->where('type', 'government')->count() }}</div>
            <div class="stat-lbl">جامعات حكومية</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #10b981;">{{ $universityScores->where('type', 'private')->count() }}</div>
            <div class="stat-lbl">جامعات أهلية / خاصة</div>
        </div>
        <div class="stat-card">
            @php
                $overallAvg = $universityScores->where('avg_score', '>', 0)->avg('avg_score');
                $overallAvg = $overallAvg ? round($overallAvg, 2) : 0;
            @endphp
            <div class="stat-val" style="color: #6366f1;">{{ $overallAvg }}</div>
            <div class="stat-lbl">المعدل العام لجميع التقييمات</div>
        </div>
    </div>

    {{-- Comparison Table --}}
    <h3 style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 20px 0 8px; border-right: 4px solid #1e3a5f; padding-right: 8px;">
        قائمة الترتيب الأكاديمي للجامعات وفق درجات الجودة
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">اسم الجامعة</th>
                <th style="width: 15%;">نوع الجامعة</th>
                <th style="width: 15%;">طلبات الاعتماد</th>
                <th style="width: 15%;">مؤشرات مُقيَّمة</th>
                <th style="width: 15%;">متوسط درجة الجودة (5)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($universityScores as $index => $uni)
                @php
                    $avg = (float) $uni->avg_score;
                    if ($avg >= 4) { $color = '#16a34a'; }
                    elseif ($avg >= 3) { $color = '#2563eb'; }
                    elseif ($avg >= 2) { $color = '#d97706'; }
                    else { $color = '#dc2626'; }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $uni->name }}</td>
                    <td style="text-align: center;">
                        @if($uni->type == 'government')
                            <span class="badge badge-primary">حكومية</span>
                        @else
                            <span class="badge badge-success">أهلية / خاصة</span>
                        @endif
                    </td>
                    <td style="text-align: center; color: #475569; font-weight: bold;">{{ $uni->requests_count }}</td>
                    <td style="text-align: center; color: #64748b;">{{ $uni->evaluated_indicators_count }}</td>
                    <td style="text-align: center;">
                        @if($uni->evaluated_indicators_count > 0)
                            <span style="font-size: 14px; font-weight: 900; color: {{ $color }};">{{ $avg }}</span>
                            <div style="background: #e2e8f0; border-radius: 4px; height: 5px; margin-top: 4px;">
                                <div style="background: {{ $color }}; height: 5px; border-radius: 4px; width: {{ ($avg / 5) * 100 }}%;"></div>
                            </div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">لا توجد تقييمات</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد بيانات متاحة بعد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
