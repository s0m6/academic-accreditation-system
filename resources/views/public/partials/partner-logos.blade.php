@php
    $partnerFiles = glob(public_path('images/partners/partner*.svg'));
    if ($partnerFiles) {
        natsort($partnerFiles);
    }
@endphp

@if($partnerFiles)
    @foreach($partnerFiles as $file)
        <div class="shrink-0 mx-6 md:mx-10 transition-all duration-300 transform hover:scale-105 flex items-center justify-center cursor-pointer">
            <img class="h-28 md:h-32 w-auto object-contain" src="{{ asset('images/partners/' . basename($file)) }}" alt="Partner Logo" />
        </div>
    @endforeach
@endif
