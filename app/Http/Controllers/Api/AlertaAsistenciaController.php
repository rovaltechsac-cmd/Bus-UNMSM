<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertaAsistencia;
use App\Models\Paradero;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlertaAsistenciaController extends Controller
{
    public function index(): JsonResponse
    {
        $alertas = AlertaAsistencia::query()
            ->with([
                'pasajero:id,name',
                'paradero:id,nombre,latitud,longitud',
            ])
            ->activas()
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'alertas' => $alertas->map(fn (AlertaAsistencia $alerta): array => $this->respuestaAlerta($alerta)),
        ]);
    }

    public function mias(Request $request): JsonResponse
    {
        $alertas = $request->user()
            ->alertasAsistencia()
            ->with('paradero:id,nombre,latitud,longitud')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'alertas' => $alertas->map(fn (AlertaAsistencia $alerta): array => $this->respuestaAlerta($alerta)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('crear-alerta-invidente');

        $datos = $request->validate([
            'paradero_id' => ['required', 'integer', 'exists:paraderos,id'],
            'nota' => ['nullable', 'string', 'max:255'],
        ], [
            'paradero_id.required' => 'Selecciona el paradero donde estás.',
            'paradero_id.exists' => 'El paradero seleccionado no existe.',
        ]);

        $paradero = Paradero::query()
            ->whereKey($datos['paradero_id'])
            ->where('activo', true)
            ->firstOrFail();

        $alertaActiva = $request->user()
            ->alertasAsistencia()
            ->activas()
            ->where('paradero_id', $paradero->id)
            ->first();

        if ($alertaActiva !== null) {
            $alertaActiva->load('paradero:id,nombre,latitud,longitud');

            return response()->json([
                'mensaje' => 'Ya tienes una solicitud activa en este paradero.',
                'alerta' => $this->respuestaAlerta($alertaActiva),
            ]);
        }

        $alerta = $request->user()->alertasAsistencia()->create([
            'paradero_id' => $paradero->id,
            'tipo' => AlertaAsistencia::TIPO_INVIDENTE,
            'estado' => AlertaAsistencia::ESTADO_PENDIENTE,
            'nota' => $datos['nota'] ?? null,
        ]);

        $alerta->load('paradero:id,nombre,latitud,longitud');

        return response()->json([
            'mensaje' => 'Aviso enviado al conductor.',
            'alerta' => $this->respuestaAlerta($alerta),
        ], 201);
    }

    public function atender(Request $request, AlertaAsistencia $alerta): JsonResponse
    {
        Gate::authorize('atender', $alerta);

        /** @var User $conductor */
        $conductor = $request->user();
        $alerta->atender($conductor);
        $alerta->load('paradero:id,nombre,latitud,longitud');

        return response()->json([
            'mensaje' => 'Aviso marcado en atención.',
            'alerta' => $this->respuestaAlerta($alerta),
        ]);
    }

    public function finalizar(AlertaAsistencia $alerta): JsonResponse
    {
        Gate::authorize('finalizar', $alerta);

        $alerta->marcarAtendida();
        $alerta->load('paradero:id,nombre,latitud,longitud');

        return response()->json([
            'mensaje' => 'Atención finalizada.',
            'alerta' => $this->respuestaAlerta($alerta),
        ]);
    }

    public function cancelar(AlertaAsistencia $alerta): JsonResponse
    {
        Gate::authorize('cancelar', $alerta);

        $alerta->cancelar();
        $alerta->load('paradero:id,nombre,latitud,longitud');

        return response()->json([
            'mensaje' => 'Solicitud cancelada.',
            'alerta' => $this->respuestaAlerta($alerta),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function respuestaAlerta(AlertaAsistencia $alerta): array
    {
        return [
            'id' => $alerta->id,
            'tipo' => $alerta->tipo,
            'estado' => $alerta->estado,
            'nota' => $alerta->nota,
            'paradero' => [
                'id' => $alerta->paradero->id,
                'nombre' => $alerta->paradero->nombre,
                'latitud' => (float) $alerta->paradero->latitud,
                'longitud' => (float) $alerta->paradero->longitud,
            ],
            'pasajero' => $alerta->relationLoaded('pasajero') ? [
                'id' => $alerta->pasajero->id,
                'name' => $alerta->pasajero->name,
            ] : null,
            'creado_at' => $alerta->created_at?->toIso8601String(),
            'atendido_at' => $alerta->atendido_at?->toIso8601String(),
        ];
    }
}
