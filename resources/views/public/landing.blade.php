@extends('layouts.public')

@section('content')

{{-- Modules rendered dynamically based on event configuration --}}
@php
    $sectionIds = [
        'hero'      => 'inicio',
        'info'      => 'informacion',
        'contact'   => 'contacto',
        'pdf'       => 'documentos',
        'map'       => 'mapa',
        'emergency' => 'emergencias',
    ];
@endphp

@foreach($modules as $module)
    @if($module->is_active)
        <div id="{{ $sectionIds[$module->type] ?? $module->type }}">
            @includeIf('public.modules.' . $module->type, ['module' => $module, 'event' => $event])
        </div>
    @endif
@endforeach

@if($modules->isEmpty())
    <div class="max-w-3xl mx-auto px-4 py-24 text-center text-gray-400">
        <p class="text-lg">Este evento aún no tiene contenido publicado.</p>
    </div>
@endif

@endsection
