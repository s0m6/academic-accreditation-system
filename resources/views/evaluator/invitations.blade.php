@extends('partials.app')

@section('title', 'طلبات الدعوة للتقييم')
@section('title2', 'دعوات التقييم')
@section('description', 'الدعوات الموجهة إليك للمشاركة في لجان التقييم الأكاديمي')

@section('content')
<div class="space-y-6" x-data="{
    showDeclineModal: false,
    declineMemberId: null,
    declineActionUrl: '',
    declineReasons: [''],
    addReason() { this.declineReasons.push(''); },
    removeReason(i) { if (this.declineReasons.length > 1) this.declineReasons.splice(i, 1); },

    openDecline(memberId, url) {
        this.declineMemberId = memberId;
        this.declineActionUrl = url;
        this.declineReasons = [''];
        this.showDeclineModal = true;
    },

    showAcceptModal: false,
    acceptActionUrl: '',
    openAccept(url) {
        this.acceptActionUrl = url;
        this.showAcceptModal = true;
    }
}">

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-bold shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-xl shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($invitations->isEmpty())
        {{-- Empty state --}}
        <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm p-16 flex flex-col items-center justify-center text-center gap-5">
            <div class="w-20 h-20 rounded-3xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-envelope-open-text text-3xl text-(--text-secondary) opacity-50"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-(--text-primary)">لا توجد دعوات حالياً</h3>
                <p class="text-sm text-(--text-secondary) mt-2 max-w-sm mx-auto leading-relaxed">
                    ستظهر هنا الدعوات الموجهة إليك للمشاركة في لجان تقييم البرامج الأكاديمية.
                </p>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($invitations as $invitation)
                    @php
                        $request = $invitation->committee->accreditationRequest;
                        $program = $request->program;
                        $dept = $program->department;
                        $college = $dept->college;
                        $university = $college->university;

                        $statusConfig = [
                            'pending_invite'     => ['label' => 'في انتظار ردك', 'color' => 'amber', 'icon' => 'clock'],
                            'pending_uni'        => ['label' => 'بانتظار موافقة الجامعة', 'color' => 'blue', 'icon' => 'hourglass-half'],
                            'declined_by_member' => ['label' => 'رفضت الدعوة', 'color' => 'red', 'icon' => 'circle-xmark'],
                            'declined_by_uni'    => ['label' => 'رفضت الجامعة', 'color' => 'red', 'icon' => 'circle-xmark'],
                            'accepted'           => ['label' => 'مقبولة ومؤكدة', 'color' => 'green', 'icon' => 'circle-check'],
                            'canceled'           => ['label' => 'ملغاة من الأمانة', 'color' => 'gray', 'icon' => 'ban'],
                        ];
                        $st = $statusConfig[$invitation->member_status] ?? $statusConfig['pending_invite'];

                        // Dynamic check: if member accepted and university responded, but committee not finalized
                        if ($invitation->member_status === 'accepted' && $invitation->committee->status === 'forming') {
                            $st = [
                                'label' => 'وافقت الجامعة - بانتظار الأمانة',
                                'color' => 'blue',
                                'icon' => 'hourglass-half'
                            ];
                        }
                    @endphp

                    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden {{ $invitation->member_status === 'canceled' ? 'opacity-70 grayscale' : '' }}">
                        {{-- Card Header --}}
                        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shrink-0">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-(--text-primary)">{{ $program->program_name }}</h3>
                                    <p class="text-xs text-(--text-secondary)">{{ $university->name }} — {{ $college->name }}</p>
                                </div>
                            </div>

                            {{-- Status badge --}}
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border
                                {{ $st['color'] === 'green' ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                                : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                                : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                                : ($st['color'] === 'gray' ? 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20'
                                : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'))) }}">
                                <i class="fa-solid fa-{{ $st['icon'] }}"></i>
                                {{ $st['label'] }}
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                {{-- University --}}
                                <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                                    <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-1">الجامعة</p>
                                    <p class="font-bold text-(--text-primary)">{{ $university->name }}</p>
                                </div>

                                {{-- College --}}
                                <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                                    <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-1">الكلية</p>
                                    <p class="font-bold text-(--text-primary) truncate">{{ $college->name }}</p>
                                </div>

                                {{-- Program --}}
                                <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                                    <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-1">البرنامج</p>
                                    <p class="font-bold text-(--text-primary) truncate">{{ $program->program_name }}</p>
                                </div>

                                {{-- Current Phase --}}
                                <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                                    <p class="text-xs font-bold text-(--text-secondary) uppercase tracking-wider mb-1">المرحلة الحالية</p>
                                    <p class="font-bold text-(--text-primary)">{{ $st['label'] }}</p>
                                </div>
                            </div>

                            {{-- Timeline - History --}}
                            <div class="mb-8">
                                <h4 class="text-xs font-black text-(--text-secondary) uppercase tracking-[0.1em] flex items-center gap-2 mb-5">
                                    <i class="fa-solid fa-clock-rotate-left"></i> تسلسل حالة الدعوة
                                </h4>
                                <div class="relative space-y-6 before:content-[''] before:absolute before:right-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-(--border-primary)">
                                    
                                    {{-- 1. Invitation Sent --}}
                                    <div class="relative ps-8">
                                        <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-500/10 border-2 border-orange-500 flex items-center justify-center z-10">
                                            <i class="fa-solid fa-paper-plane text-[10px] text-orange-600"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-(--text-primary)">إرسال الدعوة من أمانة المجلس</span>
                                            <span class="text-[11px] text-(--text-secondary)">{{ $invitation->invite_sent_at?->format('Y/m/d H:i') ?: '—' }} ({{ $invitation->invite_sent_at?->diffForHumans() }})</span>
                                        </div>
                                    </div>

                                    {{-- 2. Evaluator Response --}}
                                    @if($invitation->member_responded_at)
                                        @php $mApproved = !in_array($invitation->member_status, ['declined_by_member']); @endphp
                                        <div class="relative ps-8">
                                            <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full {{ $mApproved ? 'bg-indigo-100 dark:bg-indigo-500/10 border-2 border-indigo-500' : 'bg-red-100 dark:bg-red-500/10 border-2 border-red-500' }} flex items-center justify-center z-10">
                                                <i class="fa-solid fa-{{ $mApproved ? 'check' : 'xmark' }} text-[10px] {{ $mApproved ? 'text-indigo-600' : 'text-red-600' }}"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-(--text-primary)">ردك على الدعوة:</span>
                                                    <span class="text-xs font-black {{ $mApproved ? 'text-indigo-600' : 'text-red-600' }}">({{ $mApproved ? 'بالموافقة' : 'بالاعتذار' }})</span>
                                                </div>
                                                <span class="text-[11px] text-(--text-secondary)">{{ $invitation->member_responded_at->format('Y/m/d H:i') }}</span>
                                                @if(!$mApproved && $invitation->reject_reason)
                                                    <p class="text-xs text-red-500/80 mt-1 italic">الأسباب: {{ implode('، ', $invitation->reject_reason) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 3. University Response --}}
                                    @if($invitation->university_responded_at)
                                        @php $uApproved = !in_array($invitation->member_status, ['declined_by_uni']); @endphp
                                        <div class="relative ps-8">
                                            <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full {{ $uApproved ? 'bg-green-100 dark:bg-green-500/10 border-2 border-green-500' : 'bg-red-100 dark:bg-red-500/10 border-2 border-red-500' }} flex items-center justify-center z-10">
                                                <i class="fa-solid fa-building-circle-{{ $uApproved ? 'check' : 'xmark' }} text-[10px] {{ $uApproved ? 'text-green-600' : 'text-red-600' }}"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-(--text-primary)">رد الجامعة (منسق البرنامج):</span>
                                                    <span class="text-xs font-black {{ $uApproved ? 'text-green-600' : 'text-red-600' }}">({{ $uApproved ? 'بالموافقة' : 'بالرفض' }})</span>
                                                </div>
                                                <span class="text-[11px] text-(--text-secondary)">{{ $invitation->university_responded_at->format('Y/m/d H:i') }}</span>
                                                @if(!$uApproved && $invitation->reject_reason)
                                                    <p class="text-xs text-red-500/80 mt-1 italic">الأسباب: {{ is_array($invitation->reject_reason) ? implode('، ', $invitation->reject_reason) : $invitation->reject_reason }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 4. Final Steps - Waiting for Secretariat vs Official Approval --}}
                                    @if($invitation->member_status === 'accepted')
                                        @if($invitation->committee->status === 'forming')
                                            <div class="relative ps-8">
                                                <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-500/10 border-2 border-blue-500 flex items-center justify-center z-10">
                                                    <i class="fa-solid fa-hourglass-start text-[10px] text-blue-600"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-blue-600">وافقت الجامعة - بانتظار اعتماد الأمانة النهائي</span>
                                                    <span class="text-[11px] text-(--text-secondary)">تم استكمال موافقات الأطراف وبانتظار الإجراء الإداري الأخير</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="relative ps-8">
                                                <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full bg-green-600 border-2 border-green-600 flex items-center justify-center z-10 shadow-lg shadow-green-500/40">
                                                    <i class="fa-solid fa-flag-checkered text-[10px] text-white"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-green-600">تم اعتمادك عضواً نهائياً في اللجنة</span>
                                                    <span class="text-[11px] text-(--text-secondary)">اكتملت جميع مراحل الموافقة واعتماد اللجنة</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- 5. Cancellation (If happened) --}}
                                    @if($invitation->member_status === 'canceled')
                                        <div class="relative ps-8">
                                            <div class="absolute right-0 top-1.5 w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-500/10 border-2 border-gray-500 flex items-center justify-center z-10">
                                                <i class="fa-solid fa-ban text-[10px] text-gray-600"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-600">إلغاء الطلب من قبل الأمانة</span>
                                                <span class="text-[11px] text-(--text-secondary)">{{ $invitation->updated_at->format('Y/m/d H:i') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        {{-- Action buttons for pending_invite --}}
                        @if($invitation->member_status === 'pending_invite')
                            <div class="flex items-center gap-3 pt-4 border-t border-(--border-primary) flex-wrap">
                                <button type="button"
                                    @click="openAccept('{{ route('evaluator.invitations.accept', $invitation) }}')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold transition-colors shadow-md shadow-green-500/20 cursor-pointer">
                                    <i class="fa-solid fa-circle-check"></i> قبول الدعوة
                                </button>

                                <button type="button"
                                    @click="openDecline({{ $invitation->id }}, '{{ route('evaluator.invitations.decline', $invitation) }}')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-50 dark:bg-red-500/10 hover:bg-red-100 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20 text-sm font-bold transition-colors cursor-pointer">
                                    <i class="fa-solid fa-circle-xmark"></i> رفض الدعوة
                                </button>
                            </div>
                        @elseif($invitation->member_status === 'pending_uni')
                            <div class="flex items-center gap-3 pt-4 border-t border-(--border-primary)">
                                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                                    <i class="fa-solid fa-clock text-blue-500 dark:text-blue-400"></i>
                                    <p class="text-sm font-bold text-blue-700 dark:text-blue-300">قبلت الدعوة — في انتظار مراجعة الجامعة</p>
                                </div>
                            </div>
                        @elseif($invitation->member_status === 'accepted')
                            <div class="flex items-center gap-3 pt-4 border-t border-(--border-primary)">
                                @if($invitation->committee->status === 'forming')
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                                        <i class="fa-solid fa-clock text-blue-500 dark:text-blue-400"></i>
                                        <p class="text-sm font-bold text-blue-700 dark:text-blue-300">وافقت الجامعة بنجاح — في انتظار اعتماد الأمانة النهائي للجنة</p>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                                        <i class="fa-solid fa-circle-check text-green-500 dark:text-green-400"></i>
                                        <p class="text-sm font-bold text-green-700 dark:text-green-300">تم اعتمادك عضواً في اللجنة بنجاح</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- MODAL: رفض الدعوة --}}
    <template x-teleport="body">
        <div x-show="showDeclineModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showDeclineModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showDeclineModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">

                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shrink-0">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">رفض الدعوة</h3>
                            <p class="text-xs text-(--text-secondary)">يرجى توضيح أسباب رفضك للمشاركة في هذه اللجنة</p>
                        </div>
                    </div>

                    <form method="POST" :action="declineActionUrl">
                        @csrf @method('PATCH')
                        <div class="p-5 space-y-2">
                            <template x-for="(reason, i) in declineReasons" :key="i">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-bold text-(--text-secondary) shrink-0" x-text="i + 1"></span>
                                    <input type="text" :name="`reasons[${i}]`" x-model="declineReasons[i]" required
                                        placeholder="أدخل سبب الرفض..."
                                        class="flex-1 bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
                                    <button type="button" @click="removeReason(i)" x-show="declineReasons.length > 1"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition cursor-pointer">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addReason()"
                                class="inline-flex items-center gap-2 text-xs font-bold text-(--text-secondary) hover:text-(--text-primary) transition cursor-pointer mt-1">
                                <i class="fa-solid fa-plus-circle"></i> إضافة سبب آخر
                            </button>
                        </div>
                        <div class="px-5 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showDeclineModal = false"
                                class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition cursor-pointer">
                                إلغاء
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-black transition cursor-pointer">
                                <i class="fa-solid fa-circle-xmark me-1"></i> تأكيد الرفض
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    {{-- MODAL: قبول الدعوة --}}
    <template x-teleport="body">
        <div x-show="showAcceptModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showAcceptModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showAcceptModal = false"
                    class="relative w-full max-w-md rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start overflow-hidden">
                    
                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 flex items-center justify-center border border-green-100 dark:border-green-500/20">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">تأكيد قبول الدعوة</h3>
                            <p class="text-xs text-(--text-secondary)">سيتم تسجيل موافقتك على المشاركة في اللجنة</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-sm text-(--text-primary) leading-relaxed">
                            هل أنت متأكد من رغبتك في قبول هذه الدعوة؟ بقبولك للدعوة، فإنك تؤكد استعدادك للبدء في عملية التقييم الأكاديمي لهذا البرنامج وفق الجداول الزمنية المحددة.
                        </p>
                    </div>

                    <form method="POST" :action="acceptActionUrl">
                        @csrf @method('PATCH')
                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showAcceptModal = false"
                                class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition cursor-pointer">
                                إلغاء
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black shadow-lg shadow-green-500/20 transition cursor-pointer">
                                <i class="fa-solid fa-check me-1"></i> تأكيد القبول
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
