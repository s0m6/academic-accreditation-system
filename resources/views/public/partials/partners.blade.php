<!-- University Partners -->
<section class="py-20 bg-white dark:bg-slate-950 border-y border-slate-100 dark:border-slate-900 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 mb-16 flex flex-col items-center">
        <h2 class="text-center text-primary dark:text-accent font-black text-3xl md:text-4xl tracking-tight mb-4">
            شركاؤنا في الجودة التعليمية
        </h2>
        <div class="w-20 h-1 bg-accent rounded-full shadow-[0_2px_10px_rgba(233,193,118,0.3)]"></div>
    </div>

    <!-- Ticker Container -->
    <div class="relative w-full overflow-hidden mask-gradient py-4 flex">
        <!-- Set 1 -->
        <div class="flex shrink-0 min-w-full justify-around items-center animate-marquee-ltr">
            @include('public.partials.partner-logos')
        </div>
        <!-- Set 2 (Duplicate for seamless loop) -->
        <div class="flex shrink-0 min-w-full justify-around items-center animate-marquee-ltr" aria-hidden="true">
            @include('public.partials.partner-logos')
        </div>
    </div>
</section>

<style>
    /* Continuous Left-to-Right Scrolling animation */
    @keyframes marquee-ltr {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(0%);
        }
    }

    .animate-marquee-ltr {
        animation: marquee-ltr 25s linear infinite;
    }

    /* Pause animation on hover */
    /* Hovering over either container pauses the animation for both since they share the same state */
    .mask-gradient:hover .animate-marquee-ltr {
        animation-play-state: paused;
    }

    /* Gradients for smooth fade out at edges */
    .mask-gradient {
        mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
    }
</style>
