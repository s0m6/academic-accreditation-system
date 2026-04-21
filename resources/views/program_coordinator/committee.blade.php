@extends('partials.app')

@section('title', 'مراجعة أعضاء اللجنة')
@section('title2', 'موافقة اللجنة')
@section('description', 'مراجعة والموافقة على أعضاء لجنة التقييم المقترحين من المجلس')

@section('content')
@php
    $program = $accreditationRequest->program;
    $college = $program->department->college;
    $university = $college->university;

    $activeMembers = $committee?->activeMembers ?? collect();
@endphp

<div class="space-y-6" x-data="{
    showDeclineModal: false,
    declineActionUrl: '',
    declineReasons: [''],
    addReason() { this.declineReasons.push(''); },
    removeReason(i) { if (this.declineReasons.length > 1) this.declineReasons.splice(i, 1); },

    openDecline(url) {
        this.declineActionUrl = url;
        this.declineReasons = [''];
        this.showDeclineModal = true;
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

    {{-- Request info banner --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main) flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 shrink-0">
                <i class="fa-solid fa-file-circle-check text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-(--text-primary)">طلب الاعتماد #{{ $accreditationRequest->id }}</h3>
                <p class="text-xs text-(--text-secondary)">{{ $university->name }} — {{ $college->name }} — {{ $program->program_name }}</p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                <p class="text-xs font-bold text-(--text-secondary) mb-1">الجامعة</p>
                <p class="font-bold text-(--text-primary)">{{ $university->name }}</p>
            </div>
            <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                <p class="text-xs font-bold text-(--text-secondary) mb-1">الكلية</p>
                <p class="font-bold text-(--text-primary)">{{ $college->name }}</p>
            </div>
            <div class="bg-(--bg-main) rounded-xl p-4 border border-(--border-primary)">
                <p class="text-xs font-bold text-(--text-secondary) mb-1">البرنامج</p>
                <p class="font-bold text-(--text-primary)">{{ $program->program_name }}</p>
            </div>
        </div>
    </div>

    {{-- Members section --}}
    <div class="rounded-2xl border border-(--border-primary) bg-(--surface-card) shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-(--border-primary) bg-(--bg-main)">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-100 dark:border-violet-500/20 shrink-0">
                    <i class="fa-solid fa-users-gear text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-(--text-primary)">أعضاء اللجنة المقترحون</h3>
                    <p class="text-xs text-(--text-secondary)">راجع بيانات كل عضو وقم بالموافقة أو الرفض</p>
                </div>
            </div>
        </div>

        @if($activeMembers->isEmpty())
            <div class="p-16 flex flex-col items-center justify-center text-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-(--bg-main) border border-(--border-primary) flex items-center justify-center">
                    <i class="fa-solid fa-users text-2xl text-(--text-secondary) opacity-40"></i>
                </div>
                <p class="font-bold text-(--text-primary)">لم يتم اختيار أعضاء اللجنة بعد</p>
                <p class="text-sm text-(--text-secondary)">ستظهر هنا أعضاء اللجنة المقترحون من أمانة المجلس.</p>
            </div>
        @else
            <div class="divide-y divide-(--border-primary)">
                @foreach ($activeMembers as $member)
                    @php
                        $ev = $member->evaluator;
                        $statusMap = [
                            'pending_invite'     => ['label' => 'في انتظار رد المقيم', 'color' => 'amber'],
                            'pending_uni'        => ['label' => 'بانتظار رد الجامعة', 'color' => 'blue'],
                            'declined_by_member' => ['label' => 'رفض المقيم', 'color' => 'red'],
                            'declined_by_uni'    => ['label' => 'رفضت الجامعة', 'color' => 'red'],
                            'accepted'           => ['label' => 'مقبول', 'color' => 'green'],
                        ];
                        $st = $statusMap[$member->member_status];
                    @endphp
                    <div class="p-5 flex items-center gap-4 flex-wrap">
                        {{-- Avatar --}}
                        <div class="w-14 h-14 rounded-2xl bg-(--bg-main) border-2 border-(--border-primary) flex items-center justify-center shrink-0">
                            <span class="text-xl font-black text-(--text-primary)">{{ mb_substr($ev->user->name, 0, 1) }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-bold text-(--text-primary)">{{ $ev->user->name }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold
                                    {{ $st['color'] === 'green' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'
                                    : ($st['color'] === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'
                                    : ($st['color'] === 'blue' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'
                                    : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20')) }}">
                                    {{ $st['label'] }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-3 text-sm text-(--text-secondary)">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-graduation-cap w-3"></i>
                                    {{ $ev->academic_rank }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-flask w-3"></i>
                                    {{ $ev->general_specialty }}
                                </span>
                                @if($ev->city)
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot w-3"></i>
                                        {{ $ev->city->city_name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions — only for pending_uni --}}
                        @if($member->member_status === 'pending_uni')
                            <div class="flex gap-2 shrink-0">
                                <form method="POST" action="{{ route('program_coordinator.committee.approve', $member->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold transition-colors shadow-md shadow-green-500/20 cursor-pointer">
                                        <i class="fa-solid fa-check"></i> موافقة
                                    </button>
                                </form>
                                <button type="button"
                                    @click="openDecline('{{ route('program_coordinator.committee.decline', $member->id) }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 dark:bg-red-500/10 hover:bg-red-100 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20 text-sm font-bold transition-colors cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i> رفض
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- MODAL: رفض العضو --}}
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
                            <h3 class="font-bold text-(--text-primary)">رفض عضو اللجنة</h3>
                            <p class="text-xs text-(--text-secondary)">أدخل أسباب رفض الجامعة لهذا المقيم</p>
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
</div>
@endsection
