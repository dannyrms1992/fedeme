{{-- Hero Module with Carousel --}}
@php
    $settings      = $module->settings ?? [];
    $title         = $settings['title']         ?? $event->name;
    $subtitle      = $settings['subtitle']      ?? ($event->description ?? '');
    $showCarousel  = filter_var($settings['show_carousel'] ?? true, FILTER_VALIDATE_BOOLEAN);
@endphp

@if($showCarousel)
<section class="relative" style="background: linear-gradient(to bottom, var(--color-bg), color-mix(in srgb, var(--color-primary) 8%, var(--color-bg)))">

    {{-- Carousel or Hero Image --}}
    <div class="container mx-auto px-4 pt-6 pb-12">
        @if($event->carousel_images && count($event->carousel_images) > 0)
            <x-carousel :event="$event" />
        @else
            {{-- Fallback hero sin imagen --}}
            <div class="h-[400px] md:h-[500px] flex items-center justify-center -mx-4 px-4 bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-secondary)] to-[var(--color-accent)]">
                <div class="text-center max-w-4xl px-4">
                    <h1 class="hero-title text-white mb-6">
                        {{ $title }}
                    </h1>
                    @if($subtitle)
                        <p class="hero-subtitle text-white/90">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>

</section>
@endif
