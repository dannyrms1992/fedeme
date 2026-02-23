@extends('layouts.admin')
@section('title', 'Editar — ' . $event->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.show', $event) }}" class="text-gray-500 hover:text-gray-800 text-sm">&larr; Volver</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Editar: {{ $event->name }}</h1>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
@endif

{{-- ─── Datos generales ───────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow p-6 max-w-2xl mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Datos generales</h2>
    <form method="POST" action="{{ route('admin.events.update', $event) }}">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 gap-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del evento *</label>
                <input type="text" name="name" value="{{ old('name', $event->name) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description', $event->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @foreach(\App\Domain\Event\ValueObjects\EventStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ $event->status === $status ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Paleta de 5 colores --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Paleta de colores</label>
                <div class="grid grid-cols-5 gap-3">
                    <div class="text-center">
                        <input type="color" name="primary_color"
                               value="{{ old('primary_color', $event->primary_color ?? '#1F4E79') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Primario</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="secondary_color"
                               value="{{ old('secondary_color', $event->secondary_color ?? '#C1121F') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Secundario</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="accent_color"
                               value="{{ old('accent_color', $event->accent_color ?? '#F59E0B') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Acento</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="bg_color"
                               value="{{ old('bg_color', $event->bg_color ?? '#F8FAFC') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Fondo</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="surface_color"
                               value="{{ old('surface_color', $event->surface_color ?? '#FFFFFF') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Superficie</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Primario: navbar y botones · Secundario: gradientes · Acento: CTAs destacados · Fondo: fondo de la página · Superficie: base del efecto glass</p>
            </div>

            {{-- Read-only info --}}
            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-500 space-y-1">
                <p><span class="font-semibold">Subdominio:</span> <span class="font-mono">{{ $event->subdomain }}.fedeme.ec</span> (no editable)</p>
                <p><span class="font-semibold">Slug:</span> <span class="font-mono">{{ $event->slug }}</span> (no editable)</p>
            </div>

        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.events.show', $event) }}"
               class="text-sm border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Cancelar</a>
            <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-2 rounded-lg transition">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

{{-- ─── Logo ───────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow p-6 max-w-2xl mb-6" x-data="logoUpload()">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Logo del evento</h2>

    {{-- Preview actual --}}
    <div class="flex items-center gap-6 mb-4">
        <div class="w-24 h-24 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
            @if($event->logo_path)
                <img id="logo-preview-current"
                     src="{{ asset('storage/' . $event->logo_path) }}"
                     alt="Logo actual"
                     class="w-full h-full object-contain">
            @else
                <span class="text-gray-300 text-xs text-center leading-tight px-2">Sin logo</span>
            @endif
        </div>
        <div class="text-sm text-gray-500">
            <p class="font-medium text-gray-700">Logo actual</p>
            @if($event->logo_path)
                <p class="font-mono text-xs text-gray-400 mt-1">{{ basename($event->logo_path) }}</p>
            @else
                <p class="text-gray-400 mt-1">No hay logo cargado.</p>
            @endif
        </div>
    </div>

    <form method="POST"
          action="{{ route('admin.events.logo.upload', $event) }}"
          enctype="multipart/form-data">
        @csrf

        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition cursor-pointer"
             @click="$refs.logoInput.click()">
            <template x-if="!preview">
                <div>
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 16l4-4a2 2 0 012.8 0l1.2 1.2M3 16v3a1 1 0 001 1h16a1 1 0 001-1v-3M3 16h18M16 7l-4-4-4 4M12 3v10"/>
                    </svg>
                    <p class="text-sm text-gray-500">Haz clic o arrastra una imagen aquí</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, SVG — máx. 2 MB</p>
                </div>
            </template>
            <template x-if="preview">
                <div>
                    <img :src="preview" class="mx-auto h-24 object-contain rounded-lg mb-2">
                    <p class="text-xs text-gray-500" x-text="fileName"></p>
                </div>
            </template>
        </div>

        <input type="file" name="logo" x-ref="logoInput" class="hidden"
               accept="image/jpg,image/jpeg,image/png,image/webp,image/svg+xml"
               @change="onFileChange($event)">

        @error('logo') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

        <div class="mt-4 flex justify-end">
            <button type="submit" :disabled="!preview"
                class="bg-blue-700 hover:bg-blue-800 disabled:opacity-40 text-white font-semibold text-sm px-6 py-2 rounded-lg transition">
                Subir logo
            </button>
        </div>
    </form>
</div>

{{-- ─── Carrusel ───────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow p-6 max-w-2xl mb-6" x-data="carouselUpload()">
    <h2 class="text-lg font-semibold text-gray-700 mb-1">Imágenes del carrusel</h2>
    <p class="text-sm text-gray-400 mb-4">Se muestran en la sección hero de la landing. Máx. 10 imágenes.</p>

    {{-- Imágenes existentes --}}
    @if($event->carousel_images && count($event->carousel_images) > 0)
        <div class="grid grid-cols-3 gap-3 mb-5">
            @foreach($event->carousel_images as $i => $img)
                <div class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-50">
                    <img src="{{ asset('storage/' . $img) }}"
                         class="w-full h-full object-cover"
                         alt="Imagen carrusel {{ $i + 1 }}">
                    <form method="POST"
                          action="{{ route('admin.events.carousel.delete', [$event, $i]) }}"
                          class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('¿Eliminar esta imagen del carrusel?')"
                                class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold">
                            Eliminar
                        </button>
                    </form>
                    <span class="absolute top-1 left-1 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded">{{ $i + 1 }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-400 mb-4 italic">No hay imágenes en el carrusel todavía.</p>
    @endif

    {{-- Nueva subida --}}
    <form method="POST"
          action="{{ route('admin.events.carousel.upload', $event) }}"
          enctype="multipart/form-data">
        @csrf

        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition cursor-pointer"
             @click="$refs.carouselInput.click()">
            <template x-if="previews.length === 0">
                <div>
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Selecciona una o varias imágenes</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — máx. 4 MB por imagen</p>
                </div>
            </template>
            <template x-if="previews.length > 0">
                <div>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <template x-for="(src, idx) in previews" :key="idx">
                            <img :src="src" class="h-16 w-24 object-cover rounded-lg border border-gray-200">
                        </template>
                    </div>
                    <p class="text-xs text-gray-500 mt-2" x-text="previews.length + ' imagen(es) seleccionada(s)'"></p>
                </div>
            </template>
        </div>

        <input type="file" name="carousel[]" multiple x-ref="carouselInput" class="hidden"
               accept="image/jpg,image/jpeg,image/png,image/webp"
               @change="onFilesChange($event)">

        @error('carousel') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        @error('carousel.*') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

        <div class="mt-4 flex justify-end">
            <button type="submit" :disabled="previews.length === 0"
                class="bg-blue-700 hover:bg-blue-800 disabled:opacity-40 text-white font-semibold text-sm px-6 py-2 rounded-lg transition">
                Agregar al carrusel
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function logoUpload() {
    return {
        preview: null,
        fileName: '',
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { this.preview = ev.target.result; };
            reader.readAsDataURL(file);
        }
    };
}

function carouselUpload() {
    return {
        previews: [],
        onFilesChange(e) {
            this.previews = [];
            const files = Array.from(e.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (ev) => { this.previews.push(ev.target.result); };
                reader.readAsDataURL(file);
            });
        }
    };
}
</script>
@endpush
@endsection
