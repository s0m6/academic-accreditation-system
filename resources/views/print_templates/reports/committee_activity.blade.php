@extends('print_templates.reports.layout')

@section('title', 'تقرير نشاط لجان التقييم')
@section('report_title', 'تقرير نشاط لجان التقييم ومتابعة المقيمين')
@section('report_subtitle', 'سجل تفصيلي بحالة اللجان الحالية، البرامج الخاضعة للتقييم، ورؤساء وأعضاء اللجان')

@section('content')

    {{-- Summary Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val">{{ $committees->count() }}</div>
            <div class="stat-lbl">إجمالي لجان التقييم</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #d97706;">{{ $committees->where('status', 'forming')->count() }}</div>
            <div class="stat-lbl">لجان قيد التشكيل</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #16a34a;">{{ $committees->where('status', 'approved')->count() }}</div>
            <div class="stat-lbl">لجان معتمدة / نشطة</div>
        </div>
        <div class="stat-card">
            @php
                $totalMembers = $committees->sum('members_count');
            @endphp
            <div class="stat-val" style="color: #2563eb;">{{ $totalMembers }}</div>
            <div class="stat-lbl">إجمالي المقيمين المشاركين</div>
        </div>
    </div>

    {{-- Committees Table --}}
    <h3 style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 20px 0 8px; border-right: 4px solid #1e3a5f; padding-right: 8px;">
        تفاصيل وسجل لجان التقييم الأكاديمي
    </h3>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">البرنامج الأكاديمي</th>
                <th style="width: 25%;">الجامعة المستهدفة</th>
                <th style="width: 20%;">رئيس اللجنة</th>
                <th style="width: 12%;">عدد الأعضاء</th>
                <th style="width: 13%;">الحالة الحالية</th>
            </tr>
        </thead>
        <tbody>
            @forelse($committees as $index => $com)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $com->program_name ?? '—' }}</td>
                    <td>{{ $com->university_name ?? '—' }}</td>
                    <td style="color: #3b82f6; font-weight: bold;">{{ $com->chair_name ?? '—' }}</td>
                    <td style="text-align: center; color: #475569; font-weight: bold;">{{ $com->members_count }}</td>
                    <td style="text-align: center;">
                        @if($com->status == 'approved')
                            <span class="badge badge-success">نشطة / معتمدة</span>
                        @else
                            <span class="badge badge-warning">قيد التشكيل</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">لا توجد لجان تقييم مسجلة بعد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
