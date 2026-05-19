@extends('public.layout')

@section('title', 'دليل البرامج المعتمدة - CAAQAHE')

@section('content')
<div class="min-h-screen bg-(--bg-main) py-20" x-data="certificateExplorer()">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header -->
        <div class="mb-16 border-r-4 border-accent pr-8">
            <h1 class="text-5xl font-black text-primary dark:text-white mb-6">دليل البرامج المعتمدة</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xl font-medium max-w-3xl leading-relaxed">
                قاعدة البيانات الرسمية للبرامج الأكاديمية والمؤسسات التعليمية التي حصلت على شهادات الاعتماد الوطني وضمان الجودة في الجمهورية اليمنية.
            </p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl p-8 mb-16 relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-accent/5 rounded-full blur-3xl group-hover:bg-accent/10 transition-all duration-700"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 relative z-10">
                <!-- University Filter -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-black text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-[0.2em]">الجامعة / المؤسسة</label>
                    <div class="relative group/input">
                        <i class="fa-solid fa-building-columns absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/input:text-accent transition-colors"></i>
                        <select x-model="filters.university_id" @change="search()"
                                class="w-full rounded-2xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-primary dark:text-white pr-12 pl-4 py-4 text-sm font-bold focus:border-accent focus:ring-0 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">جميع الجامعات والمؤسسات</option>
                            @foreach($universities as $uni)
                                <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- City Filter -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-black text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-[0.2em]">المدينة</label>
                    <div class="relative group/input">
                        <i class="fa-solid fa-location-dot absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/input:text-accent transition-colors"></i>
                        <select x-model="filters.city_id" @change="search()"
                                class="w-full rounded-2xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-primary dark:text-white pr-12 pl-4 py-4 text-sm font-bold focus:border-accent focus:ring-0 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">جميع المدن</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-black text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-[0.2em]">نوع الاعتماد</label>
                    <div class="relative group/input">
                        <i class="fa-solid fa-circle-check absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/input:text-accent transition-colors"></i>
                        <select x-model="filters.decision_type" @change="search()"
                                class="w-full rounded-2xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-primary dark:text-white pr-12 pl-4 py-4 text-sm font-bold focus:border-accent focus:ring-0 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">جميع أنواع الاعتماد</option>
                            @foreach($decisionTypes as $type)
                                <option value="{{ $type['id'] }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Search Reset -->
                <div class="md:col-span-2 flex items-end">
                    <button @click="resetFilters()"
                            class="w-full h-[58px] rounded-2xl border-2 border-red-100 dark:border-red-900/30 text-red-500 font-black text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2 group">
                        <i class="fa-solid fa-rotate-left group-hover:rotate-180 transition-transform duration-500"></i>
                        مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="relative min-h-[500px]">
            <!-- Loading Indicator -->
            <div x-show="loading" class="absolute inset-0 bg-slate-50/50 dark:bg-slate-950/50 backdrop-blur-md z-20 flex items-center justify-center rounded-3xl">
                <div class="flex flex-col items-center gap-6">
                    <div class="relative">
                        <div class="w-20 h-20 border-4 border-slate-200 dark:border-slate-800 rounded-full"></div>
                        <div class="w-20 h-20 border-t-4 border-accent rounded-full animate-spin absolute top-0 left-0"></div>
                    </div>
                    <span class="font-black text-primary dark:text-white tracking-widest uppercase text-sm">جاري تحديث البيانات...</span>
                </div>
            </div>

            <!-- No Results -->
            <div x-show="!loading && results.length === 0" x-cloak class="flex flex-col items-center justify-center py-32 text-center bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <div class="w-32 h-32 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 mb-8 border border-slate-100 dark:border-slate-700 shadow-inner">
                    <i class="fa-solid fa-magnifying-glass text-6xl"></i>
                </div>
                <h3 class="text-3xl font-black text-primary dark:text-white mb-4">لا توجد نتائج مطابقة</h3>
                <p class="text-slate-500 dark:text-slate-400 font-bold max-w-md mx-auto leading-relaxed">
                    لم نتمكن من العثور على أي برامج معتمدة تطابق معايير البحث الحالية. حاول توسيع نطاق البحث أو تغيير الفلاتر.
                </p>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <template x-for="cert in results" :key="cert.id">
                    <div class="group bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-lg hover:shadow-2xl hover:border-accent transition-all duration-500 overflow-hidden relative">
                        <!-- Achievement level strip -->
                        <div class="h-2 w-full bg-accent opacity-20 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 bg-primary dark:bg-slate-800 rounded-2xl flex items-center justify-center text-accent shadow-lg group-hover:scale-110 transition-transform duration-500">
                                    <i class="fa-solid fa-award text-4xl"></i>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        سارية المفعول
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-2xl font-black text-primary dark:text-white mb-3 group-hover:text-accent transition-colors leading-tight" x-text="cert.program_name"></h3>
                            <p class="text-slate-500 dark:text-slate-400 font-bold mb-8 flex items-center gap-2">
                                <i class="fa-solid fa-building-columns text-lg opacity-50"></i>
                                <span x-text="cert.university_name"></span>
                            </p>

                            <div class="space-y-4 mb-10 bg-slate-50 dark:bg-slate-950 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between text-xs font-black">
                                    <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider">المدينة</span>
                                    <span class="text-primary dark:text-white" x-text="cert.city_name"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-black">
                                    <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider">نوع الاعتماد</span>
                                    <span class="text-accent" x-text="cert.achievement_level"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-black">
                                    <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider">تاريخ الانتهاء</span>
                                    <span class="text-primary dark:text-white" x-text="cert.expires_at"></span>
                                </div>
                            </div>

                            <a :href="cert.url" 
                               target="_blank"
                               class="w-full py-4 bg-slate-100 dark:bg-slate-800 text-primary dark:text-white font-black text-sm rounded-2xl hover:bg-primary hover:text-accent transition-all duration-300 flex items-center justify-center gap-3">
                                التحقق من الشهادة
                                <i class="fa-solid fa-arrow-up-right-from-square text-lg"></i>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function certificateExplorer() {
        return {
            loading: true,
            results: [],
            filters: {
                university_id: '',
                city_id: '',
                decision_type: ''
            },
            init() {
                this.search();
            },
            search() {
                this.loading = true;
                axios.get('{{ route("api.certificates.search") }}', {
                    params: this.filters
                })
                .then(response => {
                    this.results = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    this.loading = false;
                });
            },
            resetFilters() {
                this.filters = {
                    university_id: '',
                    city_id: '',
                    decision_type: ''
                };
                this.search();
            }
        }
    }
</script>
@endpush

