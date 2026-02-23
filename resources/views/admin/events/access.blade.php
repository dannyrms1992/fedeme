@extends('layouts.admin')
@section('title', 'Configurar Acceso — ' . $event->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.show', $event) }}" class="text-gray-500 hover:text-gray-800 text-sm">&larr; Volver</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-1">Código de acceso — {{ $event->name }}</h1>
    <p class="text-gray-500 text-sm mt-1">
        Configura si el evento requiere un código para ser visualizado en
        <span class="font-mono">{{ $event->subdomain }}.fedeme.ec</span>
    </p>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.events.access.update', $event) }}">
        @csrf
        @method('PATCH')

        {{-- Enable/disable toggle --}}
        <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100">
            <div>
                <p class="font-medium text-gray-800">Activar protección por código</p>
                <p class="text-sm text-gray-500">Si está activo, los visitantes deben ingresar el código para acceder.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="access_enabled" value="0">
                <input type="checkbox" name="access_enabled" value="1" class="sr-only peer"
                    {{ $event->access_enabled ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        {{-- New code --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo código de acceso</label>
            <input type="password" name="access_code" autocomplete="new-password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Dejar vacío para mantener el actual">
            <p class="text-xs text-gray-400 mt-1">Mínimo 4 caracteres. El código nunca se almacena en texto plano.</p>
            @error('access_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Expiration --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Expiración del acceso (opcional)</label>
            <input type="datetime-local" name="access_expires_at"
                value="{{ $event->access_expires_at?->format('Y-m-d\TH:i') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <p class="text-xs text-gray-400 mt-1">Después de esta fecha el acceso queda abierto.</p>
            @error('access_expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Status info --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm text-gray-600 space-y-1">
            <p><span class="font-semibold">Estado actual:</span>
                @if($event->requiresAccessCode())
                    <span class="text-orange-600 font-medium">Protegido con código</span>
                @else
                    <span class="text-green-600 font-medium">Acceso abierto</span>
                @endif
            </p>
            @if($event->access_expires_at)
                <p><span class="font-semibold">Expira:</span> {{ $event->access_expires_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm px-6 py-2 rounded-lg transition">
                Guardar configuración
            </button>
        </div>
    </form>
</div>
@endsection
