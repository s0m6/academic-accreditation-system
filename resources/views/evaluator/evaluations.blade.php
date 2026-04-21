@extends('partials.app')

@section('title', 'تقييماتي المعتمدة')
@section('title2', 'التقييمات الحالية')
@section('description', 'قائمة لجان التقييم المعتمدة التي تشارك في عضويتها')

@section('content')
<div x-data="{
    showMembersModal: false,
    selectedMembers: [],
    openMembers(members) {
        this.selectedMembers = members;
        this.showMembersModal = true;
    }
}">

    @if($evaluations->isEmpty())
        <div class="relative overflow-hidden rounded-[2.5rem] border border-(--border-primary) bg-(--surface-card) p-20 flex flex-col items-center justify-center text-center gap-6 shadow-2xl shadow-gray-200/10">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
            <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-2xl shadow-indigo-500/40 relative z-10">
                <i class="fa-solid fa-magnifying-glass-chart text-4xl text-white"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-black text-(--text-primary)">لا توجد تقييمات معتمدة حالياً</h3>
                <p class="text-(--text-secondary) mt-3 max-w-sm mx-auto leading-relaxed font-medium">
                    بمجرد اعتماد لجنتك نهائياً من قبل أمانة المجلس، ستظهر جميع تفاصيل العمل هنا.
                </p>
                <div class="mt-8">
                    <a href="{{ route('evaluator.invitations') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all shadow-xl shadow-indigo-500/20">
                        مراجعة دعوات المشاركة
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-8">
            @foreach($evaluations as $member)
                @php 
                    $request = $member->committee->accreditationRequest;
                    $program = $request->program;
                    $university = $program->department->college->university;
                    
                    // Simple logic to determine the current stage name in Arabic
                    $stages = [
                        'stage_one' => 'المرحلة الأولى',
                        'stage_two' => 'المرحلة الثانية',
                        'stage_three' => 'المرحلة الثالثة',
                        'stage_four' => 'المرحلة الرابعة',
                        'stage_five' => 'المرحلة الخامسة',
                    ];
                    $currentStage = $stages[$request->current_stage] ?? 'قيد المعالجة';
                @endphp

                <div class="group relative bg-(--surface-card) rounded-[2rem] border border-(--border-primary) shadow-xl shadow-gray-200/10 hover:border-indigo-500/30 transition-all duration-500 overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-blue-600"></div>
                    
                    <div class="p-8 md:p-10">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                            {{-- Info Section --}}
                            <div class="flex items-start gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-3xl border border-indigo-500/10 shadow-inner group-hover:scale-110 transition-transform duration-500">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <h2 class="text-2xl font-black text-(--text-primary) tracking-tight">{{ $program->program_name }}</h2>
                                        <span class="px-3 py-1 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-green-100 dark:border-green-500/20">
                                             {{ $currentStage }}
                                        </span>
                                    </div>
                                    <p class="text-base text-(--text-secondary) font-bold flex items-center gap-2">
                                        <i class="fa-solid fa-university text-orange-500/60"></i>
                                        {{ $university->name }} — <span class="opacity-75">{{ $program->department->college->name }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Action Section --}}
                            <div class="flex items-center gap-4 shrink-0">
                                <button type="button"
                                    @click="openMembers({{ json_encode($member->committee->members->filter(fn($m) => $m->member_status === 'accepted')->values()->map(fn($m) => [
                                        'name' => $m->evaluator->user->name,
                                        'email' => $m->evaluator->user->email,
                                        'phone' => $m->evaluator->user->phone ?: $m->evaluator->user->mobile ?: '—',
                                        'is_chair' => $m->evaluator_id === $member->committee->chair_evaluator_id
                                    ])) }})"
                                    class="px-6 py-4 rounded-2xl bg-white dark:bg-slate-800 text-indigo-600 border border-indigo-100 hover:border-indigo-500 font-black transition-all cursor-pointer flex items-center gap-2 text-sm">
                                    <i class="fa-solid fa-users"></i>
                                    أعضاء اللجنة
                                </button>

                                <a href="{{ route('requests.stage', ['accreditationRequest' => $request->id, 'stage' => 'stage_one']) }}"
                                   class="px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black transition-all shadow-xl shadow-indigo-500/20 flex items-center gap-3 text-sm">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    لوحة الطلب
                                </a>
                            </div>
                        </div>

                        {{-- Metadata Row --}}
                        <div class="mt-10 pt-8 border-t border-(--border-primary) flex flex-wrap gap-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-(--bg-main) flex items-center justify-center text-xs text-orange-500 border border-(--border-primary)">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest leading-none mb-1">تاريخ الاعتماد</p>
                                    <span class="text-xs font-black text-(--text-primary)" dir="ltr">{{ $member->committee->updated_at->format('Y/m/d') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-(--bg-main) flex items-center justify-center text-xs text-blue-500 border border-(--border-primary)">
                                    <i class="fa-solid fa-hashtag"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-(--text-secondary) uppercase tracking-widest leading-none mb-1">الرقم المرجعي</p>
                                    <span class="text-xs font-black text-(--text-primary)">COM-{{ str_pad($member->committee->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </div>
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
                            <button @click="showMembersModal = false" class="px-10 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-2 border-(--border-primary) text-sm font-black text-(--text-primary) hover:bg-(--surface-card) transition-all duration-300 cursor-pointer shadow-lg">
                                إغلاق
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
