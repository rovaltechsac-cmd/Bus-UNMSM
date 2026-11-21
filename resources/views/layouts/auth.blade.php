<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8c1d40">
    <title>@yield('titulo', 'Bus San Marcos')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#8c1d40] via-[#6f1731] to-slate-900 px-4 py-6 sm:py-10">
    <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-md flex-col justify-center">
        <div class="mb-5 text-center text-white">
            <img src="{{ asset('logo-unmsm.png') }}" alt="Universidad Nacional Mayor de San Marcos"
                 class="mx-auto h-16 w-auto rounded-2xl bg-white p-2 shadow-lg sm:h-20">
            <h1 class="mt-3 text-2xl font-bold">Burrito San Marcos</h1>
            <p class="text-sm text-white/80">Ubicación del bus y avisos de accesibilidad</p>
        </div>

        @if (session('estado'))
            <div class="mb-3 rounded-xl border border-white/30 bg-white/15 p-3 text-center text-sm font-semibold text-white" role="status">
                {{ session('estado') }}
            </div>
        @endif

        <div class="rounded-2xl bg-white p-5 shadow-2xl sm:p-6">
            @yield('contenido')
        </div>

        <p class="mt-6 text-center text-xs text-white/70">Universidad Nacional Mayor de San Marcos</p>
    </div>
</body>
</html>
