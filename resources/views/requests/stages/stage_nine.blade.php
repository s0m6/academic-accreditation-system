{{-- stage_nine: القرار النهائي --}}
@php
    $stageOrder = ['stage_one','stage_two','stage_three','stage_four','stage_five','stage_six','stage_seven','stage_eight','stage_nine'];
    $currentStageIndex = array_search($accreditationRequest->current_stage, $stageOrder);
    $thisStageIndex    = array_search('stage_nine', $stageOrder);
    $isLocked          = $currentStageIndex < $thisStageIndex;
    $isSecretariat     = $user->role === 'council_secretariat';
    $decision          = $finalDecision ?? null;
    $suggestion        = $stageNineSuggestion ?? [];
    $suggestedType     = $suggestion['suggested_type'] ?? null;
    $suggestedLevel    = $suggestion['level'] ?? null;
    $suggestedAverage  = $suggestion['average'] ?? null;

    $decisionMeta = \App\Models\FinalDecision::$decisionMeta;

    // Color helpers per decision group
    $approvalTypes = ['approved_achieved','approved_with_mastery','approved_with_excellence'];
    $rejectionTypes = ['rejected_partial','rejected_not_achieved'];
@endphp

@if($isLocked)
    <div class="flex flex-col items-center justify-center py-20 text-center gap-6">
        <div class="relative">
            <div class="w-24 h-24 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-700 shadow-inner">
                <i class="fa-solid fa-lock text-4xl"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-slate-900">
                <i class="fa-solid fa-hourglass-half text-sm"></i>
            </div>
        </div>
        <div class="max-w-md">
            <h3 class="text-xl font-bold text-(--text-primary) mb-2">المرحلة غير متاحة حالياً</h3>
            <p class="text-(--text-secondary) leading-relaxed">
                لا يمكنك الوصول إلى محتوى "القرار النهائي" حتى يتم الانتهاء من المراحل السابقة. الطلب حالياً في:
                <br>
                <span class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-bold border border-orange-200 dark:border-orange-500/20">
                    {{ $stages[$accreditationRequest->current_stage] ?? $accreditationRequest->current_stage }}
                </span>
            </p>
        </div>
    </div>

