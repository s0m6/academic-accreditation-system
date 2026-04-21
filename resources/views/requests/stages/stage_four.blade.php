{{-- Stage Four: اختيار لجنة التقييم --}}
@php
    $userRole = $user->role;
    $isSecretariat = $userRole === 'council_secretariat';
    $isProgramCoord = $userRole === 'program_coordinator';

    // Active members (max 3 displayed)
    $activeMembers = $committee?->activeMembers ?? collect();

    // Filtering for Program Coordinator: hide pending response or rejected by member
    if ($isProgramCoord) {
        $activeMembers = $activeMembers->filter(fn($m) => !in_array($m->member_status, ['pending_invite', 'declined_by_member']));
    }

    // Pad to always show 3 card slots
    $memberSlots = collect([null, null, null]);
    foreach ($activeMembers->values() as $i => $member) {
        if ($i < 3) {
            $memberSlots[$i] = $member;
        }
    }

    // Check if all 3 are accepted → gate for committee approval
    $allAccepted = $activeMembers->count() === 3
        && $activeMembers->every(fn ($m) => $m->member_status === 'accepted');

    $committeeApproved = $committee?->status === 'approved';

    $statusMap = [
        'pending_invite'     => ['label' => 'في انتظار رد المقيم', 'color' => 'amber'],
        'pending_uni'        => ['label' => 'في انتظار موافقة الجامعة', 'color' => 'blue'],
        'declined_by_member' => ['label' => 'رفض المقيم', 'color' => 'red'],
        'declined_by_uni'    => ['label' => 'رفضت الجامعة', 'color' => 'red'],
        'accepted'           => ['label' => 'مقبول', 'color' => 'green'],
    ];
@endphp

