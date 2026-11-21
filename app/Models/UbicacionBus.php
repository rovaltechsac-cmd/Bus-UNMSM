<?php

namespace App\Models;

use Database\Factories\UbicacionBusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conductor_id', 'latitud', 'longitud', 'rumbo', 'exactitud_m', 'registrado_at'])]
class UbicacionBus extends Model
{
    /** @use HasFactory<UbicacionBusFactory> */
    use HasFactory;

    protected $table = 'ubicaciones_bus';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'rumbo' => 'decimal:2',
            'registrado_at' => 'datetime',
        ];
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    public function distanciaHasta(Paradero $paradero): float
    {
        $radioTierraMetros = 6371000.0;
        $latitudOrigen = deg2rad((float) $this->latitud);
        $latitudDestino = deg2rad((float) $paradero->latitud);
        $deltaLatitud = $latitudDestino - $latitudOrigen;
        $deltaLongitud = deg2rad((float) $paradero->longitud - (float) $this->longitud);

        $a = sin($deltaLatitud / 2) ** 2
            + cos($latitudOrigen) * cos($latitudDestino) * sin($deltaLongitud / 2) ** 2;

        return round($radioTierraMetros * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    public function etaMinutos(Paradero $paradero): int
    {
        $velocidadMetrosPorMinuto = max(1.0, ((float) config('campus.velocidad_promedio_kmh') * 1000) / 60);

        return (int) ceil($this->distanciaHasta($paradero) / $velocidadMetrosPorMinuto);
    }

    public function esReciente(): bool
    {
        return $this->registrado_at?->gte(
            now()->subMinutes((int) config('campus.bus_inactivo_minutos'))
        ) ?? false;
    }
}
