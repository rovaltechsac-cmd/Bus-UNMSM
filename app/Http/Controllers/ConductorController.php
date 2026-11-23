<?php

namespace App\Http\Controllers;

use App\Models\Paradero;
use Illuminate\View\View;

class ConductorController extends Controller
{
    public function index(): View
    {
        $paraderos = Paradero::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'latitud', 'longitud']);

        return view('conductor.index', [
            'pagina' => 'conductor',
            'paraderos' => $paraderos,
            'radioParadero' => config('campus.radio_paradero_m'),
        ]);
    }
}
