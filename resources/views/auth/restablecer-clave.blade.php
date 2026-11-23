@extends('layouts.auth')

@section('titulo', 'Nueva contraseña · Bus San Marcos')

@section('contenido')
    <h2 class="text-xl font-extrabold text-ink">Nueva contraseña</h2>
    <p class="mt-1 text-sm text-muted">Elige una contraseña de al menos 8 caracteres.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-brand/40 bg-brand/10 p-3 text-sm text-ink" role="alert">
            <ul class="list-inside list-disc gap-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('clave.restablecer.store') }}" class="mt-5 grid gap-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $correo }}">

        <div>
            <label for="password" class="mb-1 block text-sm font-bold text-ink">Nueva contraseña</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                   class="w-full rounded-xl border border-line bg-canvas px-3 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-bold text-ink">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                   class="w-full rounded-xl border border-line bg-canvas px-3 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
        </div>

        <button type="submit" class="w-full rounded-xl bg-brand px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
            Guardar contraseña
        </button>
    </form>

    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-bold text-brand underline underline-offset-4 hover:text-brand-hover">
        Volver al ingreso
    </a>
@endsection
