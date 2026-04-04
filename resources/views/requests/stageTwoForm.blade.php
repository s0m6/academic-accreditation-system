<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة بيانات البرنامج الأكاديمي</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <style>
* {
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #F9FAFC;
    min-height: 100vh;
    padding: 20px;
    color: #212529;
}

/* ====== الحاوية الرئيسية ====== */
.app-wrapper {
    background: #FFFFFF;
    border-radius: 8px;
    border: 1px solid #DEE2E6;
    max-width: 1100px;
    margin: 0 auto;
    overflow: hidden;
}

/* ====== الهيدر ====== */
.header {
    background: #FFFFFF;
    border-bottom: 2px solid #DEE2E6;
    padding: 25px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #0D6EFD;
}

/* ====== شريط التقدم ====== */
.progress-container {
    background: #F8F9FA;
    padding: 20px 30px;
    border-bottom: 1px solid #DEE2E6;
}

.progress-bar {
    height: 8px;
    background: #E9ECEF;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 8px;
}

.progress-fill {
    height: 100%;
    background: #0D6EFD;
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    font-size: 14px;
    color: #0D6EFD;
    font-weight: 600;
}

/* ====== التبويبات ====== */
.tabs-container {
    display: flex;
    border-bottom: 1px solid #DEE2E6;
    background: #FFFFFF;
    overflow-x: auto;
}

.tab-button {
    flex: 1;
    padding: 16px;
    border: none;
    background: transparent;
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: 0.2s;
}

.tab-button.active {
    color: #0D6EFD;
    border-bottom-color: #0D6EFD;
    background: #F8F9FA;
}

.tab-button:hover {
    background: #F1F3F5;
}

/* ====== محتوى التبويب ====== */
.tab-content {
    display: none;
    padding: 30px;
}

.tab-content.active {
    display: block;
}

/* ====== عناوين الأقسام ====== */
.section-title {
    color: #0D6EFD;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 1px solid #DEE2E6;
}

/* ====== الجداول ====== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    font-size: 14px;
}

th {
    background: #F8F9FA;
    color: #212529;
    padding: 12px;
    text-align: right;
    font-weight: 600;
    border-bottom: 1px solid #DEE2E6;
}

td {
    padding: 10px;
    border-bottom: 1px solid #E9ECEF;
}

tr:hover {
    background: #F8F9FA;
}

/* ====== الحقول ====== */
input[type="text"],
input[type="number"],
input[type="date"],
input[type="file"],
select,
textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #CED4DA;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    transition: 0.2s;
    background: #FFFFFF;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    height: 40px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #0D6EFD;
    box-shadow: 0 0 0 2px rgba(13,110,253,0.15);
}

input.required-field {
    background-color: #ffffff;
}

/* ====== الملاحظات ====== */
.notes-section {
    background: #FFFFFF;
    border: 1px solid #DEE2E6;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.notes-section label {
    margin-bottom: 10px;
    font-weight: 600;
    color: #212529;
}

/* ====== الأزرار ====== */
.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
    padding: 20px 0;
}

.btn {
    padding: 10px 18px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-primary {
    background: #0D6EFD;
    color: #FFFFFF;
    border: none;
}

.btn-primary:hover {
    background: #0B5ED7;
}

.btn-secondary {
    background: #FFFFFF;
    color: #0D6EFD;
    border: 1px solid #0D6EFD;
}

.btn-secondary:hover {
    background: #E7F1FF;
}

.btn-danger {
    background: #FFF5F5;
    color: #DC3545;
    border: 1px solid #DC3545;
    font-size: 12px;
    padding: 6px 10px;
}

/* ====== الإشعارات ====== */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 18px;
    border-radius: 4px;
    color: #FFFFFF;
    font-weight: 600;
    z-index: 1000;
}

