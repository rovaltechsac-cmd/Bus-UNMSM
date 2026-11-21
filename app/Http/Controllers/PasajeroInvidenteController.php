<?php

namespace App\Http\Controllers;

use App\Models\Paradero;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PasajeroInvidenteController extends Controller
{
    public function index(): View
    {
        Gate::authorize('ver-panel-invidente');

        $paraderos = Paradero::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'descripcion', 'latitud', 'longitud']);

        return view('pasajero.invidente', [
            'pagina' => 'pasajero',
            'paraderos' => $paraderos,
            'googleMapsKey' => config('services.google_maps.api_key'),
            'radioParadero' => config('campus.radio_paradero_m'),
            'esInvidenteInicial' => true,
        ]);
    }
}
