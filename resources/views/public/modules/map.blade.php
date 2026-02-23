{{-- Map Module — Google Maps embed via search query --}}
@php
    $settings    = $module->settings ?? [];
    $title       = $settings['title']       ?? 'Mapa Turístico';
    $description = $settings['description'] ?? '';
    $query       = $settings['map_query']   ?? '';
    $height      = $settings['map_height']  ?? '550';
    $embedUrl    = 'https://maps.google.com/maps?q=' . urlencode($query) . '&output=embed&hl=es';
@endphp

@if($query)
<section class="py-20" style="background: linear-gradient(to bottom, color-mix(in srgb, var(--color-primary) 5%, var(--color-bg)), color-mix(in srgb, var(--color-secondary) 7%, var(--color-bg)))">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Header --}}
        <div class="section-header">
            <div class="section-badge"
                 style="background: color-mix(in srgb, var(--color-primary) 12%, white); color: var(--color-primary)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Ubicación
            </div>
            <h2 class="section-title">{{ $title }}</h2>
            @if($description)
                <p class="section-description">{{ $description }}</p>
            @endif
            <div class="section-accent-bar"></div>
        </div>

        {{-- Map container --}}
        <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-200">

            {{-- Barra superior estilo navegador --}}
            <div class="flex items-center justify-between bg-gray-800 px-5 py-3 gap-3">

                {{-- Decoración browser (solo desktop) --}}
                <div class="hidden sm:flex items-center gap-3 min-w-0 flex-1">
                    <div class="flex gap-1.5 shrink-0">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-700 rounded-lg px-3 py-1 min-w-0">
                        <svg class="w-3.5 h-3.5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-gray-300 text-xs font-mono truncate">maps.google.com · {{ $query }}</span>
                    </div>
                </div>

                {{-- Botón — siempre visible, centrado en mobile --}}
                <a href="https://www.google.com/maps/search/{{ urlencode($query) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-1.5 shrink-0 mx-auto sm:mx-0
                          text-xs font-semibold text-white
                          bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40
                          px-3 py-1.5 rounded-lg transition-all duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Abrir en Google Maps
                </a>
            </div>

            {{-- Iframe --}}
            <iframe src="{{ $embedUrl }}"
                    class="w-full"
                    style="height: {{ $height }}px;"
                    frameborder="0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
            </iframe>

        </div>

    </div>
</section>
@endif
