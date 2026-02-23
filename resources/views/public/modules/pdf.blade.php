{{-- PDF Module — Google Docs Viewer (desktop) + botón nueva pestaña (mobile) --}}
@php
    $settings    = $module->settings ?? [];
    $title       = $settings['title']       ?? 'Documento';
    $description = $settings['description'] ?? '';
    $rawUrl      = $settings['pdf_url']     ?? '';

    // Convertir URLs de Google Drive al formato embed nativo (/preview)
    // De: https://drive.google.com/file/d/FILE_ID/view
    // A:  https://drive.google.com/file/d/FILE_ID/preview
    if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $rawUrl, $m)) {
        $embedUrl  = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        $directUrl = 'https://drive.google.com/file/d/' . $m[1] . '/view';
    } else {
        // Para cualquier otra URL pública, usar Google Docs Viewer
        $embedUrl  = 'https://docs.google.com/viewer?url=' . urlencode($rawUrl) . '&embedded=true';
        $directUrl = $rawUrl;
    }
@endphp

@if($rawUrl)
<section class="py-20" style="background: linear-gradient(to bottom, color-mix(in srgb, var(--color-accent) 6%, var(--color-bg)), color-mix(in srgb, var(--color-primary) 5%, var(--color-bg)))">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Header de sección --}}
        <div class="section-header">
            <div class="section-badge"
                 style="background: color-mix(in srgb, var(--color-primary) 12%, white); color: var(--color-primary)">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                </svg>
                Documento PDF
            </div>
            <h2 class="section-title">{{ $title }}</h2>
            @if($description)
                <p class="section-description">{{ $description }}</p>
            @endif
            <div class="section-accent-bar"></div>
        </div>

        {{-- Visor / Card --}}
        <div x-data="{ isMobile: window.innerWidth < 768 }"
             x-init="isMobile = window.innerWidth < 768"
             @resize.window="isMobile = window.innerWidth < 768">

            {{-- MOBILE: Card prominente con botón --}}
            <div x-show="isMobile"
                 class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6"
                     style="background: color-mix(in srgb, var(--color-primary) 12%, white)">
                    <svg class="w-10 h-10" style="color: var(--color-primary)" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                    </svg>
                </div>
                <h3 class="card-title text-center text-xl mb-2">{{ $title }}</h3>
                @if($description)
                    <p class="card-body text-center mb-6">{{ $description }}</p>
                @else
                    <p class="card-body text-center mb-6">Toca el botón para ver el documento completo</p>
                @endif
                <a href="{{ $directUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 text-white font-semibold px-6 py-3 rounded-xl transition shadow-md"
                   style="background: var(--color-accent)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver documento
                </a>
            </div>

            {{-- DESKTOP: Iframe embebido --}}
            <div x-show="!isMobile" class="rounded-2xl overflow-hidden shadow-xl border border-gray-200">
                {{-- Barra superior --}}
                <div class="flex items-center justify-between bg-gray-800 px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <span class="text-gray-300 text-sm font-medium truncate max-w-xs">{{ $title }}</span>
                    </div>
                    <a href="{{ $directUrl }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Abrir en nueva pestaña
                    </a>
                </div>

                {{-- Iframe Google Docs / Drive --}}
                <iframe src="{{ $embedUrl }}"
                        class="w-full"
                        style="height: 720px;"
                        frameborder="0"
                        allowfullscreen
                        loading="lazy">
                    <p class="p-8 text-center text-gray-400">
                        Tu navegador no soporta la visualización de PDFs.
                        <a href="{{ $directUrl }}" target="_blank" class="text-blue-600 underline">Descargar PDF</a>
                    </p>
                </iframe>
            </div>

        </div>

    </div>
</section>
@endif
