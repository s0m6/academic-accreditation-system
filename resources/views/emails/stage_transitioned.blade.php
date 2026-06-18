<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>تحديث حالة طلب الاعتماد</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #2563eb; margin-top: 0; text-align: center;">نظام مجلس الاعتماد الأكاديمي</h2>
        
        <p style="font-size: 16px;">السلام عليكم ورحمة الله وبركاته،</p>
        
        @if($isFinalDecision)
            <p style="font-size: 15px;">نود إشعاركم بأنه قد <strong>تم إصدار القرار النهائي</strong> لطلب الاعتماد الخاص بالبرنامج التالي:</p>
        @else
            <p style="font-size: 15px;">نود إشعاركم بأن طلب الاعتماد قد <strong>انتقل إلى مرحلة جديدة</strong> وفقاً للتفاصيل أدناه:</p>
        @endif

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #e2e8f0;">
            <p style="margin: 0 0 10px 0;"><strong>الجامعة:</strong> {{ $accreditationRequest->program->department->college->university->name ?? '—' }}</p>
            <p style="margin: 0 0 10px 0;"><strong>البرنامج:</strong> {{ $accreditationRequest->program->program_name ?? '—' }}</p>
            
            @if($isFinalDecision)
                @php
                    $decision = $accreditationRequest->finalDecision;
                @endphp
                <p style="margin: 0 0 10px 0;"><strong>القرار النهائي:</strong> <span style="color: {{ $decision && $decision->isApproved() ? '#16a34a' : '#dc2626' }}; font-weight: bold;">{{ $decision ? $decision->decisionLabel() : '—' }}</span></p>
                @if($decision && $decision->notes)
                    <p style="margin: 0;"><strong>ملاحظات:</strong> {{ $decision->notes }}</p>
                @endif
            @else
                @php
                    $stages = [
                        'stage_one' => 'طلب الاعتماد الأولي',
                        'stage_two' => 'البيانات الأساسية',
                        'stage_three' => 'تقرير الدراسة الذاتية',
                        'stage_four' => 'اختيار لجنة التقييم',
                        'stage_five' => 'تحديد جدول الزيارة',
                        'stage_six' => 'تقارير نتائج التقييم(الأولية)',
                        'stage_seven' => 'توصيات اللجنة والرد عليها',
                        'stage_eight' => 'تقارير نتائج التقييم(الختامية)',
                        'stage_nine' => 'القرار النهائي',
                    ];
                    $newStageLabel = $stages[$newStage] ?? $newStage;
                @endphp
                <p style="margin: 0;"><strong>المرحلة الحالية:</strong> <span style="color: #2563eb; font-weight: bold;">{{ $newStageLabel }}</span></p>
            @endif
        </div>

        <p style="font-size: 15px; text-align: center;">لمتابعة الطلب وتفاصيله، يرجى الضغط على الزر أدناه للدخول إلى المنصة:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            @php
                $targetStage = $isFinalDecision ? 'stage_nine' : ($newStage ?? 'stage_one');
                $actionUrl = route('requests.stage', [$accreditationRequest, $targetStage]);
            @endphp
            <a href="{{ $actionUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">عرض تفاصيل الطلب</a>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="color: #94a3b8; font-size: 12px; text-align: center;">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
    </div>
</body>
</html>
