@extends('layouts.admin')
@section('title', $event->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.events.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">&larr; Volver</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $event->name }}</h1>
        <p class="text-sm text-gray-500 font-mono">{{ $event->subdomain }}.fedeme.ec</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.events.edit', $event) }}"
           class="text-sm border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Editar</a>
        <a href="{{ route('admin.events.modules.edit', $event) }}"
           class="text-sm bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg transition">Configurar módulos</a>
        <a href="{{ route('admin.events.access.edit', $event) }}"
           class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition">Código de acceso</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-400 mb-1">Estado</p>
        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
            @if($event->status->value === 'active') bg-green-100 text-green-700
            @elseif($event->status->value === 'draft') bg-yellow-100 text-yellow-700
            @else bg-gray-100 text-gray-500 @endif">
            {{ $event->status->label() }}
        </span>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-400 mb-1">Acceso</p>
        <p class="font-semibold {{ $event->access_enabled ? 'text-orange-600' : 'text-green-600' }}">
            {{ $event->access_enabled ? 'Protegido con código' : 'Abierto' }}
        </p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-400 mb-1">Módulos activos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $event->modules->where('is_active', true)->count() }}</p>
    </div>
</div>

{{-- Brand preview --}}
<div class="bg-white rounded-xl shadow p-5 mb-6">
    <h3 class="font-semibold text-gray-700 mb-3">Vista previa de marca</h3>
    <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-full border-4" style="background:{{ $event->primary_color }}; border-color: {{ $event->secondary_color }}"></div>
        <div>
            <p class="text-xs text-gray-400">Primario: <span class="font-mono">{{ $event->primary_color }}</span></p>
            <p class="text-xs text-gray-400">Secundario: <span class="font-mono">{{ $event->secondary_color }}</span></p>
        </div>
        @if($event->logo_path)
            <img src="{{ asset('storage/' . $event->logo_path) }}" alt="Logo" class="h-12 object-contain border rounded p-1">
        @endif
    </div>
</div>

{{-- Modules --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-700">Módulos de la landing</h3>
        <a href="{{ route('admin.events.modules.edit', $event) }}"
           class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
            Configurar contenido &rarr;
        </a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Tipo</th>
                <th class="px-6 py-3 text-left">Orden</th>
                <th class="px-6 py-3 text-left">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($event->modules as $module)
                <tr>
                    <td class="px-6 py-3 font-mono capitalize">{{ $module->type }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $module->order }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs {{ $module->is_active ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $module->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
