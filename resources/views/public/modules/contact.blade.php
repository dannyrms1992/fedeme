{{-- Contact Module with Glass Effect --}}
@php
    $settings = $module->settings ?? [];
    $title   = $settings['title']   ?? 'Contactos Importantes';
    $contacts = $settings['contacts'] ?? [];
    // Fallback para formato anterior
    if (empty($contacts)) {
        $contacts = [[
            'name' => $settings['name'] ?? 'Organizador',
            'email' => $settings['email'] ?? null,
            'phone' => $settings['phone'] ?? null,
            'role' => $settings['role'] ?? null,
        ]];
    }
@endphp

<section class="py-10 px-4" style="background: linear-gradient(to bottom, color-mix(in srgb, var(--color-secondary) 6%, var(--color-bg)), color-mix(in srgb, var(--color-accent) 6%, var(--color-bg)))">
    <div class="max-w-6xl mx-auto">

        {{-- Header de sección --}}
        <div class="section-header">
            <div class="section-badge"
                 style="background: color-mix(in srgb, var(--color-primary) 12%, white); color: var(--color-primary)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contacto
            </div>
            <h2 class="section-title">{{ $title }}</h2>
            <div class="section-accent-bar"></div>
        </div>
        
        {{-- Glass card wrapper --}}
        <div class="glass rounded-3xl shadow-2xl p-8 md:p-12 hover:shadow-3xl transition-shadow duration-300">

        {{-- Contact cards grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($contacts as $contact)
                @if(!empty($contact['email']) || !empty($contact['phone']))
                    <div class="bg-white/60 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 group border border-white/50">
                        
                        <div class="mb-4">
                            <h4 class="card-title group-hover:text-[var(--color-primary)] transition">
                                {{ $contact['name'] ?? 'Contacto' }}
                            </h4>
                            @if(!empty($contact['role']))
                                <p class="card-meta mt-1">{{ $contact['role'] }}</p>
                            @endif
                        </div>
                        
                        {{-- Contact info --}}
                        <div class="space-y-3">
                            @if(!empty($contact['email']))
                                <div class="flex items-start gap-3">
                                    <svg class="w-4 h-4 text-[var(--color-primary)] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <a href="mailto:{{ $contact['email'] }}" class="card-body hover:text-[var(--color-primary)] hover:underline break-all transition">
                                        {{ $contact['email'] }}
                                    </a>
                                </div>
                            @endif
                            
                            @if(!empty($contact['phone']))
                                <div class="flex items-start gap-3">
                                    <svg class="w-4 h-4 text-[var(--color-primary)] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" class="card-body hover:text-[var(--color-primary)] hover:underline transition">
                                        {{ $contact['phone'] }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                    </div>
                @endif
            @endforeach
        </div>
        
        @if(empty($contacts) || collect($contacts)->filter(fn($c) => !empty($c['email']) || !empty($c['phone']))->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <p>No hay contactos configurados aún.</p>
            </div>
        @endif

        </div>{{-- /glass wrapper --}}
        
    </div>
</section>
