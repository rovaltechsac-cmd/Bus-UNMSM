@extends('layouts.app')

@section('titulo', 'Cambiar contraseña · Bus San Marcos')

@section('contenido')
    <div class="mx-auto max-w-lg">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-bold">Cambiar contraseña</h1>
            <p class="mt-1 text-sm text-slate-500">Cámbiala una sola vez. Después, este botón desaparece y solo podrás recuperarla desde «Olvidé mi contraseña» en el ingreso.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
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
                    <label for="current_password" class="mb-1 block text-sm font-semibold">Contraseña actual</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold">Nueva contraseña</label>
                    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
                </div>

                <button type="submit" class="rounded-lg bg-[#8c1d40] px-4 py-3 font-bold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
                    Guardar contraseña
                </button>
            </form>
        </div>
    </div>
@endsection
