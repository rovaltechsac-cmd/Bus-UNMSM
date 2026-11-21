<?php

namespace App\Http\Controllers;

use App\Models\Paradero;
use Illuminate\View\View;

class PasajeroController extends Controller
{
    public function index(): View
    {
        $paraderos = Paradero::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'descripcion', 'latitud', 'longitud']);

        return view('pasajero.index', [
            'pagina' => 'pasajero',
            'paraderos' => $paraderos,
            'googleMapsKey' => config('services.google_maps.api_key'),
            'radioParadero' => config('campus.radio_paradero_m'),
            'esInvidenteInicial' => false,
        ]);
    }
}
