<?php

namespace App\Models;

use Database\Factories\AlertaAsistenciaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pasajero_id', 'paradero_id', 'conductor_id', 'tipo', 'estado', 'nota', 'atendido_at'])]
class AlertaAsistencia extends Model
{
    /** @use HasFactory<AlertaAsistenciaFactory> */
    use HasFactory;

    protected $table = 'alertas_asistencia';

    public const TIPO_INVIDENTE = 'invidente';

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_EN_ATENCION = 'en_atencion';

    public const ESTADO_ATENDIDA = 'atendida';

    public const ESTADO_CANCELADA = 'cancelada';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'atendido_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function pendientes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    #[Scope]
    protected function activas(Builder $query): Builder
    {
        return $query->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_EN_ATENCION]);
    }

    public function pasajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pasajero_id');
    }

    public function paradero(): BelongsTo
    {
        return $this->belongsTo(Paradero::class);
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    public function atender(User $conductor): bool
    {
        return $this->update([
            'conductor_id' => $conductor->id,
            'estado' => self::ESTADO_EN_ATENCION,
            'atendido_at' => now(),
        ]);
    }

    public function marcarAtendida(): bool
    {
        return $this->update(['estado' => self::ESTADO_ATENDIDA]);
    }

    public function cancelar(): bool
    {
        return $this->update(['estado' => self::ESTADO_CANCELADA]);
    }
}
