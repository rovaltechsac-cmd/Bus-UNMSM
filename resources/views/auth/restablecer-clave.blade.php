@extends('layouts.auth')

@section('titulo', 'Nueva contraseña · Bus San Marcos')

@section('contenido')
    <h2 class="text-xl font-extrabold">Nueva contraseña</h2>
    <p class="mt-1 text-sm text-slate-500">Elige una contraseña de al menos 8 caracteres.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
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
            <label for="password" class="mb-1 block text-sm font-bold">Nueva contraseña</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-bold">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
        </div>

        <button type="submit" class="w-full rounded-xl bg-[#8c1d40] px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
            Guardar contraseña
        </button>
    </form>

    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-bold text-[#8c1d40] underline underline-offset-4">
        Volver al ingreso
    </a>
@endsection
