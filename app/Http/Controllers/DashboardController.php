<?php

namespace App\Http\Controllers;

use App\Models\AlertaAsistencia;
use App\Models\Paradero;
use App\Models\UbicacionBus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $usuario */
        $usuario = Auth::user();

        $paraderosActivos = Paradero::query()->where('activo', true)->count();
        $totalParaderos = Paradero::query()->count();
        $ultimaUbicacion = UbicacionBus::query()->latest('registrado_at')->first();
        $busEnLinea = $ultimaUbicacion?->esReciente() ?? false;
        $detalleUbicacion = $ultimaUbicacion?->registrado_at?->diffForHumans() ?? 'Sin reportes aún';

        $alertasQuery = AlertaAsistencia::query()->activas();

        if (! $usuario->esAdministrador() && ! $usuario->esConductor()) {
            $alertasQuery->where('pasajero_id', $usuario->id);
        }

        $alertasActivas = (clone $alertasQuery)->count();
        $alertasRecientes = (clone $alertasQuery)
            ->with(['pasajero:id,name,codigo', 'paradero:id,nombre'])
            ->latest()
            ->limit(5)
            ->get(['id', 'pasajero_id', 'paradero_id', 'estado', 'created_at']);

        $etaMinutos = null;

        if ($busEnLinea && $ultimaUbicacion) {
            $etaMinutos = Paradero::query()
                ->where('activo', true)
                ->get(['id', 'latitud', 'longitud'])
                ->map(fn (Paradero $paradero): int => $ultimaUbicacion->etaMinutos($paradero))
                ->min();
        }

        return view('dashboard.index', [
            'pagina' => 'dashboard',
            'usuario' => $usuario,
            'estadisticas' => $this->estadisticas(
                $usuario,
                $paraderosActivos,
                $totalParaderos,
                $alertasActivas,
                $busEnLinea,
                $detalleUbicacion,
                $etaMinutos,
            ),
            'accesos' => $this->accesos($usuario),
            'alertasRecientes' => $alertasRecientes,
        ]);
    }

    /**
     * @return array<int, array{etiqueta: string, valor: string, detalle: string, tono: string, icono: string}>
     */
    private function estadisticas(
        User $usuario,
        int $paraderosActivos,
        int $totalParaderos,
        int $alertasActivas,
        bool $busEnLinea,
        string $detalleUbicacion,
        ?int $etaMinutos,
    ): array {
        $estadoBus = [
            'etiqueta' => 'Bus en línea',
            'valor' => $busEnLinea ? 'Sí' : 'No',
            'detalle' => $detalleUbicacion,
            'tono' => $busEnLinea ? 'emerald' : 'slate',
            'icono' => 'bus',
        ];

        if ($usuario->esAdministrador()) {
            return [
                [
                    'etiqueta' => 'Paraderos activos',
                    'valor' => (string) $paraderosActivos,
                    'detalle' => "de {$totalParaderos} registrados",
                    'tono' => 'emerald',
                    'icono' => 'paraderos',
                ],
                [
                    'etiqueta' => 'Avisos activos',
                    'valor' => (string) $alertasActivas,
                    'detalle' => 'requieren atención',
                    'tono' => 'amber',
                    'icono' => 'alertas',
                ],
                $estadoBus,
                [
                    'etiqueta' => 'Usuarios activos',
                    'valor' => (string) User::query()->where('activo', true)->count(),
                    'detalle' => 'cuentas habilitadas',
                    'tono' => 'maroon',
                    'icono' => 'usuarios',
                ],
            ];
        }

        if ($usuario->esConductor()) {
            return [
                [
                    'etiqueta' => 'Avisos activos',
                    'valor' => (string) $alertasActivas,
                    'detalle' => 'pendientes de atención',
                    'tono' => 'amber',
                    'icono' => 'alertas',
                ],
                [
                    'etiqueta' => 'Paraderos en ruta',
                    'valor' => (string) $paraderosActivos,
                    'detalle' => 'disponibles en el campus',
                    'tono' => 'emerald',
                    'icono' => 'paraderos',
                ],
                $estadoBus,
                [
                    'etiqueta' => 'Atendidos hoy',
                    'valor' => (string) AlertaAsistencia::query()
                        ->where('conductor_id', $usuario->id)
                        ->where('estado', AlertaAsistencia::ESTADO_ATENDIDA)
                        ->whereDate('atendido_at', today())
                        ->count(),
                    'detalle' => 'avisos finalizados',
                    'tono' => 'maroon',
                    'icono' => 'check',
                ],
            ];
        }

        return [
            [
                'etiqueta' => 'Llegada estimada',
                'valor' => $etaMinutos === null ? '—' : "~{$etaMinutos} min",
                'detalle' => $etaMinutos === null ? 'sin ubicación reciente' : 'al paradero más cercano',
                'tono' => 'maroon',
                'icono' => 'reloj',
            ],
            [
                'etiqueta' => 'Paraderos disponibles',
                'valor' => (string) $paraderosActivos,
                'detalle' => 'en el campus',
                'tono' => 'emerald',
                'icono' => 'paraderos',
            ],
            [
                'etiqueta' => 'Mis avisos activos',
                'valor' => (string) $alertasActivas,
                'detalle' => 'en espera del bus',
                'tono' => 'amber',
                'icono' => 'alertas',
            ],
            $estadoBus,
        ];
    }

    /**
     * @return array<int, array{titulo: string, descripcion: string, ruta: string, icono: string}>
     */
    private function accesos(User $usuario): array
    {
        if ($usuario->esAdministrador()) {
            return [
                [
                    'titulo' => 'Administrar paraderos',
                    'descripcion' => 'Agrega y revisa las ubicaciones del campus.',
                    'ruta' => route('admin.index'),
                    'icono' => 'paraderos',
                ],
                [
                    'titulo' => 'Vista del pasajero',
                    'descripcion' => 'Revisa el seguimiento y el tiempo de llegada.',
                    'ruta' => route('pasajero.index'),
                    'icono' => 'bus',
                ],
            ];
        }

        if ($usuario->esConductor()) {
            return [
                [
                    'titulo' => 'Mi viaje',
                    'descripcion' => 'Envía tu ubicación y atiende los avisos.',
                    'ruta' => route('conductor.index'),
                    'icono' => 'bus',
                ],
            ];
        }

        $accesos = [
            [
                'titulo' => 'Mi bus',
                'descripcion' => 'Consulta el tiempo de llegada a tu paradero.',
                'ruta' => route('pasajero.index'),
                'icono' => 'bus',
            ],
        ];

        if ($usuario->esInvidente()) {
            $accesos[] = [
                'titulo' => 'Panel accesible',
                'descripcion' => 'Detección automática y avisos por voz.',
                'ruta' => route('pasajero.invidente.index'),
                'icono' => 'alertas',
            ];
        }

        return $accesos;
    }
}
