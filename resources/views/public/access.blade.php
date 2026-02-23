@extends('layouts.public')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">

        {{-- Branding --}}
        <div class="text-center mb-8">
            @if($event->logo_path)
                <img src="{{ asset('storage/' . $event->logo_path) }}" alt="{{ $event->name }}"
                     class="h-16 mx-auto mb-4 object-contain">
            @endif
            <h1 class="text-2xl font-bold text-gray-800">{{ $event->name }}</h1>
            <p class="text-gray-500 text-sm mt-1">Acceso restringido</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <form method="POST" action="{{ route('event.access.store') }}" autocomplete="off">
                @csrf

                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Código de acceso
                    </label>
                    <input
                        id="code"
                        name="code"
                        type="password"
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition text-center text-lg tracking-widest @error('code') border-red-400 @enderror"
                        placeholder="••••••••"
                        maxlength="128"
                    >
                    @error('code')
                        {{-- Neutral message — does not reveal if code exists or is correct --}}
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-[var(--color-primary)] hover:opacity-90 text-white font-semibold py-3 px-6 rounded-lg transition"
                >
                    Continuar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Si no dispone del código de acceso, comuníquese con el organizador del evento.
        </p>

    </div>
</div>
@endsection
