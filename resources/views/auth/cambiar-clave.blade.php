@extends('layouts.app')

@section('titulo', 'Cambiar contraseña · Bus San Marcos')

@section('contenido')
    <div class="mx-auto max-w-lg">
        <div class="rounded-2xl border border-line bg-surface p-6 shadow-sm">
            <h1 class="text-xl font-bold text-ink">Cambiar contraseña</h1>
            <p class="mt-1 text-sm text-muted">Cámbiala una sola vez. Después, este botón desaparece y solo podrás recuperarla desde «Olvidé mi contraseña» en el ingreso.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-brand/40 bg-brand/10 p-3 text-sm text-ink" role="alert">
                    <ul class="list-inside list-disc gap-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('clave.update') }}" class="mt-6 grid gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-1 block text-sm font-semibold text-ink">Contraseña actual</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-line bg-canvas px-3 py-2.5 text-ink outline-none focus:border-brand focus:ring-4 focus:ring-brand/20">
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold text-ink">Nueva contraseña</label>
                    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                           class="w-full rounded-lg border border-line bg-canvas px-3 py-2.5 text-ink outline-none focus:border-brand focus:ring-4 focus:ring-brand/20">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold text-ink">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                           class="w-full rounded-lg border border-line bg-canvas px-3 py-2.5 text-ink outline-none focus:border-brand focus:ring-4 focus:ring-brand/20">
                </div>

                <button type="submit" class="rounded-lg bg-brand px-4 py-3 font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
                    Guardar contraseña
                </button>
            </form>
        </div>
    </div>
@endsection
