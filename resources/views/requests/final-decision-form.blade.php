<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نموذج 10 - القرار النهائي</title>
  <style>

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

    input[type="text"] {
      width: 100%;
      border: none;
      background: transparent;
      font-family: 'Tajawal', Arial, sans-serif;
      font-size: 15px;
      color: #333;
    }
    
    input[type="text"]:focus {
      outline: none;
    }

    .inline-input {
      width: 80px;
      border: none;
      border-bottom: 2px dashed var(--primary-color);
      text-align: center;
      font-weight: bold;
      color: var(--primary-color);
      font-size: 16px;
      background: transparent;
      font-family: 'Tajawal', Arial, sans-serif;
    }
    
    .inline-input:focus {
      outline: none;
      border-bottom-color: #c19a3f;
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
      cursor: pointer;
      transition: background 0.2s, border-color 0.2s;
    }

    .checkbox-group label:hover {
      background: #e9ecef;
      border-color: var(--primary-color);
    }

    .checkbox-group input[type="radio"] {
      margin-left: 12px;
      transform: scale(1.2);
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
    }
  </style>
</head>

<body>

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
            <td><input type="text" id="requestNumber"></td>
          </tr>
          <tr>
            <td class="label-cell">اسم البرنامج</td>
            <td><input type="text" id="programName"></td>
          </tr>
          <tr>
            <td class="label-cell">القسم</td>
            <td><input type="text" id="departmentName"></td>
          </tr>
          <tr>
            <td class="label-cell">الكلية</td>
            <td><input type="text" id="collegeName"></td>
          </tr>
          <tr>
            <td class="label-cell">المؤسسة التعليمية</td>
            <td><input type="text" id="institutionName"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- نص التوصية -->
    <div class="paragraph-section">
      بناءً على تقرير الزيارة الميدانية نموذج رقم (5) والتقرير النهائي للجنة المقيمين نموذج رقم (7)، وبعد إجراء التعديلات اللازمة عليه على ضوء رد المؤسسة التعليمية على توصيات اللجنة، والتي تبين أن الدرجة المتحققة للبرنامج حسب معايير مجلس الاعتماد الأكاديمي هي 
      ( <input type="number" min="0" max="5" step="0.1" class="inline-input" id="evaluationScore"> ) 
      من أصل (5) درجات فإننا نوصي بالآتي:
    </div>

    <!-- صندوق القرار والتوصيات -->
    <div class="decision-box">
      <div class="decision-header">التوصية النهائية لاعتماد البرنامج</div>
      <div class="decision-content">
        
        <div class="decision-group-title">الموافقة على منح البرنامج الاعتماد الأكاديمي، بمستوى:</div>
        <div class="checkbox-group">
          <label><input type="radio" name="decision"> محقق (متابعة الاعتماد بعد ثلاث سنوات)</label>
          <label><input type="radio" name="decision"> محقق بإتقان (متابعة الاعتماد بعد أربع سنوات)</label>
          <label><input type="radio" name="decision"> محقق بتميز (متابعة الاعتماد بعد خمس سنوات)</label>
        </div>

        <hr class="divider">

        <div class="decision-group-title mt">عدم الموافقة على منح البرنامج الاعتماد الأكاديمي، لأن البرنامج بمستوى:</div>
        <div class="checkbox-group">
          <label><input type="radio" name="decision"> غير محقق (يمنح مهلة سنتين لإعادة التقدم)</label>
          <label><input type="radio" name="decision"> محقق جزئياً (يمنح مهلة سنة لإعادة التقدم)</label>
        </div>

      </div>
    </div>

    <!-- جدول التوقيعات (محدث) -->
    <div class="table-responsive" style="margin-top: 40px;">
      <table>
        <thead>
          <tr>
            <th style="width: 20%;">الصفة</th>
            <th style="width: 20%;">الاسم</th>
            <th style="width: 40%;">التوقيع</th>
            <th style="width: 20%;">التاريخ</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="label-cell" style="text-align: center;">عضو اللجنة</td>
            <td><input type="text" id="committeeMember1"></td>
            <td><input type="text" id="signature1"></td>
            <td><input type="text" id="date1"></td>
          </tr>
          <tr>
            <td class="label-cell" style="text-align: center;">عضو اللجنة</td>
            <td><input type="text" id="committeeMember2"></td>
            <td><input type="text" id="signature2"></td>
            <td><input type="text" id="date2" ></td>
          </tr>
          <tr>
            <td class="label-cell" style="text-align: center;">رئيس اللجنة</td>
            <td><input type="text" id="committeeChair"></td>
            <td><input type="text" id="signature3"></td>
            <td><input type="text" id="date3" ></td>
          </tr>
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