@extends('layouts.app')

@section('titulo', 'Dashboard · Bus San Marcos')

@section('contenido')
    @php
        $esAdmin = auth()->user()->esAdministrador();
        $esConductor = auth()->user()->esConductor();
        $esInvidente = auth()->user()->esInvidente();
    @endphp

    <section class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand">Panel de control</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Resumen general</h1>
            <p class="mt-2 max-w-2xl text-sm text-muted">
                Hola, {{ auth()->user()->name }}.
                @if ($esAdmin)
                    Administra paraderos, rutas y usuarios desde un solo lugar.
                @elseif ($esConductor)
                    Revisa tu viaje y comparte tu ubicación con los pasajeros.
                @elseif ($esInvidente)
                    Accede a la información de los buses en formato accesible.
                @else
                    Consulta dónde está tu bus y cuándo llega al paradero.
                @endif
            </p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-line bg-surface px-3 py-1.5 text-xs font-bold text-muted">
            <span class="size-2 rounded-full bg-brand" aria-hidden="true"></span>
            {{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
        </span>
    </section>

    <section aria-labelledby="metricas-dashboard" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <h2 id="metricas-dashboard" class="sr-only">Indicadores principales</h2>
        @foreach ($estadisticas as $estadistica)
            <x-stat-card
                :etiqueta="$estadistica['etiqueta']"
                :valor="$estadistica['valor']"
                :detalle="$estadistica['detalle']"
                :icono="$estadistica['icono']"
                :destacado="$estadistica['tono'] === 'maroon'"
            />
        @endforeach
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="rounded-2xl border border-line bg-surface p-5 lg:col-span-2" aria-labelledby="actividad-reciente">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 id="actividad-reciente" class="text-lg font-extrabold text-ink">Actividad reciente</h2>
                <span class="text-xs font-semibold text-muted">Últimos avisos</span>
            </div>

            @if ($alertasRecientes->isEmpty())
                <div class="rounded-xl border border-dashed border-line bg-canvas p-6 text-center">
                    <p class="text-sm font-semibold text-ink">Todavía no hay actividad registrada.</p>
                    <p class="mt-1 text-xs text-muted">Cuando el sistema registre novedades, aparecerán aquí.</p>
                </div>
            @else
                <ul class="divide-y divide-line">
                    @foreach ($alertasRecientes as $alerta)
                        <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                            <span class="mt-0.5 grid size-8 shrink-0 place-items-center rounded-lg border border-line bg-surface-alt text-metallic">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-ink">
                                    {{ $alerta->pasajero?->name ?? 'Pasajero' }} · {{ $alerta->paradero?->name ?? 'Paradero' }}
                                </p>
                                <p class="truncate text-xs text-muted">
                                    Aviso {{ str_replace('_', ' ', $alerta->estado) }}
                                </p>
                            </div>
                            <time class="shrink-0 text-xs text-metallic" datetime="{{ $alerta->created_at?->toIso8601String() }}">
                                {{ $alerta->created_at?->diffForHumans() }}
                            </time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-2xl border border-line bg-surface p-5" aria-labelledby="accesos-rapidos">
            <h2 id="accesos-rapidos" class="text-lg font-extrabold text-ink">Accesos rápidos</h2>
            <p class="mt-1 text-xs text-muted">Lo más usado en tu panel.</p>

            <div class="mt-4 space-y-2">
                @foreach ($accesos as $acceso)
                    <a href="{{ $acceso['ruta'] }}" class="flex items-center justify-between gap-3 rounded-xl border border-line bg-surface-alt px-3 py-3 text-sm font-bold text-ink transition hover:border-brand hover:text-brand focus:outline-none focus:ring-4 focus:ring-brand/20">
                        <span class="min-w-0">
                            <span class="block truncate">{{ $acceso['titulo'] }}</span>
                            <span class="mt-0.5 block truncate text-xs font-normal text-muted">{{ $acceso['descripcion'] }}</span>
                        </span>
                        <span aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection
