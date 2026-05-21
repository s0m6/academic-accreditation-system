@php
    $partnerFiles = glob(public_path('images/partners/partner*.svg'));
    if ($partnerFiles) {
        natsort($partnerFiles);
    }
@endphp

@if($partnerFiles)
    @foreach($partnerFiles as $file)
        <div class="grayscale hover:grayscale-0 opacity-50 hover:opacity-100 transition-all duration-500 transform hover:scale-110 flex items-center justify-center cursor-pointer">
            <img class="h-24 w-auto" src="{{ asset('images/partners/' . basename($file)) }}" alt="Partner Logo" />
        </div>
    @endforeach
@endif
