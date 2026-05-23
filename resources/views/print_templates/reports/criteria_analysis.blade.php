@extends('print_templates.reports.layout')

@section('title', 'تقرير تحليل المعايير والمؤشرات')
@section('report_title', 'تقرير تحليل أداء المعايير والمؤشرات الأكاديمية')
@section('report_subtitle', 'تصنيف المعايير والمؤشرات حسب متوسط درجات تقييم المقيمين')

@section('content')

    {{-- Summary Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val">{{ $totalIndicatorsEvaluated }}</div>
            <div class="stat-lbl">مؤشرات مُقيَّمة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #2563eb;">{{ $standardScores->count() }}</div>
            <div class="stat-lbl">معايير شملها التقييم</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #16a34a;">{{ $totalEvaluationsCount }}</div>
            <div class="stat-lbl">إجمالي عمليات التقييم</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: {{ $overallAvgScore >= 3 ? '#16a34a' : ($overallAvgScore >= 2 ? '#d97706' : '#dc2626') }};">
                {{ $overallAvgScore }}
            </div>
            <div class="stat-lbl">متوسط الدرجات الكلي</div>
        </div>
    </div>

    {{-- Standards Ranking Table --}}
    <h3 style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 20px 0 8px; border-right: 4px solid #1e3a5f; padding-right: 8px;">
        أولاً: ترتيب المعايير حسب متوسط الدرجة
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 8%;">رقم المعيار</th>
                <th style="width: 47%;">اسم المعيار</th>
                <th style="width: 15%;">متوسط الدرجة</th>
                <th style="width: 12%;">عدد التقييمات</th>
                <th style="width: 13%;">المستوى</th>
            </tr>
        </thead>
        <tbody>
            @forelse($standardScores as $index => $std)
                @php
                    $avg = (float) $std->avg_score;
                    if ($avg >= 4) { $level = 'ممتاز'; $color = '#16a34a'; }
                    elseif ($avg >= 3) { $level = 'جيد'; $color = '#2563eb'; }
                    elseif ($avg >= 2) { $level = 'مقبول'; $color = '#d97706'; }
                    else { $level = 'ضعيف'; $color = '#dc2626'; }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-align: center;">{{ $std->number }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $std->name }}</td>
                    <td style="text-align: center;">
                        <span style="font-size: 15px; font-weight: 900; color: {{ $color }};">{{ $avg }}</span>
                        <div style="background: #e2e8f0; border-radius: 4px; height: 5px; margin-top: 4px;">
                            <div style="background: {{ $color }}; height: 5px; border-radius: 4px; width: {{ min(100, ($avg / 5) * 100) }}%;"></div>
                        </div>
                    </td>
                    <td style="text-align: center; color: #64748b; font-size: 10px;">{{ number_format($std->eval_count) }}</td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: {{ $color }}15; color: {{ $color }}; border: 1px solid {{ $color }}40; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;">
                            {{ $level }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد بيانات تقييم متاحة بعد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Top 10 Indicators --}}
    <h3 style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 24px 0 8px; border-right: 4px solid #16a34a; padding-right: 8px;">
        ثانياً: أعلى 10 مؤشرات درجةً (الأفضل أداءً)
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 8%;">رقم</th>
                <th style="width: 42%;">المؤشر</th>
                <th style="width: 20%;">المعيار الرئيسي</th>
                <th style="width: 12%;">متوسط</th>
                <th style="width: 13%;">عدد التقييمات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topIndicators as $index => $ind)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-align: center;">{{ $ind->number }}</td>
                    <td>{{ Str::limit($ind->name, 80) }}</td>
                    <td style="font-size: 9px; color: #64748b; text-align: center;">
                        {{ optional(optional($ind->subStandard)->standard)->number }}
                        -
                        {{ Str::limit(optional(optional($ind->subStandard)->standard)->name ?? '—', 25) }}
                    </td>
                    <td style="text-align: center; font-weight: 900; color: #16a34a; font-size: 14px;">{{ $ind->avg_score }}</td>
                    <td style="text-align: center; color: #64748b; font-size: 10px;">{{ $ind->eval_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد بيانات.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Bottom 10 Indicators --}}
    <h3 style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 24px 0 8px; border-right: 4px solid #dc2626; padding-right: 8px;">
        ثالثاً: أدنى 10 مؤشرات درجةً (الأضعف أداءً - تستحق الاهتمام)
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 8%;">رقم</th>
                <th style="width: 42%;">المؤشر</th>
                <th style="width: 20%;">المعيار الرئيسي</th>
                <th style="width: 12%;">متوسط</th>
                <th style="width: 13%;">عدد التقييمات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bottomIndicators as $index => $ind)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-align: center;">{{ $ind->number }}</td>
                    <td>{{ Str::limit($ind->name, 80) }}</td>
                    <td style="font-size: 9px; color: #64748b; text-align: center;">
                        {{ optional(optional($ind->subStandard)->standard)->number }}
                        -
                        {{ Str::limit(optional(optional($ind->subStandard)->standard)->name ?? '—', 25) }}
                    </td>
                    <td style="text-align: center; font-weight: 900; color: #dc2626; font-size: 14px;">{{ $ind->avg_score }}</td>
                    <td style="text-align: center; color: #64748b; font-size: 10px;">{{ $ind->eval_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد بيانات.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
