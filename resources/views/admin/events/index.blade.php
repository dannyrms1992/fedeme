@extends('layouts.admin')
@section('title', 'Eventos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Eventos</h1>
    <a href="{{ route('admin.events.create') }}"
       class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nuevo evento
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
            <tr>
                <th class="px-6 py-3">Nombre</th>
                <th class="px-6 py-3">Subdominio</th>
                <th class="px-6 py-3">Estado</th>
                <th class="px-6 py-3">Acceso</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $event->name }}</td>
                    <td class="px-6 py-4 font-mono text-gray-500">{{ $event->subdomain }}.fedeme.app</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                            @if($event->status->value === 'active') bg-green-100 text-green-700
                            @elseif($event->status->value === 'draft') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-500 @endif">
                            {{ $event->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($event->access_enabled)
                            <span class="text-orange-600 font-semibold text-xs">Con código</span>
                        @else
                            <span class="text-gray-400 text-xs">Abierto</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-2 items-center">
                        <a href="{{ route('admin.events.show', $event) }}" class="text-blue-600 hover:underline text-xs">Editar</a>
                        <a href="{{ route('admin.events.access.edit', $event) }}" class="text-orange-600 hover:underline text-xs">Acceso</a>
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el evento «{{ addslashes($event->name) }}»? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-xs">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        No hay eventos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $events->links() }}
</div>
@endsection
