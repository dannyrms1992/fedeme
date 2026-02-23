{{-- Carousel Component — CSS opacity transitions + Lightbox --}}
@php
    $images   = $event->carousel_images ?? [];
    $autoplay = $autoplay ?? true;
    $interval = $interval ?? 5000;
    $count    = count($images);
    $srcs     = array_map(fn($img) => asset('storage/' . $img), $images);
@endphp

@if($count > 0)
<div x-data="{
        current: 0,
        total: {{ $count }},
        timer: null,
        lb: false,
        srcs: {{ Js::from($srcs) }},
        next() { this.current = (this.current + 1) % this.total; this.restart(); },
        prev() { this.current = (this.current - 1 + this.total) % this.total; this.restart(); },
        go(i)  { this.current = i; this.restart(); },
        restart() {
            clearInterval(this.timer);
            if ({{ $autoplay && $count > 1 ? 'true' : 'false' }}) {
                this.timer = setInterval(() => this.next(), {{ $interval }});
            }
        },
        openLb()  { this.lb = true;  clearInterval(this.timer); document.body.style.overflow = 'hidden'; },
        closeLb() { this.lb = false; document.body.style.overflow = ''; this.restart(); },
        lbNext()  { this.current = (this.current + 1) % this.total; },
        lbPrev()  { this.current = (this.current - 1 + this.total) % this.total; }
     }"
     x-init="if ({{ $autoplay && $count > 1 ? 'true' : 'false' }}) { timer = setInterval(() => next(), {{ $interval }}); }"
     @keydown.escape.window="if (lb) closeLb()"
     @keydown.arrow-right.window="if (lb) lbNext()"
     @keydown.arrow-left.window="if (lb) lbPrev()"
     class="relative w-full h-[400px] md:h-[500px] lg:h-[580px] overflow-hidden rounded-2xl shadow-2xl">

    {{-- Slides --}}
    @foreach($images as $i => $img)
    <div :class="current === {{ $i }} ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 z-0'"
         class="absolute inset-0 transition-all duration-700 ease-in-out">
        <img src="{{ asset('storage/' . $img) }}"
             alt="{{ $event->name }} — imagen {{ $i + 1 }}"
             @click="openLb()"
             class="w-full h-full object-cover cursor-zoom-in">
        <div class="absolute inset-0 bg-gradient-to-t from-[var(--color-primary)]/60 via-transparent to-transparent pointer-events-none"></div>
    </div>
    @endforeach

    {{-- Expand hint --}}
    <div class="absolute top-3 left-4 z-20 flex items-center gap-1.5 bg-black/40 backdrop-blur-sm text-white/80 text-xs px-2.5 py-1 rounded-full pointer-events-none">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
        </svg>
        Ver pantalla completa
    </div>

    @if($count > 1)
    {{-- Prev/Next arrows --}}
    <button @click="prev()"
            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm hover:bg-white/80 p-3 rounded-full transition-all duration-200 group">
        <svg class="w-5 h-5 text-white group-hover:text-[var(--color-primary)] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="next()"
            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm hover:bg-white/80 p-3 rounded-full transition-all duration-200 group">
        <svg class="w-5 h-5 text-white group-hover:text-[var(--color-primary)] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    {{-- Dot indicators --}}
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
        @foreach($images as $i => $_)
        <button @click="go({{ $i }})"
                :class="current === {{ $i }} ? 'w-8 bg-white' : 'w-3 bg-white/50 hover:bg-white/80'"
                class="h-3 rounded-full transition-all duration-300">
        </button>
        @endforeach
    </div>
    @endif

    {{-- Slide counter --}}
    <div class="absolute top-3 right-4 z-20 bg-black/40 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full font-medium">
        <span x-text="current + 1"></span>&nbsp;/&nbsp;{{ $count }}
    </div>

    {{-- ── Lightbox ─────────────────────────────────────────────────── --}}
    <div x-show="lb"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="closeLb()"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/92 backdrop-blur-md"
         style="display:none">

        {{-- Imagen principal --}}
        <div class="relative max-w-[92vw] max-h-[88vh] flex items-center justify-center">
            <img :src="srcs[current]"
                 :alt="`Imagen ${current + 1} de {{ $count }}`"
                 class="max-w-full max-h-[88vh] object-contain rounded-xl shadow-2xl select-none"
                 draggable="false">

            {{-- Contador lightbox --}}
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                <span x-text="current + 1"></span> / {{ $count }}
            </div>
        </div>

        {{-- Botón cerrar --}}
        <button @click="closeLb()"
                class="absolute top-4 right-4 bg-white/10 hover:bg-white/25 border border-white/20 text-white p-2.5 rounded-full transition-all duration-150 backdrop-blur-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        @if($count > 1)
        {{-- Flechas lightbox --}}
        <button @click="lbPrev()"
                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/25 border border-white/20 text-white p-3 rounded-full transition-all duration-150 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="lbNext()"
                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/25 border border-white/20 text-white p-3 rounded-full transition-all duration-150 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Thumbnails lightbox --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 px-4">
            @foreach($images as $i => $img)
            <button @click="current = {{ $i }}"
                    :class="current === {{ $i }} ? 'ring-2 ring-white opacity-100' : 'opacity-50 hover:opacity-80'"
                    class="w-12 h-8 rounded-md overflow-hidden transition-all duration-200 shrink-0">
                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover" draggable="false">
            </button>
            @endforeach
        </div>
        @endif

    </div>
    {{-- ── /Lightbox ────────────────────────────────────────────────── --}}

</div>
@endif
