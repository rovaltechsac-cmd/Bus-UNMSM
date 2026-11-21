@extends('layouts.app')

@section('titulo', 'Panel del conductor · San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-[#8c1d40]">Conductor</p>
        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Tu viaje, simple</h1>
        <p class="mt-1 text-sm text-slate-600">Mantén esta pantalla abierta. Tu celular envía la ubicación del bus.</p>
    </div>

    <section aria-labelledby="titulo-avisos" class="mb-4 rounded-2xl border-2 border-[#8c1d40] bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 id="titulo-avisos" class="text-lg font-extrabold">Avisos de pasajeros</h2>
                <p class="text-xs text-slate-500">Suena automáticamente cuando alguien espera.</p>
            </div>
            <span id="contador-alertas" class="grid size-8 shrink-0 place-items-center rounded-full bg-[#8c1d40] text-sm font-extrabold text-white">0</span>
        </div>

        <div id="lista-alertas" class="mt-3 grid gap-3" aria-live="polite" aria-relevant="additions removals"></div>
        <p id="sin-alertas" class="mt-3 rounded-xl bg-slate-50 p-4 text-center text-sm font-bold text-slate-500">Sin avisos por ahora</p>
    </section>

    <section aria-labelledby="titulo-gps" class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
            <span id="indicador-gps" class="size-3 shrink-0 rounded-full bg-amber-400" aria-hidden="true"></span>
            <div class="min-w-0">
                <p id="estado-gps" class="font-bold" aria-live="polite">Solicitando ubicación…</p>
                <p id="detalle-gps" class="truncate text-xs text-slate-500">—</p>
            </div>
        </div>

        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <button id="btn-reintentar-gps" type="button" class="rounded-xl bg-[#8c1d40] px-3 py-3 text-sm font-extrabold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
                Reintentar ubicación
            </button>
            <button id="btn-notificaciones" type="button" class="rounded-xl border border-slate-300 px-3 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/20">
                Activar avisos
            </button>
        </div>

        <details class="mt-3 rounded-xl border border-slate-200 p-3 text-xs text-slate-600">
            <summary class="cursor-pointer font-bold text-slate-800">¿No aparece la ubicación?</summary>
            <ol class="mt-2 list-decimal space-y-1 pl-5">
                <li>Permite la ubicación cuando el navegador lo pregunte.</li>
                <li>Activa también la ubicación del celular.</li>
                <li>Vuelve aquí y pulsa <strong>Reintentar ubicación</strong>.</li>
            </ol>
        </details>

        <p id="estado-notificaciones" class="mt-2 text-xs text-slate-500" aria-live="polite"></p>
    </section>

    <section aria-labelledby="titulo-audio-conductor" class="mb-4 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <label class="flex cursor-pointer items-center justify-between gap-3 p-4">
            <span class="min-w-0">
                <span id="titulo-audio-conductor" class="block font-bold">Avisos por voz</span>
                <span class="block text-xs text-slate-500">Anuncia el paradero del pasajero que espera.</span>
            </span>
            <input id="conductor-voz" type="checkbox" checked class="size-6 shrink-0 accent-[#8c1d40]">
        </label>
    </section>

    <section aria-labelledby="titulo-mapa-conductor" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
            <h2 id="titulo-mapa-conductor" class="font-bold">Mapa del recorrido</h2>
            <span class="flex items-center gap-2 text-xs text-slate-500">
                <span class="size-2 rounded-full bg-emerald-500"></span> Automático
            </span>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-slate-200 sm:h-[420px]" role="img" aria-label="Mapa con los paraderos y la ubicación del bus"></div>
        @include('partials.mapa-aviso')
    </section>

    <p class="mt-4 text-center text-xs text-slate-500">Instala esta página desde el menú del navegador para usarla como aplicación.</p>
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
                'ubicacion' => route('api.conductor.ubicacion.store'),
                'alertas' => route('api.conductor.alertas.index'),
                'atender' => route('api.conductor.alertas.atender', ['alerta' => '__ID__']),
                'finalizar' => route('api.conductor.alertas.finalizar', ['alerta' => '__ID__']),
            ],
        ]) }};
    </script>
    @include('partials.mapa-script')
@endpush
