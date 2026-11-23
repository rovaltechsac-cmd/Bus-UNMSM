<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B0C0E">
    <title>{{ config('app.name', 'Bus San Marcos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-canvas px-4 py-10 text-ink antialiased">
    <main class="w-full max-w-lg rounded-2xl border border-line bg-surface p-8 text-center shadow-2xl">
        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-brand">ROVAL TECH · UNMSM</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight">Bus San Marcos</h1>
        <p class="mt-3 text-sm text-muted">Ubicación del bus y avisos de accesibilidad para la comunidad universitaria.</p>

        @if (Route::has('login'))
            <nav class="mt-7 flex flex-wrap items-center justify-center gap-3" aria-label="Accesos">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                        Ir al panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                        Ingresar
                    </a>
                @endauth
            </nav>
        @endif
    </main>
</body>
</html>
