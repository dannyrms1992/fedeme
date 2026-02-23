<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') — FEDEME</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-100 antialiased">

    {{-- Top nav --}}
    <nav class="bg-slate-900 text-white px-6 py-3 flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-3">
            <span class="text-lg font-bold tracking-wide text-white">FEDEME</span>
            <span class="text-slate-400 text-sm">Panel Administrativo</span>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('admin.events.index') }}" class="text-slate-300 hover:text-white transition">Eventos</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-white transition">Salir</button>
            </form>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto mt-4 px-4">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-5xl mx-auto mt-4 px-4">
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Page content --}}
    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
