<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دليل: {{ $evidenceName }}</title>
    @include('print_templates.fonts')
    <style>
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: white;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @page { margin: 0; size: a4; }
        .cover-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a3c5e 0%, #0f2340 100%);
            text-align: center;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }
        .accent-bar { width: 80px; height: 5px; background: #b8860b; border-radius: 3px; margin-bottom: 40px; }
        .icon-circle {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(184,134,11,0.2);
            border: 2px solid #b8860b;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 30px;
            font-size: 36px;
        }
        .label { font-size: 13px; font-weight: 600; color: #94a3b8; letter-spacing: 2px; margin-bottom: 16px; text-transform: uppercase; }
        .title {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.4;
            max-width: 480px;
            margin: 0 0 20px 0;
        }
        .divider { width: 200px; height: 1px; background: rgba(255,255,255,0.15); margin-bottom: 20px; }
        .context { font-size: 12px; color: #94a3b8; }
        .sub-context { font-size: 11px; color: #64748b; margin-top: 8px; }
        .bottom-accent { position: absolute; bottom: 40px; width: 80px; height: 5px; background: #b8860b; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="cover-wrapper">
        <div class="accent-bar"></div>
        <div class="icon-circle">📄</div>
        <div class="label">وثيقة دليل</div>
        <h1 class="title">{{ $evidenceName }}</h1>
        <div class="divider"></div>
        <div class="context">المعيار الرئيسي {{ $standardNumber }}: {{ $standardName }}</div>
        <div class="sub-context">المؤشر {{ $indicatorNumber }}</div>
        <div class="bottom-accent"></div>
    </div>
</body>
</html>
