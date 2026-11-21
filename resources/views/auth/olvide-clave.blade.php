@extends('layouts.auth')

@section('titulo', 'Recuperar contraseña · Bus San Marcos')

@section('contenido')
    <h2 class="text-xl font-extrabold">Olvidé mi contraseña</h2>
    <p class="mt-1 text-sm text-slate-500">
        Ingresa tu código. Te enviaremos un enlace a tu correo san marquino predefinido.
    </p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc gap-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('enlace_recuperacion'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900" role="status">
            <p class="font-bold">Listo. En producción este enlace llega a tu correo institucional.</p>
            <p class="mt-1">Para esta demostración, ábrelo aquí:</p>
            <a href="{{ session('enlace_recuperacion') }}" class="mt-2 block break-all font-bold text-[#8c1d40] underline underline-offset-2">
                Restablecer mi contraseña
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('clave.olvide.store') }}" class="mt-5 grid gap-4">
        @csrf
        <div>
            <label for="codigo" class="mb-1 block text-sm font-bold">Código de estudiante o conductor</label>
            <input id="codigo" name="codigo" type="text" inputmode="numeric" required autofocus
                   value="{{ old('codigo') }}"
                   class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base outline-none focus:border-[#8c1d40] focus:ring-4 focus:ring-[#8c1d40]/15">
        </div>

        <button type="submit" class="w-full rounded-xl bg-[#8c1d40] px-4 py-3.5 text-base font-extrabold text-white transition hover:bg-[#70162f] focus:outline-none focus:ring-4 focus:ring-[#8c1d40]/30">
            Enviar enlace
        </button>
    </form>

    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-bold text-[#8c1d40] underline underline-offset-4">
        Volver al ingreso
    </a>
@endsection
