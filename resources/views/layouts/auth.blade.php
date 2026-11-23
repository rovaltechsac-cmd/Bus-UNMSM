<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0B0C0E">
    <title>@yield('titulo', 'Bus San Marcos')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas px-4 py-6 text-ink antialiased sm:py-10">
    <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-md flex-col justify-center">
        <div class="mb-5 text-center">
            <img src="{{ asset('logo-unmsm.png') }}" alt="Universidad Nacional Mayor de San Marcos"
                 class="mx-auto h-16 w-auto rounded-2xl bg-white p-2 shadow-lg sm:h-20">
            <h1 class="mt-3 text-2xl font-bold">Burrito San Marcos</h1>
            <p class="text-sm text-muted">Ubicación del bus y avisos de accesibilidad</p>
            <p class="mt-1 text-[11px] font-bold uppercase tracking-[0.16em] text-metallic">ROVAL TECH · UNMSM</p>
        </div>

        @if (session('estado'))
            <div class="mb-3 rounded-xl border border-brand/40 bg-brand/10 p-3 text-center text-sm font-semibold text-ink" role="status">
                {{ session('estado') }}
            </div>
        @endif

        <div class="rounded-2xl border border-line bg-surface p-5 shadow-2xl sm:p-6">
            @yield('contenido')
        </div>

        <p class="mt-6 text-center text-xs text-muted">Universidad Nacional Mayor de San Marcos</p>
    </div>
</body>
</html>
