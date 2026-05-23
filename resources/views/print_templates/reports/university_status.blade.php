@extends('print_templates.reports.layout')

@section('title', 'تقرير حالة طلبات الاعتماد للجامعات')
@section('report_title', 'تقرير حالة ومراحل طلبات الاعتماد للجامعات')
@section('report_subtitle', 'كشف تفصيلي بطلبات الاعتماد للبرامج الأكاديمية ومراحلها الحالية')

@section('content')

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val">{{ $totalRequestsCount }}</div>
            <div class="stat-lbl">إجمالي الطلبات</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #16a34a;">{{ $activeRequestsCount }}</div>
            <div class="stat-lbl">الطلبات النشطة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #2563eb;">{{ $completedRequestsCount }}</div>
            <div class="stat-lbl">الطلبات المكتملة</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #ea580c;">{{ $draftRequestsCount }}</div>
            <div class="stat-lbl">الطلبات المسودة</div>
        </div>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">الجامعة</th>
                <th style="width: 25%;">البرنامج الأكاديمي</th>
                <th style="width: 15%;">الكلية / القسم</th>
                <th style="width: 18%;">المرحلة الحالية</th>
                <th style="width: 12%;">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $index => $req)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $req->program->department->college->university->name ?? '—' }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $req->program->program_name ?? '—' }}</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                            المستوى الدراسي: 
                            {{ match($req->program->degree_level ?? '') {
                                'diploma' => 'دبلوم',
                                'bachelor' => 'بكالوريوس',
                                'master' => 'ماجستير',
                                'phd' => 'دكتوراه',
                                default => $req->program->degree_level ?? 'غير محدد'
                            } }}
                        </div>
                    </td>
                    <td>
                        <div>{{ $req->program->department->college->name ?? '—' }}</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $req->program->department->name ?? '—' }}</div>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ match($req->current_stage) {
                                'stage_one' => 'الطلب الأولي (1)',
                                'stage_two' => 'البيانات الأساسية (2)',
                                'stage_three' => 'تقرير الدراسة الذاتية (3)',
                                'stage_four' => 'اختيار لجنة التقييم (4)',
                                'stage_five' => 'تحديد جدول الزيارة (5)',
                                'stage_six' => 'تقارير التقييم الأولية (6)',
                                'stage_seven' => 'توصيات اللجنة والردود (7)',
                                'stage_eight' => 'تقارير التقييم الختامية (8)',
                                'stage_nine' => 'القرار النهائي والشهادة (9)',
                                default => str_replace('_', ' ', $req->current_stage)
                            } }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($req->request_status) {
                                'Active' => 'badge-success',
                                'draft' => 'badge-neutral',
                                'completed' => 'badge-primary',
                                'canceled' => 'badge-danger',
                                default => 'badge-neutral'
                            };
                            $statusText = match($req->request_status) {
                                'Active' => 'نشط',
                                'draft' => 'مسودة',
                                'completed' => 'مكتمل',
                                'canceled' => 'ملغي',
                                default => $req->request_status
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
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
