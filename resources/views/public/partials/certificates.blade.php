<!-- Accreditation Certificates -->
<section class="py-24 bg-slate-50 dark:bg-slate-950 relative overflow-hidden" id="accredited-programs">
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-accent/5 blur-[150px] rounded-full"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="mb-16 flex flex-col md:flex-row justify-between items-end gap-6 border-r-4 border-accent pr-8">
            <div>
                <h2 class="text-4xl font-black mb-4 dark:text-white">شهادات الاعتماد الصادرة</h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg font-medium">البرامج الأكاديمية التي حققت التميز الوطني وتجاوزت معايير الجودة</p>
            </div>
            <a href="{{ route('certificates.explorer') }}" 
               class="group flex items-center gap-3 text-primary dark:text-accent font-black text-lg hover:gap-5 transition-all">
                تصفح كافة الشهادات
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        @if($latestCertificates->isEmpty())
            <div class="text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="fa-solid fa-user-shield text-4xl"></i>
                </div>
                <p class="text-slate-500 font-bold">لا توجد شهادات صادرة حالياً</p>
            </div>
        @else
                <div class="relative px-12" x-data="{ 
                    active: 0, 
                    count: {{ $latestCertificates->count() }},
                    autoplay: null,
                    next() { 
                        this.active = (this.active + 1 >= this.count) ? 0 : this.active + 1 
                    },
                    prev() { 
                        this.active = (this.active - 1 < 0) ? this.count - 1 : this.active - 1 
                    },
                    start() {
                        this.autoplay = setInterval(() => this.next(), 5000);
                    },
                    stop() {
                        clearInterval(this.autoplay);
                    }
                }" x-init="start()" @mouseenter="stop()" @mouseleave="start()">
                    
                    <!-- Navigation Arrows - Side Positioned -->
                    <div class="absolute inset-y-0 -left-6 flex items-center z-20 pointer-events-none">
                        <button @click="prev()" 
                                class="pointer-events-auto w-14 h-14 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-primary dark:text-accent hover:bg-accent hover:text-primary dark:hover:bg-accent dark:hover:text-primary transition-all shadow-[0_10px_30px_rgba(0,0,0,0.1)] active:scale-90 group">
                            <i class="fa-solid fa-chevron-left group-hover:scale-110 transition-transform"></i>
                        </button>
                    </div>

                    <div class="absolute inset-y-0 -right-6 flex items-center z-20 pointer-events-none">
                        <button @click="next()" 
                                class="pointer-events-auto w-14 h-14 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-primary dark:text-accent hover:bg-accent hover:text-primary dark:hover:bg-accent dark:hover:text-primary transition-all shadow-[0_10px_30px_rgba(0,0,0,0.1)] active:scale-90 group">
                            <i class="fa-solid fa-chevron-right group-hover:scale-110 transition-transform"></i>
                        </button>
                    </div>

                    <!-- Slider Container -->
                    <div class="overflow-hidden py-10">
                        <div class="flex transition-transform duration-500 ease-in-out gap-8" 
                             :style="`transform: translateX(${active * (350 + 32)}px)`">
                        @foreach($latestCertificates as $cert)
                        <div class="shrink-0 w-[350px] group/card bg-white dark:bg-slate-900 p-8 rounded-3xl shadow-lg border border-slate-100 dark:border-slate-800 hover:border-accent hover:shadow-2xl transition-all duration-500">
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 bg-accent/10 rounded-2xl flex items-center justify-center text-accent group-hover/card:bg-accent group-hover/card:text-primary transition-colors">
                                    <i class="fa-solid fa-award text-4xl"></i>
                                </div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 px-3 py-1.5 rounded-xl border border-emerald-100 dark:border-emerald-500/20">برنامج معتمد</span>
                            </div>
                            
                            <h4 class="font-black text-2xl mb-4 dark:text-white group-hover/card:text-primary dark:group-hover/card:text-accent transition-colors leading-tight min-h-[4rem]">
                                {{ $cert['program'] }}
                            </h4>
                            
                            <div class="flex items-start gap-3 mb-8">
                                <i class="fa-solid fa-building-columns text-slate-300 mt-1"></i>
                                <p class="text-slate-500 dark:text-slate-400 font-bold text-sm leading-relaxed">
                                    {{ $cert['university'] }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-slate-50 dark:border-slate-800">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">نوع الاعتماد</span>
                                    <span class="text-xs font-black text-primary dark:text-accent">{{ $cert['level'] }}</span>
                                </div>
                                <a href="{{ $cert['url'] }}" target="_blank"
                                   class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 group-hover/card:bg-accent group-hover/card:text-primary transition-all duration-500">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xl"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Progress Dots -->
                <div class="flex justify-center gap-3 mt-8">
                    <template x-for="(i, index) in count" :key="index">
                        <button @click="active = index" 
                                class="h-1.5 rounded-full transition-all duration-500"
                                :class="active === index ? 'w-10 bg-accent' : 'w-4 bg-slate-200 dark:bg-slate-800'"></button>
                    </template>
                </div>
            </div>
        @endif
    </div>
</section>
