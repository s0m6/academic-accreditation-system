<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة اعتماد - {{ $data['program_name'] ?? '' }} | مجلس الاعتماد الأكاديمي</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .cert-scale { transform: scale(0.6); transform-origin: center center; }
        @media (min-height: 800px)  { .cert-scale { transform: scale(0.7); } }
        @media (min-height: 900px)  { .cert-scale { transform: scale(0.8); } }
        @media (min-height: 1000px) { .cert-scale { transform: scale(0.9); } }
    </style>
</head>
<body class="bg-[#f0f4f8] font-sans antialiased min-h-screen">
    <!-- Global Preloader -->
    @include('public.partials.preloader')


    {{-- Top status bar --}}
    <div class="w-full py-3 px-6 flex items-center justify-between text-sm font-bold
        {{ $isValid ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
        <div class="flex items-center gap-2">
            <i class="fa-solid {{ $isValid ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
            <span>{{ $isValid ? 'شهادة سارية المفعول — تم التحقق من صحتها' : 'هذه الشهادة منتهية الصلاحية' }}</span>
        </div>
        <div class="flex items-center gap-2 text-xs opacity-80">
            <i class="fa-solid fa-shield-halved"></i>
            <span>مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</span>
        </div>
    </div>

    {{-- Certificate area --}}
    <div class="w-full flex items-center justify-center py-8" style="min-height: calc(100vh - 120px); overflow: hidden;">
        {{-- Certificate Card (A4 Landscape) --}}
        <div class="cert-scale relative w-[297mm] h-[210mm] bg-white flex flex-col p-0 overflow-hidden shadow-[0_40px_80px_rgba(0,37,70,0.15)] origin-center shrink-0 z-10">

            {{-- Professional Certificate Frame --}}
            <div class="absolute inset-[16px] border border-[#e9c176]/50 pointer-events-none z-40"></div>
            <div class="absolute inset-[22px] border-[3px] border-[#e9c176] pointer-events-none z-40"></div>
            <div class="absolute inset-[28px] border border-[#002546]/30 pointer-events-none z-40"></div>

            {{-- Watermark Background --}}
            <img src="{{ asset('images/logo.png') }}"
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] opacity-[0.03] pointer-events-none z-0" alt="">

            {{-- Medal Ribbon --}}
            <div class="absolute bottom-[40px] left-[70px] z-50 w-[130px] drop-shadow-[0_10px_20px_rgba(0,0,0,0.1)]">
                <svg viewBox="0 0 100 140">
                    <path d="M25 40 L10 130 L50 115 L90 130 L75 40" fill="#002546"/>
                    <circle cx="50" cy="50" r="45" fill="#e9c176"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="white" stroke-width="1.5" stroke-dasharray="2,2"/>
                    <path d="M35 50 L45 60 L65 40" fill="none" stroke="#002546" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    <text x="50" y="75" text-anchor="middle" font-size="9" font-weight="900" fill="#002546">اعتماد</text>
                    <text x="50" y="83" text-anchor="middle" font-size="6" font-weight="bold" fill="#002546">CAAQAHE</text>
                </svg>
            </div>

            {{-- Top Left Diamonds --}}
            <div class="absolute top-[40px] left-[60px] flex gap-3 z-20">
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-80"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-60"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-40"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-20"></div>
            </div>

            {{-- Header: Logo & Name --}}
            <div class="absolute top-[45px] right-[70px] flex items-center gap-5 text-right z-20">
                <img src="{{ asset('images/logo.png') }}" class="h-[90px] object-contain" alt="Council Logo">
                <div class="text-[#002546] leading-tight">
                    <p class="font-black text-2xl uppercase">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex-1 mt-[130px] px-[80px] flex flex-col items-center justify-center text-center w-full z-10 pb-[60px]">
                <h1 class="text-[60px] font-black text-[#c39f58] m-0 leading-none">شهادة اعتماد</h1>
                <div class="w-[50px] h-[6px] bg-[#e9c176] mt-4 mb-8 rounded-full"></div>

                <div class="text-[21px] text-[#002546] leading-[1.7] mb-5 font-medium">
                    يشهد مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي
                    <br>
                    بأن البرنامج الأكاديمي:
                    <span class="text-[#002546] text-[32px] font-black block my-1">{{ $data['program_name'] ?? '—' }}</span>
                    <span class="text-[#64748b] text-[25px] font-bold block mb-3">{{ $data['university_name'] ?? '—' }}</span>
                </div>

                <div class="text-[21px] text-[#002546] font-semibold">
                    قد حصل على الاعتماد البرامجي، بمستوى:
                    <span class="text-[#059669] text-[38px] font-black inline-block mt-2 px-5">{{ $data['achievement_level'] ?? '—' }}</span>
                </div>

                <div class="mt-8 flex gap-[50px] text-[18px] text-[#002546] font-semibold">
                    <div class="flex items-center">من تاريخ: <span class="font-extrabold text-[#001a33] px-4 mx-2">{{ $data['issued_at'] ?? '—' }}</span></div>
                    <div class="flex items-center">إلى تاريخ: <span class="font-extrabold text-[#001a33] px-4 mx-2">{{ $data['expires_at'] ?? '—' }}</span></div>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="absolute bottom-[40px] right-[80px] w-[130px] h-[130px] z-[100] bg-white border-[3px] border-[#e9c176] rounded-xl flex items-center justify-center p-2 shadow-sm overflow-hidden">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->format('svg')->generate($certificateUrl) !!}
            </div>

            {{-- Certificate Number --}}
            <div class="absolute bottom-[44px] left-[220px] z-20 text-center">
                <p class="text-[10px] text-[#002546]/50 font-bold">رقم الشهادة</p>
                <p class="text-[9px] font-mono text-[#002546]/40">{{ $certificate->certificate_number }}</p>
            </div>

        </div>
    </div>

    {{-- Info panel below certificate --}}
    <div class="max-w-2xl mx-auto px-4 pb-10">
        <div class="rounded-2xl border {{ $isValid ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10' : 'border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10' }} p-5 text-center">
            <p class="text-sm font-bold {{ $isValid ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                <i class="fa-solid {{ $isValid ? 'fa-shield-check' : 'fa-triangle-exclamation' }} ml-2"></i>
                {{ $isValid ? 'هذه الشهادة أصيلة وصادرة من مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي.' : 'هذه الشهادة منتهية الصلاحية. يُرجى التواصل مع الجهة المعنية للتحقق.' }}
            </p>
        </div>
    </div>

</body>
</html>
