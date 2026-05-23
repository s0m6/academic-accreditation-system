@extends('print_templates.reports.layout')

@section('title', 'تقرير القرارات والشهادات الصادرة')
@section('report_title', 'تقرير قرارات وشهادات الاعتماد الأكاديمي الصادرة')
@section('report_subtitle', 'سجل تفصيلي بقرارات الاعتماد الأكاديمي وشهادات الجودة الصادرة والمنتهية الصلاحية')

@section('content')

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val">{{ $totalDecisionsCount }}</div>
            <div class="stat-lbl">إجمالي القرارات</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #16a34a;">{{ $approvedDecisionsCount }}</div>
            <div class="stat-lbl">قرارات معتمدة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #dc2626;">{{ $rejectedDecisionsCount }}</div>
            <div class="stat-lbl">قرارات غير معتمدة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #2563eb;">{{ $activeCertificatesCount }}</div>
            <div class="stat-lbl">الشهادات النشطة حالياً</div>
        </div>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">البرنامج والجامعة</th>
                <th style="width: 20%;">قرار الاعتماد</th>
                <th style="width: 15%;">تاريخ القرار</th>
                <th style="width: 20%;">الشهادة والصلاحية</th>
                <th style="width: 15%;">موقع القرار</th>
            </tr>
        </thead>
        <tbody>
            @forelse($decisions as $index => $dec)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $dec->accreditationRequest->program->program_name ?? '—' }}</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                            {{ $dec->accreditationRequest->program->department->college->university->name ?? '—' }}
                        </div>
                    </td>
                    <td>
                        @php
                            $approved = $dec->isApproved();
                            $statusClass = $approved ? 'badge-success' : 'badge-danger';
                            $meta = \App\Models\FinalDecision::$decisionMeta[$dec->decision_type] ?? [];
                            $label = $meta['label'] ?? $dec->decision_type;
                            $followup = $meta['followup'] ?? '';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $label }}</span>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $followup }}</div>
                    </td>
                    <td>
                        {{ $dec->issued_at ? $dec->issued_at->format('Y-m-d') : '—' }}
                    </td>
                    <td>
                        @if($dec->certificate)
                            <div style="font-weight: bold; color: #1e40af;">رقم: {{ $dec->certificate->certificate_number }}</div>
                            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                                ينتهي في: 
                                @if(isset($dec->certificate->certificate_data['expires_at']))
                                    {{ $dec->certificate->certificate_data['expires_at'] }}
                                @else
                                    —
                                @endif
                            </div>
                            <div style="margin-top: 3px;">
                                @if($dec->certificate->isValid())
                                    <span class="badge badge-success" style="font-size: 8px; padding: 1px 4px;">سارية المفعول</span>
                                @else
                                    <span class="badge badge-danger" style="font-size: 8px; padding: 1px 4px;">منتهية / غير نشطة</span>
                                @endif
                            </div>
                        @else
                            <div style="color: #64748b; font-style: italic;">لا توجد شهادة</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 10px;">{{ $dec->issuedBy->name ?? '—' }}</div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $dec->issuedBy->email ?? '—' }}</div>
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
