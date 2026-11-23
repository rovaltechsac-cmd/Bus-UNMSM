@extends('layouts.app')

@section('titulo', 'Configuración de paraderos · Bus San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Administración</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">Configuración de paraderos</h1>
        <p class="mt-1 text-sm text-muted">Gestiona los puntos del campus desde el mapa. Usa «Agregar paradero» para registrar uno nuevo.</p>
    </div>

    @include('admin.partials.formulario-paradero', [
        'paraderos' => $paraderos,
        'modal' => true,
        'textoUbicacion' => 'Usa tu ubicación actual o escribe las coordenadas. También puedes cerrar el modal y tocar el mapa.',
    ])

    <section id="listado" aria-labelledby="titulo-listado" class="scroll-mt-24 rounded-2xl border border-line bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 id="titulo-listado" class="text-lg font-extrabold text-ink">Paraderos registrados</h2>
            <span class="grid size-8 place-items-center rounded-full border border-line bg-surface-alt text-sm font-extrabold text-muted">
                {{ $paraderos->count() }}
            </span>
        </div>

        @if ($paraderos->isNotEmpty())
            <div class="mt-3 overflow-x-auto rounded-xl border border-line">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-surface-alt text-xs font-bold uppercase tracking-wider text-muted">
                        <tr>
                            <th scope="col" class="px-4 py-3">Paradero</th>
                            <th scope="col" class="px-4 py-3">Coordenadas</th>
                            <th scope="col" class="px-4 py-3">Estado</th>
                            <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($paraderos as $paradero)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-ink">{{ $paradero->nombre }}</p>
                                    @if ($paradero->descripcion)
                                        <p class="text-xs text-muted">{{ $paradero->descripcion }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-muted">
                                    {{ number_format((float) $paradero->latitud, 7, '.', '') }},
                                    {{ number_format((float) $paradero->longitud, 7, '.', '') }}
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.paraderos.estado', $paradero) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="activo" value="0">
                                        <div class="flex items-center gap-2">
                                            <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                                                <input type="checkbox" name="activo" value="1" role="switch"
                                                       @checked($paradero->activo)
                                                       onchange="this.form.submit()"
                                                       aria-label="Activar o desactivar {{ $paradero->nombre }}"
                                                       class="peer sr-only">
                                                <span class="absolute inset-0 rounded-full border border-line bg-surface-alt transition peer-checked:border-brand peer-checked:bg-brand peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-metallic"></span>
                                                <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-metallic shadow transition peer-checked:translate-x-5 peer-checked:bg-white"></span>
                                            </label>
                                            <span class="text-xs font-bold {{ $paradero->activo ? 'text-brand' : 'text-muted' }}">
                                                {{ $paradero->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.paraderos.edit', $paradero) }}"
                                           class="rounded-lg border border-line px-3 py-2 text-xs font-bold text-ink transition hover:border-brand hover:text-brand focus:outline-none focus:ring-4 focus:ring-brand/20">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.paraderos.destroy', $paradero) }}"
                                              onsubmit="return confirm('¿Eliminar este paradero?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-line px-3 py-2 text-xs font-bold text-muted transition hover:border-brand hover:text-brand focus:outline-none focus:ring-4 focus:ring-brand/20">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-3 rounded-xl border border-line bg-surface-alt p-4 text-center text-sm font-bold text-muted">
                Aún no hay paraderos registrados.
            </p>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const boton = document.getElementById('btn-agregar-paradero');
            const modal = document.getElementById('modal-agregar-paradero');

            if (!boton || !modal) {
                return;
            }

            const alternar = (abierto) => {
                modal.classList.toggle('hidden', !abierto);
                modal.setAttribute('aria-hidden', abierto ? 'false' : 'true');
                document.body.classList.toggle('overflow-hidden', abierto);

                if (abierto) {
                    modal.querySelector('#nombre')?.focus();
                } else {
                    boton.focus();
                }
            };

            boton.addEventListener('click', () => alternar(true));
            modal.querySelectorAll('[data-cerrar-modal]').forEach((elemento) => {
                elemento.addEventListener('click', () => alternar(false));
            });
            document.addEventListener('keydown', (evento) => {
                if (evento.key === 'Escape' && !modal.classList.contains('hidden')) {
                    alternar(false);
                }
            });

            if (modal.dataset.tieneErrores === '1') {
                alternar(true);
            }
        })();
    </script>
@endpush
