@extends('layouts.app')

@section('titulo', 'Panel accesible · Bus San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Panel accesible</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">Hola, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-muted">Tu paradero se detecta solo y el conductor recibe el aviso automáticamente.</p>
    </div>

    <section aria-labelledby="titulo-estado-invidente" class="mb-4 rounded-2xl border border-brand/40 bg-surface p-5 text-center shadow-sm">
        <p class="text-xs font-extrabold uppercase tracking-wider text-muted">Tiempo de llegada</p>
        <p id="estado-bus" class="mt-2 text-3xl font-extrabold leading-tight text-brand sm:text-4xl" aria-live="assertive" aria-atomic="true">
            Detectando tu paradero…
        </p>
        <p id="detalle-bus" class="mt-2 text-sm text-muted" aria-live="polite">—</p>
    </section>

    <section aria-labelledby="titulo-aviso-invidente" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-center gap-3">
            <span class="size-3 shrink-0 animate-pulse rounded-full bg-brand" aria-hidden="true"></span>
            <div class="min-w-0">
                <h2 id="titulo-aviso-invidente" class="font-extrabold text-ink">Aviso automático al conductor</h2>
                <p id="estado-aviso" class="text-sm text-muted" aria-live="assertive" aria-atomic="true">
                    Detectando tu paradero…
                </p>
            </div>
        </div>

        <div class="mt-3 rounded-xl border border-line bg-surface-alt p-3">
            <p class="text-xs font-semibold text-muted">Mi paradero</p>
            <p id="paradero-nombre" class="text-lg font-extrabold text-ink">Detectando…</p>
        </div>

        <input type="hidden" id="paradero" value="">
    </section>

    <section aria-labelledby="titulo-audio-invidente" class="mb-4 rounded-2xl border border-line bg-surface shadow-sm">
        <label class="flex cursor-pointer items-center justify-between gap-3 p-4">
            <span class="min-w-0">
                <span id="titulo-audio-invidente" class="block font-bold text-ink">Avisos de audio</span>
                <span class="block text-xs text-muted">Te dirá cuánto falta y cuándo el bus llegue a tu paradero.</span>
            </span>
            <input id="audio-activo" type="checkbox" checked class="size-6 shrink-0 accent-brand">
        </label>
    </section>

    <section aria-labelledby="titulo-mapa-invidente" class="overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
        <div class="border-b border-line px-4 py-3">
            <h2 id="titulo-mapa-invidente" class="font-bold text-ink">Mapa del campus</h2>
        </div>
        <div id="mapa" class="h-[240px] w-full bg-surface-alt sm:h-[360px]" role="img" aria-label="Mapa con los paraderos y la ubicación del bus"></div>
    </section>
@endsection

@push('scripts')
    @include('pasajero.partials.configuracion')
@endpush
