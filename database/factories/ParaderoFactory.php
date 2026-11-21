<?php

namespace Database\Factories;

use App\Models\Paradero;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paradero>
 */
class ParaderoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Paradero '.fake()->unique()->word().' '.fake()->numberBetween(1, 999),
            'descripcion' => fake()->sentence(),
            'latitud' => fake()->latitude(-12.10, -12.02),
            'longitud' => fake()->longitude(-77.12, -77.04),
            'orden' => fake()->numberBetween(1, 1000),
            'activo' => true,
        ];
    }
}
