<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة اعتماد - مجلس الاعتماد الأكاديمي</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .cert-scale { transform: scale(0.6); }
        @media (min-height: 800px) { .cert-scale { transform: scale(0.7); } }
        @media (min-height: 900px) { .cert-scale { transform: scale(0.8); } }
        @media (min-height: 1000px) { .cert-scale { transform: scale(0.9); } }
    </style>
</head>
<body class="bg-[#f7fafc] font-['Cairo'] antialiased h-screen flex items-center justify-center overflow-hidden m-0 p-0">

    <div class="w-full h-full p-4 flex items-center justify-center">
        <!-- Certificate Card (A4 Landscape) -->
        <div class="cert-scale relative w-[297mm] h-[210mm] bg-white flex flex-col p-0 overflow-hidden shadow-[0_40px_80px_rgba(0,37,70,0.12)] origin-center shrink-0 z-10">
            
            <!-- Professional Certificate Frame -->
            <div class="absolute inset-[16px] border border-[#e9c176]/50 pointer-events-none z-40"></div>
            <div class="absolute inset-[22px] border-[3px] border-[#e9c176] pointer-events-none z-40"></div>
            <div class="absolute inset-[28px] border border-[#002546]/30 pointer-events-none z-40"></div>

            <!-- Watermark Background (z-0) -->
            <img src="{{ asset('images/logo.png') }}" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] opacity-[0.03] pointer-events-none z-0" alt="">

            <!-- Medal Ribbon - Bottom Left (z-50) -->
            <div class="absolute bottom-[40px] left-[70px] z-50 w-[130px] drop-shadow-[0_10px_20px_rgba(0,0,0,0.1)]">
                <svg viewBox="0 0 100 140">
                    <path d="M25 40 L10 130 L50 115 L90 130 L75 40" fill="#002546" />
                    <circle cx="50" cy="50" r="45" fill="#e9c176" />
                    <circle cx="50" cy="50" r="38" fill="none" stroke="white" stroke-width="1.5" stroke-dasharray="2,2" />
                    <path d="M35 50 L45 60 L65 40" fill="none" stroke="#002546" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                    <text x="50" y="75" text-anchor="middle" font-size="9" font-weight="900" fill="#002546">اعتماد</text>
                    <text x="50" y="83" text-anchor="middle" font-size="6" font-weight="bold" fill="#002546">CAAQAHE</text>
                </svg>
            </div>

            <!-- Top Left Diamonds (z-20) -->
            <div class="absolute top-[40px] left-[60px] flex gap-3 z-20">
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-80"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-60"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-40"></div>
                <div class="w-[16px] h-[16px] bg-[#e9c176] rotate-45 opacity-20"></div>
            </div>

            <!-- Header Right: Logo & Name (z-20) -->
            <div class="absolute top-[45px] right-[70px] flex items-center gap-5 text-right z-20">
                <img src="{{ asset('images/logo.png') }}" class="h-[90px] object-contain" alt="Council Logo">
                <div class="text-[#002546] leading-tight">
                    <p class="font-black text-2xl uppercase">مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي</p>
                </div>
            </div>

            <!-- Main Content Container (z-10) -->
            <div class="flex-1 mt-[130px] px-[80px] flex flex-col items-center justify-center text-center w-full z-10 pb-[60px]">
                <h1 class="text-[60px] font-black text-[#c39f58] m-0 leading-none">شهادة اعتماد</h1>
                <div class="w-[50px] h-[6px] bg-[#e9c176] mt-4 mb-8 rounded-full"></div>

                <div class="text-[21px] text-[#002546] leading-[1.7] mb-5 font-medium">
                    يشهد مجلس الاعتماد الأكاديمي وضمان جودة التعليم العالي
                    <br>
                    بأن البرنامج الأكاديمي:
                    
                    <span class="text-[#002546] text-[32px] font-black block my-1">بكالوريوس الدراسات الإسلامية</span>
                    <span class="text-[#64748b] text-[25px] font-bold block mb-3">جامعة العلوم والتكنولوجيا</span>
                </div>

                <div class="text-[21px] text-[#002546] font-semibold">
                   قد حصل على الإعتماد البرامجي, بمستوى:
                    
                    <span class="text-[#059669] text-[38px] font-black inline-block mt-2 px-5">محقق بإتقان</span>
                </div>

                <div class="mt-8 flex gap-[50px] text-[18px] text-[#002546] font-semibold">
                    <div class="flex items-center">من تاريخ: <span class="font-extrabold text-[#001a33] px-4 mx-2">20 مايو 2026م</span></div>
                    <div class="flex items-center">إلى تاريخ: <span class="font-extrabold text-[#001a33] px-4 mx-2">19 فبراير 2029م</span></div>
                </div>
            </div>

            <!-- QR Code Area - Ensuring solid background and high z-index -->
            <div class="absolute bottom-[40px] right-[80px] w-[130px] h-[130px] z-[100] bg-white border-[3px] border-[#e9c176] rounded-xl flex items-center justify-center p-2 shadow-sm overflow-hidden">
                <img src="{{ asset('images/QR-test.png') }}" class="w-full h-full object-contain relative z-[110]" alt="QR Code">
            </div>

        </div>
    </div>

</body>
</html>
