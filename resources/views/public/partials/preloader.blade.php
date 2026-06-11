<style>
    /* Preloader Animations */
    @keyframes preloader-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes preloader-fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .preloader-ring {
        animation: preloader-spin 1.1s linear infinite;
    }

    .preloader-text {
        opacity: 0;
        animation: preloader-fade-in 0.7s ease forwards 0.3s;
    }

    .preloader-sub {
        opacity: 0;
        animation: preloader-fade-in 0.7s ease forwards 0.5s;
    }

    /* Fallback/explicit styles for when Tailwind is not loaded */
    #site-preloader {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        z-index: 9999 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: #f8fafc !important;
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1) !important;
        user-select: none !important;
    }
    
    html.dark #site-preloader {
        background-color: #020617 !important;
    }

    #site-preloader.opacity-0 {
        opacity: 0 !important;
    }

    #site-preloader.pointer-events-none {
        pointer-events: none !important;
    }

    #site-preloader .preloader-container {
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        max-width: 448px !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
        text-align: center !important;
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    #site-preloader .preloader-container.scale-95 {
        transform: scale(0.95) !important;
    }
    
    #site-preloader .preloader-container.translate-y-\[-15px\] {
        transform: scale(0.95) translateY(-15px) !important;
    }

    #site-preloader .preloader-logo-wrap {
        position: relative !important;
        width: 112px !important;
        height: 112px !important;
        margin-bottom: 28px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    #site-preloader .preloader-ring {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }

    #site-preloader .preloader-logo {
        width: 64px !important;
        height: 64px !important;
        object-fit: contain !important;
    }

    #site-preloader .preloader-title {
        font-size: 20px !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
    }

    html.dark #site-preloader .preloader-title {
        color: #ffffff !important;
    }

    #site-preloader .preloader-title span {
        font-size: 18px !important;
    }
</style>

<!-- Modern Premium Preloader -->
<div id="site-preloader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 dark:bg-[#020617] transition-all duration-700 ease-in-out select-none">
    <div class="relative flex flex-col items-center max-w-md px-6 text-center transition-all duration-700 preloader-container">
        
        <!-- Logo + Spinner -->
        <div class="relative w-28 h-28 mb-7 flex items-center justify-center preloader-logo-wrap">
            <!-- Single spinning ring -->
            <svg class="absolute inset-0 w-full h-full preloader-ring" viewBox="0 0 112 112" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="56" cy="56" r="52" stroke="#e2e8f0" stroke-width="3"/>
                <circle cx="56" cy="56" r="52" stroke="#e9c176" stroke-width="3"
                    stroke-linecap="round"
                    stroke-dasharray="327"
                    stroke-dashoffset="245"/>
            </svg>
            <!-- Logo -->
            <img src="{{ asset('images/logo.png') }}" alt="CAAQAHE" class="w-16 h-16 object-contain preloader-logo">
        </div>

        <!-- Title & Subtitle -->
        <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight preloader-text preloader-title">
            مجلس الاعتماد الأكاديمي<br>
            <span class="text-lg">وضمان جودة التعليم العالي</span>
        </h2>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const preloader = document.getElementById('site-preloader');
        if (!preloader) return;
        
        const startTime = Date.now();
        const minDuration = 1000; // Minimum time to showcase the premium animation (1s)
        
        const hidePreloader = () => {
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(0, minDuration - elapsed);
            
            setTimeout(() => {
                // Add fade-out classes
                preloader.classList.add('opacity-0', 'pointer-events-none');
                
                // Material transformation on exit
                const container = preloader.querySelector('.preloader-container');
                if (container) {
                    container.classList.add('scale-95', 'translate-y-[-15px]');
                }
                
                // Clean up DOM after transition ends
                setTimeout(() => {
                    preloader.remove();
                }, 700);
            }, remaining);
        };
        
        // Hide when everything is loaded
        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }
        
        // Safeguard: hide after 5 seconds max if window load event doesn't fire
        setTimeout(() => {
            if (document.getElementById('site-preloader')) {
                hidePreloader();
            }
        }, 5000);
    });
</script>
