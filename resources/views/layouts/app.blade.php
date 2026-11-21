<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8c1d40">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Bus San Marcos')</title>

    @if (($pagina ?? '') === 'conductor')
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased" data-pagina="{{ $pagina ?? 'general' }}">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:shadow-lg">
        Saltar al contenido principal
    </a>

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-3xl items-center justify-between gap-2 px-3 py-2.5 sm:px-6 sm:py-3">
            <div class="flex min-w-0 items-center gap-2.5">
                <img src="{{ asset('logo-unmsm.png') }}" alt="Universidad Nacional Mayor de San Marcos"
                     class="size-10 shrink-0 rounded-xl bg-white object-contain p-0.5 shadow-sm ring-1 ring-slate-200 sm:size-12">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold sm:text-base">Burrito San Marcos</p>
                    <p class="truncate text-[11px] text-slate-500 sm:text-xs">
                        @if (auth()->user()?->esAdministrador())
                            Administrador
                        @elseif (auth()->user()?->esConductor())
                            Conductor
                        @elseif (auth()->user()?->esInvidente())
                            Pasajero invidente
                        @else
                            Pasajero
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-1.5">
                @if (auth()->user()?->puedeCambiarClave())
                    <a href="{{ route('clave.edit') }}"
                       class="rounded-lg px-2 py-2 text-xs font-bold text-[#8c1d40] transition hover:bg-[#8c1d40]/10 focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/20 sm:px-3">
                        <span class="sm:hidden">Clave</span><span class="hidden sm:inline">Cambiar clave</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/20">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main id="contenido" class="mx-auto max-w-3xl px-3 py-4 pb-10 sm:px-6 sm:py-6">
        @if (session('estado'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800" role="status">
                {{ session('estado') }}
            </div>
        @endif

        @yield('contenido')
    </main>

    @stack('scripts')
</body>
</html>
