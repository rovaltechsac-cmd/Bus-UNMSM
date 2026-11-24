@extends('layouts.app')

@section('titulo', 'Panel del conductor · San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Conductor</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">Tu viaje, simple</h1>
        <p class="mt-1 text-sm text-muted">Mantén esta pantalla abierta. Tu celular envía la ubicación del bus.</p>
    </div>

    <section aria-labelledby="titulo-avisos" class="mb-4 rounded-2xl border border-brand/40 bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 id="titulo-avisos" class="text-lg font-extrabold text-ink">Avisos de pasajeros</h2>
                <p class="text-xs text-muted">Suena automáticamente cuando alguien espera.</p>
            </div>
            <span id="contador-alertas" class="grid size-8 shrink-0 place-items-center rounded-full bg-brand text-sm font-extrabold text-white">0</span>
        </div>

        <div id="lista-alertas" class="mt-3 grid gap-3" aria-live="polite" aria-relevant="additions removals"></div>
        <p id="sin-alertas" class="mt-3 rounded-xl border border-line bg-surface-alt p-4 text-center text-sm font-bold text-muted">Sin avisos por ahora</p>
    </section>

    <section aria-labelledby="titulo-gps" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-center gap-3 rounded-xl border border-line bg-surface-alt p-3">
            <span id="indicador-gps" class="size-3 shrink-0 rounded-full bg-metallic" aria-hidden="true"></span>
            <div class="min-w-0">
                <p id="estado-gps" class="font-bold text-ink" aria-live="polite">Solicitando ubicación…</p>
                <p id="detalle-gps" class="truncate text-xs text-muted">—</p>
            </div>
        </div>

        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <button id="btn-reintentar-gps" type="button" class="rounded-xl bg-brand px-3 py-3 text-sm font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                Reintentar ubicación
            </button>
            <button id="btn-notificaciones" type="button" class="rounded-xl border border-line bg-surface-alt px-3 py-3 text-sm font-bold text-ink transition hover:border-metallic focus:outline-none focus:ring-4 focus:ring-brand/20">
                Activar avisos
            </button>
        </div>

        <details class="mt-3 rounded-xl border border-line p-3 text-xs text-muted">
            <summary class="cursor-pointer font-bold text-ink">¿No aparece la ubicación?</summary>
            <ol class="mt-2 list-decimal space-y-1 pl-5">
                <li>Permite la ubicación cuando el navegador lo pregunte.</li>
                <li>Activa también la ubicación del celular.</li>
                <li>Vuelve aquí y pulsa <strong>Reintentar ubicación</strong>.</li>
            </ol>
        </details>

        <p id="estado-notificaciones" class="mt-2 text-xs text-muted" aria-live="polite"></p>
    </section>

    <section aria-labelledby="titulo-audio-conductor" class="mb-4 rounded-2xl border border-line bg-surface shadow-sm">
        <label class="flex cursor-pointer items-center justify-between gap-3 p-4">
            <span class="min-w-0">
                <span id="titulo-audio-conductor" class="block font-bold text-ink">Avisos por voz</span>
                <span class="block text-xs text-muted">Anuncia el paradero del pasajero que espera.</span>
            </span>
            <input id="conductor-voz" type="checkbox" checked class="size-6 shrink-0 accent-brand">
        </label>
    </section>

    <section aria-labelledby="titulo-mapa-conductor" class="overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-3">
            <h2 id="titulo-mapa-conductor" class="font-bold text-ink">Mapa del recorrido</h2>
            <span class="flex items-center gap-2 text-xs text-muted">
                <span class="size-2 rounded-full bg-blue-500"></span> Tu bus en vivo
            </span>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-surface-alt sm:h-[420px]" role="img" aria-label="Mapa con los paraderos y la ubicación del bus"></div>
    </section>

    <p class="mt-4 text-center text-xs text-muted">Instala esta página desde el menú del navegador para usarla como aplicación.</p>
@endsection

@push('scripts')
    <script>
        window.configuracionConductor = {{ Illuminate\Support\Js::from([
            'paraderos' => $paraderos->map(fn ($paradero) => [
                'id' => $paradero->id,
                'nombre' => $paradero->nombre,
                'latitud' => (float) $paradero->latitud,
                'longitud' => (float) $paradero->longitud,
            ]),
            'endpoints' => [
                'bus' => route('api.bus.show'),
                'ubicacion' => route('api.conductor.ubicacion.store'),
                'alertas' => route('api.conductor.alertas.index'),
                'atender' => route('api.conductor.alertas.atender', ['alerta' => '__ID__']),
                'finalizar' => route('api.conductor.alertas.finalizar', ['alerta' => '__ID__']),
            ],
        ]) }};
    </script>
    @include('partials.mapa-script')
@endpush
