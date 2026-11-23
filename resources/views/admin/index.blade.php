@extends('layouts.app')

@section('titulo', 'Panel de administración · Bus San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-[#8c1d40]">Administración</p>
        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Agregar paraderos</h1>
        <p class="mt-1 text-sm text-slate-600">Toca el mapa o usa tu ubicación para marcar el punto exacto. Luego guarda.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc gap-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section aria-labelledby="titulo-formulario" class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h2 id="titulo-formulario" class="text-lg font-extrabold">Datos del paradero</h2>

        <form method="POST" action="{{ route('admin.paraderos.store') }}" class="mt-4 grid gap-4">
            @csrf

            <div>
                <label for="nombre" class="mb-1 block text-sm font-bold">Nombre del paradero</label>
                <input id="nombre" name="nombre" type="text" required maxlength="120" autocomplete="off"
                       value="{{ old('nombre') }}" placeholder="Ej. Puerta Principal"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                @error('nombre')
                    <p class="mt-1 text-xs font-semibold text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="descripcion" class="mb-1 block text-sm font-bold">
                    Descripción <span class="font-normal text-slate-500">(opcional)</span>
                </label>
                <input id="descripcion" name="descripcion" type="text" maxlength="255"
                       value="{{ old('descripcion') }}" placeholder="Ej. Ingreso por Av. Amezaga"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                @error('descripcion')
                    <p class="mt-1 text-xs font-semibold text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitud" class="mb-1 block text-sm font-bold">Latitud</label>
                    <input id="latitud" name="latitud" type="number" step="0.0000001" inputmode="decimal" required
                           value="{{ old('latitud') }}" placeholder="-12.0582000"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                    @error('latitud')
                        <p class="mt-1 text-xs font-semibold text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud" class="mb-1 block text-sm font-bold">Longitud</label>
                    <input id="longitud" name="longitud" type="number" step="0.0000001" inputmode="decimal" required
                           value="{{ old('longitud') }}" placeholder="-77.0840000"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                    @error('longitud')
                        <p class="mt-1 text-xs font-semibold text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button id="btn-ubicacion-admin" type="button"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/20">
                Usar mi ubicación actual
            </button>
            <p id="estado-ubicacion" class="text-xs text-slate-500" aria-live="polite">
                También puedes tocar el mapa para elegir el punto.
            </p>

            <button type="submit"
                    class="w-full rounded-xl bg-[#8c1d40] px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
                Guardar paradero
            </button>
        </form>
    </section>

    <section aria-labelledby="titulo-mapa-admin" class="mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 id="titulo-mapa-admin" class="font-bold">Ubicación en el mapa</h2>
            <p class="text-xs text-slate-500">Los círculos son los paraderos actuales; el verde es el nuevo paradero.</p>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-slate-200 sm:h-[420px]" role="img" aria-label="Mapa para elegir la ubicación del paradero"></div>
    </section>

    <section aria-labelledby="titulo-listado" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 id="titulo-listado" class="text-lg font-extrabold">Paraderos registrados</h2>
            <span class="grid size-8 place-items-center rounded-full bg-slate-100 text-sm font-extrabold text-slate-600">
                {{ $paraderos->count() }}
            </span>
        </div>

        @if ($paraderos->isNotEmpty())
            <ul class="mt-3 grid gap-3">
                @foreach ($paraderos as $paradero)
                    <li class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-bold">{{ $paradero->nombre }}</p>
                                @if ($paradero->descripcion)
                                    <p class="text-xs text-slate-500">{{ $paradero->descripcion }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-bold {{ $paradero->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                {{ $paradero->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="mt-2 font-mono text-xs text-slate-600">
                            {{ number_format((float) $paradero->latitud, 7, '.', '') }},
                            {{ number_format((float) $paradero->longitud, 7, '.', '') }}
                        </p>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 rounded-xl bg-slate-50 p-4 text-center text-sm font-bold text-slate-500">
                Aún no hay paraderos registrados.
            </p>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        window.configuracionAdmin = {{ Illuminate\Support\Js::from([
            'paraderos' => $paraderos->map(fn ($paradero) => [
                'id' => $paradero->id,
                'nombre' => $paradero->nombre,
                'latitud' => (float) $paradero->latitud,
                'longitud' => (float) $paradero->longitud,
            ]),
        ]) }};
    </script>
    @include('partials.mapa-script')
@endpush
