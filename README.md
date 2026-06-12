<h1 align="center">
  <br/>
  🎓 نظام إدارة عمليات مجلس الأكاديمي وضمان جودة التعليم العالي
  <br/>
  <sub>Council for Academic Accreditation & Quality Assurance in Higher Education — CAAQAHE</sub>
  <br/>
  <sub>وزارة التعليم العالي والبحث العلمي · الجمهورية اليمنية</sub>
</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS 4">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js 3">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/WebSockets-Reverb-6875F5?style=for-the-badge&logo=socketdotio&logoColor=white" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/License-MIT-22C55E?style=for-the-badge" alt="MIT License">
</p>

<p align="center">
  <strong>منصة رقمية حكومية متكاملة لإدارة دورة حياة الاعتماد الأكاديمي للبرامج الجامعية في اليمن</strong><br/>
  تُغطي كافة المراحل من تقديم الطلب الأولي وحتى إصدار شهادة الاعتماد الرسمية، مع دعم التنسيق الفوري بين الجامعات والمجلس.
</p>

---

## 📋 جدول المحتويات

- [نبذة عن المجلس والنظام](#-نبذة-عن-المجلس-والنظام)
- [لماذا هذا النظام؟](#-لماذا-هذا-النظام)
- [المميزات الرئيسية](#-المميزات-الرئيسية)
- [مراحل الاعتماد](#-مراحل-الاعتماد)
- [نتائج القرار النهائي](#نتائج-القرار-النهائي)
- [أدوار المستخدمين](#-أدوار-المستخدمين)
- [المتطلبات التقنية](#-المتطلبات-التقنية)
- [التثبيت والإعداد](#-التثبيت-والإعداد)
- [تشغيل المشروع](#️-تشغيل-المشروع)
- [هيكل المشروع](#-هيكل-المشروع)
- [التقنيات المستخدمة](#-التقنيات-المستخدمة)
- [الاختبارات](#-الاختبارات)
- [الرخصة](#-الرخصة)

---

## 🏛 نبذة عن المجلس والنظام

### مجلس  الاعتماد الأكاديمي وضمان جودة التعليم العالي

**مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي (CAAQAHE)** هو الجهة الرسمية المُختصة بمنح الاعتماد الأكاديمي للبرامج والمؤسسات التعليمية في الجمهورية اليمنية، ويعمل تحت مظلة **وزارة التعليم العالي والبحث العلمي**. يهدف المجلس إلى رفع جودة مخرجات التعليم العالي وضمان التزام الجامعات اليمنية بالمعايير الأكاديمية الوطنية والدولية.

### النظام الإلكتروني

**CAAQAHE System** هو نظام إلكتروني حكومي متكامل مبني بـ **Laravel 13** لأتمتة وإدارة عملية الاعتماد الأكاديمي للبرامج الجامعية رقمياً. يُعالج النظام دورة حياة الاعتماد بشكل كامل عبر **9 مراحل متسلسلة** تبدأ من تقديم الطلب الأولي من قِبَل الجامعة، مروراً بالتقييم الميداني من لجان المُقيِّمين، وصولاً إلى إصدار القرار النهائي وشهادة الاعتماد الرسمية.

يدعم النظام التوثيق الكامل باللغة العربية، ويوفر إشعارات فورية عبر **WebSockets (Laravel Reverb)**، وطباعة التقارير والشهادات بصيغة PDF باللغة العربية مع رمز QR للتحقق العام.

---

## 💡 لماذا هذا النظام؟

قبل هذا النظام، كانت عملية الاعتماد الأكاديمي تعتمد على الأوراق والمراسلات التقليدية، مما أدى إلى:

- **تأخر** في معالجة الطلبات والتواصل بين الجامعات والمجلس
- **صعوبة التتبع** والرقابة على مراحل سير الطلبات
- **احتمالية الأخطاء** البشرية في التقييم والتوثيق
- **غياب الشفافية** في إصدار القرارات والشهادات

**الحل:** نظام رقمي موحد يُدير دورة الاعتماد من البداية للنهاية، بشفافية عالية، وتنبيهات فورية، وأرشفة رقمية كاملة — لا ورق، لا تأخير، لا غموض.

---

## ✨ المميزات الرئيسية

| الميزة | الوصف |
|--------|-------|
| 🔄 **سير عمل محكم** | 9 مراحل اعتماد متسلسلة، كل مرحلة تنتقل تلقائياً بعد إتمام السابقة |
| 👥 **إدارة متعددة الأدوار** | 5 أدوار مستخدمين مختلفة بصلاحيات محددة وواجهات مخصصة لكل دور |
| 🔔 **إشعارات فورية** | تنبيهات لحظية عبر Laravel Reverb (WebSockets) لجميع الأطراف المعنية |
| 📄 **طباعة PDF بالعربية** | تقارير وشهادات اعتماد رسمية باللغة العربية قابلة للطباعة والأرشفة |
| 🔒 **إدارة تعارض المصالح** | التحقق التلقائي من تعارضات المُقيِّمين مع الجامعات قبل تشكيل اللجان |
| ✍️ **توقيعات رقمية** | دعم التوقيع الرقمي لأعضاء لجنة التقييم داخل تقارير اللجان |
| 🔗 **رمز QR للتحقق** | شهادات اعتماد مزودة برمز QR للتحقق العام من صحة الشهادة إلكترونياً |
| 📊 **لوحة تحكم شاملة** | متابعة حالة جميع طلبات الاعتماد ومراحلها من مكان واحد لكل دور |
| 📧 **إشعارات بريدية تلقائية** | إرسال تلقائي للبريد الإلكتروني عند كل حدث مهم في دورة الاعتماد |
| 🌐 **دعم كامل للعربية** | الواجهة والتقارير والشهادات والتوثيق كلها باللغة العربية |
| 🏗 **معمارية قابلة للتوسع** | بنية تقنية متينة تستوعب مئات الجامعات والبرامج بكفاءة |

---

## 🔄 مراحل الاعتماد

يمر طلب الاعتماد بـ **9 مراحل رسمية** متسلسلة تشمل جميع الأطراف، وكل مرحلة لها أصحابها ومسؤولياتها:

```
 ┌─────────────────────────────────────────────────────────────────────┐
 │                      دورة حياة طلب الاعتماد                        │
 └─────────────────────────────────────────────────────────────────────┘

 المرحلة 1  ──  الطلب الأولي
               [مسؤول الاعتماد بالجامعة] → يُقدِّم طلباً رسمياً للمجلس

      ▼

 المرحلة 2  ──  استيفاء متطلبات الجودة
               [منسق البرنامج] → يرفع المتطلبات الأولية للجودة

      ▼

 المرحلة 3  ──  تقييم المعايير والمؤشرات والشواهد
               [منسق البرنامج] → يُقيِّم المعايير ويرفع الشواهد والأدلة

      ▼

 المرحلة 4  ──  تشكيل لجنة المُقيِّمين
               [منسق المجلس] → يختار المُقيِّمين ويتحقق من تعارض المصالح

      ▼

 المرحلة 5  ──  جدولة الزيارة الميدانية
               [منسق المجلس] → يُحدِّد موعد ومكان الزيارة الميدانية للجامعة

      ▼

 المرحلة 6  ──  إعداد تقرير لجنة التقييم
               [المُقيِّمون] → يكتبون التقرير ويُوقِّعون عليه رقمياً

      ▼

 المرحلة 7  ──  مراجعة وإقرار التقرير
               [منسق المجلس] → يراجع التقرير ويُقرِّره للمرحلة التالية

      ▼

 المرحلة 8  ──  مراجعة التقرير النهائي وإبداء الرأي
               [منسق البرنامج] → يطّلع على التقرير ويُبدي تعليقاته

      ▼

 المرحلة 9  ──  إصدار القرار النهائي وشهادة الاعتماد
               [أمانة المجلس] → تُصدِر القرار الرسمي وشهادة الاعتماد بـ QR Code
```

### نتائج القرار النهائي

| القرار | الحالة | مدة الاعتماد | التفاصيل |
|--------|--------|--------------|----------|
| **محقق بتميز** | ✅ معتمد | 5 سنوات | أعلى مستوى من الجودة |
| **محقق بإتقان** | ✅ معتمد | 4 سنوات | مستوى جودة عالٍ |
| **محقق** | ✅ معتمد | 3 سنوات | مستوى جودة مقبول |
| **محقق جزئياً** | ❌ غير معتمد | — | مهلة سنة للتصحيح وإعادة التقديم |
| **غير محقق** | ❌ غير معتمد | — | مهلة سنتين للتصحيح وإعادة التقديم |

---

## 👥 أدوار المستخدمين

يعتمد النظام على **5 أدوار رسمية** مرتبطة بالهيكل التنظيمي للمجلس والجامعات:

| الدور | المعرف | الجهة | الصلاحيات الرئيسية |
|-------|--------|-------|---------------------|
| **مسؤول الاعتماد** | `accreditation_officer` | الجامعة | تقديم الطلب الأولي، إدارة بيانات البرامج |
| **منسق البرنامج** | `program_coordinator` | الجامعة | رفع متطلبات الجودة، تقييم المعايير والمؤشرات، مراجعة تقرير التقييم |
| **أمانة المجلس** | `council_secretariat` | المجلس | مراجعة الطلبات، إصدار القرارات والشهادات الرسمية، إدارة قاعدة المُقيِّمين |
| **منسق المجلس** | `council_coordinator` | المجلس | تشكيل لجان التقييم، جدولة الزيارات الميدانية، مراجعة التقارير |
| **المُقيِّم** | `evaluator` | المجلس | كتابة تقارير التقييم الميداني، التوقيع الرقمي |

---

## 🛠 المتطلبات التقنية

### بيئة الخادم

| المتطلب | الإصدار المطلوب |
|---------|----------------|
| PHP | `>= 8.3` |
| Composer | `>= 2.x` |
| Node.js | `>= 20.x` |
| MySQL | `>= 8.0` |

### PHP Extensions المطلوبة

```
BCMath · Ctype · Fileinfo · JSON · Mbstring
OpenSSL · PDO · PDO_MySQL · Tokenizer · XML
```

---

## 🚀 التثبيت والإعداد

### 1. استنساخ المستودع

```bash
git clone https://github.com/your-username/academic-accreditation-system.git
cd academic-accreditation-system
```

### 2. الإعداد التلقائي (موصى به)

```bash
composer run setup
```

> هذا الأمر يقوم بـ: تثبيت dependencies، نسخ `.env`، توليد `APP_KEY`، تشغيل المايغريشن، وبناء الأصول — كل ذلك بأمر واحد.

### 3. الإعداد اليدوي (خطوة بخطوة)

```bash
# تثبيت حزم PHP
composer install

# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# تثبيت حزم Node.js
npm install

# بناء الأصول الأمامية
npm run build
```

### 4. إعداد قاعدة البيانات

في ملف `.env`، قم بتعديل بيانات الاتصال:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caaqahe_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

ثم تشغيل المايغريشن (والـ Seeders إن وُجدت):

```bash
php artisan migrate
# أو مع بيانات تجريبية:
php artisan migrate --seed
```

### 5. إعداد البث الفوري (Laravel Reverb)

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST="127.0.0.1"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 6. إعداد البريد الإلكتروني

```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="نظام الاعتماد الأكاديمي - CAAQAHE"
```

---

## ▶️ تشغيل المشروع

### وضع التطوير (الكل في أمر واحد)

```bash
composer run dev
```

يُشغِّل هذا الأمر بشكل متوازٍ:
- 🌐 `php artisan serve` — خادم التطبيق
- ⚡ `npm run dev` — خادم Vite للأصول الأمامية
- 📡 `php artisan queue:listen` — معالجة قوائم الانتظار
- 📜 `php artisan pail` — مشاهدة السجلات مباشرة

### تشغيل Reverb (WebSockets) بشكل منفصل

```bash
php artisan reverb:start
```

### بناء الإنتاج (Production Build)

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📁 هيكل المشروع

```
academic-accreditation-system/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── stages/                    # كنترولرز المراحل (1-9)
│   │   │   │   ├── StageOneController.php
│   │   │   │   ├── StageTwoController.php
│   │   │   │   └── ... (حتى StageNine)
│   │   │   ├── AccreditationOfficer/      # كنترولرز مسؤول الاعتماد
│   │   │   ├── CouncilCoordinator/        # كنترولرز منسق المجلس
│   │   │   ├── CouncilSecretariat/        # كنترولرز أمانة المجلس
│   │   │   ├── Evaluator/                 # كنترولرز المُقيِّم
│   │   │   ├── ProgramCoordinator/        # كنترولرز منسق البرنامج
│   │   │   ├── PrintController.php        # طباعة التقارير والشهادات
│   │   │   └── PublicCertificateController.php  # التحقق العام من الشهادات
│   │   └── Requests/                      # Form Requests للتحقق من المدخلات
│   │
│   ├── Models/                            # نماذج Eloquent
│   │   ├── AccreditationRequest.php       # طلب الاعتماد
│   │   ├── FormSubmission.php             # تقديمات المراحل
│   │   ├── Standard.php                   # المعايير الأكاديمية
│   │   ├── SubStandard.php                # المعايير الفرعية
│   │   ├── Indicator.php                  # المؤشرات
│   │   ├── IndicatorEvaluation.php        # تقييم المؤشرات
│   │   ├── Evidence.php                   # الشواهد والأدلة
│   │   ├── Committee.php                  # لجنة التقييم
│   │   ├── CommitteeMember.php            # أعضاء اللجنة
│   │   ├── CommitteeReport.php            # تقرير اللجنة
│   │   ├── CommitteeApproval.php          # موافقات الأعضاء
│   │   ├── Evaluator.php                  # ملف المُقيِّم
│   │   ├── EvaluatorConflict.php          # تعارضات المصالح
│   │   ├── VisitSchedule.php              # جدول الزيارات الميدانية
│   │   ├── FinalDecision.php              # القرار النهائي
│   │   ├── AccreditationCertificate.php   # شهادة الاعتماد
│   │   ├── University.php                 # الجامعة
│   │   ├── College.php                    # الكلية
│   │   ├── Department.php                 # القسم
│   │   └── Program.php                    # البرنامج الأكاديمي
│   │
│   ├── Notifications/
│   │   └── RealTimeNotification.php       # إشعارات WebSocket الفورية
│   │
│   └── Mail/                              # قوالب البريد الإلكتروني
│
├── database/
│   ├── migrations/                        # مايغريشن قاعدة البيانات
│   ├── factories/                         # مصانع البيانات للاختبار
│   └── seeders/                           # بذور البيانات الأولية
│
├── resources/
│   └── views/                             # قوالب Blade
│
├── routes/                                # تعريف المسارات
└── tests/                                 # اختبارات Pest
```

---

## 🧰 التقنيات المستخدمة

### Backend

| التقنية | الإصدار | الغرض |
|---------|---------|-------|
| [Laravel](https://laravel.com) | 13.x | إطار العمل الأساسي |
| [Laravel Reverb](https://reverb.laravel.com) | 1.x | WebSockets للإشعارات الفورية |
| [Spatie Laravel PDF](https://spatie.be/docs/laravel-pdf) | 2.x | توليد ملفات PDF |
| [FPDF / FPDI](http://www.fpdf.org) | — | تخصيص قوالب شهادات PDF |
| [Simple QrCode](https://github.com/SimpleSoftwareIO/simple-qrcode) | — | توليد رموز QR للتحقق من الشهادات |
| [Pusher PHP Server](https://pusher.com) | 7.2 | دعم بروتوكول Broadcasting |

### Frontend

| التقنية | الإصدار | الغرض |
|---------|---------|-------|
| [Tailwind CSS](https://tailwindcss.com) | 4.x | تصميم الواجهات |
| [Alpine.js](https://alpinejs.dev) | 3.x | التفاعلية في الواجهة |
| [Laravel Echo](https://laravel.com/docs/broadcasting) | 2.x | استقبال البث الفوري |
| [FlyonUI](https://flyonui.com) | 2.x | مكونات UI جاهزة ومتكاملة |
| [Font Awesome](https://fontawesome.com) | 7.x | أيقونات |
| [SweetAlert2](https://sweetalert2.github.io) | 11.x | نوافذ تأكيد وتنبيه تفاعلية |
| [Signature Pad](https://github.com/szimek/signature_pad) | 5.x | التوقيع الرقمي |
| [Vite](https://vitejs.dev) | 8.x | تجميع الأصول الأمامية |

### أدوات التطوير

| الأداة | الغرض |
|--------|-------|
| [Pest PHP](https://pestphp.com) | إطار الاختبارات التلقائية |
| [Laravel Pint](https://laravel.com/docs/pint) | تنسيق الكود (Code Style) |
| [Laravel Pail](https://github.com/laravel/pail) | عرض السجلات في الـ Terminal |
| [Laravel Breeze](https://laravel.com/docs/starter-kits) | نظام المصادقة وإدارة الجلسات |
| [Laravel DebugBar](https://github.com/barryvdh/laravel-debugbar) | أداة تصحيح الأخطاء في بيئة التطوير |

---

## 🧪 الاختبارات

يستخدم المشروع **Pest PHP** لكتابة اختبارات وحدة (Unit Tests) واختبارات تكاملية (Feature Tests) تغطي مراحل الاعتماد وأدوار المستخدمين والمنطق الحيوي للنظام.

```bash
# تشغيل جميع الاختبارات
php artisan test --compact

# تشغيل اختبار بعينه
php artisan test --compact --filter=TestName

# تشغيل ملف اختبار محدد
php artisan test --compact tests/Feature/AccreditationTest.php
```

---

## 📄 الرخصة

هذا المشروع مرخص تحت **[MIT License](LICENSE)** — مفتوح المصدر وقابل للتطوير.

---

<p align="center">
  بُني بـ ❤️ لخدمة التعليم العالي اليمني · <a href="https://laravel.com">Laravel 13</a> · CAAQAHE &copy; 2025
</p>
