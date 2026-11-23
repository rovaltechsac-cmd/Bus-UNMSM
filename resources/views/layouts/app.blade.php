<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0B0C0E">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <title>@yield('titulo', 'Bus San Marcos')</title>

    @if (($pagina ?? '') === 'conductor')
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-canvas text-ink antialiased" data-pagina="{{ $pagina ?? 'general' }}">
    @php
        $seccionLayout = match ($pagina ?? 'general') {
            'dashboard' => 'Resumen',
            'admin' => 'Configuración',
            'conductor' => 'Conductor',
            'pasajero' => 'Pasajero',
            default => 'Panel',
        };

        $rolLayout = match (true) {
            auth()->user()?->esAdministrador() => 'Administrador',
            auth()->user()?->esConductor() => 'Conductor',
            auth()->user()?->esInvidente() => 'Pasajero invidente',
            default => 'Pasajero',
        };
    @endphp

    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:border focus:border-line focus:bg-surface focus:px-4 focus:py-2 focus:font-semibold focus:text-ink focus:shadow-lg">
        Saltar al contenido principal
    </a>

    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-canvas/80 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    @include('partials.sidebar')

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-line bg-surface/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <button id="btn-menu" type="button"
                        class="grid size-10 shrink-0 place-items-center rounded-xl border border-line bg-surface-alt text-muted transition hover:border-metallic hover:text-ink focus:outline-none focus:ring-4 focus:ring-brand/20 lg:hidden"
                        aria-controls="sidebar-principal" aria-expanded="false" aria-label="Abrir menú">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </button>

                <div class="min-w-0 flex-1">
                    <nav aria-label="Ruta de navegación" class="hidden items-center gap-2 text-xs text-muted sm:flex">
                        <span class="font-semibold">Panel</span>
                        <span aria-hidden="true">/</span>
                        <span class="font-bold text-ink">{{ $seccionLayout }}</span>
                    </nav>
                    <p class="truncate text-sm font-extrabold text-ink sm:hidden">Burrito San Marcos</p>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-xs font-bold text-brand md:inline">
                        {{ $rolLayout }}
                    </span>
                    <span class="hidden text-sm font-semibold text-muted sm:inline">{{ auth()->user()?->name }}</span>
                    <span class="grid size-9 place-items-center rounded-full border border-line bg-brand text-xs font-extrabold text-white sm:hidden">
                        {{ strtoupper(mb_substr(auth()->user()?->name ?? '?', 0, 1)) }}
                    </span>
                </div>
            </div>
        </header>

        <main id="contenido" class="mx-auto max-w-7xl px-4 py-5 pb-10 sm:px-6 sm:py-6 lg:px-8">
            @if (session('estado'))
                <div class="mb-5 rounded-xl border border-brand/40 bg-brand/10 p-3 text-sm font-semibold text-ink" role="status">
                    {{ session('estado') }}
                </div>
            @endif

            @yield('contenido')
        </main>
    </div>

    @stack('scripts')

    <script>
        (() => {
            const boton = document.getElementById('btn-menu');
            const sidebar = document.getElementById('sidebar-principal');
            const overlay = document.getElementById('sidebar-overlay');

            if (!boton || !sidebar || !overlay) {
                return;
            }

            const alternarMenu = (abierto) => {
                sidebar.classList.toggle('-translate-x-full', !abierto);
                sidebar.classList.toggle('translate-x-0', abierto);
                overlay.classList.toggle('hidden', !abierto);
                overlay.setAttribute('aria-hidden', abierto ? 'false' : 'true');
                boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
                boton.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
                document.body.classList.toggle('overflow-hidden', abierto);
            };

            boton.addEventListener('click', () => {
                alternarMenu(boton.getAttribute('aria-expanded') !== 'true');
            });

            overlay.addEventListener('click', () => alternarMenu(false));

            sidebar.querySelectorAll('a, button').forEach((enlace) => {
                enlace.addEventListener('click', () => alternarMenu(false));
            });

            document.addEventListener('keydown', (evento) => {
                if (evento.key === 'Escape') {
                    alternarMenu(false);
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    alternarMenu(false);
                }
            });
        })();
    </script>
</body>
</html>
