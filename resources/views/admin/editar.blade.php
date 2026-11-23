@extends('layouts.app')

@section('titulo', 'Editar paradero · Bus San Marcos')

@section('contenido')
    <div class="mb-4">
        <p class="text-xs font-extrabold uppercase tracking-wider text-brand">Administración · Configuración</p>
        <h1 class="mt-1 text-2xl font-extrabold text-ink sm:text-3xl">Editar paradero</h1>
        <p class="mt-1 text-sm text-muted">Actualiza el nombre, la descripción o la ubicación del paradero.</p>
        <a href="{{ route('admin.index') }}"
           class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/20">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
            Volver a la configuración
        </a>
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
        'paradero' => $paradero,
        'paraderos' => $paraderos,
        'accion' => route('admin.paraderos.update', $paradero),
        'metodo' => 'PUT',
        'etiquetaBoton' => 'Guardar cambios',
        'textoUbicacion' => 'Toca el mapa o usa tu ubicación actual para mover el paradero.',
    ])
@endsection
