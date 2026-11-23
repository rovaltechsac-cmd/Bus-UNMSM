@extends('layouts.app')

@section('titulo', 'Bus interno · San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-[#8c1d40]">Pasajero</p>
        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">¿Dónde está el burrito?</h1>
        <p class="mt-1 text-sm text-slate-600">Mira la pantalla: te avisamos cuando falte poco.</p>
    </div>

    <section aria-labelledby="titulo-estado" class="mb-4 rounded-2xl border-2 border-[#8c1d40] bg-white p-5 text-center shadow-sm">
        <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Tiempo de llegada</p>
        <p id="estado-bus" class="mt-2 text-3xl font-extrabold leading-tight text-[#8c1d40] sm:text-4xl" aria-live="polite" aria-atomic="true">
            Consultando…
        </p>
        <p id="detalle-bus" class="mt-2 text-sm text-slate-500" aria-live="polite">—</p>
    </section>

    <section aria-labelledby="titulo-paradero" class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <label for="paradero" id="titulo-paradero" class="block text-sm font-bold">Mi paradero</label>
        <select id="paradero" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base font-semibold outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
            @foreach ($paraderos as $paraderoItem)
                <option value="{{ $paraderoItem->id }}">{{ $paraderoItem->nombre }}</option>
            @endforeach
        </select>
        <button id="btn-ubicacion" type="button" class="mt-3 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/20">
            Usar mi ubicación
        </button>
    </section>

    <section aria-labelledby="titulo-audio" class="mb-4 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <label class="flex cursor-pointer items-center justify-between gap-3 p-4">
            <span class="min-w-0">
                <span id="titulo-audio" class="block font-bold">Avisos de audio</span>
                <span class="block text-xs text-slate-500">Te avisa cuando falte 1 minuto y cuando el bus llegue.</span>
            </span>
            <input id="audio-activo" type="checkbox" class="size-6 shrink-0 accent-[#8c1d40]">
        </label>
    </section>

    <section aria-labelledby="titulo-mapa" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
            <h2 id="titulo-mapa" class="font-bold">Mapa del campus</h2>
            <span class="flex items-center gap-2 text-xs text-slate-500">
                <span class="size-2 rounded-full bg-emerald-500"></span> Automático
            </span>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-slate-200 sm:h-[420px]" role="img" aria-label="Mapa con los paraderos y la ubicación del bus"></div>
    </section>
@endsection

@push('scripts')
    @include('pasajero.partials.configuracion')
@endpush
