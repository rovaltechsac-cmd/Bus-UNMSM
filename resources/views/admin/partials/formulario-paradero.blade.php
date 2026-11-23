@php
    $paradero = $paradero ?? null;
    $esEdicion = $paradero !== null;
    $accion = $accion ?? route('admin.paraderos.store');
    $metodo = $metodo ?? 'POST';
    $etiquetaBoton = $etiquetaBoton ?? 'Guardar paradero';
    $textoUbicacion = $textoUbicacion ?? 'Toca el mapa o usa tu ubicación para elegir el punto.';
    $modal = $modal ?? false;
@endphp

@if ($modal)
    <div id="modal-agregar-paradero"
         class="fixed inset-0 z-[70] hidden"
         role="dialog"
         aria-modal="true"
         aria-labelledby="titulo-formulario"
         aria-hidden="true"
         data-tiene-errores="{{ $errors->any() ? '1' : '0' }}">
        <div class="absolute inset-0 bg-canvas/80 backdrop-blur-sm" data-cerrar-modal></div>
        <div class="relative z-10 flex h-full items-center justify-center p-4 sm:p-6">
            <div class="max-h-[90dvh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-line bg-surface p-4 shadow-2xl sm:p-5">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Administración</p>
                        <h2 id="titulo-formulario" class="mt-1 text-lg font-extrabold text-ink">Agregar paraderos</h2>
                    </div>
                    <button type="button"
                            data-cerrar-modal
                            aria-label="Cerrar"
                            class="grid size-9 shrink-0 place-items-center rounded-xl border border-line bg-surface-alt text-muted transition hover:border-brand hover:text-brand focus:outline-none focus:ring-4 focus:ring-brand/20">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
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
            </div>
        </div>
    </div>
@else
    <section aria-labelledby="titulo-formulario" class="mb-4 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <h2 id="titulo-formulario" class="text-lg font-extrabold text-ink">
            {{ $esEdicion ? 'Editar datos del paradero' : 'Datos del paradero' }}
        </h2>
@endif

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

@if ($modal)
            </div>
        </div>
    </div>
@else
    </section>
@endif

<section aria-labelledby="titulo-mapa-admin" class="mb-4 overflow-hidden rounded-2xl border border-line bg-surface shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-3">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <h2 id="titulo-mapa-admin" class="font-bold text-ink">Ubicación en el mapa</h2>
            <div class="flex items-center gap-3 text-xs font-semibold text-muted">
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full" style="background: #22C55E;"></span> Activo</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full" style="background: #EF4444;"></span> Inactivo</span>
            </div>
        </div>
        @if ($modal)
            <button id="btn-agregar-paradero" type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-3 py-2 text-sm font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Agregar paradero
            </button>
        @endif
    </div>
    <p class="px-4 pt-3 text-xs text-muted">El marcador es el punto que se guardará.</p>
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
