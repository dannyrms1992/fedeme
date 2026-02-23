<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Dynamic SEO per event --}}
    <title>{{ $event->name ?? config('app.name') }}</title>
    <meta name="description" content="{{ $event->description ?? '' }}">

    {{-- OpenGraph --}}
    <meta property="og:title" content="{{ $event->name ?? config('app.name') }}">
    <meta property="og:description" content="{{ $event->description ?? '' }}">
    @if($event->logo_path ?? false)
        <meta property="og:image" content="{{ asset($event->logo_path) }}">
    @endif
    <meta property="og:type" content="website">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Dynamic brand colors --}}
    <style>
        :root {
            /* Colores base del evento */
            --color-primary:   {{ $event->primary_color   ?? '#1F4E79' }};
            --color-secondary: {{ $event->secondary_color ?? '#C1121F' }};
            --color-accent:    {{ $event->accent_color    ?? '#F59E0B' }};
            --color-bg:        {{ $event->bg_color        ?? '#F8FAFC' }};
            --color-surface:   {{ $event->surface_color   ?? '#FFFFFF' }};

            /* Derivados automáticos */
            --color-primary-light:    color-mix(in srgb, var(--color-primary)   12%, white);
            --color-primary-dark:     color-mix(in srgb, var(--color-primary)   80%, black);
            --color-secondary-light:  color-mix(in srgb, var(--color-secondary) 12%, white);
            --color-accent-light:     color-mix(in srgb, var(--color-accent)    15%, white);
            --color-bg-tinted:        color-mix(in srgb, var(--color-bg)        92%, var(--color-primary));

            /* Glass */
            --glass-bg:     color-mix(in srgb, var(--color-surface) 70%, transparent);
            --glass-border: color-mix(in srgb, var(--color-surface) 50%, transparent);
        }

        /* Clase utilitaria glass — sobreescrita por app.css pero con estas vars */
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html { scroll-behavior: smooth; scroll-padding-top: 6rem; }
        body { padding-top: 0 !important; }
    </style>
</head>
<body class="min-h-screen text-gray-900 antialiased" style="background: var(--color-bg)">

    {{-- ── Navbar flotante glass ───────────────────────────────────────── --}}
    <header class="fixed top-4 left-0 right-0 z-50 px-4"
            x-data="{ open: false }">
        <div class="max-w-6xl mx-auto flex flex-col gap-1.5">

            {{-- Pill principal --}}
            <div class="rounded-2xl shadow-2xl border border-white/20 text-white"
                 style="background: color-mix(in srgb, var(--color-primary) 55%, transparent); backdrop-filter: blur(24px) saturate(180%);">                
                <div class="px-5 grid grid-cols-[auto_1fr_auto] items-center min-h-14 py-1.5 gap-3">

                    {{-- Logo (izquierda) --}}
                    <a href="#inicio" class="flex items-center shrink-0">
                        @if($event->logo_path ?? false)
                            <img src="{{ asset('storage/' . $event->logo_path) }}"
                                 alt="{{ $event->name }}"
                                 class="h-9 w-auto object-contain drop-shadow-md">
                        @else
                            <div class="h-9 w-9 rounded-xl bg-white/20 flex items-center justify-center border border-white/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                            </div>
                        @endif
                    </a>

                    {{-- Título centrado --}}
                    <a href="#inicio" class="text-center min-w-0">
                        <span class="font-bold text-sm md:text-base leading-tight line-clamp-1 block drop-shadow-sm">
                            {{ $event->name ?? config('app.name') }}
                        </span>
                    </a>

                    {{-- Desktop nav / Mobile hamburger (derecha) --}}
                    <div class="flex items-center shrink-0">
                        @php
                            $activeTypes = isset($modules) ? $modules->pluck('type')->toArray() : [];
                            $navLinks = [
                                'hero'      => ['href' => '#inicio',       'label' => 'Inicio'],
                                'info'      => ['href' => '#informacion',  'label' => 'Información'],
                                'pdf'       => ['href' => '#documentos',   'label' => 'Documentos'],
                                'map'       => ['href' => '#mapa',         'label' => 'Mapa'],
                                'emergency' => ['href' => '#emergencias',  'label' => 'Emergencias'],
                                'contact'   => ['href' => '#contacto',     'label' => 'Contacto'],
                            ];
                        @endphp

                        {{-- Desktop nav --}}
                        <nav class="hidden md:flex items-center gap-0.5 text-sm font-medium">
                            @foreach($navLinks as $type => $link)
                                @if(empty($activeTypes) || in_array($type, $activeTypes))
                                    <a href="{{ $link['href'] }}"
                                       class="px-3 py-1.5 rounded-xl hover:bg-white/20 active:bg-white/30 transition-all duration-150">
                                        {{ $link['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </nav>

                        {{-- Mobile hamburger --}}
                        <button @click="open = !open"
                                class="md:hidden p-2 rounded-xl hover:bg-white/20 transition"
                                :aria-expanded="open">
                            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            {{-- Mobile menu — pill separada --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                 class="md:hidden rounded-2xl shadow-xl border border-white/20 text-white overflow-hidden"
                 style="background: color-mix(in srgb, var(--color-primary) 60%, transparent); backdrop-filter: blur(24px) saturate(180%);">
                <nav class="px-4 py-3 space-y-0.5 text-sm font-medium">
                    @foreach($navLinks as $type => $link)
                        @if(empty($activeTypes) || in_array($type, $activeTypes))
                            <a href="{{ $link['href'] }}" @click="open=false"
                               class="flex items-center px-4 py-2.5 rounded-xl hover:bg-white/20 transition">
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

        </div>
    </header>

    {{-- Main content --}}
    <main class="pt-24">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="text-center text-sm py-6 mt-16" style="background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 92%, black) 0%, color-mix(in srgb, var(--color-secondary) 75%, black) 100%)">
        <p class="text-white text-xs">
            &copy; {{ date('Y') }} <span class="text-white font-semibold">FEDEME — Federación Deportiva Militar del Ecuador</span>
            <span class="mx-2 text-white">·</span>
            Desarrollado por <span class="text-white font-semibold">Devercon S.A.S</span>
        </p>
    </footer>

    @livewireScripts
</body>
</html>
