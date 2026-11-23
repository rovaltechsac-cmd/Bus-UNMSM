@extends('layouts.app')

@section('titulo', 'Bus interno · San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Pasajero</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">¿Dónde está el burrito?</h1>
        <p class="mt-1 text-sm text-muted">Mira la pantalla: te avisamos cuando falte poco.</p>
    </div>

    <section aria-labelledby="titulo-estado" class="mb-4 rounded-2xl border border-brand/40 bg-surface p-5 text-center shadow-sm">
        <p class="text-xs font-extrabold uppercase tracking-wider text-muted">Tiempo de llegada</p>
        <p id="estado-bus" class="mt-2 text-3xl font-extrabold leading-tight text-brand sm:text-4xl" aria-live="polite" aria-atomic="true">
            Consultando…
        </p>
        <p id="detalle-bus" class="mt-2 text-sm text-muted" aria-live="polite">—</p>
    </section>

    <section aria-labelledby="titulo-paradero" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <label for="paradero" id="titulo-paradero" class="block text-sm font-bold text-ink">Mi paradero</label>
        <select id="paradero" class="mt-1 w-full rounded-xl border border-line bg-surface-alt px-4 py-3 text-base font-semibold text-ink outline-none focus:border-brand focus:ring-4 focus:ring-brand/20">
            @foreach ($paraderos as $paraderoItem)
                <option value="{{ $paraderoItem->id }}">{{ $paraderoItem->nombre }}</option>
            @endforeach
        </select>
        <button id="btn-ubicacion" type="button" class="mt-3 w-full rounded-xl border border-line bg-surface-alt px-4 py-3 text-sm font-bold text-ink transition hover:border-metallic focus:outline-none focus:ring-4 focus:ring-brand/20">
            Usar mi ubicación
        </button>
    </section>

    <section aria-labelledby="titulo-audio" class="mb-4 rounded-2xl border border-line bg-surface shadow-sm">
        <label class="flex cursor-pointer items-center justify-between gap-3 p-4">
            <span class="min-w-0">
                <span id="titulo-audio" class="block font-bold text-ink">Avisos de audio</span>
                <span class="block text-xs text-muted">Te avisa cuando falte 1 minuto y cuando el bus llegue.</span>
            </span>
            <input id="audio-activo" type="checkbox" class="size-6 shrink-0 accent-brand">
        </label>
    </section>

    <section aria-labelledby="titulo-mapa" class="overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-3">
            <h2 id="titulo-mapa" class="font-bold text-ink">Mapa del campus</h2>
            <span class="flex items-center gap-2 text-xs text-muted">
                <span class="size-2 rounded-full bg-brand"></span> Automático
            </span>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-surface-alt sm:h-[420px]" role="img" aria-label="Mapa con los paraderos y la ubicación del bus"></div>
    </section>
@endsection

@push('scripts')
    @include('pasajero.partials.configuracion')
@endpush
