{{-- Expert Data-Driven Breadcrumb Component --}}
@php
    $items = $items ?? []; // Expecting an associative array [label => url]
@endphp

<nav aria-label="Breadcrumb" class="flex mb-6 ">
    <ol class="inline-flex items-center gap-x-1 md:gap-x-2">
        
        {{-- 1. ROOT START (Always Included) --}}
        <li class="inline-flex items-center">
            <a href="/" 
               class="inline-flex items-center text-sm transition-colors duration-200 
                      {{ empty($items) 
                         ? 'font-bold text-brand-600 dark:text-brand-400' 
                         : 'font-medium text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400' }}">
                <i class="fa-solid fa-house me-2"></i>
                الرئيسية
            </a>
        </li>

        {{-- 2. DYNAMIC SEQUENCE --}}
        @foreach($items as $label => $url)
            <li class="flex items-center" @if($loop->last) aria-current="page" @endif>
                {{-- Separator Icon (chevron-left for RTL breadcrumbs) --}}
                <i class="fa-solid fa-chevron-left text-slate-300 dark:text-slate-600 mx-1 text-[10px]"></i>
                
                @if($loop->last)
                    {{-- Active Page Label --}}
                    <a href="{{ $url }}" class="ms-1 text-sm font-bold text-brand-600 dark:text-brand-400 md:ms-2">
                        {{ $label }}
            </a>
                @else
                    {{-- Intermediate Links --}}
                    <a href="{{ $url }}" 
                       class="ms-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 md:ms-2 transition-colors duration-200">
                        {{ $label }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
