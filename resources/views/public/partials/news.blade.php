<!-- Latest News Section -->
<section class="py-24 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-20 gap-4">
            <div class="text-right border-r-4 border-primary dark:border-accent pr-6">
                <h2 class="text-4xl font-black dark:text-white">آخر المستجدات</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">متابعة مستمرة لأنشطة المجلس وفعالياته
                </p>
            </div>
            <button
                class="group flex items-center gap-3 text-primary dark:text-accent font-black text-lg hover:gap-5 transition-all">
                عرض كافة الأخبار
                <span class="material-symbols-outlined rtl:rotate-180"
                    data-icon="arrow_forward">arrow_forward</span>
            </button>
        </div>
        <div class="grid lg:grid-cols-3 gap-10">
            @for ($i = 0; $i < 3; $i++)
            <article
                class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-700">
                <div class="h-64 overflow-hidden relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700"
                        alt="news"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuACgXWutfUfWQ-tzpJvRFdILskeuZNlrLa8fJFj1FDTh1QjOumqseQxvvR2e3jVP9PRW0l4sDWGlfeppUihKHrN4nlJ70tKARBIJixyJEImO242byOE7JL_7PyVCHQHe3h-I7ELQx0CueMKsbnDCvvYAjA_o5fDP94p9QI7qISvXm9FQbKH4shBhy3IF1BtDatDeMyxkMZTCBxe3pc7E1i10qEavYTTkebXsbPnbr5x2dCX84CUtr3VC90bFsXFqkY3DA7OHKiGMLCT" />
                    <div
                        class="absolute top-6 right-6 bg-accent text-primary px-4 py-1.5 rounded-lg text-xs font-black shadow-lg">
                        إعلان</div>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-2 text-slate-400 text-sm mb-4 font-bold">
                        <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                        15 يونيو 2024
                    </div>
                    <h3
                        class="font-black text-xl mb-4 leading-snug group-hover:text-primary dark:group-hover:text-accent transition-colors dark:text-white">
                        تطوير معايير الاعتماد الأكاديمي للعام الجامعي الجديد 2024</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">ناقشت اللقاءات الفنية آليات تحديث مخرجات التعلم لتتواكب مع متطلبات سوق العمل...</p>
                </div>
            </article>
            @endfor
        </div>
    </div>
</section>
