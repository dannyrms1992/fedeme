{{-- Emergency Module — Número de Emergencias --}}
@php
    $settings  = $module->settings ?? [];
    $title     = $settings['title']     ?? 'Número de Emergencias';
    $logoPath  = $settings['logo_path'] ?? '';
    $phone     = $settings['phone']     ?? '';
@endphp

@if(!empty($phone))
<section id="emergencias" class="py-10 px-4" style="background: linear-gradient(to bottom, color-mix(in srgb, var(--color-secondary) 7%, var(--color-bg)), color-mix(in srgb, #DC2626 6%, var(--color-bg)))">
    <div class="max-w-6xl mx-auto">

        {{-- Header de sección --}}
        <div class="section-header">
            <div class="section-badge"
                 style="background: color-mix(in srgb, #DC2626 12%, white); color: #DC2626">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Emergencias
            </div>
            <h2 class="section-title">{{ $title }}</h2>
            <div class="mt-5 h-1 w-16 rounded-full mx-auto bg-gradient-to-r from-red-500 to-red-700"></div>
        </div>

        {{-- Tarjeta centrada --}}
        <div class="flex justify-center">
            <div class="glass rounded-3xl shadow-2xl p-8 md:p-12 flex flex-col items-center text-center max-w-sm w-full">

                {{-- Logo / Icono --}}
                @if($logoPath)
                    <div class="w-28 h-28 rounded-2xl overflow-hidden mb-6 shadow-md border border-white/60 flex items-center justify-center bg-white">
                        <img src="{{ asset('storage/' . $logoPath) }}"
                             alt="{{ $title }}"
                             class="w-full h-full object-contain p-2">
                    </div>
                @else
                    <div class="w-28 h-28 rounded-2xl mb-6 shadow-md flex items-center justify-center bg-red-50 border border-red-100">
                        <svg class="w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                @endif

                {{-- Número destacado --}}
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-base text-white shadow-md transition hover:scale-105 active:scale-95"
                   style="background: #DC2626">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $phone }}
                </a>

            </div>
        </div>

    </div>
</section>
@endif
