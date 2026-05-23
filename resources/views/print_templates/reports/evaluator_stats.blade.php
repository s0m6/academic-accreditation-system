@extends('print_templates.reports.layout')

@section('title', 'تقرير بيانات وإحصائيات المقيمين')
@section('report_title', 'تقرير بيانات وإحصائيات خبراء ومقيمي الاعتماد')
@section('report_subtitle', 'سجل تفصيلي ببيانات مقيمي لجان التقييم الأكاديمي، تخصصاتهم، وحالتهم في النظام')

@section('content')

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card" style="width: 33.33%;">
            <div class="stat-val">{{ $totalEvaluatorsCount }}</div>
            <div class="stat-lbl">إجمالي المقيمين والخبراء</div>
        </div>
        <div class="stat-card" style="width: 33.33%;">
            <div class="stat-val" style="color: #16a34a;">{{ $activeEvaluatorsInCommittees }}</div>
            <div class="stat-lbl">مشاركون في لجان نشطة</div>
        </div>
        <div class="stat-card" style="width: 33.33%;">
            <div class="stat-val" style="color: #2563eb;">{{ $totalCitiesCount }}</div>
            <div class="stat-lbl">المدن والمحافظات الممثلة</div>
        </div>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">الاسم</th>
                <th style="width: 25%;">التخصص (العام / الدقيق)</th>
                <th style="width: 15%;">الرتبة الأكاديمية</th>
                <th style="width: 20%;">الجهة الأكاديمية الحالية</th>
                <th style="width: 15%; text-align: center;">العضوية في اللجان</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluators as $index => $eval)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $eval->user->name ?? '—' }}</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $eval->user->email ?? '—' }} | {{ $eval->user->mobile ?? '—' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $eval->general_specialty ?? '—' }}</div>
                        <div style="font-size: 10px; color: #475569; margin-top: 2px;">{{ $eval->detailed_specialty ?? '—' }}</div>
                    </td>
                    <td>
                        {{ match($eval->academic_rank ?? '') {
                            'professor' => 'أستاذ (بروفيسور)',
                            'associate_professor' => 'أستاذ مشارك',
                            'assistant_professor' => 'أستاذ مساعد',
                            default => $eval->academic_rank ?? '—'
                        } }}
                    </td>
                    <td>
                        <div>{{ $eval->currentUniversity->name ?? '—' }}</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">المدينة: {{ $eval->city->city_name ?? '—' }}</div>
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #1e40af;">
                        {{ $eval->committee_memberships_count ?? 0 }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد بيانات مطابقة لمحددات البحث الحالية.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
