<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="utf-8">
    <title>حساب مقيم أكاديمي</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #2563eb; margin-top: 0;">مرحباً بك {{ $user->name }}،</h2>
        <p>تم إنشاء حساب لك كمقيم أكاديمي في نظام مجلس الاعتماد الأكاديمي.</p>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #e2e8f0;">
            <p style="margin: 0 0 10px 0;"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
            <p style="margin: 0;"><strong>كلمة المرور المؤقتة:</strong> <span style="font-family: monospace; background-color: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $password }}</span></p>
        </div>

        <p style="color: #dc2626; font-weight: bold; font-size: 14px;">يرجى ملاحظة أنه يجب تغيير كلمة المرور فور تسجيل الدخول لأول مرة حفاظاً على سرية بياناتك.</p>

        <p>لتفعيل حسابك، يرجى النقر على الزر أدناه:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">تفعيل الحساب الآن</a>
        </div>

        <p style="color: #64748b; font-size: 13px;">إذا لم تقم بطلب هذا الحساب، يمكنك تجاهل هذه الرسالة بأمان.</p>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="color: #94a3b8; font-size: 12px; text-align: center;">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
    </div>
</body>
</html>
