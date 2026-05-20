<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نموذج 10 - القرار النهائي</title>
  <style>
    @import url("{{ asset('fonts/fonts.css') }}");

    :root {
      --primary-color: #1a3c5e;
      --secondary-color: #f8f9fa;
      --border-color: #2c3e50;
      --header-bg: #e9ecef;
      --row-bg-alt: #f8f9fa;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Tajawal', Arial, sans-serif;
      direction: rtl;
      background: #e2e8f0;
      color: #333;
      font-size: 15px;
      line-height: 1.8;
    }

    .page-container {
      max-width: 297mm;
      min-height: 210mm;
      margin: 30px auto;
      padding: 40px;
      background: #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .report-header {
      text-align: center;
      margin-bottom: 30px;
      border-bottom: 2px solid var(--primary-color);
      padding-bottom: 15px;
    }

    .report-title {
      font-size: 24px;
      font-weight: 800;
      color: var(--primary-color);
      margin-bottom: 10px;
    }

    .official-title {
      font-size: 18px;
      font-weight: 700;
      color: #555;
    }

    .intro-text {
      margin-bottom: 20px;
      font-weight: 500;
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
      background: #fff;
    }

    th, td {
      border: 1px solid var(--border-color);
      padding: 12px 15px;
      text-align: right;
      vertical-align: middle;
    }

    thead th {
      background: var(--header-bg);
      color: var(--primary-color);
      font-weight: 700;
      text-align: center;
      font-size: 15px;
    }

    .label-cell {
      background: var(--header-bg);
      color: var(--primary-color);
      font-weight: 700;
      width: 25%;
    }

    .data-cell {
      font-weight: 500;
      color: #2c3e50;
    }

    .inline-score {
      font-weight: bold;
      color: var(--primary-color);
      font-size: 18px;
      border-bottom: 2px solid var(--primary-color);
      padding: 0 10px;
    }

    .paragraph-section {
      margin: 25px 0;
      font-size: 16px;
      text-align: justify;
      line-height: 2;
    }

    .decision-box {
      border: 1px solid var(--border-color);
      margin-top: 20px;
      border-radius: 4px;
      overflow: hidden;
    }

    .decision-header {
      background: var(--primary-color);
      color: #fff;
      padding: 12px 15px;
      font-weight: 700;
      font-size: 16px;
    }

    .decision-content {
      padding: 20px;
      background: #fff;
    }

    .decision-group-title {
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 15px;
      font-size: 15px;
    }

    .decision-group-title.mt {
      margin-top: 25px;
    }

    .checkbox-group label {
      display: block;
      padding: 10px 15px;
      margin-bottom: 8px;
      background: var(--row-bg-alt);
      border: 1px solid #dce0e5;
      border-radius: 4px;
      font-weight: 500;
    }

    .checkbox-group input[type="radio"] {
      margin-left: 12px;
      transform: scale(1.2);
    }

    .checkbox-group label.active {
        background: #e7f3ff;
        border-color: #3b82f6;
        color: #1e40af;
        font-weight: 700;
    }

    hr.divider {
      border: none;
      border-top: 1px solid #dce0e5;
      margin: 25px 0;
    }

    .footer-note {
      margin-top: 50px;
      text-align: center;
      font-weight: 700;
      color: var(--primary-color);
      border-top: 2px solid var(--primary-color);
      padding-top: 20px;
      font-size: 16px;
    }

    .signature-wrapper {
        width: 100%;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .signature-wrapper svg {
        max-height: 50px;
        max-width: 100%;
    }

    @media print {
      body {
        background: #fff;
      }
      .page-container {
        margin: 0;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
        max-width: 100%;
      }
      th, td {
        border: 1pt solid #000;
        padding: 8px;
      }
      .label-cell, thead th {
        background: #f2f2f2 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .decision-header {
        background: #d9d9d9 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .checkbox-group label {
        border: 1pt solid #000;
        background: #fff !important;
      }
      .checkbox-group label.active {
          background: #f0f0f0 !important;
          border: 2pt solid #000 !important;
      }
    }
  </style>
</head>

<body>
    <!-- Global Preloader -->
    @include('public.partials.preloader')


  <div class="page-container">

    <!-- الترويسة -->
    <div class="report-header">
      <h1 class="report-title">نموذج رقم (10)</h1>
      <div class="official-title">القرار النهائي وتوصيات لجنة المقيمين بخصوص اعتماد البرنامج</div>
    </div>

    <!-- المقدمة -->
    <div class="intro-text">
      <p>الأستاذ الدكتور / رئيس مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
      <p>تحية طيبة وبعد ...</p>
      <p><strong>الموضوع:</strong> التوصية النهائية بخصوص الطلب حسب البيانات أدناه:</p>
    </div>

    <!-- جدول بيانات الطلب -->
    <div class="table-responsive">
      <table>
        <tbody>
          <tr>
            <td class="label-cell">رقم الطلب</td>
            <td class="data-cell">{{ $accreditationRequest->id }}</td>
          </tr>
          <tr>
            <td class="label-cell">اسم البرنامج</td>
            <td class="data-cell">{{ $program->program_name }}</td>
          </tr>
          <tr>
            <td class="label-cell">القسم</td>
            <td class="data-cell">{{ $department->name }}</td>
          </tr>
          <tr>
            <td class="label-cell">الكلية</td>
            <td class="data-cell">{{ $college->name }}</td>
          </tr>
          <tr>
            <td class="label-cell">المؤسسة التعليمية</td>
            <td class="data-cell">{{ $university->name }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- نص التوصية -->
    <div class="paragraph-section">
      بناءً على تقرير الزيارة الميدانية نموذج رقم (5) والتقرير النهائي للجنة المقيمين نموذج رقم (7)، وبعد إجراء التعديلات اللازمة عليه على ضوء رد المؤسسة التعليمية على توصيات اللجنة، والتي تبين أن الدرجة المتحققة للبرنامج حسب معايير مجلس الاعتماد الأكاديمي هي 
      <span class="inline-score">{{ number_format($grandAverage, 2) }}</span> 
      من أصل (5) درجات فإننا نوصي بالآتي:
    </div>

    <!-- صندوق القرار والتوصيات -->
    <div class="decision-box">
      <div class="decision-header">التوصية النهائية لاعتماد البرنامج</div>
      <div class="decision-content">
        
        <div class="decision-group-title">الموافقة على منح البرنامج الاعتماد الأكاديمي، بمستوى:</div>
        <div class="checkbox-group">
          <label class="{{ $achievementLevel === 'محقق' ? 'active' : '' }}">
            <input type="radio" disabled {{ $achievementLevel === 'محقق' ? 'checked' : '' }}> محقق (متابعة الاعتماد بعد ثلاث سنوات)
          </label>
          <label class="{{ $achievementLevel === 'محقق بإتقان' ? 'active' : '' }}">
            <input type="radio" disabled {{ $achievementLevel === 'محقق بإتقان' ? 'checked' : '' }}> محقق بإتقان (متابعة الاعتماد بعد أربع سنوات)
          </label>
          <label class="{{ $achievementLevel === 'محقق بامتياز' ? 'active' : '' }}">
            <input type="radio" disabled {{ $achievementLevel === 'محقق بامتياز' ? 'checked' : '' }}> محقق بتميز (متابعة الاعتماد بعد خمس سنوات)
          </label>
        </div>

        <hr class="divider">

        <div class="decision-group-title mt">عدم الموافقة على منح البرنامج الاعتماد الأكاديمي، لأن البرنامج بمستوى:</div>
        <div class="checkbox-group">
          <label class="{{ $achievementLevel === 'غير محقق' ? 'active' : '' }}">
            <input type="radio" disabled {{ $achievementLevel === 'غير محقق' ? 'checked' : '' }}> غير محقق (يمنح مهلة سنتين لإعادة التقدم)
          </label>
          <label class="{{ $achievementLevel === 'محقق جزئياً' ? 'active' : '' }}">
            <input type="radio" disabled {{ $achievementLevel === 'محقق جزئياً' ? 'checked' : '' }}> محقق جزئياً (يمنح مهلة سنة لإعادة التقدم)
          </label>
        </div>

      </div>
    </div>

    <!-- جدول التوقيعات -->
    <div class="table-responsive" style="margin-top: 40px;">
      <table>
        <thead>
          <tr>
            <th style="width: 20%;">الصفة</th>
            <th style="width: 25%;">الاسم</th>
            <th style="width: 35%;">التوقيع</th>
            <th style="width: 20%;">التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @php
            $chair = collect($membersData)->firstWhere('is_chair', true);
            $members = collect($membersData)->where('is_chair', false);
          @endphp

          @foreach($members as $m)
          <tr>
            <td class="label-cell" style="text-align: center;">عضو اللجنة</td>
            <td class="data-cell">{{ $m['name'] }}</td>
            <td>
                @if($m['signature_path'] && \Illuminate\Support\Facades\Storage::exists($m['signature_path']))
                    <div class="signature-wrapper">
                        {!! \Illuminate\Support\Facades\Storage::get($m['signature_path']) !!}
                    </div>
                @else
                    <div style="text-align: center; color: #999; font-size: 12px;">(لم يتم التوقيع)</div>
                @endif
            </td>
            <td class="data-cell" style="text-align: center;">{{ $m['signed_at'] ? $m['signed_at']->format('Y/m/d') : '—' }}</td>
          </tr>
          @endforeach

          @if($chair)
          <tr>
            <td class="label-cell" style="text-align: center;">رئيس اللجنة</td>
            <td class="data-cell">{{ $chair['name'] }}</td>
            <td>
                @if($chair['signature_path'] && \Illuminate\Support\Facades\Storage::exists($chair['signature_path']))
                    <div class="signature-wrapper">
                        {!! \Illuminate\Support\Facades\Storage::get($chair['signature_path']) !!}
                    </div>
                @else
                    <div style="text-align: center; color: #999; font-size: 12px;">(لم يتم التوقيع)</div>
                @endif
            </td>
            <td class="data-cell" style="text-align: center;">{{ $chair['signed_at'] ? $chair['signed_at']->format('Y/m/d') : '—' }}</td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>

    <!-- تذييل الصفحة -->
    <div class="footer-note">
      مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي
    </div>

  </div>

</body>
</html>