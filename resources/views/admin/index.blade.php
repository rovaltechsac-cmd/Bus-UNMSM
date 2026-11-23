@extends('layouts.app')

@section('titulo', 'Configuración de paraderos · Bus San Marcos')

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

    @include('admin.partials.formulario-paradero', [
        'paraderos' => $paraderos,
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
                                    <span class="shrink-0 rounded-full border px-2 py-1 text-[11px] font-bold {{ $paradero->activo ? 'border-brand/30 bg-brand/10 text-brand' : 'border-line bg-surface text-muted' }}">
                                        {{ $paradero->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
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
