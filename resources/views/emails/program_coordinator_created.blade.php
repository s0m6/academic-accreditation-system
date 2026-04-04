<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>حساب منسق البرنامج</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #ea580c; margin-top: 0;">مرحباً بك {{ $user->name }}،</h2>
        <p>تم إنشاء حساب لك كمنسق برنامج في نظام مجلس الاعتماد الأكاديمي، وذلك للبرنامج:</p>

        <div style="background-color: #fff7ed; padding: 12px 16px; border-radius: 6px; margin: 16px 0; border: 1px solid #fed7aa;">
            <p style="margin: 0; font-weight: bold; color: #9a3412;">{{ $programName }}</p>
        </div>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #e2e8f0;">
            <p style="margin: 0 0 10px 0;"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
            <p style="margin: 0;"><strong>كلمة المرور المؤقتة:</strong> <span style="font-family: monospace; background-color: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $password }}</span></p>
        </div>

        <p style="color: #dc2626; font-weight: bold; font-size: 14px;">يرجى ملاحظة أنه يجب تغيير كلمة المرور فور تسجيل الدخول لأول مرة حفاظاً على سرية بياناتك.</p>

        <p>سيُستخدم بريدك الإلكتروني لتلقي كافة المراسلات المتعلقة بمراحل الاعتماد القادمة، لذا تأكد من الاطلاع عليه باستمرار.</p>

        <p>لتفعيل حسابك، يرجى النقر على الزر أدناه:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" style="background-color: #ea580c; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">تفعيل الحساب الآن</a>
        </div>

        <p style="color: #64748b; font-size: 13px;">إذا لم تقم بطلب هذا الحساب، يمكنك تجاهل هذه الرسالة بأمان.</p>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="color: #94a3b8; font-size: 12px; text-align: center;">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
    </div>
</body>
</html>
