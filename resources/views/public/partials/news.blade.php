<!-- Latest News Section -->
@php
    $newsItems = [
        [
            'title' => 'طلبات اعتماد البرامج الطبية لجامعة العلوم والتكنولوجيا',
            'date' => '21 مايو 2026',

            'tag' => 'اعتماد أكاديمي',
            'summary' =>
                'تسلّم مجلس الاعتماد الأكاديمي ملفات الاعتماد البرامجي الوطني لبرامج الطب والجراحة وطب الأسنان والصيدلة بجامعة العلوم والتكنولوجيا - عدن، ضمن جهود تعزيز جودة التعليم العالي واستكمال إجراءات التقييم والاعتماد وفق المعايير الوطنية.',
            'image' => asset('images/news/news1.jpg'),
        ],
        [
            'title' => 'لقاء تشاوري لتعزيز جودة الجامعات',
            'date' => '30 أبريل 2026',
            'tag' => 'لقاء تشاوري',
            'summary' =>
                'عقد مجلس الاعتماد الأكاديمي لقاءً تشاورياً مع مراكز الجودة بجامعات عدن ولحج وأبين والضالع لمناقشة تطوير الأداء الأكاديمي، وتعزيز معايير الجودة، وتفعيل خطط التحسين المستمر والاعتماد الأكاديمي.',
            'image' => asset('images/news/news2.jpg'),
        ],
        [
            'title' => 'وزير التعليم يؤكد إصلاح الجودة الأكاديمية',
            'date' => '09 أبريل 2026',
            'tag' => 'تصريح',
            'summary' =>
                'أكد وزير التعليم العالي أن العام الجاري يمثل نقطة تحول في إصلاح التعليم وتحسين الجودة الأكاديمية، مشدداً على دعم مجلس الاعتماد الأكاديمي وتعزيز دوره في تقييم المؤسسات التعليمية وتطبيق معايير الجودة.',
            'image' => asset('images/news/news3.jpg'),
        ],
        [
            'title' => 'اعتماد برنامج الترجمة بجامعة عدن',
            'date' => '13 مايو 2026',
            'tag' => 'اعتماد أكاديمي',
            'summary' =>
                'تسلّم مجلس الاعتماد الأكاديمي ملف الدراسة الذاتية لبرنامج بكالوريوس الترجمة بجامعة عدن، تمهيداً لاستكمال إجراءات الاعتماد البرامجي وفق المعايير الوطنية المعتمدة، ضمن جهود الجامعة لتعزيز جودة التعليم.',
            'image' => asset('images/news/news4.jpg'),
        ],
        [
           'title' => 'تأهيل مراجعين لتقييم البرامج الأكاديمية',
'date' => '20 ديسمبر 2025',
'tag' => 'دورة تدريبية',
'summary' => 'نفذ مجلس الاعتماد الأكاديمي بالشراكة مع الأكاديمية العربية دورة تدريبية مكثفة لتأهيل المراجعين الخارجيين، بهدف إعداد كوادر متخصصة لتقييم البرامج الأكاديمية وتعزيز منظومة الجودة والاعتماد في مؤسسات التعليم العالي.',
        'image' => asset('images/news/news5.jpg'),
             ],
        [
            'title' => 'ورشة لتطوير جودة الدراسات العليا',
'date' => '16 ديسمبر 2025',
'tag' => 'ورشة علمية',
'summary' => 'نظم مجلس الاعتماد الأكاديمي ورشة علمية في عدن لمناقشة واقع برامج الدراسات العليا وآليات تطويرها، بمشاركة واسعة من الجامعات اليمنية بهدف تعزيز الجودة الأكاديمية ودعم التنمية المستدامة.',
'image' => asset('images/news/news6.jpg'),
 ],
    ];

    // Double the items for seamless loop (cloning the first 3 items at the end)
    $clonedItems = array_merge($newsItems, array_slice($newsItems, 0, 3));
@endphp

<style>
    .news-track {
        display: flex;
        gap: 2rem;
        --slide-width: calc(100% + 2rem);
    }

    .news-card {
        flex-shrink: 0;
        width: 100%;
    }

    @media (min-width: 640px) {
        .news-track {
            --slide-width: calc((100% + 2rem) / 2);
        }

        .news-card {
            width: calc((100% - 2rem) / 2);
        }
    }

    @media (min-width: 1024px) {
        .news-track {
            --slide-width: calc((100% + 2rem) / 3);
        }

        .news-card {
            width: calc((100% - 4rem) / 3);
        }
    }
</style>