@elseif($decision)
    {{-- ===== DECISION ALREADY ISSUED ===== --}}
    @php
        $isApproved   = $decision->isApproved();
        $cert         = $decision->certificate;
        $certData     = $cert?->certificate_data ?? [];
        $decLabel     = $decision->decisionLabel();
        $meta         = $decisionMeta[$decision->decision_type];
    @endphp

    <div class="w-full space-y-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm">
                <i class="fa-solid fa-circle-check text-xl shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Decision Banner --}}
        <div class="rounded-2xl border-2 {{ $isApproved ? 'border-emerald-400 dark:border-emerald-500/60 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/5' : 'border-red-400 dark:border-red-500/60 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-500/10 dark:to-rose-500/5' }} shadow-sm overflow-hidden">
            <div class="px-6 py-5 flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl {{ $isApproved ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30' : 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/30' }} flex items-center justify-center text-3xl shadow-inner shrink-0">
                    <i class="fa-solid {{ $isApproved ? 'fa-award' : 'fa-ban' }}"></i>
                </div>
                <div>
                    <p class="text-xs font-bold {{ $isApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} mb-1 uppercase tracking-widest">القرار النهائي للمجلس</p>
                    <h2 class="text-2xl font-black text-(--text-primary)">
                        {{ $isApproved ? 'الموافقة على منح الاعتماد الأكاديمي' : 'عدم الموافقة على منح الاعتماد الأكاديمي' }}
                    </h2>
                    <p class="text-(--text-secondary) text-sm mt-1">بمستوى: <span class="font-black {{ $isApproved ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $decLabel }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 pb-6">
                <div class="rounded-xl bg-white/60 dark:bg-white/5 border border-white/80 dark:border-white/10 p-4">
                    <p class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ الإصدار</p>
                    <p class="font-black text-(--text-primary)">{{ $decision->issued_at->format('Y/m/d H:i') }}</p>
                </div>
                <div class="rounded-xl bg-white/60 dark:bg-white/5 border border-white/80 dark:border-white/10 p-4">
                    <p class="text-xs font-bold text-(--text-secondary) mb-1">مُصدَر بواسطة</p>
                    <p class="font-black text-(--text-primary)">{{ $decision->issuedBy->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-white/60 dark:bg-white/5 border border-white/80 dark:border-white/10 p-4">
                    <p class="text-xs font-bold text-(--text-secondary) mb-1">المتابعة</p>
                    <p class="font-black text-(--text-primary)">{{ $meta['followup'] }}</p>
                </div>
            </div>
        </div>

        @if($cert)
            {{-- Certificate Card --}}
            <div class="rounded-2xl border border-[#e9c176]/60 bg-gradient-to-br from-[#fdf8ed] to-[#fff9f0] dark:from-[#1a1608] dark:to-[#1c1a0a] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e9c176]/40 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#002546]/10 dark:bg-[#e9c176]/10 text-[#002546] dark:text-[#e9c176] flex items-center justify-center border border-[#002546]/20 dark:border-[#e9c176]/20 shadow-inner">
                            <i class="fa-solid fa-certificate text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">شهادة الاعتماد الأكاديمي</h3>
                            <p class="text-xs text-(--text-secondary)">رقم الشهادة: <span class="font-mono font-bold text-[#002546] dark:text-[#e9c176]">{{ substr($cert->certificate_number, 0, 8) }}...</span></p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold {{ $cert->isValid() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20' }}">
                        <i class="fa-solid {{ $cert->isValid() ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                        {{ $cert->isValid() ? 'سارية المفعول' : 'منتهية' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6">
                    <div>
                        <p class="text-xs font-bold text-(--text-secondary) mb-1">البرنامج</p>
                        <p class="font-black text-(--text-primary) text-sm">{{ $certData['program_name'] ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-(--text-secondary) mb-1">الجامعة</p>
                        <p class="font-black text-(--text-primary) text-sm">{{ $certData['university_name'] ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ الاعتماد</p>
                        <p class="font-black text-(--text-primary) text-sm">{{ $certData['issued_at'] ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-(--text-secondary) mb-1">تاريخ الانتهاء</p>
                        <p class="font-black text-(--text-primary) text-sm">{{ $certData['expires_at'] ?? '—' }}</p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('certificate.show', $cert->certificate_number) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#002546] hover:bg-[#001a33] text-white font-bold shadow-lg transition-colors">
                        <i class="fa-solid fa-certificate"></i>
                        عرض الشهادة الرسمية
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm opacity-70"></i>
                    </a>
                </div>
            </div>
        @endif

        @if($decision->notes)
            <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-5">
                <p class="text-xs font-bold text-(--text-secondary) mb-2"><i class="fa-solid fa-note-sticky ml-1"></i>ملاحظات القرار</p>
                <p class="text-(--text-primary) text-sm leading-relaxed">{{ $decision->notes }}</p>
            </div>
        @endif
    </div>

@else
    @if($isSecretariat)
    {{-- ===== DECISION FORM (Secretariat Only) ===== --}}
    <div class="w-full space-y-6"
         x-data="{
             selected: {{ $suggestedType ? "'" . $suggestedType . "'" : 'null' }},
             showConfirm: false,
             submitting: false,
             meta: {{ json_encode($decisionMeta) }},
             get isApproval() { return this.selected && ['approved_achieved','approved_with_mastery','approved_with_excellence'].includes(this.selected); },
             get label() { return this.selected ? this.meta[this.selected].label : ''; },
             get years() { return this.selected ? this.meta[this.selected].years : 0; },
             get followup() { return this.selected ? this.meta[this.selected].followup : ''; },
         }">

        {{-- Flash --}}
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-bold shadow-sm">
                <i class="fa-solid fa-circle-xmark text-xl shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Suggestion Banner from Stage 8 --}}
        @if($suggestedType)
        <div class="rounded-2xl border border-blue-200 dark:border-blue-500/30 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-500/10 dark:to-indigo-500/5 shadow-sm p-5 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-200 dark:border-blue-500/30 shadow-inner shrink-0">
                <i class="fa-solid fa-lightbulb text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mb-1 uppercase tracking-widest">اقتراح تلقائي — بناءً على تقييم اللجنة</p>
                <p class="font-black text-(--text-primary) text-lg">مستوى التحقق: <span class="text-blue-700 dark:text-blue-300">{{ $suggestedLevel }}</span></p>
                <p class="text-sm text-(--text-secondary) mt-1">المعدل الإجمالي: <strong>{{ $suggestedAverage }}</strong> — الاقتراح: <strong class="text-blue-700 dark:text-blue-300">{{ $decisionMeta[$suggestedType]['label'] }}</strong></p>
            </div>
        </div>
        @endif

        {{-- Decision Selector --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-gavel text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">إصدار القرار النهائي</h3>
                    <p class="text-xs text-(--text-secondary)">اختر قرار المجلس بشأن منح البرنامج الاعتماد الأكاديمي</p>
                </div>
            </div>

            <div class="p-6 space-y-4">

                {{-- Approval Options --}}
                <div>
                    <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        الموافقة على منح البرنامج الاعتماد الأكاديمي
                    </p>
                    <div class="space-y-3">
                        @foreach(['approved_achieved','approved_with_mastery','approved_with_excellence'] as $type)
                        @php $m = $decisionMeta[$type]; @endphp
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none"
                               :class="selected === '{{ $type }}' ? 'border-emerald-400 dark:border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 shadow-md' : 'border-(--border-primary) bg-(--bg-main) hover:border-emerald-200 dark:hover:border-emerald-500/30'">
                            <input type="radio" name="decision_type" value="{{ $type }}" x-model="selected" class="hidden">
                            <div class="w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center shrink-0"
                                 :class="selected === '{{ $type }}' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-gray-600'">
                                <div class="w-2 h-2 rounded-full bg-white" x-show="selected === '{{ $type }}'"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-(--text-primary)">{{ $m['label'] }}</p>
                                <p class="text-xs text-(--text-secondary) mt-0.5">{{ $m['followup'] }}</p>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/25">
                                {{ $m['years'] }} سنوات
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-(--border-primary) my-2"></div>

                {{-- Rejection Options --}}
                <div>
                    <p class="text-xs font-black text-red-600 dark:text-red-400 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        عدم الموافقة على منح البرنامج الاعتماد الأكاديمي
                    </p>
                    <div class="space-y-3">
                        @foreach(['rejected_not_achieved','rejected_partial'] as $type)
                        @php $m = $decisionMeta[$type]; @endphp
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none"
                               :class="selected === '{{ $type }}' ? 'border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-500/10 shadow-md' : 'border-(--border-primary) bg-(--bg-main) hover:border-red-200 dark:hover:border-red-500/30'">
                            <input type="radio" name="decision_type" value="{{ $type }}" x-model="selected" class="hidden">
                            <div class="w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center shrink-0"
                                 :class="selected === '{{ $type }}' ? 'border-red-500 bg-red-500' : 'border-gray-300 dark:border-gray-600'">
                                <div class="w-2 h-2 rounded-full bg-white" x-show="selected === '{{ $type }}'"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-(--text-primary)">{{ $m['label'] }}</p>
                                <p class="text-xs text-(--text-secondary) mt-0.5">{{ $m['followup'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Selected Summary --}}
                <div x-show="selected" style="display:none"
                     class="mt-4 p-4 rounded-xl border transition-all duration-300"
                     :class="isApproval ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20' : 'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/20'">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid text-xl" :class="isApproval ? 'fa-circle-check text-emerald-600 dark:text-emerald-400' : 'fa-circle-xmark text-red-600 dark:text-red-400'"></i>
                        <div>
                            <p class="font-black text-(--text-primary)">القرار المختار: <span x-text="label" :class="isApproval ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300'"></span></p>
                            <p class="text-xs text-(--text-secondary) mt-0.5" x-text="followup"></p>
                        </div>
                    </div>
                </div>
            </div>

            @if($isSecretariat)
            <div class="px-6 pb-6 flex justify-end">
                <button @click="if(selected) showConfirm = true; else alert('يرجى اختيار نوع القرار أولاً')"
                        :disabled="!selected"
                        class="px-8 py-3 rounded-xl font-black text-white shadow-lg transition-all duration-200 flex items-center gap-3 disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="selected ? (isApproval ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30' : 'bg-red-600 hover:bg-red-700 shadow-red-500/30') : 'bg-gray-400'">
                    <i class="fa-solid fa-gavel"></i>
                    إصدار القرار النهائي
                </button>
            </div>
            @endif
        </div>

        {{-- Confirm Modal --}}
        @if($isSecretariat)
        <template x-teleport="body">
            <div x-show="showConfirm" style="display:none" class="relative z-[300]">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showConfirm = false"></div>
                <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                    <div @click.away="showConfirm = false"
                         class="relative w-full max-w-md rounded-2xl bg-(--surface-card) shadow-2xl border border-(--border-primary) overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl border-2"
                                 :class="isApproval ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30' : 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/30'">
                                <i class="fa-solid" :class="isApproval ? 'fa-award' : 'fa-ban'"></i>
                            </div>
                            <h3 class="text-xl font-black text-(--text-primary) mb-2">تأكيد إصدار القرار النهائي</h3>
                            <p class="text-(--text-secondary) text-sm leading-relaxed mb-1">
                                سيتم إصدار قرار:
                            </p>
                            <p class="font-black text-lg mb-1" :class="isApproval ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="label"></p>
                            <p class="text-xs text-(--text-secondary)" x-text="followup"></p>
                            <p class="mt-3 text-xs text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl px-4 py-2">
                                <i class="fa-solid fa-triangle-exclamation ml-1"></i>
                                هذا الإجراء لا يمكن التراجع عنه. سيتم إغلاق الطلب نهائياً.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('requests.stage_nine.issue_decision', $accreditationRequest) }}"
                              @submit.prevent="submitting = true; $el.submit()">
                            @csrf
                            <input type="hidden" name="decision_type" :value="selected">
                            <div class="px-6 pb-5">
                                <label class="block text-xs font-bold text-(--text-secondary) mb-2">ملاحظات إضافية (اختياري)</label>
                                <textarea name="notes" rows="3" placeholder="أضف أي ملاحظات على القرار..."
                                          class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
                            </div>
                            <div class="px-6 pb-6 flex gap-3 justify-end border-t border-(--border-primary) pt-4 bg-(--bg-main)">
                                <button type="button" @click="showConfirm = false"
                                        class="px-5 py-2.5 rounded-xl border border-(--border-primary) font-bold text-(--text-primary) hover:bg-(--bg-main) transition">
                                    إلغاء
                                </button>
                                <button type="submit" :disabled="submitting"
                                        class="px-6 py-2.5 rounded-xl text-white font-black shadow-lg transition-all flex items-center gap-2 disabled:opacity-50"
                                        :class="isApproval ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'">
                                    <i class="fa-solid fa-gavel" x-show="!submitting"></i>
                                    <i class="fa-solid fa-spinner fa-spin" x-show="submitting"></i>
                                    <span x-text="submitting ? 'جاري الإصدار...' : 'تأكيد وإصدار القرار'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        @endif

    </div>
    @else
        {{-- ===== WAITING VIEW (Others) ===== --}}
        <div class="flex flex-col items-center justify-center py-20 text-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-3xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                    <i class="fa-solid fa-gavel text-4xl animate-pulse"></i>
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-blue-500 text-white flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-slate-900">
                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                </div>
            </div>
            <div class="max-w-md">
                <h3 class="text-xl font-bold text-(--text-primary) mb-2">بانتظار إصدار القرار النهائي</h3>
                <p class="text-(--text-secondary) leading-relaxed">
                    تم الانتهاء من جميع إجراءات التقييم الميداني والدراسة الذاتية. الطلب حالياً لدى <strong>أمانة المجلس</strong> للمراجعة النهائية وإصدار القرار الرسمي.
                </p>
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-xs font-bold border border-blue-100 dark:border-blue-500/20">
                    <i class="fa-solid fa-circle-info"></i>
                    سيتم إشعاركم فور صدور القرار النهائي وتوليد الشهادة.
                </div>
            </div>
        </div>
    @endif
@endif
