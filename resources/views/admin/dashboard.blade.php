@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-gray-500 mb-1">Eventos activos</p>
        <p class="text-3xl font-bold text-blue-700">
            {{ \App\Domain\Event\Models\Event::where('status', \App\Domain\Event\ValueObjects\EventStatus::Active)->count() }}
        </p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-gray-500 mb-1">Total eventos</p>
        <p class="text-3xl font-bold text-gray-800">
            {{ \App\Domain\Event\Models\Event::count() }}
        </p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm text-gray-500 mb-1">Con código de acceso</p>
        <p class="text-3xl font-bold text-orange-600">
            {{ \App\Domain\Event\Models\Event::where('access_enabled', true)->count() }}
        </p>
    </div>
</div>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Eventos recientes</h2>
    <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline text-sm">Ver todos</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @php $recent = \App\Domain\Event\Models\Event::latest()->take(5)->get(); @endphp
    @if($recent->isEmpty())
        <p class="text-gray-400 text-sm px-6 py-8 text-center">No hay eventos aún. <a href="{{ route('admin.events.create') }}" class="text-blue-600 hover:underline">Crear el primero</a>.</p>
    @else
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @foreach($recent as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium">{{ $event->name }}</td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $event->subdomain }}.fedeme.app</td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($event->status->value === 'active') bg-green-100 text-green-700
                                @elseif($event->status->value === 'draft') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ $event->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-600 hover:underline text-xs">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
