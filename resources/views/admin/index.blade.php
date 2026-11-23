@extends('layouts.app')

@section('titulo', 'Panel de administración · Bus San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Administración</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">Agregar paraderos</h1>
        <p class="mt-1 text-sm text-muted">Toca el mapa o usa tu ubicación para marcar el punto exacto. Luego guarda.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-brand/40 bg-brand/10 p-3 text-sm text-ink" role="alert">
            <ul class="list-inside list-disc gap-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section aria-labelledby="titulo-formulario" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <h2 id="titulo-formulario" class="text-lg font-extrabold text-ink">Datos del paradero</h2>

        <form method="POST" action="{{ route('admin.paraderos.store') }}" class="mt-4 grid gap-4">
            @csrf

            <div>
                <label for="nombre" class="mb-1 block text-sm font-bold text-ink">Nombre del paradero</label>
                <input id="nombre" name="nombre" type="text" required maxlength="120" autocomplete="off"
                       value="{{ old('nombre') }}" placeholder="Ej. Puerta Principal"
                       class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                @error('nombre')
                    <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="descripcion" class="mb-1 block text-sm font-bold text-ink">
                    Descripción <span class="font-normal text-muted">(opcional)</span>
                </label>
                <input id="descripcion" name="descripcion" type="text" maxlength="255"
                       value="{{ old('descripcion') }}" placeholder="Ej. Ingreso por Av. Amezaga"
                       class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                @error('descripcion')
                    <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitud" class="mb-1 block text-sm font-bold text-ink">Latitud</label>
                    <input id="latitud" name="latitud" type="number" step="0.0000001" inputmode="decimal" required
                           value="{{ old('latitud') }}" placeholder="-12.0582000"
                           class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                    @error('latitud')
                        <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud" class="mb-1 block text-sm font-bold text-ink">Longitud</label>
                    <input id="longitud" name="longitud" type="number" step="0.0000001" inputmode="decimal" required
                           value="{{ old('longitud') }}" placeholder="-77.0840000"
                           class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                    @error('longitud')
                        <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button id="btn-ubicacion-admin" type="button"
                    class="w-full rounded-xl border border-line bg-surface-alt px-4 py-3 text-sm font-bold text-ink transition hover:border-metallic focus:outline-none focus:ring-4 focus:ring-brand/20">
                Usar mi ubicación actual
            </button>
            <p id="estado-ubicacion" class="text-xs text-muted" aria-live="polite">
                También puedes tocar el mapa para elegir el punto.
            </p>

            <button type="submit"
                    class="w-full rounded-xl bg-brand px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                Guardar paradero
            </button>
        </form>
    </section>

    <section aria-labelledby="titulo-mapa-admin" class="mb-4 overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
        <div class="border-b border-line px-4 py-3">
            <h2 id="titulo-mapa-admin" class="font-bold text-ink">Ubicación en el mapa</h2>
            <p class="text-xs text-muted">Los círculos son los paraderos actuales; el verde es el nuevo paradero.</p>
        </div>
        <div id="mapa" class="h-[280px] w-full bg-surface-alt sm:h-[420px]" role="img" aria-label="Mapa para elegir la ubicación del paradero"></div>
    </section>

    <section aria-labelledby="titulo-listado" class="rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 id="titulo-listado" class="text-lg font-extrabold text-ink">Paraderos registrados</h2>
            <span class="grid size-8 place-items-center rounded-full border border-line bg-surface-alt text-sm font-extrabold text-muted">
                {{ $paraderos->count() }}
            </span>
        </div>

        @if ($paraderos->isNotEmpty())
            <ul class="mt-3 grid gap-3">
                @foreach ($paraderos as $paradero)
                    <li class="rounded-xl border border-line bg-surface-alt p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-bold text-ink">{{ $paradero->nombre }}</p>
                                @if ($paradero->descripcion)
                                    <p class="text-xs text-muted">{{ $paradero->descripcion }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 rounded-full border px-2 py-1 text-[11px] font-bold {{ $paradero->activo ? 'border-brand/30 bg-brand/10 text-brand' : 'border-line bg-surface text-muted' }}">
                                {{ $paradero->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="mt-2 font-mono text-xs text-muted">
                            {{ number_format((float) $paradero->latitud, 7, '.', '') }},
                            {{ number_format((float) $paradero->longitud, 7, '.', '') }}
                        </p>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 rounded-xl border border-line bg-surface-alt p-4 text-center text-sm font-bold text-muted">
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
