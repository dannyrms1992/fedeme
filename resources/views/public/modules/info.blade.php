{{-- Info Module with Glass Effect --}}
@php
    $settings       = $module->settings ?? [];
    $title          = $settings['title']          ?? 'Información General';
    $content        = $settings['content']        ?? '';
    $location       = $settings['location']       ?? '';
    $locationQuery  = $settings['location_query'] ?? $location;
    $dateStart      = $settings['date_start']     ?? '';
    $dateEnd        = $settings['date_end']        ?? '';

    // Formatear fechas en español
    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $formatDate = function(string $d) use ($meses): string {
        if (!$d) return '';
        $dt = \DateTime::createFromFormat('Y-m-d', $d);
        if (!$dt) return $d;
        return $dt->format('j') . ' de ' . $meses[(int)$dt->format('n') - 1] . ' de ' . $dt->format('Y');
    };
    $dateLabel = '';
    if ($dateStart && $dateEnd && $dateStart !== $dateEnd) {
        $dtS = \DateTime::createFromFormat('Y-m-d', $dateStart);
        $dtE = \DateTime::createFromFormat('Y-m-d', $dateEnd);
        // Mismo mes y año → "5 al 10 de marzo de 2026"
        if ($dtS && $dtE && $dtS->format('n-Y') === $dtE->format('n-Y')) {
            $dateLabel = $dtS->format('j') . ' al ' . $dtE->format('j') . ' de ' . $meses[(int)$dtE->format('n') - 1] . ' de ' . $dtE->format('Y');
        } else {
            $dateLabel = $formatDate($dateStart) . ' — ' . $formatDate($dateEnd);
        }
    } elseif ($dateStart) {
        $dateLabel = $formatDate($dateStart);
    }

    $mapsNavUrl = $locationQuery ? 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($locationQuery) : '';
@endphp

<section class="py-20 px-4" style="background: linear-gradient(to bottom, color-mix(in srgb, var(--color-primary) 8%, var(--color-bg)), color-mix(in srgb, var(--color-secondary) 6%, var(--color-bg)))">
    <div class="max-w-6xl mx-auto">

        {{-- Header de sección --}}
        <div class="section-header">
            <div class="section-badge"
                 style="background: color-mix(in srgb, var(--color-primary) 12%, white); color: var(--color-primary)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Información
            </div>
            <h2 class="section-title">{{ $title }}</h2>
            <div class="section-accent-bar"></div>
        </div>

        {{-- Glass card --}}
        <div class="glass rounded-3xl shadow-2xl p-8 md:p-12 hover:shadow-3xl transition-shadow duration-300">
            @if($content)
                <div class="prose prose-lg prose-slate max-w-none leading-relaxed text-gray-700" style="font-family: var(--font-sans)">
                    {!! nl2br(e($content)) !!}
                </div>
            @else
                <p class="text-gray-400 italic text-center py-8">Sin información configurada aún.</p>
            @endif

            @if($location || $dateLabel)
                <div class="mt-8 pt-8 border-t border-gray-200/70 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Lugar --}}
                    @if($location)
                    <div class="flex items-start gap-4 rounded-2xl p-5"
                         style="background: color-mix(in srgb, var(--color-primary) 7%, white)">
                        <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                             style="background: color-mix(in srgb, var(--color-primary) 15%, white)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: var(--color-primary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--color-primary)">Lugar del Evento</p>
                            <p class="text-sm font-medium text-gray-800 leading-snug">{{ $location }}</p>
                            @if($mapsNavUrl)
                                <a href="{{ $mapsNavUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 mt-2.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-150 shadow-sm hover:shadow-md hover:-translate-y-px"
                                   style="background: var(--color-primary); color: white">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Cómo llegar
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Fecha --}}
                    @if($dateLabel)
                    <div class="flex items-start gap-4 rounded-2xl p-5"
                         style="background: color-mix(in srgb, var(--color-secondary) 7%, white)">
                        <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                             style="background: color-mix(in srgb, var(--color-secondary) 15%, white)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: var(--color-secondary)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color: var(--color-secondary)">
                                {{ ($dateStart && $dateEnd && $dateStart !== $dateEnd) ? 'Fechas del Evento' : 'Fecha del Evento' }}
                            </p>
                            <p class="text-sm font-medium text-gray-800 leading-snug">{{ $dateLabel }}</p>
                            @if($dateStart && $dateEnd && $dateStart !== $dateEnd)
                                @php
                                    $dtS2 = \DateTime::createFromFormat('Y-m-d', $dateStart);
                                    $dtE2 = \DateTime::createFromFormat('Y-m-d', $dateEnd);
                                    $diff = $dtS2 && $dtE2 ? ($dtS2->diff($dtE2)->days + 1) : null;
                                @endphp
                                @if($diff)
                                    <p class="text-xs text-gray-500 mt-1">{{ $diff }} {{ $diff === 1 ? 'día' : 'días' }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                </div>
            @endif
        </div>

    </div>
</section>
