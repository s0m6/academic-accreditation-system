<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء مقيم جديد | نظام الاعتماد الأكاديمي</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .tab-active {
            background: linear-gradient(135deg, #3b82f6, #7c3aed);
            color: white;
        }

        .fade-in {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .btn-gradient {
            background: linear-gradient(135deg, #3b82f6, #7c3aed);
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
        }

        .conflict-row,
        .attach-row {
            animation: fadeIn 0.3s ease;
        }

        select,
        input,
        textarea {
            transition: border-color 0.2s, box-shadow 0.2s;
        }
    </style>
</head>

<body class="bg-dark-900 text-gray-200 min-h-screen font-tajawal" x-data="evaluatorForm()">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    <!-- Header -->
    <div class="bg-dark-800 border-b border-gray-700/50">
        <div class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-11 h-11 rounded-xl btn-gradient flex items-center justify-center text-white text-lg font-bold shadow-lg">
                    م</div>
                <div>
                    <h1 class="text-xl font-bold text-white">إنشاء مقيم جديد</h1>
                    <p class="text-xs text-gray-400 mt-0.5">نظام الاعتماد الأكاديمي</p>
                </div>
            </div>
            <span class="text-xs text-gray-500 bg-gray-800 px-3 py-1.5 rounded-full border border-gray-700/50">لوحة
                الإدارة</span>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">

        <!-- Tabs -->
        <div class="flex gap-2 mb-8 bg-dark-800 p-1.5 rounded-2xl border border-gray-700/30">
            <template x-for="(tab, i) in tabs" :key="i">
                <button @click="activeTab = i"
                    :class="activeTab === i ? 'tab-active shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700/30'"
                    class="flex-1 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <span x-text="tab.icon"></span>
                    <span x-text="tab.name"></span>
                </button>
            </template>
        </div>

        <!-- Tab 1: Personal Data -->
        <div x-show="activeTab === 0" x-transition class="fade-in">
            <div class="glass rounded-2xl p-8">
                <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">👤 البيانات الشخصية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">الاسم الكامل <span
                                class="text-red-400">*</span></label>
                        <input x-model="form.name" type="text" name="name" placeholder="أدخل اسم المقيم"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                    </div>
                    <!-- email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">البريد الإلكتروني <span
                                class="text-red-400">*</span></label>
                        <input x-model="form.email" type="email" name="email" placeholder="example@email.com" dir="ltr"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                    </div>
                    <!-- city_id -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">المدينة <span
                                class="text-red-400">*</span></label>
                        <select x-model="form.city_id" name="city_id"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                            <option value="">-- اختر المدينة --</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id" x-text="city.name"></option>
                            </template>
                        </select>
                    </div>
                    <!-- phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">رقم الهاتف</label>
                        <input x-model="form.phone" type="tel" name="phone" placeholder="01XXXXXXXX" dir="ltr"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                    </div>
                    <!-- mobile -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">رقم الجوال <span
                                class="text-red-400">*</span></label>
                        <input x-model="form.mobile" type="tel" name="mobile" placeholder="05XXXXXXXX" dir="ltr"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                    </div>
                    <!-- general_specialty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">التخصص العام <span
                                class="text-red-400">*</span></label>
                        <input x-model="form.general_specialty" type="text" name="general_specialty"
                            placeholder="مثال: علوم الحاسب"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                    </div>
                    <!-- detailed_specialty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">التخصص الدقيق <span
                                class="text-red-400">*</span></label>
                        <input x-model="form.detailed_specialty" type="text" name="detailed_specialty"
                            placeholder="مثال: الذكاء الاصطناعي"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                    </div>
                    <!-- scientific_degree -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">الدرجة العلمية <span
                                class="text-red-400">*</span></label>
                        <select x-model="form.scientific_degree" name="scientific_degree"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                            <option value="">-- اختر الدرجة --</option>
                            <option value="بكالوريوس">بكالوريوس</option>
                            <option value="ماجستير">ماجستير</option>
                            <option value="دكتوراه">دكتوراه</option>
                            <option value="أستاذ مساعد">أستاذ مساعد</option>
                            <option value="أستاذ مشارك">أستاذ مشارك</option>
                            <option value="أستاذ">أستاذ</option>
                        </select>
                    </div>
                    <!-- current_university_id -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">يعمل حالياً لدى</label>
                        <select x-model="form.current_university_id" name="current_university_id"
                            class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                            <option value="">لا يعمل حالياً</option>
                            <template x-for="uni in universities" :key="uni.id">
                                <option :value="uni.id" x-text="uni.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Conflicts -->
        <div x-show="activeTab === 1" x-transition class="fade-in">
            <div class="glass rounded-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">⚖️ تعارض المصالح</h2>
                    <button @click="addConflict()"
                        class="btn-gradient text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2">
                        <span class="text-lg">+</span> إضافة تعارض
                    </button>
                </div>
                <div x-show="conflicts.length === 0" class="text-center py-16 text-gray-500">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="font-medium">لا يوجد تعارض مصالح مُضاف</p>
                    <p class="text-sm mt-1">اضغط على "إضافة تعارض" لإدراج تعارض جديد</p>
                </div>
                <div class="space-y-4">
                    <template x-for="(c, idx) in conflicts" :key="idx">
                        <div class="conflict-row bg-dark-800/60 border border-gray-700/50 rounded-xl p-5">
                            <div class="flex items-start justify-between mb-3">
                                <span
                                    class="text-xs font-bold text-primary-400 bg-primary-500/10 px-3 py-1 rounded-full"
                                    x-text="'تعارض #' + (idx+1)"></span>
                                <button @click="conflicts.splice(idx, 1)"
                                    class="text-red-400 hover:text-red-300 hover:bg-red-500/10 p-1.5 rounded-lg transition">✕</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-400 mb-1.5">نص التعارض (conflict_text)</label>
                                    <textarea x-model="c.conflict_text" name="conflict_text[]" rows="3"
                                        placeholder="اكتب تفاصيل تعارض المصلحة..."
                                        class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none resize-none text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">الجامعة (university_id)</label>
                                    <select x-model="c.university_id" name="conflict_university_id[]"
                                        class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none text-sm">
                                        <option value="">-- اختر الجامعة --</option>
                                        <template x-for="uni in universities" :key="uni.id">
                                            <option :value="uni.id" x-text="uni.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Tab 3: Attachments -->
        <div x-show="activeTab === 2" x-transition class="fade-in">
            <div class="glass rounded-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">📎 المرفقات</h2>
                    <button @click="addAttachment()"
                        class="btn-gradient text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2">
                        <span class="text-lg">+</span> إضافة مرفق
                    </button>
                </div>
                <div x-show="attachments.length === 0" class="text-center py-16 text-gray-500">
                    <p class="text-4xl mb-3">📁</p>
                    <p class="font-medium">لا توجد مرفقات مُضافة</p>
                    <p class="text-sm mt-1">اضغط على "إضافة مرفق" لإرفاق ملف جديد</p>
                </div>
                <div class="space-y-4">
                    <template x-for="(a, idx) in attachments" :key="idx">
                        <div class="attach-row bg-dark-800/60 border border-gray-700/50 rounded-xl p-5">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-xs font-bold text-accent-400 bg-accent-500/10 px-3 py-1 rounded-full"
                                    x-text="'مرفق #' + (idx+1)"></span>
                                <button @click="attachments.splice(idx, 1)"
                                    class="text-red-400 hover:text-red-300 hover:bg-red-500/10 p-1.5 rounded-lg transition">✕</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">اسم المرفق (name)</label>
                                    <input x-model="a.name" type="text" name="attachment_name[]"
                                        placeholder="مثال: السيرة الذاتية"
                                        class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1.5">الملف (path)</label>
                                    <input type="file" :id="'file_'+idx"
                                        @change="a.file = $event.target.files[0]; a.fileName = $event.target.files[0]?.name || ''"
                                        class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-500 text-sm cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Navigation & Submit -->
        <div class="flex items-center justify-between mt-8">
            <button x-show="activeTab > 0" @click="activeTab--"
                class="px-6 py-3 rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700/30 font-semibold text-sm transition flex items-center gap-2">
                → السابق
            </button>
            <div x-show="activeTab === 0"></div>
            <div class="flex gap-3">
                <button x-show="activeTab < 2" @click="activeTab++"
                    class="btn-gradient text-white px-8 py-3 rounded-xl font-semibold text-sm flex items-center gap-2">
                    التالي ←
                </button>
                <button x-show="activeTab === 2" @click="submitForm()"
                    class="bg-gradient-to-l from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white px-10 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 hover:-translate-y-0.5 flex items-center gap-2">
                    ✓ إنشاء مقيم
                </button>
            </div>
        </div>

        <!-- Steps Indicator -->
        <div class="flex items-center justify-center gap-3 mt-6">
            <template x-for="(tab, i) in tabs" :key="'dot'+i">
                <div class="flex items-center gap-3">
                    <div :class="i <= activeTab ? 'bg-primary-500 scale-110' : 'bg-gray-700'"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-300"></div>
                    <div x-show="i < tabs.length - 1" :class="i < activeTab ? 'bg-primary-500' : 'bg-gray-700'"
                        class="w-10 h-0.5 rounded transition-all duration-300"></div>
                </div>
            </template>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccess" x-transition.opacity
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div x-show="showSuccess" x-transition.scale
            class="bg-dark-800 border border-gray-700 rounded-2xl p-10 text-center max-w-md mx-4 shadow-2xl">
            <div class="w-20 h-20 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-5"><span
                    class="text-4xl">✅</span></div>
            <h3 class="text-xl font-bold text-white mb-2">تم إنشاء المقيم بنجاح!</h3>
            <p class="text-gray-400 text-sm mb-6">تم حفظ جميع البيانات والمرفقات وتعارضات المصالح</p>
            <button @click="showSuccess = false; resetForm()"
                class="btn-gradient text-white px-8 py-3 rounded-xl font-semibold text-sm">إغلاق</button>
        </div>
    </div>

    <script>
        function evaluatorForm() {
            return {
                activeTab: 0,
                showSuccess: false,
                tabs: [
                    { name: 'البيانات الشخصية', icon: '👤' },
                    { name: 'تعارض المصالح', icon: '⚖️' },
                    { name: 'المرفقات', icon: '📎' }
                ],
                cities: [
                    { id: 1, name: 'الرياض' }, { id: 2, name: 'جدة' }, { id: 3, name: 'مكة المكرمة' },
                    { id: 4, name: 'المدينة المنورة' }, { id: 5, name: 'الدمام' }, { id: 6, name: 'الخبر' },
                    { id: 7, name: 'تبوك' }, { id: 8, name: 'أبها' }, { id: 9, name: 'القصيم' }, { id: 10, name: 'حائل' }
                ],
                universities: [
                    { id: 1, name: 'جامعة الملك سعود' }, { id: 2, name: 'جامعة الملك عبدالعزيز' },
                    { id: 3, name: 'جامعة الملك فهد للبترول والمعادن' }, { id: 4, name: 'جامعة الإمام محمد بن سعود' },
                    { id: 5, name: 'جامعة أم القرى' }, { id: 6, name: 'جامعة الملك خالد' },
                    { id: 7, name: 'جامعة القصيم' }, { id: 8, name: 'جامعة طيبة' },
                    { id: 9, name: 'جامعة تبوك' }, { id: 10, name: 'جامعة الأميرة نورة' }
                ],
                form: { name: '', email: '', city_id: '', phone: '', mobile: '', general_specialty: '', detailed_specialty: '', scientific_degree: '', current_university_id: '' },
                conflicts: [],
                attachments: [],
                addConflict() { this.conflicts.push({ conflict_text: '', university_id: '' }); },
                addAttachment() { this.attachments.push({ name: '', file: null, fileName: '' }); },
                submitForm() {
                    if (!this.form.name || !this.form.email || !this.form.mobile) {
                        alert('يرجى تعبئة الحقول المطلوبة: الاسم، البريد الإلكتروني، رقم الجوال');
                        this.activeTab = 0; return;
                    }
                    // Build payload matching DB schema
                    const payload = {
                        user: { name: this.form.name, email: this.form.email, password: 'auto_generated', role: 'evaluator', phone: this.form.phone, mobile: this.form.mobile },
                        evaluator: { city_id: this.form.city_id || null, general_specialty: this.form.general_specialty, detailed_specialty: this.form.detailed_specialty, scientific_degree: this.form.scientific_degree, current_university_id: this.form.current_university_id || null },
                        evaluator_conflicts: this.conflicts.filter(c => c.conflict_text && c.university_id),
                        evaluator_attachments: this.attachments.filter(a => a.name)
                    };
                    console.log('Payload to send to API:', JSON.stringify(payload, null, 2));
                    this.showSuccess = true;
                },
                resetForm() {
                    this.form = { name: '', email: '', city_id: '', phone: '', mobile: '', general_specialty: '', detailed_specialty: '', scientific_degree: '', current_university_id: '' };
                    this.conflicts = []; this.attachments = []; this.activeTab = 0;
                }
            }
        }
    </script>
</body>

</html>