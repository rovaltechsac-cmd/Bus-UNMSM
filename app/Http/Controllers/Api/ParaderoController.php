<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paradero;
use Illuminate\Http\JsonResponse;

class ParaderoController extends Controller
{
    public function index(): JsonResponse
    {
        $paraderos = Paradero::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'descripcion', 'latitud', 'longitud']);

        return response()->json(['paraderos' => $paraderos]);
    }
}
