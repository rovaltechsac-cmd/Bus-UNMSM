<?php

namespace Database\Factories;

use App\Models\AlertaAsistencia;
use App\Models\Paradero;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlertaAsistencia>
 */
class AlertaAsistenciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pasajero_id' => User::factory(),
            'paradero_id' => Paradero::factory(),
            'tipo' => AlertaAsistencia::TIPO_INVIDENTE,
            'estado' => AlertaAsistencia::ESTADO_PENDIENTE,
            'nota' => null,
            'atendido_at' => null,
        ];
    }

    public function enAtencion(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => AlertaAsistencia::ESTADO_EN_ATENCION,
            'atendido_at' => now(),
        ]);
    }

    public function atendida(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => AlertaAsistencia::ESTADO_ATENDIDA,
            'atendido_at' => now(),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => AlertaAsistencia::ESTADO_CANCELADA,
        ]);
    }
}
