<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $mode = $mode ?? 'create';
        $title = match($mode) {
            'create' => 'إنشاء مقيم جديد',
            'edit'   => 'تعديل بيانات المقيم',
            'show'   => 'عرض بيانات المقيم',
        };
        
        $actionUrl = match($mode) {
            'create' => route('council_secretariat.evaluators.store'),
            'edit'   => route('council_secretariat.evaluators.update', $evaluator->id ?? 0),
            'show'   => '#',
        };
        $isReadOnly = $mode === 'show';
    @endphp
    <title>{{ $title }} | نظام الاعتماد الأكاديمي</title>

    {{-- Google Font: Tajawal --}}
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">

    {{-- Vite compiled assets (Tailwind v4 + Alpine.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Tajawal', sans-serif; }

        body { background-color: #0f1219 !important; color: #e5e7eb; }

        .bg-dark-900  { background-color: #0f1219 !important; }
        .bg-dark-800  { background-color: #1a1f2e !important; }
        .bg-dark-800\/60 { background-color: rgba(26,31,46,0.6) !important; }

        .border-gray-700  { border-color: #374151 !important; }
        .border-gray-700\/50 { border-color: rgba(55,65,81,0.5) !important; }
        .border-gray-700\/30 { border-color: rgba(55,65,81,0.3) !important; }

        .focus\:border-primary-500:focus { border-color: #3b82f6 !important; }
        .text-primary-400 { color: #60a5fa !important; }
        .bg-primary-500\/10 { background-color: rgba(59,130,246,0.1) !important; }
        .file\:bg-primary-600::file-selector-button { background-color: #2563eb !important; }
        .hover\:file\:bg-primary-500:hover::file-selector-button { background-color: #3b82f6 !important; }

        .text-accent-400  { color: #a78bfa !important; }
        .bg-accent-500\/10 { background-color: rgba(139,92,246,0.1) !important; }

        /* Original design classes */
        .glass {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .input-glow:not([readonly]):not(:disabled):focus {
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .tab-active {
            background: linear-gradient(135deg,#3b82f6,#7c3aed);
            color: white;
        }
        .fade-in { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn {
            from { opacity:0; transform:translateY(10px) }
            to   { opacity:1; transform:translateY(0) }
        }
        .btn-gradient {
            background: linear-gradient(135deg,#3b82f6,#7c3aed);
            transition: all 0.3s;
        }
        .btn-gradient:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59,130,246,0.35);
        }
        .conflict-row,
        .attach-row { animation: fadeIn 0.3s ease; }
        select, input, textarea { transition: border-color 0.2s, box-shadow 0.2s; }
        
        [readonly], [disabled] {
            cursor: not-allowed;
            opacity: 0.8;
        }
    </style>
</head>

<body class="bg-dark-900 text-gray-200 min-h-screen font-tajawal" x-data="evaluatorForm()">

    {{-- Validation errors banner --}}
    @if($errors->any())
    <div class="bg-red-900/40 border-b border-red-700/50 px-6 py-3">
        <div class="max-w-5xl mx-auto flex items-start gap-3">
            <span class="text-red-400 mt-0.5 shrink-0">⚠</span>
            <ul class="text-red-300 text-sm list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-dark-800 border-b border-gray-700/50">
        <div class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl btn-gradient flex items-center justify-center text-white text-lg font-bold shadow-lg">
                    @if($mode === 'create') م @elseif($mode === 'edit') ت @else ع @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $title }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">{{ isset($evaluator) ? $evaluator->user->name . ' - ' : '' }}نظام الاعتماد الأكاديمي</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($mode === 'create')
                <button type="submit" form="ev-form" class="btn-gradient text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer">
                    ✓ إنشاء مقيم
                </button>
                @elseif($mode === 'edit')
                <button type="submit" form="ev-form" class="btn-gradient text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer">
                    ✓ حفظ التعديلات
                </button>
                @elseif($mode === 'show')
                <a href="{{ route('council_secretariat.evaluators.edit', $evaluator->id) }}" class="bg-emerald-600/90 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/20 hover:-translate-y-0.5 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square"></i> تعديل البيانات
                </a>
                @endif
                <a href="{{ route('council_secretariat.dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white px-5 py-2.5 rounded-xl text-sm font-semibold border border-gray-700/50 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-house"></i> لوحة الإدارة
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8">

        {{-- Tabs --}}
        <div class="flex gap-2 mb-8 bg-dark-800 p-1.5 rounded-2xl border border-gray-700/30">
            <template x-for="(tab, i) in tabs" :key="i">
                <button type="button" @click="activeTab = i"
                    :class="activeTab === i ? 'tab-active shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700/30'"
                    class="flex-1 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <span x-text="tab.icon"></span>
                    <span x-text="tab.name"></span>
                </button>
            </template>
        </div>

        {{-- ============ FORM ============ --}}
        <form method="{{ $mode === 'show' ? 'GET' : 'POST' }}" action="{{ $actionUrl }}" enctype="multipart/form-data" id="ev-form" @submit.prevent="submitForm" novalidate>
            @if($mode !== 'show')
                @csrf
            @endif
            @if($mode === 'edit')
                @method('PUT')
            @endif

            {{-- Hidden inputs for deleted attachments in edit mode --}}
            @if($mode === 'edit')
            <template x-for="id in deletedAttachments" :key="id">
                <input type="hidden" name="deleted_attachments[]" :value="id">
            </template>
            @endif

            {{-- ===== Tab 1: Personal Data ===== --}}
            <div x-show="activeTab === 0" x-transition class="fade-in">
                <div class="glass rounded-2xl p-8">
                    <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">👤 البيانات الشخصية</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">الاسم الكامل @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <input type="text" name="name" value="{{ old('name', $evaluator->user->name ?? '') }}" placeholder="أدخل اسم المقيم"
                                {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                        </div>

                        {{-- email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">البريد الإلكتروني @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <input type="email" name="email" value="{{ old('email', $evaluator->user->email ?? '') }}" placeholder="example@email.com" dir="ltr"
                                {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                        </div>

                        {{-- city_id --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">المدينة @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <select name="city_id"
                                {{ $isReadOnly ? 'disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                                <option value="">-- اختر المدينة --</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $evaluator->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->city_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">رقم الهاتف</label>
                            <input type="tel" name="phone" value="{{ old('phone', $evaluator->user->phone ?? '') }}" placeholder="01XXXXXXXX" dir="ltr"
                                {{ $isReadOnly ? 'readonly disabled' : '' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                        </div>

                        {{-- mobile --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">رقم الجوال @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <input type="tel" name="mobile" value="{{ old('mobile', $evaluator->user->mobile ?? '') }}" placeholder="05XXXXXXXX" dir="ltr"
                                {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-left">
                        </div>

                        {{-- general_specialty --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">التخصص العام @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <input type="text" name="general_specialty" value="{{ old('general_specialty', $evaluator->general_specialty ?? '') }}" placeholder="مثال: علوم الحاسب"
                                {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                        </div>

                        {{-- detailed_specialty --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">التخصص الدقيق @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <input type="text" name="detailed_specialty" value="{{ old('detailed_specialty', $evaluator->detailed_specialty ?? '') }}" placeholder="مثال: الذكاء الاصطناعي"
                                {{ $isReadOnly ? 'readonly disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none">
                        </div>

                        {{-- academic_rank --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">الدرجة العلمية @if(!$isReadOnly)<span class="text-red-400">*</span>@endif</label>
                            <select name="academic_rank"
                                {{ $isReadOnly ? 'disabled' : 'required' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                                <option value="">-- اختر الدرجة --</option>
                                @php $currentRank = old('academic_rank', $evaluator->academic_rank ?? ''); @endphp
                                <option value="Professor"            {{ $currentRank === 'Professor'            ? 'selected' : '' }}>أستاذ</option>
                                <option value="Associate Professor"  {{ $currentRank === 'Associate Professor'  ? 'selected' : '' }}>أستاذ مشارك</option>
                                <option value="Assistant Professor"  {{ $currentRank === 'Assistant Professor'  ? 'selected' : '' }}>أستاذ مساعد</option>
                                <option value="Lecturer"             {{ $currentRank === 'Lecturer'             ? 'selected' : '' }}>محاضر</option>
                                <option value="Expert"               {{ $currentRank === 'Expert'               ? 'selected' : '' }}>خبير</option>
                            </select>
                        </div>

                        {{-- current_university_id --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">يعمل حالياً لدى</label>
                            <select name="current_university_id"
                                {{ $isReadOnly ? 'disabled' : '' }}
                                class="w-full bg-dark-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none">
                                <option value="">لا يعمل حالياً</option>
                                @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}" {{ old('current_university_id', $evaluator->current_university_id ?? '') == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Tab 2: Conflicts ===== --}}
            <div x-show="activeTab === 1" x-transition class="fade-in">
                <div class="glass rounded-2xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">⚖️ تعارض المصالح</h2>
                        @if(!$isReadOnly)
                        <button type="button" @click="addConflict()"
                            class="btn-gradient text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer">
                            <span class="text-lg">+</span> إضافة تعارض
                        </button>
                        @endif
                    </div>
                    <div x-show="conflicts.length === 0" class="text-center py-16 text-gray-500">
                        <p class="text-4xl mb-3">📋</p>
                        <p class="font-medium">لا يوجد تعارض مصالح مُضاف</p>
                        @if(!$isReadOnly)<p class="text-sm mt-1">اضغط على "إضافة تعارض" لإدراج تعارض جديد</p>@endif
                    </div>
                    <div class="space-y-4">
                        <template x-for="(c, idx) in conflicts" :key="idx">
                            <div class="conflict-row bg-dark-800/60 border border-gray-700/50 rounded-xl p-5">
                                <div class="flex items-start justify-between mb-3">
                                    <span class="text-xs font-bold text-primary-400 bg-primary-500/10 px-3 py-1 rounded-full"
                                        x-text="'تعارض #' + (idx+1)"></span>
                                    @if(!$isReadOnly)
                                    <button type="button" @click="conflicts.splice(idx, 1)"
                                        class="text-red-400 hover:text-red-300 hover:bg-red-500/10 p-1.5 rounded-lg transition cursor-pointer">✕</button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs text-gray-400 mb-1.5">نص التعارض</label>
                                        <textarea
                                            :name="'conflicts[' + idx + '][conflict_text]'"
                                            x-model="c.conflict_text" rows="3"
                                            {{ $isReadOnly ? 'readonly disabled' : '' }}
                                            placeholder="اكتب تفاصيل تعارض المصلحة..."
                                            class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none resize-none text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1.5">الجامعة</label>
                                        <select
                                            :name="'conflicts[' + idx + '][university_id]'"
                                            x-model="c.university_id"
                                            {{ $isReadOnly ? 'disabled' : '' }}
                                            class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-primary-500 input-glow outline-none appearance-none text-sm">
                                            <option value="">-- اختر الجامعة --</option>
                                            <template x-for="uni in universities" :key="uni.id">
                                                <option :value="uni.id" x-text="uni.name" :selected="c.university_id == uni.id"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ===== Tab 3: Attachments ===== --}}
            <div x-show="activeTab === 2" x-transition class="fade-in">
                <div class="glass rounded-2xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">📎 المرفقات @if(!$isReadOnly)(ملفات PDF فقط)@endif</h2>
                        @if(!$isReadOnly)
                        <button type="button" @click="addAttachment()"
                            class="btn-gradient text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer">
                            <span class="text-lg">+</span> إضافة مرفق
                        </button>
                        @endif
                    </div>

                    {{-- Existing attachments list (for edit/show) --}}
                    <div x-show="existingAttachments.length > 0" class="mb-6 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-400 bg-gray-800 border-l-2 border-primary-500 pl-3 pr-2 py-1.5 rounded inline-block mb-2">المرفقات المسجلة</h3>
                        <template x-for="(ea, index) in existingAttachments" :key="ea.id">
                            <div class="attach-row bg-dark-800/60 border border-gray-700/50 rounded-xl p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-dark-900 border border-gray-700 flex items-center justify-center text-red-400">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-200 truncate max-w-[200px] md:max-w-sm" x-text="ea.name"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <template x-if="ea.view_url">
                                        <a :href="ea.view_url" target="_blank"
                                            class="bg-blue-600/20 hover:bg-blue-600/40 border border-blue-500/30 text-blue-300 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            عرض
                                        </a>
                                    </template>
                                    @if($mode === 'edit')
                                    <button type="button" @click="deleteExistingAttachment(index)"
                                        class="text-red-400 border border-red-500/30 hover:bg-red-500/20 bg-red-500/10 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition">
                                        حذف
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="attachments.length === 0 && existingAttachments.length === 0" class="text-center py-16 text-gray-500">
                        <p class="text-4xl mb-3">📁</p>
                        <p class="font-medium">لا توجد مرفقات مُضافة أو مُسجلة</p>
                        @if(!$isReadOnly)<p class="text-sm mt-1">اضغط على "إضافة مرفق" لإرفاق ملف PDF جديد</p>@endif
                    </div>
                    
                    {{-- New attachments --}}
                    @if(!$isReadOnly)
                    <div x-show="attachments.length > 0">
                        <h3 x-show="existingAttachments.length > 0" class="text-sm font-semibold text-gray-400 bg-gray-800 border-l-2 border-primary-500 pl-3 pr-2 py-1.5 rounded inline-block mb-3">مرفقات جديدة مراد رفعها</h3>
                        <div class="space-y-4">
                            <template x-for="(a, idx) in attachments" :key="idx">
                                <div class="attach-row new-attach bg-dark-800/60 border border-gray-700/50 rounded-xl p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <span class="text-xs font-bold text-accent-400 bg-accent-500/10 px-3 py-1 rounded-full"
                                            x-text="'مرفق جديد #' + (idx+1)"></span>
                                        <button type="button" @click="attachments.splice(idx, 1)"
                                            class="text-red-400 hover:text-red-300 hover:bg-red-500/10 p-1.5 rounded-lg transition cursor-pointer">✕</button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-400 mb-1.5">اسم المرفق</label>
                                            <input type="text"
                                                :name="'attachments[' + idx + '][name]'"
                                                x-model="a.name"
                                                placeholder="مثال: السيرة الذاتية"
                                                class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 input-glow outline-none text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-400 mb-1.5">ملف PDF <span class="text-red-400">*</span></label>
                                            <input type="file" accept=".pdf"
                                                :name="'attachments[' + idx + '][file]'"
                                                :id="'file_' + idx" required
                                                class="w-full bg-dark-900 border border-gray-700 rounded-xl px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-500 text-sm cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Navigation & Submit --}}
            <div class="flex items-center justify-between mt-8">
                <button type="button" x-show="activeTab > 0" @click="activeTab--"
                    class="px-6 py-3 rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700/30 font-semibold text-sm transition flex items-center gap-2 cursor-pointer">
                    → السابق
                </button>
                <div x-show="activeTab === 0"></div>
                <div class="flex gap-3">
                    <button type="button" x-show="activeTab < 2" @click="activeTab++"
                        class="btn-gradient text-white px-8 py-3 rounded-xl font-semibold text-sm flex items-center gap-2 cursor-pointer">
                        التالي ←
                    </button>
                    @if(!$isReadOnly)
                    <button type="submit" x-show="activeTab === 2"
                        class="btn-gradient text-white px-10 py-3 rounded-xl font-bold text-sm shadow-lg hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer">
                        {{ $mode === 'edit' ? '✓ حفظ التعديلات' : '✓ إنشاء مقيم' }}
                    </button>
                    @else
                    <a href="{{ route('council_secretariat.evaluators.edit', $evaluator->id) }}" x-show="activeTab === 2"
                        class="bg-emerald-600/90 hover:bg-emerald-500 text-white px-10 py-3 rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/20 hover:-translate-y-0.5 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-pen-to-square"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('council_secretariat.evaluators.index') }}" x-show="activeTab === 2"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        عودة للقائمة
                    </a>
                    @endif
                </div>
            </div>

            {{-- Steps Indicator --}}
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

        </form>
        {{-- ============ END FORM ============ --}}
    </div>

    {{-- Font Awesome & Swal loaded globally via Vite (app.css / app.js) --}}
    <script>
        function evaluatorForm() {
            @php
                $existingConflicts = isset($evaluator) ? $evaluator->conflicts->map(fn($c) => ['conflict_text' => $c->conflict_text, 'university_id' => $c->university_id])->toArray() : [];
                $oldConflicts = old('conflicts', $existingConflicts);
                
                $existingAttachments = isset($evaluator) ? $evaluator->attachments->map(fn($a) => [
                    'id' => $a->id, 
                    'name' => $a->name,
                    'view_url' => route('council_secretariat.evaluators.attachments.view', [$evaluator->id, $a->id])
                ])->toArray() : [];
            @endphp
            
            return {
                isDirty: false,
                activeTab: 0,
                tabs: [
                    { name: 'البيانات الشخصية', icon: '👤' },
                    { name: 'تعارض المصالح',    icon: '⚖️' },
                    { name: 'المرفقات',          icon: '📎' }
                ],
                // Data from backend
                universities: @json($universities->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])),
                conflicts: @json($oldConflicts),
                existingAttachments: @json($existingAttachments),
                attachments: [], // New attachments
                deletedAttachments: [], // Track deleted existing attachment IDs

                init() {
                    const form = document.getElementById('ev-form');
                    form.addEventListener('input', () => this.isDirty = true);
                    form.addEventListener('change', () => this.isDirty = true);
                    
                    if ('{{ $mode }}' !== 'show') {
                        document.addEventListener('click', (e) => {
                            let link = e.target.closest('a');
                            if (link && link.href && link.target !== '_blank' && !link.hasAttribute('download') && link.href.startsWith(window.location.origin) && !link.href.includes('#')) {
                                if (this.isDirty) {
                                    e.preventDefault();
                                    this.confirmExit(link.href);
                                }
                            }
                        });

                        window.addEventListener('beforeunload', (e) => {
                            if (this.isDirty) {
                                e.preventDefault();
                                e.returnValue = '';
                            }
                        });
                    }
                },

                confirmExit(targetUrl) {
                    Swal.fire({
                        title: 'توجد تغييرات غير محفوظة!',
                        text: 'هل ترغب بحفظ البيانات قبل المغادرة؟',
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'نعم، حفظ ومغادرة',
                        denyButtonText: 'مغادرة بدون حفظ',
                        cancelButtonText: 'إلغاء الأمر',
                        background: '#1a1f2e',
                        color: '#e5e7eb',
                        customClass: {
                            popup: 'border border-gray-700 rounded-3xl',
                            confirmButton: 'btn-gradient font-tajawal px-6 rounded-xl outline-none border-none',
                            denyButton: 'bg-red-600 hover:bg-red-500 font-tajawal px-6 rounded-xl outline-none border-none',
                            cancelButton: 'bg-gray-700 hover:bg-gray-600 font-tajawal px-6 rounded-xl outline-none border-none text-white'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Let the submit event take over
                            document.getElementById('ev-form').requestSubmit();
                        } else if (result.isDenied) {
                            this.isDirty = false;
                            window.location.href = targetUrl;
                        }
                    });
                },

                addConflict()   { this.conflicts.push({ conflict_text: '', university_id: '' }); },
                addAttachment() { this.attachments.push({ name: '' }); },
                deleteExistingAttachment(index) {
                    const id = this.existingAttachments[index].id;
                    this.deletedAttachments.push(id);
                    this.existingAttachments.splice(index, 1);
                    this.isDirty = true;
                },
                
                submitForm(e) {
                    const form = e.target;
                    
                    // 1. Process Conflicts
                    form.querySelectorAll('.conflict-row').forEach(row => {
                        let textInput = row.querySelector('textarea');
                        let select = row.querySelector('select');
                        if (textInput && select && !textInput.readOnly) {
                            if (!textInput.value.trim() && !select.value) {
                                textInput.disabled = true;
                                select.disabled = true;
                            } else {
                                textInput.required = true;
                                select.required = true;
                            }
                        }
                    });

                    // 2. Process Attachments
                    form.querySelectorAll('.attach-row.new-attach').forEach(row => {
                        let nameInput = row.querySelector('input[type="text"]');
                        let fileInput = row.querySelector('input[type="file"]');
                        if (nameInput && fileInput && !nameInput.readOnly) {
                            let hasFile = fileInput.files && fileInput.files.length > 0;
                            if (!nameInput.value.trim() && !hasFile) {
                                nameInput.disabled = true;
                                fileInput.disabled = true;
                                nameInput.required = false;
                                fileInput.required = false;
                            } else {
                                nameInput.required = true;
                                fileInput.required = true;
                            }
                        }
                    });

                    // 3. Validation
                    const requiredInputs = Array.from(form.querySelectorAll('[required]')).filter(el => !el.disabled);
                    let missingFields = [];
                    const fieldLabels = {
                        'name': 'الاسم الكامل',
                        'email': 'البريد الإلكتروني',
                        'city_id': 'المدينة',
                        'mobile': 'رقم الجوال',
                        'general_specialty': 'التخصص العام',
                        'detailed_specialty': 'التخصص الدقيق',
                        'academic_rank': 'الدرجة العلمية'
                    };

                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            if (input.name && fieldLabels[input.name]) {
                                missingFields.push(fieldLabels[input.name]);
                            } else if (input.name && input.name.includes('[file]')) {
                                missingFields.push('ملف المرفق (PDF) لصف المرفقات المعبأ جزئياً');
                            } else if (input.name && input.name.includes('[name]') && input.name.includes('attachments')) {
                                missingFields.push('اسم المرفق لصف المرفقات المعبأ جزئياً');
                            } else if (input.name && input.name.includes('[conflict_text]')) {
                                missingFields.push('نص التعارض لصف التعارضات المعبأ جزئياً');
                            } else if (input.name && input.name.includes('[university_id]')) {
                                missingFields.push('جامعة التعارض لصف التعارضات المعبأ جزئياً');
                            }
                        }
                    });

                    if (missingFields.length > 0) {
                        // Undo disabled state so user can fix it
                        form.querySelectorAll('.conflict-row, .attach-row.new-attach').forEach(r => {
                            r.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
                        });

                        const hasPersonalDataIssue = missingFields.some(f => !f.includes('المعبأ جزئياً'));
                        let msgHTML = '<p class="mb-3 text-base text-gray-200">يرجى تعبئة الحقول الإلزامية التالية:</p>';
                        if (hasPersonalDataIssue) {
                            msgHTML = '<p class="mb-3 text-sm text-gray-300">يوجد حقول إلزامية لم يتم إدخالها. يرجى التحقق من تبويب <b class="text-white">"البيانات الشخصية"</b> أو الأقسام الأخرى لتعبئتها:</p>';
                        }
                        
                        let listHTML = missingFields.map(f => '<li class="mb-1">' + f + '</li>').join('');
                        msgHTML += '<ul class="text-red-400 font-bold list-disc list-inside text-right">' + listHTML + '</ul>';

                        Swal.fire({
                            title: 'بيانات غير مكتملة',
                            html: msgHTML,
                            icon: 'warning',
                            confirmButtonText: 'حسناً',
                            background: '#1a1f2e',
                            color: '#e5e7eb',
                            customClass: {
                                popup: 'border border-gray-700 rounded-3xl',
                                confirmButton: 'btn-gradient font-tajawal px-8 py-2.5 rounded-xl border-none outline-none'
                            }
                        });
                        return false;
                    }

                    this.isDirty = false;
                    form.submit();
                }
            };
        }
    </script>
</body>

</html>