.notification.success { background: #198754; }
.notification.error { background: #DC3545; }
.notification.info { background: #0D6EFD; }

/* ====== شريط الخطأ ====== */
.error-banner {
    background: #FFF5F5;
    border: 1px solid #DC3545;
    color: #DC3545;
    padding: 15px;
    border-radius: 6px;
    margin: 20px;
    display: none;
}

.error-banner.show {
    display: block;
}

/* ====== استجابة الشاشات ====== */
@media (max-width: 768px) {

    .tab-content {
        padding: 20px;
    }

    table {
        font-size: 12px;
    }

    th, td {
        padding: 8px;
    }

    input, select, textarea {
        font-size: 12px;
    }

    .btn {
        font-size: 12px;
    }
}
</style>
</head>

<body>
    <div class="app-wrapper"><!-- Header -->
        <div class="header" style="position: relative;">
            <a href="{{ route('requests.show', $accreditationRequest) }}" class="btn btn-secondary" style="position: absolute; right: 25px; top: 25px; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                العودة للوحة الطلب
            </a>
            <h1>📋 نظام إدارة بيانات البرنامج الأكاديمي</h1>
        </div><!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text" id="progressText">
                0% مكتمل
            </div>
        </div><!-- Error Banner -->
        <div class="error-banner" id="errorBanner"></div><!-- Tabs Navigation -->
        <div class="tabs-container"><button class="tab-button active" onclick="switchTab(0, event)">📄 القرارات</button>
            <button class="tab-button" onclick="switchTab(1, event)">📊 إحصائيات الطلاب</button> <button
                class="tab-button" onclick="switchTab(2, event)">👨‍🏫 إحصائيات الهيئة</button> <button
                class="tab-button" onclick="switchTab(3, event)">👥 بيانات الهيئة</button>
        </div><!-- Tab 1: القرارات -->
        <div class="tab-content active" id="tab-0">
            <h2 class="section-title">📄 القرارات المتعلقة بالبرنامج</h2>
            <table>
                <thead>
                    <tr>
                        <th>القرار</th>
                        <th>رقم القرار</th>
                        <th>الجهة</th>
                        <th>تاريخ القرار</th>
                        <th>المرفق (PDF)</th>
                    </tr>
                </thead>
                <tbody>
@php
    $decisionsList = [
        1 => 'قرار إنشاء البرنامج',
        2 => 'قرار الطاقة الاستيعابية',
        3 => 'قرار قبول أول دفعة',
        4 => 'قرار قبول دفعة العام الماضي',
        5 => 'قرار قبول دفعة العام قبل الماضي',
        6 => 'قرار اعتماد أحدث خطة دراسية',
        7 => 'محضر قرار تخرج دفعة العام الحالي',
        8 => 'قرار تقديم طلب الاعتماد الأكاديمي',
    ];
    $files = isset($formSubmission->form_data['decision_files']) ? $formSubmission->form_data['decision_files'] : [];
@endphp

@foreach($decisionsList as $i => $label)
<tr>
    <td>{{ $label }}</td>
    <td><input type="text" id="decision_number_{{ $i }}" class="decision-input" oninput="hasChanges=true; updateProgress();"></td>
    <td><input type="text" id="decision_authority_{{ $i }}" class="decision-input" oninput="hasChanges=true; updateProgress();"></td>
    <td><input type="date" id="decision_date_{{ $i }}" class="decision-input" oninput="hasChanges=true; updateProgress();"></td>
    <td>
        <div id="file_container_{{ $i }}">
            @if(!empty($files[$i]))
                <div class="existing-file" id="existing_file_div_{{ $i }}" style="display: flex; gap: 5px; align-items: center; margin-bottom: 5px;">
                    <a href="{{ route('requests.stage_two.download_file', ['accreditationRequest' => $accreditationRequest->id, 'formSubmission' => $formSubmission->id, 'decisionIndex' => $i]) }}" target="_blank" class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px; text-decoration: none;">📄 عرض الملف</a>
                    <button type="button" class="btn btn-danger" onclick="removeFile({{ $i }})" style="padding: 4px 8px; font-size: 11px;">🗑️ حذف</button>
                    <input type="hidden" id="existing_file_path_{{ $i }}" value="{{ $files[$i] }}">
                </div>
                <input type="file" id="decision_file_{{ $i }}" accept="application/pdf" class="decision-input" style="display:none;" onchange="hasChanges=true; updateProgress();">
            @else
                <input type="file" id="decision_file_{{ $i }}" accept="application/pdf" class="decision-input" onchange="hasChanges=true; updateProgress();">
            @endif
        </div>
    </td>
</tr>
@endforeach
                </tbody>
            </table>
          <div class="notes-section">

<label><strong>📝 ملاحظات حول القرارات</strong></label>

<table id="decisionsNotesTable">
<thead>
<tr>
<th width="60">#</th>
<th>الملاحظة</th>
<th width="100">إجراء</th>
</tr>
</thead>

<tbody>
<tr>
<td class="row-number">1</td>
<td>
<input type="text" id="decision_note_1" placeholder="أدخل الملاحظة">
</td>
<td>
<button class="btn btn-danger" onclick="deleteNoteRow(this,'decisionsNotesTable')">حذف</button>
</td>
</tr>
</tbody>

</table>

<div class="button-group">
<button class="btn btn-secondary" onclick="addNoteRow('decisionsNotesTable','decision_note')">
➕ إضافة ملاحظة
</button>
</div>

</div>
        </div><!-- Tab 2: إحصائيات الطلاب -->
        <div class="tab-content" id="tab-1">
            <h2 class="section-title">📊 البيانات الإحصائية للطلاب</h2>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">الفئة</th>
                        <th>العام الماضي</th>
                        <th>العام الحالي</th>
                        <th>المتوقع للعام القادم</th>
                    </tr>
                </thead>
                <tbody><!-- عدد الطلبة المخطط -->
                  <!-- عدد الطلبة المخطط التحاقهم -->
<tr>
    <td rowspan="3"><strong>عدد الطلبة المخطط التحاقهم بالبرنامج</strong></td>
    <td>قبول عام</td>
    <td><input type="number" id="planned-general-past" min="0" class="calc-1-past"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-general-current" min="0" class="calc-1-current"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-general-next" min="0" class="calc-1-next student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
</tr>
<tr>
    <td>قبول خاص</td>
    <td><input type="number" id="planned-special-past" min="0" class="calc-1-past student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-special-current" min="0" class="calc-1-current student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-special-next" min="0" class="calc-1-next student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
</tr>
<tr>
    <td>قبول دولي</td>
    <td><input type="number" id="planned-international-past" min="0" class="calc-1-past student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-international-current" min="0" class="calc-1-current student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
    <td><input type="number" id="planned-international-next" min="0" class="calc-1-next student-input"
            oninput="calculateStudentStats(1); updateProgress();"></td>
</tr>

<!-- العدد الكلي للطلاب -->
<tr>
    <td rowspan="3"><strong>العدد الكلي للطلاب الملتحقين بالبرنامج</strong></td>
    <td>قبول عام</td>
    <td><input type="number" id="total-general-past" min="0" class="calc-2-past student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-general-current" min="0" class="calc-2-current student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-general-next" min="0" class="calc-2-next student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
</tr>
<tr>
    <td>قبول خاص</td>
    <td><input type="number" id="total-special-past" min="0" class="calc-2-past student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-special-current" min="0" class="calc-2-current student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-special-next" min="0" class="calc-2-next student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
</tr>
<tr>
    <td>قبول دولي</td>
    <td><input type="number" id="total-international-past" min="0" class="calc-2-past student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-international-current" min="0" class="calc-2-current student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
    <td><input type="number" id="total-international-next" min="0" class="calc-2-next student-input"
            oninput="calculateStudentStats(2); updateProgress();"></td>
</tr>

<!-- متوسط الطلبة بالشعبة -->
<tr>
    <td rowspan="3"><strong>متوسط عدد الطلبة في الشعبة الدراسية</strong></td>
    <td>ذكور</td>
    <td><input type="number" id="average-male-past" min="0" class="calc-3-past student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
    <td><input type="number" id="average-male-current" min="0" class="calc-3-current student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
    <td><input type="number" id="average-male-next" min="0" class="calc-3-next student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
</tr>
<tr>
    <td>إناث</td>
    <td><input type="number" id="average-female-past" min="0" class="calc-3-past student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
    <td><input type="number" id="average-female-current" min="0" class="calc-3-current student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
    <td><input type="number" id="average-female-next" min="0" class="calc-3-next student-input"
            oninput="calculateStudentStats(3); updateProgress();"></td>
</tr>
<tr class="auto-calc">
    <td><strong>الإجمالي</strong></td>
    <td><input type="text" id="total-3-past" value="0" readonly></td>
    <td><input type="text" id="total-3-current" value="0" readonly></td>
    <td><input type="text" id="total-3-next" value="0" readonly></td>
</tr>

<!-- الخريجون في الدراسات العليا -->
<tr>
    <td rowspan="3"><strong>عدد الخريجين الذين يواصلون تعليمهم في الدراسات العليا</strong></td>
    <td>ذكور</td>
    <td><input type="number" id="graduates-male-past" min="0" class="calc-4-past student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
    <td><input type="number" id="graduates-male-current" min="0" class="calc-4-current student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
    <td><input type="number" id="graduates-male-next" min="0" class="calc-4-next student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
</tr>
<tr>
    <td>إناث</td>
    <td><input type="number" id="graduates-female-past" min="0" class="calc-4-past student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
    <td><input type="number" id="graduates-female-current" min="0" class="calc-4-current student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
    <td><input type="number" id="graduates-female-next" min="0" class="calc-4-next student-input"
            oninput="calculateStudentStats(4); updateProgress();"></td>
</tr>
<tr class="auto-calc">
    <td><strong>الإجمالي</strong></td>
    <td><input type="text" id="total-4-past" value="0" readonly></td>
    <td><input type="text" id="total-4-current" value="0" readonly></td>
    <td><input type="text" id="total-4-next" value="0" readonly></td>
</tr>

<!-- الخريجون في الوظائف -->
<tr>
    <td rowspan="3"><strong>عدد الخريجين الذين التحقوا بوظائف</strong></td>
    <td>ذكور</td>
    <td><input type="number" id="employed-male-past" min="0" class="calc-5-past student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
    <td><input type="number" id="employed-male-current" min="0" class="calc-5-current student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
    <td><input type="number" id="employed-male-next" min="0" class="calc-5-next student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
</tr>
<tr>

    <td>إناث</td>
    <td><input type="number" id="employed-female-past" min="0" class="calc-5-past student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
    <td><input type="number" id="employed-female-current" min="0" class="calc-5-current student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
    <td><input type="number" id="employed-female-next" min="0" class="calc-5-next student-input"
            oninput="calculateStudentStats(5); updateProgress();"></td>
</tr>
<tr class="auto-calc">
    <td><strong>الإجمالي</strong></td>
    <td><input type="text" id="total-5-past" value="0" readonly></td>
    <td><input type="text" id="total-5-current" value="0" readonly></td>
    <td><input type="text" id="total-5-next" value="0" readonly></td>
</tr>

                </tbody>
            </table>
            <div class="notes-section">

<label><strong>📝 ملاحظات حول الطلاب</strong></label>

<table id="studentsNotesTable">
<thead>
<tr>
<th width="60">#</th>
<th>الملاحظة</th>
<th width="100">إجراء</th>
</tr>
</thead>

<tbody>
<tr>
<td class="row-number">1</td>
<td>
<input type="text" id="student_note_1" placeholder="أدخل الملاحظة">
</td>
<td>
<button class="btn btn-danger" onclick="deleteNoteRow(this,'studentsNotesTable')">حذف</button>
</td>
</tr>
</tbody>

</table>

<div class="button-group">
<button class="btn btn-secondary" onclick="addNoteRow('studentsNotesTable','student_note')">
➕ إضافة ملاحظة
</button>
</div>

</div>
        </div><!-- Tab 3: إحصائيات الهيئة التدريسية -->
        <div class="tab-content" id="tab-2">
            <h2 class="section-title">👨‍🏫 البيانات الإحصائية لأعضاء هيئة التدريس</h2>
            <table>
                <thead>
                    <tr>
                        <th>الدرجة العلمية</th>
                        <th>ذكور</th>
                        <th>إناث</th>
                        <th>متوسط العبء التدريسي</th>
                        <th>عدد غير المتفرغين</th>
                    </tr>
                </thead>
                <tbody>
                   <tr>
    <td>أستاذ</td>
    <td><input type="number" id="professor-male" min="0" class="col-male"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="professor-female" min="0" class="col-female"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="professor-load" min="0" step="0.1" class="col-load"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="professor-parttime" min="0" class="col-parttime"
            oninput="calculateFacultyStats(); updateProgress();"></td>
</tr>
<tr>
    <td>أستاذ مشارك</td>
    <td><input type="number" id="associate-male" min="0" class="col-male"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="associate-female" min="0" class="col-female"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="associate-load" min="0" step="0.1" class="col-load"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="associate-parttime" min="0" class="col-parttime"
            oninput="calculateFacultyStats(); updateProgress();"></td>
</tr>
<tr>
    <td>أستاذ مساعد</td>
    <td><input type="number" id="assistant-male" min="0" class="col-male"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="assistant-female" min="0" class="col-female"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="assistant-load" min="0" step="0.1" class="col-load"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="assistant-parttime" min="0" class="col-parttime"
            oninput="calculateFacultyStats(); updateProgress();"></td>
</tr>
<tr>
    <td>مدرس</td>
    <td><input type="number" id="lecturer-male" min="0" class="col-male"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="lecturer-female" min="0" class="col-female"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="lecturer-load" min="0" step="0.1" class="col-load"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="lecturer-parttime" min="0" class="col-parttime"
            oninput="calculateFacultyStats(); updateProgress();"></td>
</tr>
<tr>
    <td>معيد</td>
    <td><input type="number" id="teachingassistant-male" min="0" class="col-male"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="teachingassistant-female" min="0" class="col-female"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="teachingassistant-load" min="0" step="0.1" class="col-load"
            oninput="calculateFacultyStats(); updateProgress();"></td>
    <td><input type="number" id="teachingassistant-parttime" min="0" class="col-parttime"
            oninput="calculateFacultyStats(); updateProgress();"></td>
</tr>
</tbody>
<tfoot>
<tr class="auto-calc">
    <th>الإجمالي</th>
    <td><input type="text" id="total-male" value="0" readonly></td>
    <td><input type="text" id="total-female" value="0" readonly></td>
    <td><input type="text" id="total-load" value="0" readonly></td>
    <td><input type="text" id="total-parttime" value="0" readonly></td>
</tr>
</tfoot>

            </table>
            <div class="notes-section">

<label><strong>📝 ملاحظات حول الهيئة التدريسية</strong></label>

<table id="facultyNotesTable">
<thead>
<tr>
<th width="60">#</th>
<th>الملاحظة</th>
<th width="100">إجراء</th>
</tr>
</thead>

<tbody>
<tr>
<td class="row-number">1</td>
<td>
<input type="text" id="faculty_note_1" placeholder="أدخل الملاحظة">
</td>
<td>
<button class="btn btn-danger" onclick="deleteNoteRow(this,'facultyNotesTable')">حذف</button>
</td>
</tr>
</tbody>

</table>

<div class="button-group">
<button class="btn btn-secondary" onclick="addNoteRow('facultyNotesTable','faculty_note')">
➕ إضافة ملاحظة
</button>
</div>

</div>
        </div><!-- Tab 4: بيانات الهيئة التدريسية -->
        <div class="tab-content" id="tab-3">
            <h2 class="section-title">👥 بيانات أعضاء هيئة التدريس التفصيلية</h2>
            <table id="staffTable">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الدرجة العلمية</th>
                        <th>التخصص العام</th>
                        <th>التخصص الدقيق</th>
                        <th>بلد التخرج</th>
                        <th>عام التعيين</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                   <tr data-staff-id="1">
    <td>
        <input type="text" id="staff-name-1" placeholder="أدخل الاسم" class="staff-name"
               oninput="updateProgress();">
    </td>
    <td>
        <select id="staff-degree-1" class="staff-degree" oninput="updateProgress();">
            <option value="">اختر الدرجة...</option>
            <option value="prof">أستاذ</option>
            <option value="assoc">أستاذ مشارك</option>
            <option value="assist">أستاذ مساعد</option>
            <option value="lecturer">مدرس</option>
            <option value="ta">معيد</option>
        </select>
    </td>
    <td>
        <input type="text" id="staff-major-1" placeholder="التخصص العام" class="staff-major"
               oninput="updateProgress();">
    </td>
    <td>
        <input type="text" id="staff-minor-1" placeholder="التخصص الدقيق" class="staff-minor"
               oninput="updateProgress();">
    </td>
    <td>
        <input type="text" id="staff-country-1" placeholder="بلد التخرج" class="staff-country"
               oninput="updateProgress();">
    </td>
    <td>
        <input type="number" id="staff-year-1" min="1900" max="2100" placeholder="سنة" class="staff-year"
               oninput="updateProgress();">
    </td>
    <td>
        <button class="btn btn-danger" onclick="deleteStaffRow(this)">حذف</button>
    </td>
</tr>
                </tbody>
            </table>
            <div class="button-group"><button class="btn btn-secondary" onclick="addStaffRow()">➕ إضافة عضو
                    جديد</button>
            </div>
           
        </div><!-- Submit Section -->
        <div style="padding: 30px; background: #f9fafb; border-top: 2px solid #e0e7ff; text-align: center;">
            <div class="button-group"><button class="btn btn-primary" onclick="submitFormData()">💾 حفظ التغييرات</button>
                <button class="btn btn-secondary" onclick="resetForm()">↻ تفريغ النموذج</button>
            </div>
            <p style="color: #6b7280; font-size: 12px; margin-top: 15px;">🔒 بيانات آمنة | 💾 حفظ تلقائي كل 30 ثانية</p>
        </div>
    </div>
    <script>
        // ==================== البيانات الأساسية ====================
        let currentTab = 0;
        let autoSaveTimer;
        let hasChanges = false;

        // مراقبة التغييرات في جميع المدخلات
        document.addEventListener('input', (e) => {
            if (e.target.matches('input, select, textarea')) {
                hasChanges = true;
            }
        });

        // ==================== إدارة التبويبات ====================
        function switchTab(tabIndex, event) {
            // إخفاء التبويب الحالي
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));

            // إظهار التبويب الجديد
            document.getElementById(`tab-${tabIndex}`).classList.add('active');
            event.target.classList.add('active');

            currentTab = tabIndex;
            updateProgress();
        }

        // ==================== حساب الإحصائيات ====================
        function calculateStudentStats(sectionNum) {
            const periods = ['past', 'current', 'next'];

            periods.forEach(period => {
                const inputs = document.querySelectorAll(`.calc-${sectionNum}-${period}`);
                let sum = 0;
                inputs.forEach(input => {
                    const value = Number(input.value) || 0;
                    if (value < 0) {
                        input.style.borderColor = '#ef4444';
                        showNotification('الأرقام يجب أن تكون موجبة!', 'error');
                        sum += 0;
                    } else {
                        input.style.borderColor = '#d1d5db';
                        sum += value;
                    }
                });

                const totalField = document.getElementById(`total-${sectionNum}-${period}`);
                if (totalField) {
                    totalField.value = sum;
                }
            });
        }

        function calculateFacultyStats() {
            function getSum(className) {
                let sum = 0;
                document.querySelectorAll('.' + className).forEach(input => {
                    const value = Number(input.value) || 0;
                    if (value < 0) {
                        input.style.borderColor = '#ef4444';
                    } else {
                        input.style.borderColor = '#d1d5db';
                        sum += value;
                    }
                });
                return sum;
            }

            document.getElementById('total-male').value = getSum('col-male');
            document.getElementById('total-female').value = getSum('col-female');
            document.getElementById('total-load').value = getSum('col-load').toFixed(2);
            document.getElementById('total-parttime').value = getSum('col-parttime');
        }

        // ==================== إدارة بيانات الهيئة ====================
        function addStaffRow() {
            const table = document.getElementById('staffTable');
            const newId = Math.max(...Array.from(table.querySelectorAll('tr[data-staff-id]')).map(tr => parseInt(tr.dataset.staffId) || 0)) + 1;

            const row = document.createElement('tr');
            row.dataset.staffId = newId;
            row.innerHTML = `
                <td><input type="text" id="staff-name-${newId}" placeholder="أدخل الاسم" class="staff-name" oninput="updateProgress();"></td>
                <td>
                    <select class="staff-degree" id="staff-degree-${newId}" oninput="updateProgress();">
                        <option value="">اختر الدرجة...</option>
                        <option value="prof">أستاذ</option>
                        <option value="assoc">أستاذ مشارك</option>
                        <option value="assist">أستاذ مساعد</option>
                        <option value="lecturer">مدرس</option>
                        <option value="ta">معيد</option>
                    </select>
                </td>
                <td><input type="text" id="staff-major-${newId}" placeholder="التخصص العام" class="staff-major" oninput="updateProgress();"></td>
                <td><input type="text" id="staff-minor-${newId}"placeholder="التخصص الدقيق" class="staff-minor" oninput="updateProgress();"></td>
                <td><input type="text" id="staff-country-${newId}"  placeholder="بلد التخرج" class="staff-country" oninput="updateProgress();"></td>
                <td><input type="number" id="staff-year-${newId}" min="1900" max="2100" placeholder="سنة" class="staff-year" oninput="updateProgress();"></td>
                <td><button class="btn btn-danger" onclick="deleteStaffRow(this)">حذف</button></td>
            `;

            table.querySelector('tbody').appendChild(row);
            hasChanges = true;
            updateProgress();
            showNotification('✓ تم إضافة صف جديد', 'success');
        }

        function deleteStaffRow(button) {
            if (document.querySelectorAll('#staffTable tbody tr').length === 1) {
                showNotification('يجب الاحتفاظ بصف واحد على الأقل', 'error');
                return;
            }

            button.closest('tr').remove();
            hasChanges = true;
            updateProgress();
            showNotification('✓ تم حذف الصف', 'success');
        }

        // ==================== التحقق من البيانات ====================
        function validateForm() {
            const errors = [];

            // التحقق من الحقول المطلوبة
            document.querySelectorAll('.required-field').forEach(field => {
                if (!field.value.trim()) {
                    errors.push(field.previousElementSibling?.textContent || 'حقل مطلوب');
                }
            });

            // التحقق من التواريخ
            document.querySelectorAll('input[type="date"]').forEach(field => {
                if (field.value) {
                    const date = new Date(field.value);
                    if (isNaN(date.getTime())) {
                        errors.push('تاريخ غير صحيح');
                    }
                }
            });

            // التحقق من الأرقام السالبة
            document.querySelectorAll('input[type="number"]').forEach(field => {
                if (field.value && Number(field.value) < 0) {
                    errors.push('لا يمكن إدخال أرقام سالبة');
                }
            });

            return errors;
        }

        // ==================== الحفظ والاستعادة بجدول قواعد البيانات ====================
        function saveFormToLocalStorage() {
            // Disabled local storage auto save, logic moved to server save.
        }

        function loadFormFromServer() {
            const saved = {!! isset($formSubmission->form_data) && $formSubmission->form_data ? json_encode($formSubmission->form_data, JSON_UNESCAPED_UNICODE) : 'null' !!};
            if (!saved) return;

            try {
                // 1. استعادة الحقول البسيطة
                Object.entries(saved).forEach(([key, value]) => {
                    if (typeof value === 'object') return;
                    const field = document.getElementById(key);
                    if (field) field.value = value;
                });

                // 2. استعادة بيانات الهيئة التدريسية (إن وجدت)
                if (saved.structured_data && saved.structured_data.staffDetails) {
                    const tbody = document.querySelector('#staffTable tbody');
                    // تفريغ الجدول أولاً لإعادة بنائه بدقة
                    tbody.innerHTML = '';
                    saved.structured_data.staffDetails.forEach((staff, index) => {
                        const newId = index + 1;
                        const row = document.createElement('tr');
                        row.dataset.staffId = newId;
                        row.innerHTML = `
                            <td><input type="text" id="staff-name-${newId}" value="${staff.name || ''}" class="staff-name" oninput="updateProgress();"></td>
                            <td>
                                <select class="staff-degree" id="staff-degree-${newId}" oninput="updateProgress();">
                                    <option value="">اختر الدرجة...</option>
                                    <option value="prof" ${staff.degree === 'prof' ? 'selected' : ''}>أستاذ</option>
                                    <option value="assoc" ${staff.degree === 'assoc' ? 'selected' : ''}>أستاذ مشارك</option>
                                    <option value="assist" ${staff.degree === 'assist' ? 'selected' : ''}>أستاذ مساعد</option>
                                    <option value="lecturer" ${staff.degree === 'lecturer' ? 'selected' : ''}>مدرس</option>
                                    <option value="ta" ${staff.degree === 'ta' ? 'selected' : ''}>معيد</option>
                                </select>
                            </td>
                            <td><input type="text" id="staff-major-${newId}" value="${staff.major || ''}" class="staff-major" oninput="updateProgress();"></td>
                            <td><input type="text" id="staff-minor-${newId}" value="${staff.minor || ''}" class="staff-minor" oninput="updateProgress();"></td>
                            <td><input type="text" id="staff-country-${newId}" value="${staff.country || ''}" class="staff-country" oninput="updateProgress();"></td>
                            <td><input type="number" id="staff-year-${newId}" value="${staff.year || ''}" class="staff-year" oninput="updateProgress();"></td>
                            <td><button class="btn btn-danger" onclick="deleteStaffRow(this)">حذف</button></td>
                        `;
                        tbody.appendChild(row);
                    });
                }

                // 3. استعادة الملاحظات (إن وجدت)
                const noteTables = ['decisionsNotesTable', 'studentsNotesTable', 'facultyNotesTable'];
                const notePrefixes = ['decision_note', 'student_note', 'faculty_note'];
                
                noteTables.forEach((tableId, idx) => {
                    const prefix = notePrefixes[idx];
                    const tbody = document.getElementById(tableId).querySelector('tbody');
                    let noteIndex = 1;
                    let foundAny = false;
                    
                    // تحقق من وجود ملاحظات في البيانات المحفوظة
                    while(saved[`${prefix}_${noteIndex}`] !== undefined) {
                        if (noteIndex === 1) {
                            tbody.innerHTML = ''; // مسح الصف الافتراضي الأول فقط عند العثور على بيانات
                        }
                        const value = saved[`${prefix}_${noteIndex}`];
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td class="row-number">${noteIndex}</td>
                            <td><input type="text" id="${prefix}_${noteIndex}" value="${value}" placeholder="أدخل الملاحظة"></td>
                            <td><button class="btn btn-danger" onclick="deleteNoteRow(this,'${tableId}')">حذف</button></td>
                        `;
                        tbody.appendChild(row);
                        noteIndex++;
                        foundAny = true;
                    }
                });

                hasChanges = false;
                updateProgress();
                showNotification('✓ تم تحميل البيانات بنجاح', 'success');
            } catch (error) {
                console.error('خطأ في استعادة البيانات:', error);
            }
        }

        // ==================== شريط التقدم ====================
        function updateProgress() {
            const totalFields = document.querySelectorAll('input, select, textarea').length;
            const filledFields = document.querySelectorAll('input:not([readonly]), select, textarea').length;

            let filledCount = 0;
            document.querySelectorAll('input:not([readonly]), select, textarea').forEach(field => {
                if (field.value) filledCount++;
            });

            const progress = Math.round((filledCount / (filledFields || 1)) * 100);
            const progressFill = document.getElementById('progressFill');
            if(progressFill) progressFill.style.width = progress + '%';
            
            const progressText = document.getElementById('progressText');
            if(progressText) progressText.textContent = `${progress}% مكتمل`;
        }

        // ==================== الإخطارات ====================
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // ==================== رفع النموذج للحفظ في السيرفر ====================
        async function submitFormData() {
            const errors = validateForm();

            if (errors.length > 0) {
                const errorBanner = document.getElementById('errorBanner');
                errorBanner.innerHTML = `
                    <strong>⚠️ توجد أخطاء في النموذج:</strong><br>
                    ${errors.join('<br>')}
                `;
                errorBanner.classList.add('show');
                window.scrollTo(0, 0);
                return;
            }

            const errorBanner = document.getElementById('errorBanner');
            if(errorBanner) errorBanner.classList.remove('show');

            // جمع كافة الحقول لتسهيل الإستعادة
            const flatData = {};
            document.querySelectorAll('input:not([type="file"]), select, textarea').forEach(field => {
                if(field.id) flatData[field.id] = field.value;
            });

            // 2. البيانات المنظمة (Structured Data) لجداول الإحصائيات
            flatData['structured_data'] = {
                timestamp: new Date().toISOString(),
                decisions: Array.from({length: 8}).map((_, idx) => {
                    const id = idx + 1;
                    return {
                        id: id,
                        number: document.getElementById('decision_number_' + id)?.value || '',
                        authority: document.getElementById('decision_authority_' + id)?.value || '',
                        date: document.getElementById('decision_date_' + id)?.value || ''
                    };
                }),
                students: {
                    past: Array.from(document.querySelectorAll('.student-input[class*="-past"]')).map(i => i.value),
                    current: Array.from(document.querySelectorAll('.student-input[class*="-current"]')).map(i => i.value),
                    next: Array.from(document.querySelectorAll('.student-input[class*="-next"]')).map(i => i.value)
                },
                staffCount: {
                    males: document.getElementById('total-male')?.value || 0,
                    females: document.getElementById('total-female')?.value || 0,
                    teaching_load: document.getElementById('total-load')?.value || 0,
                    parttime: document.getElementById('total-parttime')?.value || 0
                },
                staffDetails: Array.from(document.querySelectorAll('#staffTable tbody tr')).map(row => ({
                    name: row.querySelector('.staff-name')?.value,
                    degree: row.querySelector('.staff-degree')?.value,
                    major: row.querySelector('.staff-major')?.value,
                    minor: row.querySelector('.staff-minor')?.value,
                    country: row.querySelector('.staff-country')?.value,
                    year: row.querySelector('.staff-year')?.value
                }))
            };

            const payload = new FormData();
            payload.append('_token', '{{ csrf_token() }}');
            // 3. مسارات الملفات للقرارات والمرفقات الجديدة
            const decisionFilesPaths = {};
            for (let i = 1; i <= 8; i++) {
                const existingPath = document.getElementById('existing_file_path_' + i);
                if (existingPath) {
                    decisionFilesPaths[i] = existingPath.value;
                }
                const fileInput = document.getElementById('decision_file_' + i);
                if (fileInput && fileInput.files.length > 0) {
                    payload.append('decision_file_' + i, fileInput.files[0]);
                }
            }
            flatData['decision_files_paths'] = decisionFilesPaths;

            payload.append('json_data', JSON.stringify(flatData));

            console.log('Sending payload:', flatData);
            showNotification('⏳ جاري حفظ التغييرات في السيرفر...', 'info');

            try {
                const response = await fetch("{{ route('requests.stage_two.save', [$accreditationRequest, $formSubmission]) }}", {
                    method: 'POST',
                    body: payload,
                    headers: { 'Accept': 'application/json' }
                });
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    hasChanges = false;
                    showNotification('✓ تم حفظ البيانات بنجاح!', 'success');
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('requests.stage', [$accreditationRequest, 'stage_two']) }}";
                    }, 800);
                } else {
                    showNotification('حدث خطأ أثناء الحفظ: ' + (result.message || 'خطأ غير معروف'), 'error');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showNotification('فشل الاتصال بالسيرفر. تأكد من جودة الإنترنت.', 'error');
            }
        }

        // ==================== إدارة الملفات الموجودة ====================
        function removeFile(id) {
            if (!confirm('هل أنت متأكد من حذف هذا المرفق المسبق رفعِه؟')) return;
            const container = document.getElementById('existing_file_div_' + id);
            if (container) {
                container.remove();
            }
            const fileInput = document.getElementById('decision_file_' + id);
            if (fileInput) {
                fileInput.style.display = 'block';
                // Trigger visual update
                hasChanges = true;
                updateProgress();
            }
            showNotification('تم إزالة الملف (لن يتم الحذف نهائياً حتى يتم حفظ التغييرات)', 'info');
        }

        function resetForm() {
            if (confirm('هل أنت متأكد من رغبتك في تفريغ جميع البيانات؟')) {
                document.querySelectorAll('input:not([readonly]), select, textarea').forEach(field => {
                    field.value = '';
                });
                localStorage.removeItem('programFormData');
                calculateStudentStats(1);
                calculateStudentStats(2);
                calculateStudentStats(3);
                calculateStudentStats(4);
                calculateStudentStats(5);
                calculateFacultyStats();
                updateProgress();
                showNotification('✓ تم تفريغ النموذج', 'success');
            }
        }

        // ==================== التهيئة الأولية ====================
        document.addEventListener('DOMContentLoaded', function () {
            loadFormFromServer();

            // تنبيه عند محاولة مغادرة الصفحة بوجود تغييرات غير محفوظة
            window.addEventListener('beforeunload', function (e) {
                if (hasChanges) {
                    const message = "لديك تغييرات غير محفوظة، هل أنت متأكد من المغادرة؟";
                    e.returnValue = message;
                    return message;
                }
            });

            updateProgress();
            
            @if(isset($readonly) && $readonly)
                document.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = true);
                document.querySelectorAll('.button-group').forEach(el => el.style.display = 'none');
            @endif
        });

        // استدعاء الحسابات عند التحميل
        window.addEventListener('load', function () {
            calculateStudentStats(1);
            calculateStudentStats(2);
            calculateStudentStats(3);
            calculateStudentStats(4);
            calculateStudentStats(5);
            calculateFacultyStats();
        });




        // ==================== إدارة جداول الملاحظات ====================

function addNoteRow(tableId, prefix) {

const table = document.getElementById(tableId).querySelector("tbody");
const rowCount = table.rows.length + 1;

const row = document.createElement("tr");

row.innerHTML = `
<td class="row-number">${rowCount}</td>

<td>
<input type="text" id="${prefix}_${rowCount}" placeholder="أدخل الملاحظة">
</td>

<td>
<button class="btn btn-danger" onclick="deleteNoteRow(this,'${tableId}')">
حذف
</button>
</td>
`;

table.appendChild(row);
hasChanges = true;
updateNoteNumbers(tableId);
updateProgress();

}

function deleteNoteRow(button, tableId) {

const table = document.getElementById(tableId).querySelector("tbody");

if (table.rows.length === 1) {
showNotification('يجب الاحتفاظ بملاحظة واحدة على الأقل', 'error');
return;
}

button.closest("tr").remove();
hasChanges = true;
updateNoteNumbers(tableId);

updateProgress();

}

function updateNoteNumbers(tableId){

const rows = document.querySelectorAll(`#${tableId} tbody tr`);

rows.forEach((row,index)=>{

row.querySelector(".row-number").textContent = index + 1;

const input = row.querySelector("input");

if(input && input.id){
    const parts = input.id.split("_");
    if(parts.length >= 3) {
        input.id = parts[0] + "_" + parts[1] + "_" + (index+1);
    }
}

});

}
    </script>
    <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'9d4bd01757b7738b',t:'MTc3MjIzODYyMS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
</body>

</html>