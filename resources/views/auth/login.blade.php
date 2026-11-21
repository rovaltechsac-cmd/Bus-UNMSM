@extends('layouts.auth')

@section('titulo', 'Ingresar · Bus San Marcos')

@section('contenido')
    <h2 class="text-xl font-extrabold">Ingresa con tu código</h2>
    <p class="mt-1 text-sm text-slate-500">Tu contraseña inicial es tu código.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
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
            <label for="codigo" class="mb-1 block text-sm font-bold">Código</label>
            <input id="codigo" name="codigo" type="text" inputmode="numeric" required autofocus autocomplete="username"
                   value="{{ old('codigo') }}"
                   class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
            <p class="mt-1 text-xs text-slate-500">Ejemplo: 20240001</p>
        </div>

        <div>
            <div class="mb-1 flex items-center justify-between gap-2">
                <label for="password" class="text-sm font-bold">Contraseña</label>
                <a href="{{ route('clave.olvide') }}" class="text-xs font-bold text-[#8c1d40] underline underline-offset-2">
                    Olvidé mi contraseña
                </a>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="recordarme" value="1" class="size-4 rounded border-slate-300 accent-[#8c1d40]">
            Recordarme en este dispositivo
        </label>

        <button type="submit" class="w-full rounded-xl bg-[#8c1d40] px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
            Ingresar
        </button>
    </form>

    <details class="mt-5 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
        <summary class="cursor-pointer font-bold text-slate-800">Cuentas de demostración</summary>
        <ul class="mt-2 grid gap-1 font-mono">
            <li>Administrador: <strong>ADMIN001</strong> / ADMIN001</li>
            <li>Conductor: <strong>COND001</strong> / COND001</li>
            <li>Pasajero: <strong>20240001</strong> / 20240001</li>
            <li>Invidente: <strong>20240002</strong> / 20240002</li>
        </ul>
    </details>
@endsection
