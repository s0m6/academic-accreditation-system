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
</style>

<!-- Modern Premium Preloader -->
<div id="site-preloader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 dark:bg-[#020617] transition-all duration-700 ease-in-out select-none">
    <div class="relative flex flex-col items-center max-w-md px-6 text-center transition-all duration-700">
        
        <!-- Logo + Spinner -->
        <div class="relative w-28 h-28 mb-7 flex items-center justify-center">
            <!-- Single spinning ring -->
            <svg class="absolute inset-0 w-full h-full preloader-ring" viewBox="0 0 112 112" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="56" cy="56" r="52" stroke="#e2e8f0" stroke-width="3"/>
                <circle cx="56" cy="56" r="52" stroke="#e9c176" stroke-width="3"
                    stroke-linecap="round"
                    stroke-dasharray="327"
                    stroke-dashoffset="245"/>
            </svg>
            <!-- Logo -->
            <img src="{{ asset('images/logo.png') }}" alt="CAAQAHE" class="w-16 h-16 object-contain">
        </div>

        <!-- Title & Subtitle -->
        <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight preloader-text">
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
                const container = preloader.querySelector('.relative');
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
