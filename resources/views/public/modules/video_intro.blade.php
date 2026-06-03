{{-- Video Intro / Announcement Module — overlay shown on page load --}}
@php
    $settings  = $module->settings ?? [];
    $videoPath = $settings['video_path'] ?? null;
    $videoUrl  = $settings['video_url']  ?? null;
    $title     = $settings['title']      ?? null;
    $showOnce  = filter_var($settings['show_once'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sessionKey = 'ann_' . $event->id;

    // Resolve video source — URL takes priority over uploaded file
    $isEmbed  = false;
    $videoSrc = null;

    if ($videoUrl) {
        // YouTube: watch URL → embed
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $videoSrc = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&rel=0';
            $isEmbed  = true;
        // YouTube: short URL
        } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $videoSrc = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&rel=0';
            $isEmbed  = true;
        // YouTube: live URL
        } elseif (preg_match('/youtube\.com\/live\/([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $videoSrc = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&rel=0';
            $isEmbed  = true;
        // Vimeo
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $m)) {
            $videoSrc = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&muted=1';
            $isEmbed  = true;
        // Direct URL (mp4, etc.)
        } else {
            $videoSrc = $videoUrl;
            $isEmbed  = false;
        }
    } elseif ($videoPath) {
        $videoSrc = asset('storage/' . $videoPath);
        $isEmbed  = false;
    }
@endphp

@if($videoSrc)
<div
    x-data="{
        open: true,
        paused: true,
        init() {
            @if($showOnce)
            if (sessionStorage.getItem('{{ $sessionKey }}')) {
                this.open = false;
            }
            @endif
        },
        close() {
            this.open = false;
            @if($showOnce)
            sessionStorage.setItem('{{ $sessionKey }}', '1');
            @endif
            @if(!$isEmbed)
            if (this.$refs.video) this.$refs.video.pause();
            @endif
        }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-2 sm:p-6"
    @keydown.escape.window="close()"
    @open-video-intro.window="open = true"
    role="dialog"
    aria-modal="true"
>
    <div class="w-full max-w-3xl">

        {{-- Barra superior: título + botón cerrar --}}
        <div class="flex items-center justify-between mb-2 px-1">
            @if($title)
                <p class="text-white/70 text-sm truncate pr-4">{{ $title }}</p>
            @else
                <span></span>
            @endif
            <button
                @click="close()"
                class="flex items-center gap-1.5 text-white/80 hover:text-white transition text-sm font-medium shrink-0"
                aria-label="Cerrar anuncio"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="hidden sm:inline">Cerrar</span>
            </button>
        </div>

        {{-- Video --}}
        <div class="relative rounded-xl overflow-hidden shadow-2xl bg-black aspect-video"
             @if($isEmbed) x-data="{ muted: true, unmute() {
                 this.$refs.embedFrame.src = this.$refs.embedFrame.src.replace('mute=1', 'mute=0');
                 this.muted = false;
             }}" @endif>
            @if($isEmbed)
                <iframe
                    x-ref="embedFrame"
                    src="{{ $videoSrc }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                ></iframe>

                {{-- Botón activar sonido — visible en mobile donde los controles del iframe no son accesibles --}}
                <div x-show="muted"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10">
                    <button @click="unmute()"
                            class="flex items-center gap-2 bg-black/70 hover:bg-black/90 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2.5 rounded-full border border-white/20 shadow-lg transition-all duration-150 active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.536 8.464a5 5 0 010 7.072M12 6v12m0 0l-3-3m3 3l3-3M9 10H5.5a.5.5 0 00-.5.5v3a.5.5 0 00.5.5H9l4 4V6L9 10z"/>
                        </svg>
                        Activar sonido
                    </button>
                </div>
            @else
                {{--
                    Sin muted ni autoplay: el overlay aparece automáticamente pero el video
                    arranca pausado. El usuario pulsa el botón ▶ central → sonido completo.
                    Los controles nativos dan acceso a volumen y pantalla completa.
                --}}
                <video
                    x-ref="video"
                    src="{{ $videoSrc }}"
                    class="w-full h-full object-contain"
                    playsinline
                    controls
                    @play="paused = false"
                    @pause="paused = true"
                ></video>

                {{-- Botón Play visible al centro cuando está pausado --}}
                <div
                    x-show="paused"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="absolute inset-0 flex items-center justify-center pointer-events-none"
                >
                    <button
                        @click="$refs.video.play()"
                        class="pointer-events-auto w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white/20 hover:bg-white/35 backdrop-blur-sm flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 shadow-2xl border border-white/30"
                        aria-label="Reproducir video"
                    >
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white drop-shadow-lg" style="margin-left: 4px" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>
@endif