<div class="w-full text-start space-y-6" x-data="{
    // Coordinator modal
    showCoordModal: false,
    selectedCoordId: '',

    // Big member-picker modal
    showPickerModal: false,
    pickerTargetMemberId: null,   // committee_member id (for replace), or null (for invite)
    isReplacing: false,

    // Picker state
    pickerSearch: '',
    pickerRankFilter: '',
    pickerSameCityFilter: false,
    pickerNoConflictFilter: false,
    pickerResults: [],
    pickerLoading: false,
    pickerSelectedId: null,
    pickerSelectedName: '',

    // Reject reason modal (member)
    showRejectModal: false,
    rejectMemberId: null,
    rejectReasons: [''],

    // Approve committee modal
    showApproveCommitteeModal: false,
    chairEvaluatorId: '',

    // View reject reasons modal
    showViewReasonsModal: false,
    viewReasons: [],

    // Open the member picker
    openPicker(memberId, isReplace) {
        this.pickerTargetMemberId = memberId;
        this.isReplacing = isReplace;
        this.pickerSearch = '';
        this.pickerRankFilter = '';
        this.pickerSameCityFilter = false;
        this.pickerNoConflictFilter = false;
        this.pickerResults = [];
        this.pickerSelectedId = null;
        this.pickerSelectedName = '';
        this.showPickerModal = true;
        this.$nextTick(() => this.searchEvaluators());
    },

    // Fetch evaluators via AJAX
    async searchEvaluators() {
        this.pickerLoading = true;
        const params = new URLSearchParams({
            search: this.pickerSearch,
            academic_rank: this.pickerRankFilter,
            same_city: this.pickerSameCityFilter ? '1' : '0',
            no_conflict: this.pickerNoConflictFilter ? '1' : '0',
        });
        try {
            const res = await fetch(`{{ route('requests.stage_four.search_evaluators', $accreditationRequest) }}?` + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.pickerResults = await res.json();
        } catch(e) {
            this.pickerResults = [];
        }
        this.pickerLoading = false;
    },

    selectEvaluator(id, name) {
        this.pickerSelectedId = id;
        this.pickerSelectedName = name;
    },

    addReason() { this.rejectReasons.push(''); },
    removeReason(i) { if (this.rejectReasons.length > 1) this.rejectReasons.splice(i, 1); },
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

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: منسق المجلس --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-user-tie text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">منسق المجلس</h3>
                    <p class="text-xs text-(--text-secondary)">المسؤول عن تنسيق عمل اللجنة من جانب المجلس</p>
                </div>
            </div>
            @if($isSecretariat && !$committeeApproved)
                <button type="button" @click="showCoordModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition-colors shadow-sm cursor-pointer">
                    <i class="fa-solid fa-{{ $accreditationRequest->council_coord_id ? 'pen' : 'plus' }}"></i>
                    {{ $accreditationRequest->council_coord_id ? 'تغيير المنسق' : 'اختيار المنسق' }}
                </button>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-6">
            @if($accreditationRequest->councilCoordinator)
                @php $coord = $accreditationRequest->councilCoordinator; @endphp
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 dark:bg-indigo-500/15 flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-500/30">
                        <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ mb_substr($coord->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-(--text-primary) text-lg">{{ $coord->name }}</p>
                        <p class="text-sm text-(--text-secondary)">{{ $coord->email }}</p>
                    </div>
                    <div class="ms-auto">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20 text-xs font-bold">
                            <i class="fa-solid fa-circle-check"></i> تم التعيين
                        </span>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border-2 border-dashed border-(--border-primary) flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-2xl text-(--text-secondary) opacity-50"></i>
                    </div>
                    <div>
                        <p class="font-bold text-(--text-primary)">لم يُختر منسق المجلس بعد</p>
                        @if($isSecretariat)
                            <p class="text-sm text-(--text-secondary) mt-1">اضغط على "اختيار المنسق" لتعيين منسق المجلس</p>
                        @else
                            <p class="text-sm text-(--text-secondary) mt-1">في انتظار تعيين منسق المجلس من قبل الأمانة</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: أعضاء اللجنة --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-users-gear text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">أعضاء لجنة التقييم</h3>
                    <p class="text-xs text-(--text-secondary)">يتطلب اختيار 3 مقيمين وموافقة الجميع لإتمام الاعتماد</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-(--text-secondary)">
                    {{ $activeMembers->count() }}/3 أعضاء
                </span>
                @if($allAccepted && $isSecretariat && !$committeeApproved)
                    <button type="button" @click="showApproveCommitteeModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold transition-colors shadow-sm shadow-green-500/20 cursor-pointer">
                        <i class="fa-solid fa-check-double"></i>
                        اعتماد اللجنة
                    </button>
                @endif
                @if($committeeApproved)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20 text-sm font-bold">
                        <i class="fa-solid fa-circle-check"></i> اللجنة معتمدة
                    </span>
                @endif
            </div>
        </div>

        {{-- 3-column member cards --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ($memberSlots as $slotIndex => $member)
                <div class="rounded-2xl border-2 {{ $member ? 'border-(--border-primary)' : 'border-dashed border-(--border-primary)' }} bg-(--bg-main) overflow-hidden flex flex-col">

                    {{-- Card Header --}}
                    <div class="px-4 py-3 border-b border-(--border-primary) flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                            {{ $member
                                ? ($member->member_status === 'accepted' ? 'bg-green-100 dark:bg-green-500/15 text-green-600 dark:text-green-400'
                                : ($member->member_status === 'pending_invite' ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400'
                                : ($member->member_status === 'pending_uni' ? 'bg-blue-100 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400'
                                : 'bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400')))
                                : 'bg-(--surface-card) text-(--text-secondary)' }}">
                            <i class="fa-solid fa-{{ $member ? 'user' : 'user-plus' }} text-xs"></i>
                        </div>
                        <span class="text-sm font-bold text-(--text-primary)">المقيم {{ $slotIndex + 1 }}</span>
                        @if($member)
                            @php $st = $statusMap[$member->member_status] ?? null; @endphp
                            @if($st)
                                <span class="ms-auto inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    {{ $st['color'] === 'green' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                                    : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                                    : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                                    : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20')) }}">
                                    {{ $st['label'] }}
                                </span>
                            @endif
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 flex-1 flex flex-col gap-3">
                        @if($member)
                            {{-- Evaluator info --}}
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-(--surface-card) border border-(--border-primary) flex items-center justify-center shrink-0">
                                    <span class="font-black text-lg text-(--text-primary)">{{ mb_substr($member->evaluator->user->name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ url('/council-secretariat/evaluators/' . $member->evaluator_id) }}" target="_blank"
                                       class="font-bold text-(--text-primary) truncate hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors block">
                                        {{ $member->evaluator->user->name }}
                                    </a>
                                    <p class="text-xs text-(--text-secondary) truncate">{{ $member->evaluator->academic_rank }}</p>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="space-y-2 text-xs text-(--text-secondary)">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-graduation-cap w-3.5"></i>
                                    <span class="truncate">{{ $member->evaluator->general_specialty }}</span>
                                </div>
                                @if($member->evaluator->city)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot w-3.5"></i>
                                        <span>{{ $member->evaluator->city->city_name }}</span>
                                    </div>
                                @endif

                                <div class="space-y-1.5 border-t border-(--border-primary) pt-2 mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-plus w-3.5 text-amber-500"></i>
                                        <span class="font-medium">طلب المشاركة:</span>
                                        <span>{{ $member->invite_sent_at?->format('Y/m/d H:i') ?: '—' }}</span>
                                    </div>

                                    @if($member->member_responded_at)
                                        @php
                                            $mApproved = !in_array($member->member_status, ['declined_by_member']);
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 {{ $mApproved ? 'text-indigo-600 dark:text-indigo-400' : 'text-red-600 dark:text-red-400' }}">
                                                <i class="fa-solid fa-calendar-check w-3.5"></i>
                                                <span class="font-medium">رد المقيم:</span>
                                                <span class="font-bold">({{ $mApproved ? 'بالموافقة' : 'بالرفض' }})</span>
                                            </div>
                                            <div class="ps-5.5 opacity-80">{{ $member->member_responded_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                    @endif

                                    @if($member->university_responded_at)
                                        @php
                                            $uApproved = $member->member_status === 'accepted';
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 {{ $uApproved ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                <i class="fa-solid fa-building-circle-check w-3.5"></i>
                                                <span class="font-medium">رد الجامعة:</span>
                                                <span class="font-bold">({{ $uApproved ? 'بالموافقة' : 'بالرفض' }})</span>
                                            </div>
                                            <div class="ps-5.5 opacity-80">{{ $member->university_responded_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Reject reasons (view) --}}
                            @if(in_array($member->member_status, ['declined_by_member', 'declined_by_uni']) && $member->reject_reason)
                                @php $isMemberReject = $member->member_status === 'declined_by_member'; @endphp
                                <button type="button"
                                    @click="showViewReasonsModal = true; viewReasons = {{ json_encode($member->reject_reason) }}"
                                    class="w-full text-xs text-red-600 dark:text-red-400 font-bold flex items-center gap-1.5 hover:underline cursor-pointer py-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i> 
                                    عرض أسباب الرفض ({{ $isMemberReject ? 'المقيم' : 'الجامعة' }})
                                </button>
                            @endif

                            {{-- Actions --}}
                            @if($isSecretariat && !$committeeApproved)
                                @if(in_array($member->member_status, ['declined_by_member', 'declined_by_uni']))
                                    <button type="button"
                                        @click="openPicker({{ $member->id }}, true)"
                                        class="w-full mt-auto inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition-colors shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-arrows-rotate"></i> استبدال المقيم
                                    </button>
                                @endif
                            @endif

                            {{-- Program coord actions —  pending_uni only --}}
                            @if($isProgramCoord && $member->member_status === 'pending_uni')
                                <div class="flex gap-2 mt-auto">
                                    <form method="POST" action="{{ route('program_coordinator.committee.approve', $member->id) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-check"></i> موافقة
                                        </button>
                                    </form>
                                    <button type="button"
                                        @click="showRejectModal = true; rejectMemberId = {{ $member->id }}; rejectReasons = ['']"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-red-50 dark:bg-red-500/10 hover:bg-red-100 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20 text-xs font-bold transition-colors cursor-pointer">
                                        <i class="fa-solid fa-xmark"></i> رفض
                                    </button>
                                </div>
                            @endif

                        @else
                            {{-- Empty slot --}}
                            <div class="flex-1 flex flex-col items-center justify-center py-6 text-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-(--surface-card) border-2 border-dashed border-(--border-primary) flex items-center justify-center">
                                    <i class="fa-solid fa-user-plus text-2xl text-(--text-secondary) opacity-40"></i>
                                </div>
                                <p class="text-sm text-(--text-secondary)">لم يُختر مقيم بعد</p>
                                @if($isSecretariat && !$committeeApproved)
                                    <button type="button"
                                        @click="openPicker(null, false)"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-(--text-primary) hover:opacity-80 text-(--bg-main) text-xs font-bold transition-opacity shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-user-plus"></i> اختيار مقيم
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Progress Banner --}}
        @if(!$committeeApproved)
            @php
                $acceptedCount = $activeMembers->where('member_status', 'accepted')->count();
                $progressPct = ($acceptedCount / 3) * 100;
            @endphp
            <div class="px-6 pb-6">
                <div class="rounded-xl p-4 bg-(--bg-main) border border-(--border-primary)">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-(--text-secondary)">تقدم قبول الأعضاء</span>
                        <span class="text-xs font-black text-(--text-primary)">{{ $acceptedCount }}/3</span>
                    </div>
                    <div class="w-full h-2 bg-(--border-primary) rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full transition-all duration-700"
                            style="width: {{ $progressPct }}%"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: اختيار منسق المجلس --}}
    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showCoordModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showCoordModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.away="showCoordModal = false"
                    class="relative w-full max-w-md rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">

                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-(--text-primary)">اختيار منسق المجلس</h3>
                                <p class="text-xs text-(--text-secondary)">اختر المنسق المسؤول عن هذا الطلب</p>
                            </div>
                        </div>
                        <button @click="showCoordModal = false" class="text-(--text-secondary) hover:text-(--text-primary) p-2 rounded-lg transition cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('requests.stage_four.assign_coordinator', $accreditationRequest) }}">
                        @csrf @method('PATCH')
                        <div class="p-5">
                            <label class="block text-sm font-bold text-(--text-primary) mb-2">المنسق</label>
                            <select name="coordinator_id" x-model="selectedCoordId" required
                                class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                                <option value="" disabled selected>— اختر منسقاً —</option>
                                @foreach ($coordinators as $coord)
                                    <option value="{{ $coord->id }}" {{ $accreditationRequest->council_coord_id === $coord->id ? 'selected' : '' }}>
                                        {{ $coord->name }} — {{ $coord->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="px-5 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showCoordModal = false"
                                class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--border-primary)/40 transition cursor-pointer">
                                إلغاء
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black shadow-lg shadow-indigo-500/20 transition cursor-pointer">
                                <i class="fa-solid fa-check me-1"></i> تأكيد التعيين
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: اختيار عضو اللجنة (Full-screen picker) --}}
    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showPickerModal" style="display:none" class="fixed inset-0 z-[300] flex flex-col" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div x-show="showPickerModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="relative flex flex-col m-4 md:m-8 lg:m-16 rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl overflow-hidden"
                style="max-height: calc(100vh - 4rem)">

                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20">
                            <i class="fa-solid fa-user-magnifying-glass text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)" x-text="isReplacing ? 'استبدال المقيم' : 'اختيار مقيم لللجنة'"></h3>
                            <p class="text-xs text-(--text-secondary)">ابحث وصفّ لاختيار المقيم المناسب</p>
                        </div>
                    </div>
                    <button @click="showPickerModal = false" class="text-(--text-secondary) hover:text-(--text-primary) p-2 rounded-lg transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                {{-- Filters bar --}}
                <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) shrink-0">
                    <div class="flex flex-wrap gap-3 items-end">
                        {{-- Search --}}
                        <div class="relative flex-1 min-w-48">
                            <i class="fa-solid fa-search absolute start-3.5 top-1/2 -translate-y-1/2 text-(--text-secondary) text-sm pointer-events-none"></i>
                            <input type="text" x-model="pickerSearch"
                                @input.debounce.400ms="searchEvaluators()"
                                placeholder="ابحث بالاسم أو التخصص..."
                                class="w-full pe-4 ps-10 py-2.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-(--text-primary) text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                        </div>

                        {{-- Academic rank --}}
                        <select x-model="pickerRankFilter" @change="searchEvaluators()"
                            class="rounded-xl border border-(--border-primary) bg-(--surface-card) text-(--text-primary) px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                            <option value="">جميع الدرجات العلمية</option>
                            <option value="Professor">أستاذ</option>
                            <option value="Associate Professor">أستاذ مشارك</option>
                            <option value="Assistant Professor">أستاذ مساعد</option>
                            <option value="Lecturer">محاضر</option>
                            <option value="Expert">خبير</option>
                        </select>

                        {{-- Checkboxes --}}
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2.5 rounded-xl border border-(--border-primary) bg-(--surface-card) hover:bg-(--bg-main) transition select-none">
                            <input type="checkbox" x-model="pickerSameCityFilter" @change="searchEvaluators()"
                                class="w-4 h-4 rounded accent-orange-500">
                            <span class="text-sm font-medium text-(--text-primary)">نفس مدينة الكلية</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2.5 rounded-xl border border-(--border-primary) bg-(--surface-card) hover:bg-(--bg-main) transition select-none">
                            <input type="checkbox" x-model="pickerNoConflictFilter" @change="searchEvaluators()"
                                class="w-4 h-4 rounded accent-orange-500">
                            <span class="text-sm font-medium text-(--text-primary)">بدون تعارض مصالح</span>
                        </label>
                    </div>
                </div>

                {{-- Results table --}}
                <div class="flex-1 overflow-y-auto">
                    {{-- Loading --}}
                    <div x-show="pickerLoading" class="flex items-center justify-center py-20">
                        <div class="flex flex-col items-center gap-3 text-(--text-secondary)">
                            <i class="fa-solid fa-spinner fa-spin text-3xl text-orange-500"></i>
                            <span class="text-sm font-medium">جاري البحث...</span>
                        </div>
                    </div>

                    {{-- No results --}}
                    <div x-show="!pickerLoading && pickerResults.length === 0" class="flex flex-col items-center justify-center py-20 text-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center">
                            <i class="fa-solid fa-users-slash text-2xl text-(--text-secondary)"></i>
                        </div>
                        <p class="text-(--text-secondary) font-medium">لا توجد نتائج مطابقة</p>
                    </div>

                    {{-- Table --}}
                    <table x-show="!pickerLoading && pickerResults.length > 0" class="w-full text-sm">
                        <thead class="sticky top-0 bg-(--bg-main) border-b border-(--border-primary) text-xs uppercase text-(--text-secondary)">
                            <tr>
                                <th class="px-5 py-3 font-bold text-start">المقيم</th>
                                <th class="px-5 py-3 font-bold text-center">الدرجة العلمية</th>
                                <th class="px-5 py-3 font-bold text-center">المدينة</th>
                                <th class="px-5 py-3 font-bold text-center">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-location-crosshairs text-orange-500"></i> نفس المدينة
                                    </span>
                                </th>
                                <th class="px-5 py-3 font-bold text-center">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-solid fa-handshake-slash text-red-500"></i> بلا تعارض
                                    </span>
                                </th>
                                <th class="px-5 py-3 font-bold text-center">اختيار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-(--border-primary)">
                            <template x-for="ev in pickerResults" :key="ev.id">
                                <tr :class="ev.already_selected ? 'opacity-40 pointer-events-none' : (pickerSelectedId === ev.id ? 'bg-orange-50 dark:bg-orange-500/5' : 'hover:bg-(--bg-main)')"
                                    class="transition-colors cursor-pointer"
                                    @click="!ev.already_selected && selectEvaluator(ev.id, ev.name)">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-(--surface-card) border border-(--border-primary) flex items-center justify-center shrink-0">
                                                <span class="font-black text-(--text-primary)" x-text="ev.name.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <a :href="`{{ url('/council-secretariat/evaluators') }}/${ev.id}`" target="_blank" 
                                                   class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline transition-colors" 
                                                   x-text="ev.name"></a>
                                                <p class="text-xs text-(--text-secondary)" x-text="ev.general_specialty"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-(--bg-main) border border-(--border-primary) text-(--text-primary)"
                                            x-text="ev.academic_rank"></span>
                                    </td>
                                    <td class="px-5 py-4 text-center text-(--text-secondary)" x-text="ev.city || '—'"></td>
                                    <td class="px-5 py-4 text-center">
                                        <i :class="ev.same_city ? 'fa-solid fa-circle-check text-green-500' : 'fa-solid fa-circle-xmark text-gray-300 dark:text-gray-600'"></i>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <i :class="!ev.has_conflict ? 'fa-solid fa-circle-check text-green-500' : 'fa-solid fa-triangle-exclamation text-red-500'"></i>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <template x-if="ev.already_selected">
                                            <span class="text-xs font-bold text-(--text-secondary)">موجود</span>
                                        </template>
                                        <template x-if="!ev.already_selected">
                                            <button type="button" @click.stop="selectEvaluator(ev.id, ev.name)"
                                                :class="pickerSelectedId === ev.id
                                                    ? 'bg-orange-500 text-white border-orange-500'
                                                    : 'bg-(--surface-card) text-(--text-primary) border-(--border-primary) hover:border-orange-400 hover:text-orange-600'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-bold transition cursor-pointer">
                                                <i :class="pickerSelectedId === ev.id ? 'fa-solid fa-check' : 'fa-solid fa-circle-plus'"></i>
                                                <span x-text="pickerSelectedId === ev.id ? 'محدد' : 'اختيار'"></span>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex items-center justify-between shrink-0 gap-4">
                    <div class="text-sm text-(--text-secondary)">
                        <template x-if="pickerSelectedId">
                            <span class="font-bold text-(--text-primary)">
                                <i class="fa-solid fa-user-check text-orange-500 me-1"></i>
                                المحدد: <span x-text="pickerSelectedName"></span>
                            </span>
                        </template>
                        <span x-show="!pickerSelectedId" class="text-(--text-secondary)">لم يتم الاختيار بعد</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showPickerModal = false"
                            class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition cursor-pointer">
                            إلغاء
                        </button>

                        {{-- INVITE form --}}
                        <form x-show="!isReplacing" method="POST"
                            action="{{ route('requests.stage_four.invite_member', $accreditationRequest) }}">
                            @csrf
                            <input type="hidden" name="evaluator_id" :value="pickerSelectedId">
                            <button type="submit" :disabled="!pickerSelectedId"
                                :class="pickerSelectedId ? 'bg-orange-500 hover:bg-orange-600 cursor-pointer' : 'bg-orange-300 cursor-not-allowed'"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-black shadow-lg shadow-orange-500/20 transition">
                                <i class="fa-solid fa-user-plus me-1"></i> إضافة للجنة
                            </button>
                        </form>

                        {{-- REPLACE form --}}
                        <form x-show="isReplacing" method="POST"
                            :action="`{{ url('/requests/' . $accreditationRequest->id . '/stage-four/replace-member/') }}/${pickerTargetMemberId}`">
                            @csrf @method('PATCH')
                            <input type="hidden" name="evaluator_id" :value="pickerSelectedId">
                            <button type="submit" :disabled="!pickerSelectedId"
                                :class="pickerSelectedId ? 'bg-orange-500 hover:bg-orange-600 cursor-pointer' : 'bg-orange-300 cursor-not-allowed'"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-black shadow-lg shadow-orange-500/20 transition">
                                <i class="fa-solid fa-arrows-rotate me-1"></i> استبدال
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: رفض عضو (من منسق البرنامج) --}}
    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showRejectModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showRejectModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showRejectModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">

                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20 shrink-0">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">رفض عضو اللجنة</h3>
                            <p class="text-xs text-(--text-secondary)">أدخل أسباب رفض الجامعة لهذا المقيم</p>
                        </div>
                    </div>

                    <form method="POST"
                        :action="`{{ url('/program-coordinator/committee-member/') }}/${rejectMemberId}/decline`">
                        @csrf @method('PATCH')
                        <div class="p-5 space-y-2">
                            <template x-for="(reason, i) in rejectReasons" :key="i">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-bold text-(--text-secondary) shrink-0" x-text="i + 1"></span>
                                    <input type="text" :name="`reasons[${i}]`" x-model="rejectReasons[i]" required
                                        placeholder="أدخل سبب الرفض..."
                                        class="flex-1 bg-(--bg-main) border border-(--border-primary) text-(--text-primary) text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
                                    <button type="button" @click="removeReason(i)" x-show="rejectReasons.length > 1"
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
                            <button type="button" @click="showRejectModal = false"
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

    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: اعتماد اللجنة واختيار الرئيس --}}
    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showApproveCommitteeModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showApproveCommitteeModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showApproveCommitteeModal = false"
                    class="relative w-full max-w-md rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start overflow-hidden">

                    {{-- Decorative top band --}}
                    <div class="h-1.5 bg-gradient-to-r from-orange-400 via-green-400 to-blue-400"></div>

                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 flex items-center justify-center border border-green-100 dark:border-green-500/20">
                            <i class="fa-solid fa-check-double text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-(--text-primary)">اعتماد اللجنة</h3>
                            <p class="text-xs text-(--text-secondary)">اختر رئيس اللجنة من الأعضاء المعتمدين</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('requests.stage_four.approve_committee', $accreditationRequest) }}">
                        @csrf @method('PATCH')
                        <div class="p-6 space-y-4">
                            <div class="p-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                                <div class="flex gap-3">
                                    <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400 mt-0.5 shrink-0"></i>
                                    <p class="text-sm text-green-800 dark:text-green-300 leading-relaxed font-medium">
                                        اكتمل الأعضاء الثلاثة. باعتماد اللجنة سينتقل الطلب إلى المرحلة الخامسة تلقائياً.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-(--text-primary) mb-2">
                                    <i class="fa-solid fa-crown text-amber-500 me-1"></i> رئيس اللجنة
                                </label>
                                <select name="chair_evaluator_id" x-model="chairEvaluatorId" required
                                    class="w-full rounded-xl border border-(--border-primary) bg-(--bg-main) text-(--text-primary) px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                                    <option value="" disabled selected>— اختر رئيساً —</option>
                                    @foreach ($activeMembers->where('member_status', 'accepted') as $m)
                                        <option value="{{ $m->evaluator_id }}">{{ $m->evaluator->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-3">
                            <button type="button" @click="showApproveCommitteeModal = false"
                                class="px-5 py-2.5 rounded-xl border border-(--border-primary) text-(--text-primary) text-sm font-bold hover:bg-(--bg-main) transition cursor-pointer">
                                إلغاء
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-black shadow-lg shadow-green-500/20 transition cursor-pointer">
                                <i class="fa-solid fa-flag-checkered me-1"></i> اعتماد اللجنة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: عرض أسباب الرفض --}}
    {{-- ═══════════════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showViewReasonsModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showViewReasonsModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showViewReasonsModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start">

                    <div class="px-6 py-5 border-b border-(--border-primary) bg-(--bg-main) flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-100 dark:border-red-500/20">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <h3 class="font-bold text-(--text-primary)">أسباب الرفض</h3>
                        </div>
                        <button @click="showViewReasonsModal = false" class="text-(--text-secondary) hover:text-(--text-primary) p-2 rounded-lg transition cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="p-5">
                        <ul class="space-y-3">
                            <template x-for="(reason, i) in viewReasons" :key="i">
                                <li class="flex items-start gap-3 bg-(--bg-main) border border-(--border-primary) rounded-xl p-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0"></div>
                                    <p class="text-sm font-medium text-(--text-primary) leading-relaxed" x-text="reason"></p>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <div class="px-5 py-4 border-t border-(--border-primary) bg-(--bg-main) flex justify-end">
                        <button @click="showViewReasonsModal = false"
                            class="px-6 py-2 rounded-xl bg-orange-500 text-white text-xs font-black shadow-lg shadow-orange-500/20 transition cursor-pointer">
                            فهمت ذلك
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
