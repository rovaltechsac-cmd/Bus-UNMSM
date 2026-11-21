<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paradero;
use App\Models\UbicacionBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UbicacionBusController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $paradero = null;

        if ($request->filled('paradero_id')) {
            $paradero = Paradero::query()
                ->where('activo', true)
                ->find($request->integer('paradero_id'));
        }

        $ubicacion = UbicacionBus::query()
            ->with('conductor:id,name')
            ->latest('registrado_at')
            ->first();

        $activa = $ubicacion?->esReciente() ?? false;

        $distancia = $ubicacion !== null && $paradero !== null
            ? $ubicacion->distanciaHasta($paradero)
            : null;

        $minutos = $ubicacion !== null && $paradero !== null && $activa
            ? $ubicacion->etaMinutos($paradero)
            : null;

        return response()->json([
            'activo' => $activa,
            'bus' => $ubicacion === null ? null : [
                'latitud' => (float) $ubicacion->latitud,
                'longitud' => (float) $ubicacion->longitud,
                'rumbo' => $ubicacion->rumbo === null ? null : (float) $ubicacion->rumbo,
                'conductor' => $ubicacion->conductor?->name,
                'actualizado_at' => $ubicacion->registrado_at?->toIso8601String(),
            ],
            'eta' => $minutos === null || $distancia === null ? null : [
                'minutos' => $minutos,
                'distancia_m' => $distancia,
                'llega_pronto' => $distancia <= (float) config('campus.radio_paradero_m'),
                'texto' => $this->textoETA($minutos, $distancia),
            ],
            'servidor_at' => now()->toIso8601String(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $limites = config('campus.limites');

        $datos = $request->validate([
            'latitud' => ['required', 'numeric', "between:{$limites['latitud_min']},{$limites['latitud_max']}"],
            'longitud' => ['required', 'numeric', "between:{$limites['longitud_min']},{$limites['longitud_max']}"],
            'rumbo' => ['nullable', 'numeric', 'between:0,360'],
            'exactitud_m' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $ubicacion = UbicacionBus::query()->updateOrCreate(
            ['conductor_id' => $request->user()->id],
            [
                'latitud' => $datos['latitud'],
                'longitud' => $datos['longitud'],
                'rumbo' => $datos['rumbo'] ?? null,
                'exactitud_m' => $datos['exactitud_m'] ?? null,
                'registrado_at' => now(),
            ],
        );

        return response()->json([
            'mensaje' => 'Ubicación registrada.',
            'latitud' => (float) $ubicacion->latitud,
            'longitud' => (float) $ubicacion->longitud,
            'registrado_at' => $ubicacion->registrado_at?->toIso8601String(),
        ], 201);
    }

    private function textoETA(int $minutos, float $distancia): string
    {
        if ($distancia <= (float) config('campus.radio_paradero_m')) {
            return 'El bus está en el paradero';
        }

        if ($minutos <= 1) {
            return 'El bus llega en menos de 1 minuto';
        }

        return "El bus llega en {$minutos} minutos";
    }
}
