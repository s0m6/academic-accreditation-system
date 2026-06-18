@extends('partials.app')

@section('title', 'طلبات الدعوة للتقييم')
@section('title2', 'دعوات التقييم')
@section('description', 'الدعوات الموجهة إليك للمشاركة في لجان التقييم الأكاديمي')

@section('content')
<div class="space-y-10 pb-20" x-data="{
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
    },

    showMembersModal: false,
    selectedMembers: [],
    openMembers(members) {
        this.selectedMembers = members;
        this.showMembersModal = true;
    }
}">

    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-4 p-5 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 font-bold shadow-sm animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="w-10 h-10 rounded-xl bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/30 shrink-0">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-4 p-5 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 font-bold shadow-sm animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="w-10 h-10 rounded-xl bg-red-500 text-white flex items-center justify-center shadow-lg shadow-red-500/30 shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($invitations->isEmpty())
        {{-- Empty state --}}
        <div class="relative overflow-hidden rounded-[2.5rem] border border-(--border-primary) bg-(--surface-card) p-20 flex flex-col items-center justify-center text-center gap-6 shadow-2xl shadow-gray-200/20">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
            
            <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shadow-2xl shadow-orange-500/40 relative z-10">
                <i class="fa-solid fa-envelope-open-text text-4xl text-white"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-black text-(--text-primary)">لا توجد دعوات حالياً</h3>
                <p class="text-(--text-secondary) mt-3 max-w-sm mx-auto leading-relaxed font-medium">
                    سيتم إشعارك فور تلقي دعوة جديدة للمشاركة في لجان الاعتماد الأكاديمي.
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-12">
            @foreach ($invitations as $invitation)
                    @php
                        $request = $invitation->committee->accreditationRequest;
                        $program = $request->program;
                        $dept = $program->department;
                        $college = $dept->college;
                        $university = $college->university;

                        $statusConfig = [
                            'pending_invite'     => ['label' => 'بانتظار ردك', 'color' => 'amber', 'icon' => 'clock-rotate-left'],
                            'pending_uni'        => ['label' => 'بانتظار الجامعة', 'color' => 'blue', 'icon' => 'building-columns'],
                            'declined_by_member' => ['label' => 'تم اعتذارك', 'color' => 'red', 'icon' => 'user-xmark'],
                            'declined_by_uni'    => ['label' => 'رفضت الجامعة', 'color' => 'red', 'icon' => 'ban'],
                            'accepted'           => ['label' => 'مقبولة نهائياً', 'color' => 'green', 'icon' => 'certificate'],
                            'canceled'           => ['label' => 'ملغاة', 'color' => 'gray', 'icon' => 'xmark'],
                        ];
                        $st = $statusConfig[$invitation->member_status] ?? $statusConfig['pending_invite'];

                        if ($invitation->member_status === 'accepted' && $invitation->committee->status === 'forming') {
                            $st = ['label' => 'وافقت الجامعة - بانتظار الاعتماد النهائي', 'color' => 'blue', 'icon' => 'hourglass-half'];
                        }
                    @endphp

                    <div class="group relative bg-(--surface-card) rounded-[2.5rem] border border-(--border-primary) shadow-2xl shadow-gray-200/30 dark:shadow-none hover:shadow-orange-500/5 transition-all duration-500 overflow-hidden {{ $invitation->member_status === 'canceled' ? 'opacity-60 grayscale scale-[0.98]' : '' }}">
                        
                        {{-- Top Accent Gradient --}}
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-orange-400 via-orange-600 to-indigo-600 opacity-80"></div>

                        {{-- Main Header --}}
                        <div class="p-8 md:p-10 border-b border-(--border-primary) bg-gradient-to-b from-(--bg-main) to-transparent relative">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                                <div class="flex items-start md:items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center border border-orange-500/20 shadow-inner shrink-0 text-3xl group-hover:rotate-12 transition-transform duration-500">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                                            <h2 class="text-2xl md:text-3xl font-black text-(--text-primary) tracking-tight">{{ $program->program_name }}</h2>
                                            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-100 dark:border-indigo-500/20">
                                                {{ $program->degree_level }}
                                            </span>
                                        </div>
                                        <p class="text-base text-(--text-secondary) font-bold flex items-center gap-2">
                                            <i class="fa-solid fa-university text-orange-500/60"></i>
                                            {{ $university->name }} — <span class="opacity-75">{{ $college->name }}</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Summary Badge --}}
                                <div class="flex flex-col lg:items-end gap-3">
                                    <div class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl text-sm font-black shadow-lg
                                        {{ $st['color'] === 'green' ? 'bg-green-600 text-white shadow-green-500/30' 
                                        : ($st['color'] === 'amber' ? 'bg-orange-500 text-white shadow-orange-500/30'
                                        : ($st['color'] === 'blue' ? 'bg-blue-600 text-white shadow-blue-500/30'
                                        : ($st['color'] === 'red' ? 'bg-red-600 text-white shadow-red-500/30'
                                        : 'bg-gray-500 text-white shadow-gray-500/20'))) }}">
                                        <i class="fa-solid fa-{{ $st['icon'] }} text-lg"></i>
                                        {{ $st['label'] }}
                                    </div>
                                    <p class="text-[11px] text-(--text-secondary) font-black opacity-60 uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-history"></i> تم التحديث: <span dir="ltr">{{ $invitation->updated_at->format('Y/m/d | H:i') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 md:p-10">
                            {{-- Info Tiles Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                                <div class="bg-(--bg-main) border border-(--border-primary) rounded-3xl p-6 transition-all duration-300 hover:border-orange-500/30 hover:shadow-xl hover:shadow-orange-500/5 group/tile">
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-building-columns text-orange-500"></i> الجهة التعليمية
                                    </p>
                                    <p class="text-sm font-black text-(--text-primary) leading-tight">{{ $university->name }}</p>
                                </div>
                                <div class="bg-(--bg-main) border border-(--border-primary) rounded-3xl p-6 transition-all duration-300 hover:border-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/5 group/tile">
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-layer-group text-indigo-500"></i> القسم والكلية
                                    </p>
                                    <p class="text-sm font-black text-(--text-primary) leading-tight">{{ $college->name }} - {{ $dept->name }}</p>
                                </div>
                                <div class="bg-(--bg-main) border border-(--border-primary) rounded-3xl p-6 transition-all duration-300 hover:border-blue-500/30 hover:shadow-xl hover:shadow-blue-500/5 group/tile">
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-alt text-blue-500"></i> تاريخ وصول الدعوة
                                    </p>
                                    <p class="text-sm font-black text-(--text-primary) uppercase" dir="ltr">{{ $invitation->invite_sent_at?->format('Y / m / d') ?: '—' }}</p>
                                </div>
                                <div class="bg-(--bg-main) border border-(--border-primary) rounded-3xl p-6 transition-all duration-300 hover:border-green-500/30 hover:shadow-xl hover:shadow-green-500/5 group/tile">
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-green-500"></i> المركز المرجعي
                                    </p>
                                    <p class="text-sm font-black text-(--text-primary) font-mono">INV-{{ str_pad($invitation->id, 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col xl:flex-row items-start gap-12">
                                {{-- TIMELINE SECTION (Full width if no actions) --}}
                                <div class="flex-1 w-full relative">
                                    <div class="flex items-center gap-4 mb-10">
                                        <div class="w-1.5 h-8 bg-gradient-to-b from-orange-500 to-indigo-600 rounded-full"></div>
                                        <h4 class="text-lg font-black text-(--text-primary)">سجل سير إجراءات الدعوة</h4>
                                    </div>

                                    <div class="relative space-y-12 before:content-[''] before:absolute before:right-[15px] before:top-4 before:bottom-4 before:w-[3px] before:bg-gradient-to-b before:from-orange-100 before:via-(--border-primary) before:to-transparent dark:before:from-orange-500/20 pe-6">
                                        
                                        {{-- 1. SENT --}}
                                        <div class="relative ps-12">
                                            <div class="absolute right-0 top-1 w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center z-10 shadow-lg shadow-orange-500/40 ring-4 ring-orange-50 dark:ring-orange-500/10">
                                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                            </div>
                                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-[1.5rem] bg-(--bg-main) border border-(--border-primary) border-r-4 border-r-orange-500 shadow-sm">
                                                <div>
                                                    <p class="text-sm font-black text-(--text-primary)">تم توجيه الدعوة الرسمية</p>
                                                    <p class="text-xs text-(--text-secondary) mt-1 font-bold">من قبل أمانة المجلس</p>
                                                </div>
                                                <span class="px-4 py-1.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-xs font-black text-(--text-secondary) shadow-inner" dir="ltr">
                                                    {{ $invitation->invite_sent_at?->format('Y/m/d - H:i') ?: '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- 2. EVALUATOR RESPONSE --}}
                                        @if($invitation->member_responded_at)
                                            @php $mApproved = !in_array($invitation->member_status, ['declined_by_member']); @endphp
                                            <div class="relative ps-12">
                                                <div class="absolute right-0 top-1 w-8 h-8 rounded-xl {{ $mApproved ? 'bg-indigo-600' : 'bg-red-600' }} text-white flex items-center justify-center z-10 shadow-lg {{ $mApproved ? 'shadow-indigo-500/40' : 'shadow-red-500/40' }} ring-4 {{ $mApproved ? 'ring-indigo-50' : 'ring-red-50' }} dark:ring-opacity-10">
                                                    <i class="fa-solid fa-{{ $mApproved ? 'user-check' : 'user-xmark' }} text-xs"></i>
                                                </div>
                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-[1.5rem] bg-(--bg-main) border border-(--border-primary) border-r-4 {{ $mApproved ? 'border-r-indigo-600' : 'border-r-red-600' }} shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-black {{ $mApproved ? 'text-indigo-600' : 'text-danger' }}">ردك على طلب المشاركة</p>
                                                        <p class="text-xs text-(--text-secondary) mt-1 font-bold">الحالة: <span class="uppercase">{{ $mApproved ? 'موافقة' : 'اعتذار' }}</span></p>
                                                        @if(!$mApproved && $invitation->reject_reason)
                                                            <div class="mt-3 p-3 rounded-xl bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20">
                                                                <p class="text-[11px] text-red-600 font-bold leading-relaxed">الأسباب: {{ is_array($invitation->reject_reason) ? implode('، ', $invitation->reject_reason) : $invitation->reject_reason }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span class="px-4 py-1.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-xs font-black text-(--text-secondary)" dir="ltr">
                                                        {{ $invitation->member_responded_at->format('Y/m/d - H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 3. UNIVERSITY RESPONSE --}}
                                        @if($invitation->university_responded_at || in_array($invitation->member_status, ['accepted', 'declined_by_uni']))
                                            @php 
                                                $uApproved = !in_array($invitation->member_status, ['declined_by_uni']); 
                                                $uPending = !$invitation->university_responded_at && !in_array($invitation->member_status, ['declined_by_uni']);
                                            @endphp
                                            <div class="relative ps-12">
                                                <div class="absolute right-0 top-1 w-8 h-8 rounded-xl {{ $uPending ? 'bg-amber-500' : ($uApproved ? 'bg-green-600' : 'bg-red-600') }} text-white flex items-center justify-center z-10 shadow-lg ring-4 ring-opacity-10">
                                                    <i class="fa-solid fa-{{ $uPending ? 'building-columns' : ($uApproved ? 'building-circle-check' : 'building-circle-xmark') }} text-xs"></i>
                                                </div>
                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-[1.5rem] bg-(--bg-main) border border-(--border-primary) border-r-4 {{ $uPending ? 'border-r-amber-500' : ($uApproved ? 'border-r-green-600' : 'border-r-red-600') }} shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-black {{ $uPending ? 'text-amber-600' : ($uApproved ? 'text-green-600' : 'text-red-600') }}">رد إدارة الجامعة</p>
                                                        <p class="text-xs text-(--text-secondary) mt-1 font-bold">الحالة: {{ $uPending ? 'بانتظار المراجعة' : ($uApproved ? 'بالموافقة' : 'بالرفض') }}</p>
                                                        @if(!$uApproved && !($uPending) && $invitation->reject_reason)
                                                             <div class="mt-3 p-3 rounded-xl bg-red-50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20">
                                                                <p class="text-[11px] text-red-600 font-bold">السبب: {{ is_array($invitation->reject_reason) ? implode('، ', $invitation->reject_reason) : $invitation->reject_reason }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($invitation->university_responded_at)
                                                        <span class="px-4 py-1.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-xs font-black text-(--text-secondary)" dir="ltr">
                                                            {{ $invitation->university_responded_at->format('Y/m/d - H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 4. FINAL APPROVAL --}}
                                        @if($invitation->member_status === 'accepted' && $invitation->committee->status === 'approved')
                                            <div class="relative ps-12">
                                                <div class="absolute right-0 top-1 w-10 h-10 -right-1 rounded-2xl bg-gradient-to-br from-green-500 to-green-700 text-white flex items-center justify-center z-10 shadow-xl shadow-green-500/40 ring-4 ring-green-50 dark:ring-green-900/20 scale-110">
                                                    <i class="fa-solid fa-flag-checkered text-lg"></i>
                                                </div>
                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-[2rem] bg-green-50 dark:bg-green-500/5 border-2 border-green-200 dark:border-green-500/20 shadow-xl shadow-green-500/5">
                                                    <div>
                                                        <p class="text-base font-black text-green-700 dark:text-green-400">الاعتماد النهائي وتشكيل اللجنة</p>
                                                        <p class="text-xs text-green-600/80 mt-1 font-bold">تم تثبيت عضويتك في فريق التقييم بنجاح</p>
                                                    </div>
                                                    <span class="px-5 py-2 rounded-2xl bg-white dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm font-black text-green-700 dark:text-green-400 shadow-inner" dir="ltr">
                                                        {{ $invitation->committee->updated_at->format('Y/m/d - H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 5. CANCELED --}}
                                        @if($invitation->member_status === 'canceled')
                                            <div class="relative ps-12">
                                                <div class="absolute right-0 top-1 w-8 h-8 rounded-xl bg-gray-500 text-white flex items-center justify-center z-10 shadow-lg shadow-gray-500/40 ring-4 ring-gray-50 dark:ring-gray-500/10">
                                                    <i class="fa-solid fa-ban text-xs"></i>
                                                </div>
                                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-[1.5rem] bg-(--bg-main) border border-(--border-primary) border-r-4 border-r-gray-500 shadow-sm">
                                                    <div>
                                                        <p class="text-sm font-black text-gray-600 dark:text-gray-400">تم إلغاء الدعوة</p>
                                                        <p class="text-xs text-(--text-secondary) mt-1 font-bold">من قبل أمانة المجلس</p>
                                                    </div>
                                                    <span class="px-4 py-1.5 rounded-xl bg-(--surface-card) border border-(--border-primary) text-xs font-black text-(--text-secondary) shadow-inner" dir="ltr">
                                                        {{ $invitation->updated_at->format('Y/m/d - H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ACTION AREA: Only show if there's a required action or accessible board --}}
                                @if($invitation->member_status === 'pending_invite' || ($invitation->member_status === 'accepted' && $invitation->committee->status === 'approved'))
                                    <div class="w-full xl:w-[320px] shrink-0 sticky top-32">
                                        <div class="bg-(--bg-main) border-2 border-(--border-primary) rounded-[2.5rem] p-8 shadow-2xl shadow-gray-200/10 space-y-6">
                                            
                                            @if($invitation->member_status === 'pending_invite')
                                                <div class="text-center space-y-2 mb-6">
                                                    <h5 class="text-sm font-black text-(--text-primary) uppercase tracking-widest">الإجراء المطلوب</h5>
                                                    <p class="text-[10px] text-(--text-secondary) font-bold opacity-75">يرجى الرد على هذه الدعوة</p>
                                                </div>
                                                <div class="space-y-4">
                                                    <button type="button"
                                                        @click="openAccept('{{ route('evaluator.invitations.accept', $invitation) }}')"
                                                        class="w-full relative group/btn overflow-hidden px-8 py-5 rounded-2xl bg-green-600 text-white font-black hover:bg-green-700 transition-all duration-300 shadow-xl shadow-green-500/30 cursor-pointer">
                                                        <span class="relative z-10 flex items-center justify-center gap-3">
                                                            <i class="fa-solid fa-circle-check text-lg"></i>
                                                            قبول الدعوة
                                                        </span>
                                                    </button>

                                                    <button type="button"
                                                        @click="openDecline({{ $invitation->id }}, '{{ route('evaluator.invitations.decline', $invitation) }}')"
                                                        class="w-full px-8 py-5 rounded-2xl bg-white dark:bg-slate-800 text-red-600 border-2 border-red-100 dark:border-red-500/20 font-black hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300 cursor-pointer">
                                                        <i class="fa-solid fa-user-xmark me-2 text-lg"></i>الاعتذار
                                                    </button>
                                                </div>
                                            @elseif($invitation->member_status === 'accepted' && $invitation->committee->status === 'approved')
                                                <div class="text-center space-y-2 mb-6">
                                                    <h5 class="text-sm font-black text-(--text-primary) uppercase tracking-widest">إدارة اللجنة</h5>
                                                    <p class="text-[10px] text-(--text-secondary) font-bold opacity-75">تم تفعيل كافة الصلاحيات</p>
                                                </div>
                                                <div class="space-y-4">
                                                    <button type="button"
                                                        @click="openMembers({{ json_encode($invitation->committee->members->filter(fn($m) => $m->member_status === 'accepted')->values()->map(fn($m) => [
                                                            'name' => $m->evaluator->user->name,
                                                            'email' => $m->evaluator->user->email,
                                                            'phone' => $m->evaluator->user->phone ?: $m->evaluator->user->mobile ?: '—',
                                                            'is_chair' => $m->evaluator_id === $invitation->committee->chair_evaluator_id
                                                        ])) }})"
                                                        class="w-full px-8 py-5 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-700 transition-all duration-300 shadow-xl shadow-indigo-500/30 cursor-pointer flex items-center justify-center gap-3">
                                                        <i class="fa-solid fa-users"></i>أعضاء اللجنة
                                                    </button>

                                                    <a href="{{ route('requests.show', $invitation->committee->accreditation_request_id) }}"
                                                    class="w-full px-8 py-5 rounded-2xl bg-(--surface-card) text-(--text-primary) border-2 border-(--border-primary) font-black hover:bg-(--bg-main) transition-all duration-300 flex items-center justify-center gap-3 shadow-sm group/link">
                                                        <i class="fa-solid fa-gauge-high text-indigo-500"></i>لوحة الطلب
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                        </div>
                    </div>
            @endforeach
        </div>
    @endif

    {{-- MODAL: عرض أعضاء اللجنة --}}
    <template x-teleport="body">
        <div x-show="showMembersModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-md" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showMembersModal" 
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90"
                         @click.away="showMembersModal = false"
                         class="relative transform overflow-hidden rounded-[2.5rem] bg-(--surface-card) text-start shadow-[0_32px_128px_-16px_rgba(0,0,0,0.3)] transition-all sm:my-8 sm:w-full sm:max-w-6xl border border-(--border-primary)">
                        
                        <div class="px-8 py-6 border-b border-(--border-primary) bg-gradient-to-r from-(--bg-main) to-(--surface-card) flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                    <i class="fa-solid fa-users-viewfinder text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-(--text-primary) tracking-tight">فريق المقيّمين</h3>
                                    <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-[0.2em]">لجنة التقييم المعتمدة</p>
                                </div>
                            </div>
                            <button @click="showMembersModal = false" class="w-10 h-10 rounded-xl bg-(--bg-main) border border-(--border-primary) text-(--text-secondary) hover:text-red-500 hover:border-red-500/30 transition-all duration-300 cursor-pointer flex items-center justify-center">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                            <template x-for="(m, i) in selectedMembers" :key="i">
                                <div class="relative group/member flex flex-col h-full">
                                    <div class="flex-1 relative p-6 rounded-[1.5rem] bg-(--bg-main) border border-(--border-primary) flex flex-col gap-5 group-hover/member:border-indigo-500 transition-all duration-300 shadow-sm">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center font-black text-xl shadow-xl shadow-indigo-500/20 shrink-0 group-hover/member:rotate-3 transition-transform">
                                                <span x-text="m.name.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <p class="text-base font-black text-(--text-primary) tracking-tight line-clamp-1" x-text="m.name"></p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span x-show="m.is_chair" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-amber-400 to-amber-600 text-white text-[11px] font-black rounded-lg shadow-md shadow-amber-500/30">
                                                        <i class="fa-solid fa-crown text-[10px]"></i>
                                                        رئيس اللجنة
                                                    </span>
                                                    <p x-show="!m.is_chair" class="text-[11px] text-indigo-500 font-bold opacity-80 uppercase tracking-widest">عضو معتمد</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-3 pt-4 border-t border-(--border-primary)/50">
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-[10px] text-indigo-500">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </div>
                                                <span class="text-xs font-bold text-(--text-primary) truncate" x-text="m.email"></span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-[10px] text-emerald-600">
                                                    <i class="fa-solid fa-phone"></i>
                                                </div>
                                                <span class="text-xs font-bold text-(--text-primary)" dir="ltr" x-text="m.phone"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-8 py-6 bg-(--bg-main) border-t border-(--border-primary) flex justify-end">
                            <button @click="showMembersModal = false" class="px-10 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-2 border-(--border-primary) text-sm font-black text-(--text-primary) hover:bg-(--surface-card) transition-all duration-300 cursor-pointer shadow-lg active:scale-95">
                                إغلاق النافذة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: رفض الدعوة --}}
    <template x-teleport="body">
        <div x-show="showDeclineModal" style="display:none" class="relative z-[200]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div x-show="showDeclineModal"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click.away="showDeclineModal = false"
                    class="relative w-full max-w-lg rounded-[2rem] bg-(--surface-card) border border-(--border-primary) shadow-2xl overflow-hidden">
                    
                    <div class="px-8 py-6 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center shadow-lg shadow-red-500/30 shrink-0">
                            <i class="fa-solid fa-user-xmark text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-(--text-primary)">الاعتذار عن المشاركة</h3>
                            <p class="text-xs text-(--text-secondary) font-bold">نقدر وقتك، يرجى تزويدنا بأسباب الاعتذار</p>
                        </div>
                    </div>

                    <form method="POST" :action="declineActionUrl">
                        @csrf @method('PATCH')
                        <div class="p-8 space-y-4">
                            <template x-for="(reason, i) in declineReasons" :key="i">
                                <div class="flex items-center gap-3 group">
                                    <div class="w-8 h-8 rounded-full bg-(--bg-main) border border-(--border-primary) flex items-center justify-center text-xs font-black text-(--text-secondary) shrink-0 group-focus-within:bg-red-500 group-focus-within:text-white transition-colors" x-text="i + 1"></div>
                                    <input type="text" :name="`reasons[${i}]`" x-model="declineReasons[i]" required
                                        placeholder="أدخل سبب الرفض هنا..."
                                        class="flex-1 bg-(--bg-main) border-2 border-(--border-primary) text-(--text-primary) text-sm font-bold rounded-2xl px-5 py-4 focus:outline-none focus:border-red-500 focus:ring-0 transition-all">
                                    <button type="button" @click="removeReason(i)" x-show="declineReasons.length > 1"
                                        class="w-10 h-10 flex items-center justify-center rounded-[1rem] bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-sm">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addReason()"
                                class="inline-flex items-center gap-3 text-xs font-black text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer ps-1">
                                <i class="fa-solid fa-plus-circle text-lg opacity-70"></i> إضافة سبب إضافي
                            </button>
                        </div>
                        <div class="px-8 py-6 border-t border-(--border-primary) bg-(--bg-main) flex justify-end gap-4">
                            <button type="button" @click="showDeclineModal = false"
                                class="px-6 py-3 rounded-2xl border border-(--border-primary) text-(--text-primary) text-sm font-black hover:bg-(--surface-card) transition cursor-pointer">
                                تراجع
                            </button>
                            <button type="submit"
                                class="px-8 py-3 rounded-2xl bg-red-600 hover:bg-red-700 text-white text-sm font-black shadow-lg shadow-red-500/30 transition cursor-pointer">
                                تأكيد الاعتذار
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
                    class="relative w-full max-w-md rounded-[2.5rem] bg-(--surface-card) border border-(--border-primary) shadow-2xl text-start overflow-hidden">
                    
                    <div class="px-8 py-10 text-center">
                        <div class="w-20 h-20 rounded-[1.75rem] bg-green-500 text-white flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-green-500/40 relative">
                            <i class="fa-solid fa-check-double text-4xl"></i>
                            <div class="absolute -inset-1 bg-green-500 rounded-[1.75rem] blur-xl opacity-20 -z-10 animate-pulse"></div>
                        </div>
                        <h3 class="text-2xl font-black text-(--text-primary) mb-3 tracking-tight">إتمام قبول الدعوة</h3>
                        <p class="text-sm text-(--text-secondary) leading-relaxed font-bold px-4">
                            بقبولك للدعوة، فإنك تنضم رسمياً لفريق التقييم وتتعهد بالالتزام بمعايير الجودة والمهنية والسرية المطلوبة.
                        </p>
                    </div>

                    <form method="POST" :action="acceptActionUrl">
                        @csrf @method('PATCH')
                        <div class="px-8 py-6 border-t border-(--border-primary) bg-(--bg-main) flex justify-center gap-4">
                            <button type="button" @click="showAcceptModal = false"
                                class="px-8 py-3 rounded-2xl border border-(--border-primary) text-(--text-primary) text-sm font-black hover:bg-white transition cursor-pointer">
                                تراجع
                            </button>
                            <button type="submit"
                                class="px-10 py-3 rounded-2xl bg-green-600 hover:bg-green-700 text-white text-sm font-black shadow-xl shadow-green-500/20 transition cursor-pointer">
                                نعم، أرغب بالمشاركة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
