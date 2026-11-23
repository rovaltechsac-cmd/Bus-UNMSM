@extends('layouts.auth')

@section('titulo', 'Ingresar · Bus San Marcos')

@section('contenido')
    <h2 class="text-xl font-extrabold text-ink">Ingresa con tu código</h2>
    <p class="mt-1 text-sm text-muted">Tu contraseña inicial es tu código.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-brand/40 bg-brand/10 p-3 text-sm text-ink" role="alert">
            <ul class="list-inside list-disc gap-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-5 grid gap-4">
        @csrf

        <div>
            <label for="codigo" class="mb-1 block text-sm font-bold text-ink">Código</label>
            <input id="codigo" name="codigo" type="text" inputmode="numeric" required autofocus autocomplete="username"
                   value="{{ old('codigo') }}"
                   class="w-full rounded-xl border border-line bg-canvas px-3 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
            <p class="mt-1 text-xs text-muted">Ejemplo: 20240001</p>
        </div>

        <div>
            <div class="mb-1 flex items-center justify-between gap-2">
                <label for="password" class="text-sm font-bold text-ink">Contraseña</label>
                <a href="{{ route('clave.olvide') }}" class="text-xs font-bold text-brand underline underline-offset-2 hover:text-brand-hover">
                    Olvidé mi contraseña
                </a>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded-xl border border-line bg-canvas px-3 py-3 text-base text-ink outline-none placeholder:text-muted focus:border-brand focus:ring-4 focus:ring-brand/20">
        </div>

        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="recordarme" value="1" class="size-4 rounded border-line accent-brand">
            Recordarme en este dispositivo
        </label>

        <button type="submit" class="w-full rounded-xl bg-brand px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-brand/30">
            Ingresar
        </button>
    </form>

    <details class="mt-5 rounded-xl border border-line bg-surface-alt p-3 text-xs text-muted">
        <summary class="cursor-pointer font-bold text-ink">Cuentas de demostración</summary>
        <ul class="mt-2 grid gap-1 font-mono">
            <li>Administrador: <strong>ADMIN001</strong> / ADMIN001</li>
            <li>Conductor: <strong>COND001</strong> / COND001</li>
            <li>Pasajero: <strong>20240001</strong> / 20240001</li>
            <li>Invidente: <strong>20240002</strong> / 20240002</li>
        </ul>
    </details>
@endsection