<section class="py-24 bg-slate-50 dark:bg-slate-900 relative overflow-hidden" id="latest-news">
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-primary/5 blur-[150px] rounded-full"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div
            class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6 border-r-4 border-primary dark:border-accent pr-8">
            <div class="text-right">
                <h2 class="text-4xl font-black dark:text-white">آخر المستجدات</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">متابعة مستمرة لأنشطة المجلس وفعالياته</p>
            </div>
            <button
                class="group flex items-center gap-3 text-primary dark:text-accent font-black text-lg hover:gap-5 transition-all bg-transparent border-none cursor-pointer">
                عرض كافة الأخبار
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
        </div>

        <div class="relative px-0 sm:px-12" x-data="{
            active: 0,
            count: {{ count($newsItems) }},
            transitionEnabled: true,
            autoplay: null,
            touchStart: null,
            next() {
                if (!this.transitionEnabled) return;
                this.active++;
                if (this.active > this.count) {
                    // Seamless wrap around: wait for slide transition to finish, then snap back to 0 without transition
                    setTimeout(() => {
                        this.transitionEnabled = false;
                        this.active = 0;
                        // Force reflow and re-enable transition in next tick
                        setTimeout(() => {
                            this.transitionEnabled = true;
                        }, 50);
                    }, 500);
                }
            },
            prev() {
                if (!this.transitionEnabled) return;
                if (this.active === 0) {
                    this.transitionEnabled = false;
                    this.active = this.count;
                    setTimeout(() => {
                        this.transitionEnabled = true;
                        this.active = this.count - 1;
                    }, 50);
                } else {
                    this.active--;
                }
            },
            start() {
                this.autoplay = setInterval(() => this.next(), 4000);
            },
            stop() {
                clearInterval(this.autoplay);
            }
        }" x-init="start()" @mouseenter="stop()"
            @mouseleave="start()">

            <!-- Navigation Arrows - Side Positioned -->
            <div class="hidden sm:flex absolute inset-y-0 -left-6 items-center z-20 pointer-events-none">
                <button @click="prev()"
                    class="pointer-events-auto w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-primary dark:text-accent hover:bg-accent hover:text-primary dark:hover:bg-accent dark:hover:text-primary transition-all shadow-[0_10px_30px_rgba(0,0,0,0.1)] active:scale-90 group cursor-pointer">
                    <i class="fa-solid fa-chevron-left group-hover:scale-110 transition-transform"></i>
                </button>
            </div>

            <div class="hidden sm:flex absolute inset-y-0 -right-6 items-center z-20 pointer-events-none">
                <button @click="next()"
                    class="pointer-events-auto w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-primary dark:text-accent hover:bg-accent hover:text-primary dark:hover:bg-accent dark:hover:text-primary transition-all shadow-[0_10px_30px_rgba(0,0,0,0.1)] active:scale-90 group cursor-pointer">
                    <i class="fa-solid fa-chevron-right group-hover:scale-110 transition-transform"></i>
                </button>
            </div>

            <!-- Slider Container -->
            <div class="overflow-hidden py-10" @touchstart="touchStart = $event.touches[0].clientX"
                @touchend="if (touchStart - $event.changedTouches[0].clientX > 50) next(); if ($event.changedTouches[0].clientX - touchStart > 50) prev();">
                <div class="news-track"
                    :class="transitionEnabled ? 'transition-transform duration-500 ease-in-out' : ''"
                    :style="`transform: translateX(calc(${active} * var(--slide-width)))`">
                    @foreach ($clonedItems as $news)
                        <article
                            class="news-card bg-white dark:bg-slate-800 rounded-3xl overflow-hidden group hover:shadow-2xl transition-all duration-500 border border-slate-100 dark:border-slate-700 hover:border-accent dark:hover:border-accent">
                            <div class="h-48 sm:h-64 overflow-hidden relative">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700"
                                    alt="news" src="{{ $news['image'] }}" />
                                <div
                                    class="absolute top-6 right-6 bg-accent text-primary px-4 py-1.5 rounded-lg text-xs font-black shadow-lg">
                                    {{ $news['tag'] }}
                                </div>
                            </div>
                            <div class="p-5 sm:p-8">
                                <div class="flex items-center gap-2 text-slate-400 text-sm mb-4 font-bold">
                                    <i class="fa-regular fa-calendar text-lg"></i>
                                    {{ $news['date'] }}
                                </div>
                                <h3
                                    class="font-black text-lg sm:text-xl mb-4 leading-snug group-hover:text-primary dark:group-hover:text-accent transition-colors dark:text-white line-clamp-2 min-h-[3rem] sm:min-h-[3.5rem]">
                                    {{ $news['title'] }}
                                </h3>
                                <p
                                    class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 line-clamp-3 min-h-[4rem] sm:min-h-[4.5rem]">
                                    {{ $news['summary'] }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <!-- Progress Dots -->
            <div class="flex justify-center gap-3 mt-4">
                <template x-for="(item, index) in Array.from({ length: count })" :key="index">
                    <button @click="active = index"
                        class="h-1.5 rounded-full transition-all duration-500 cursor-pointer"
                        :class="(active % count) === index ? 'w-10 bg-accent' : 'w-4 bg-slate-200 dark:bg-slate-700'"></button>
                </template>
            </div>

        </div>
    </div>
</section>
