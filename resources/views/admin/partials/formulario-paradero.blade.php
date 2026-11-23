@php
    $paradero = $paradero ?? null;
    $esEdicion = $paradero !== null;
    $accion = $accion ?? route('admin.paraderos.store');
    $metodo = $metodo ?? 'POST';
    $etiquetaBoton = $etiquetaBoton ?? 'Guardar paradero';
    $textoUbicacion = $textoUbicacion ?? 'También puedes tocar el mapa para elegir el punto.';
@endphp

<section aria-labelledby="titulo-formulario" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
    <h2 id="titulo-formulario" class="text-lg font-extrabold text-ink">
        {{ $esEdicion ? 'Editar datos del paradero' : 'Datos del paradero' }}
    </h2>

    <form method="POST" action="{{ $accion }}" class="mt-4 grid gap-4">
        @csrf

        @if ($metodo !== 'POST')
            @method($metodo)
        @endif

        <div>
            <label for="nombre" class="mb-1 block text-sm font-bold text-ink">Nombre del paradero</label>
            <input id="nombre" name="nombre" type="text" required maxlength="120" autocomplete="off"
                   value="{{ old('nombre', $paradero?->nombre) }}" placeholder="Ej. Puerta Principal"
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
                   value="{{ old('descripcion', $paradero?->descripcion) }}" placeholder="Ej. Ingreso por Av. Amezaga"
                   class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
            @error('descripcion')
                <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="latitud" class="mb-1 block text-sm font-bold text-ink">Latitud</label>
                <input id="latitud" name="latitud" type="number" step="0.0000001" inputmode="decimal" required
                       value="{{ old('latitud', $paradero?->latitud) }}" placeholder="-12.0582000"
                       class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                @error('latitud')
                    <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="longitud" class="mb-1 block text-sm font-bold text-ink">Longitud</label>
                <input id="longitud" name="longitud" type="number" step="0.0000001" inputmode="decimal" required
                       value="{{ old('longitud', $paradero?->longitud) }}" placeholder="-77.0840000"
                       class="w-full rounded-xl border border-line bg-canvas px-4 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
                @error('longitud')
                    <p class="mt-1 text-xs font-semibold text-brand">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($esEdicion)
            <label class="flex items-center gap-3 rounded-xl border border-line bg-surface-alt px-4 py-3 text-sm font-bold text-ink">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $paradero->activo))
                       class="size-5 rounded border-line bg-canvas text-brand focus:ring-4 focus:ring-brand/20">
                Paradero activo
            </label>
        @endif

        <button id="btn-ubicacion-admin" type="button"
                class="w-full rounded-xl border border-line bg-surface-alt px-4 py-3 text-sm font-bold text-ink transition hover:border-metallic focus:outline-none focus:ring-4 focus:ring-brand/20">
            Usar mi ubicación actual
        </button>
        <p id="estado-ubicacion" class="text-xs text-muted" aria-live="polite">
            {{ $textoUbicacion }}
        </p>

        <button type="submit"
                class="w-full rounded-xl bg-brand px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
            {{ $etiquetaBoton }}
        </button>
    </form>
</section>

<section aria-labelledby="titulo-mapa-admin" class="mb-4 overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
    <div class="border-b border-line px-4 py-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 id="titulo-mapa-admin" class="font-bold text-ink">Ubicación en el mapa</h2>
            <div class="flex items-center gap-3 text-xs font-semibold text-muted">
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full" style="background: #22C55E;"></span> Activo</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full" style="background: #EF4444;"></span> Inactivo</span>
            </div>
        </div>
        <p class="mt-1 text-xs text-muted">El marcador es el punto que se guardará.</p>
    </div>
    <div id="mapa" class="h-[280px] w-full bg-surface-alt sm:h-[420px]" role="img" aria-label="Mapa para elegir la ubicación del paradero"></div>
</section>

@push('scripts')
    <script>
        window.configuracionAdmin = {{ Illuminate\Support\Js::from([
            'paraderos' => $paraderos->map(fn ($paraderoItem) => [
                'id' => $paraderoItem->id,
                'nombre' => $paraderoItem->nombre,
                'latitud' => (float) $paraderoItem->latitud,
                'longitud' => (float) $paraderoItem->longitud,
                'activo' => (bool) $paraderoItem->activo,
            ]),
            'marcadorTitulo' => $esEdicion ? 'Ubicación del paradero' : 'Ubicación del nuevo paradero',
        ]) }};
    </script>
    @include('partials.mapa-script')
@endpush
