@extends('layouts.admin')
@section('title', 'Nuevo Evento')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">&larr; Volver</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Nuevo Evento</h1>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del evento *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono @error('slug') border-red-400 @enderror"
                        placeholder="ej: juegos-2026">
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subdominio *</label>
                    <div class="flex items-center">
                        <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                            class="w-full border border-gray-300 rounded-l-lg px-3 py-2 text-sm font-mono @error('subdomain') border-red-400 @enderror"
                            placeholder="juegos2026">
                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-xs text-gray-500 whitespace-nowrap">.fedeme.app</span>
                    </div>
                    @error('subdomain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>

            {{-- Paleta de 5 colores --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Paleta de colores</label>
                <div class="grid grid-cols-5 gap-3">
                    <div class="text-center">
                        <input type="color" name="primary_color"
                               value="{{ old('primary_color', '#1a4f8a') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Primario</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="secondary_color"
                               value="{{ old('secondary_color', '#c0392b') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Secundario</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="accent_color"
                               value="{{ old('accent_color', '#F59E0B') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Acento</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="bg_color"
                               value="{{ old('bg_color', '#F8FAFC') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Fondo</span>
                    </div>
                    <div class="text-center">
                        <input type="color" name="surface_color"
                               value="{{ old('surface_color', '#FFFFFF') }}"
                               class="h-12 w-full border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <span class="text-xs text-gray-500 mt-1 block">Superficie</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Primario: navbar y botones &middot; Secundario: gradientes &middot; Acento: CTAs &middot; Fondo: fondo de página &middot; Superficie: glass</p>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-2 rounded-lg transition">
                Crear Evento
            </button>
        </div>
    </form>
</div>
@endsection
